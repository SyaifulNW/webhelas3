<?php

namespace App\Services;

use App\Models\User;
use App\Models\SalesPlan;
use Illuminate\Support\Facades\DB;

class EarningsService
{
    /**
     * Calculate total earnings for a user (Chapter/Reseller/Agen) based on approved sales.
     * If no year/month is provided, it calculates for all time.
     */
    public static function calculateTotalEarnings($userId, $year = null, $month = null)
    {
        $user = User::find($userId);
        if (!$user) return 0;

        $transactions = self::getDynamicTransactions($userId, $year, $month);
        return (float) $transactions->where('type', 'income')->sum('amount');
    }

    public static function getDynamicTransactions($userId, $year = null, $month = null)
    {
        $user = User::find($userId);
        if (!$user) return collect([]);

        $role = strtolower($user->role);
        $chapterName = $user->chapter;
        $isChapter = ($role === 'chapter');
        $cleanChapterName = trim(str_ireplace('CHAPTER', '', $chapterName));

        // Identify Team Members
        $resellerMembersIds = User::where('role', 'reseller')
            ->where('created_by', $userId)
            ->pluck('id');
        
        $allTeamIds = $resellerMembersIds->merge([$userId])->unique();

        // Regional IDs for Chapter
        $regionalTeamIds = $allTeamIds;
        if ($isChapter) {
            $regionalMemberIds = User::where('role', 'reseller')
                ->where('chapter', 'LIKE', '%' . $cleanChapterName . '%')
                ->pluck('id');
            $regionalTeamIds = $regionalMemberIds->merge([$userId])->unique();
        }

        // Base Query for Approved Sales
        $query = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
            ->where('salesplans.status', 'sudah_transfer')
            ->where('peserta_smis.approval_status', 'Approved');

        $plans = $query->select(
            'salesplans.*',
            'peserta_smis.nama as peserta_nama',
            'peserta_smis.level as peserta_level',
            'peserta_smis.pembayaran_spp',
            'peserta_smis.biaya_pendaftaran',
            'peserta_smis.tanggal_masuk',
            'peserta_smis.spp_1', 'peserta_smis.spp_2', 'peserta_smis.spp_3', 'peserta_smis.spp_4',
            'peserta_smis.spp_5', 'peserta_smis.spp_6', 'peserta_smis.spp_7', 'peserta_smis.spp_8',
            'peserta_smis.spp_9', 'peserta_smis.spp_10', 'peserta_smis.spp_11', 'peserta_smis.spp_12',
            'peserta_smis.tanggal_spp_1', 'peserta_smis.tanggal_spp_2', 'peserta_smis.tanggal_spp_3', 'peserta_smis.tanggal_spp_4',
            'peserta_smis.tanggal_spp_5', 'peserta_smis.tanggal_spp_6', 'peserta_smis.tanggal_spp_7', 'peserta_smis.tanggal_spp_8',
            'peserta_smis.tanggal_spp_9', 'peserta_smis.tanggal_spp_10', 'peserta_smis.tanggal_spp_11', 'peserta_smis.tanggal_spp_12'
        )
        ->get();

        $transactions = collect([]);

        // Get list of CS names/users for reference
        $usersMap = User::whereIn('id', $allTeamIds->merge($regionalTeamIds))->get()->keyBy('id');

        foreach ($plans as $plan) {
            $creatorId = $plan->created_by;
            $pesertaNama = $plan->peserta_nama;
            $nominal = $plan->nominal;
            $pembayaranSpp = (float)$plan->pembayaran_spp;
            $tanggalMasuk = $plan->tanggal_masuk ?: $plan->updated_at;
            $creatorName = isset($usersMap[$creatorId]) ? $usersMap[$creatorId]->name : 'Reseller';

            // Base SQL SPP amount calculation logic
            $baseSpp = $pembayaranSpp;
            $isFallback = false;
            if ($baseSpp <= 0) {
                $baseSpp = max(0, $nominal - 500000);
                $isFallback = true;
            }

            // 1. Personal Sale -> Commission
            if ($creatorId == $userId) {
                // Monthly SPP Commissions (10%)
                for ($i = 1; $i <= 12; $i++) {
                    $sppVal = (float)$plan->{"spp_$i"};
                    $sppDate = null;
                    
                    if ($sppVal > 0) {
                        $sppDate = $plan->{"tanggal_spp_$i"} ?: $tanggalMasuk;
                    } else {
                        // Check if this month is part of the initial/scheduled plan (Blue checklist)
                        $effectiveDate = $plan->tanggal_closing ? \Carbon\Carbon::parse($plan->tanggal_closing) : \Carbon\Carbon::parse($tanggalMasuk);
                        $effectiveYear = $effectiveDate->format('Y');
                        
                        $selectedMonths = [];
                        $excludedMonths = [];
                        if ($plan->selected_months) {
                            $sel = $plan->selected_months;
                            if (is_array($sel)) {
                                $selectedMonths = $sel;
                            } else if (is_string($sel)) {
                                $selectedMonths = json_decode($sel, true) ?? [];
                            }
                        }
                        if (isset($selectedMonths['excluded_months'])) {
                            $excludedMonths = $selectedMonths['excluded_months'];
                            unset($selectedMonths['excluded_months']);
                        }

                        $isPlanChecked = false;
                        if ((int)$effectiveDate->format('n') == $i) {
                            $isPlanChecked = true;
                        }
                        if (isset($selectedMonths[$effectiveYear]) && in_array((int)$i, $selectedMonths[$effectiveYear])) {
                            $isPlanChecked = true;
                        }
                        if ($effectiveYear == 2026 && $i <= 3) {
                            $isPlanChecked = false;
                        }
                        if (isset($excludedMonths[$effectiveYear]) && in_array((int)$i, $excludedMonths[$effectiveYear])) {
                            $isPlanChecked = false;
                        }

                        if ($isPlanChecked) {
                            $itemLevel = strtolower($plan->peserta_level ?? $plan->level ?? '');
                            $levelNominal = str_contains($itemLevel, 'grow') ? 1500000 : 1000000;
                            $sppVal = $pembayaranSpp > 0 ? $pembayaranSpp : $levelNominal;
                            $sppDate = $effectiveDate;
                        }
                    }

                    if ($sppVal > 0 && $sppDate) {
                        $parsedSppDate = \Carbon\Carbon::parse($sppDate);
                        $targetMonth = $i;
                        $targetYear = $parsedSppDate->year;
                        $targetDay = min($parsedSppDate->day, \Carbon\Carbon::create($targetYear, $targetMonth, 1)->daysInMonth);
                        $transactionDate = \Carbon\Carbon::create($targetYear, $targetMonth, $targetDay, 0, 0, 0);

                        $transactions->push([
                            'created_at' => $transactionDate,
                            'type' => 'income',
                            'source' => 'Komisi SPP Bulan ' . $i . ' - ' . $pesertaNama,
                            'description' => 'Komisi 10% SPP Bulan ' . $i,
                            'amount' => $sppVal * 0.10,
                            'status' => 'success',
                            'reference_no' => 'INC-KM-' . strtoupper(substr(md5($plan->id . "_spp_{$i}_km"), -6)),
                            'admin_note' => '-',
                        ]);
                    }
                }
            }

            // 2. Direct Fee for Chapter (from regional team members)
            if ($isChapter && $regionalTeamIds->contains($creatorId)) {
                $transactions->push([
                    'created_at' => \Carbon\Carbon::parse($tanggalMasuk),
                    'type' => 'income',
                    'source' => 'Direct Fee - ' . $pesertaNama,
                    'description' => 'Direct Fee Closing Regional (CS: ' . $creatorName . ')',
                    'amount' => 500000.00,
                    'status' => 'success',
                    'reference_no' => 'INC-DF-' . strtoupper(substr(md5($plan->id . '_df'), -6)),
                    'admin_note' => '-',
                ]);
            }

            // 2b. Direct Fee for Agen Pusat (from personal closings)
            $isAgenPusat = (strtolower($user->role) === 'agen' && $user->kategori === 'Agen Pusat');
            if ($isAgenPusat && $creatorId == $userId) {
                $transactions->push([
                    'created_at' => \Carbon\Carbon::parse($tanggalMasuk),
                    'type' => 'income',
                    'source' => 'Direct Fee - ' . $pesertaNama,
                    'description' => 'Direct Fee Closing (Agen Pusat)',
                    'amount' => 200000.00,
                    'status' => 'success',
                    'reference_no' => 'INC-DF-' . strtoupper(substr(md5($plan->id . '_df_agen'), -6)),
                    'admin_note' => '-',
                ]);
            }

            // 3. Royalty from Reseller (direct downline)
            if ($resellerMembersIds->contains($creatorId)) {
                // Monthly SPP Royalties (5%)
                for ($i = 1; $i <= 12; $i++) {
                    $sppVal = (float)$plan->{"spp_$i"};
                    $sppDate = null;
                    
                    if ($sppVal > 0) {
                        $sppDate = $plan->{"tanggal_spp_$i"} ?: $tanggalMasuk;
                    } else {
                        // Check if this month is part of the initial/scheduled plan (Blue checklist)
                        $effectiveDate = $plan->tanggal_closing ? \Carbon\Carbon::parse($plan->tanggal_closing) : \Carbon\Carbon::parse($tanggalMasuk);
                        $effectiveYear = $effectiveDate->format('Y');
                        
                        $selectedMonths = [];
                        $excludedMonths = [];
                        if ($plan->selected_months) {
                            $sel = $plan->selected_months;
                            if (is_array($sel)) {
                                $selectedMonths = $sel;
                            } else if (is_string($sel)) {
                                $selectedMonths = json_decode($sel, true) ?? [];
                            }
                        }
                        if (isset($selectedMonths['excluded_months'])) {
                            $excludedMonths = $selectedMonths['excluded_months'];
                            unset($selectedMonths['excluded_months']);
                        }

                        $isPlanChecked = false;
                        if ((int)$effectiveDate->format('n') == $i) {
                            $isPlanChecked = true;
                        }
                        if (isset($selectedMonths[$effectiveYear]) && in_array((int)$i, $selectedMonths[$effectiveYear])) {
                            $isPlanChecked = true;
                        }
                        if ($effectiveYear == 2026 && $i <= 3) {
                            $isPlanChecked = false;
                        }
                        if (isset($excludedMonths[$effectiveYear]) && in_array((int)$i, $excludedMonths[$effectiveYear])) {
                            $isPlanChecked = false;
                        }

                        if ($isPlanChecked) {
                            $itemLevel = strtolower($plan->peserta_level ?? $plan->level ?? '');
                            $levelNominal = str_contains($itemLevel, 'grow') ? 1500000 : 1000000;
                            $sppVal = $pembayaranSpp > 0 ? $pembayaranSpp : $levelNominal;
                            $sppDate = $effectiveDate;
                        }
                    }

                    if ($sppVal > 0 && $sppDate) {
                        $parsedSppDate = \Carbon\Carbon::parse($sppDate);
                        $targetMonth = $i;
                        $targetYear = $parsedSppDate->year;
                        $targetDay = min($parsedSppDate->day, \Carbon\Carbon::create($targetYear, $targetMonth, 1)->daysInMonth);
                        $transactionDate = \Carbon\Carbon::create($targetYear, $targetMonth, $targetDay, 0, 0, 0);

                        $transactions->push([
                            'created_at' => $transactionDate,
                            'type' => 'income',
                            'source' => 'Royalti SPP Bulan ' . $i . ' - ' . $pesertaNama,
                            'description' => 'Royalti 5% SPP Bulan ' . $i . ' (CS: ' . $creatorName . ')',
                            'amount' => $sppVal * 0.05,
                            'status' => 'success',
                            'reference_no' => 'INC-RY-' . strtoupper(substr(md5($plan->id . "_spp_{$i}_ry"), -6)),
                            'admin_note' => '-',
                        ]);
                    }
                }
            }
        }

        // 4. Bonus Pribadi & Bonus Tim (calculated per month-year grouping)
        $groupedByMonth = $plans->groupBy(function ($plan) {
            $tanggal = \Carbon\Carbon::parse($plan->tanggal_masuk ?: $plan->updated_at);
            return $tanggal->format('Y-m');
        });

        foreach ($groupedByMonth as $yearMonth => $monthPlans) {
            // Calculate monthly values
            $monthlyOmsetPribadi = 0;
            $monthlyOmsetReseller = 0;

            foreach ($monthPlans as $plan) {
                $creatorId = $plan->created_by;
                $nominal = (float)$plan->nominal;
                $pembayaranSpp = (float)$plan->pembayaran_spp;
                $biayaPendaftaran = (float)$plan->biaya_pendaftaran;

                // Sum all SPP (excluding registration fee)
                $totalPaid = $pembayaranSpp;
                for ($i = 1; $i <= 12; $i++) {
                    $totalPaid += (float)$plan->{"spp_$i"};
                }
                if ($totalPaid <= 0) {
                    $totalPaid = max(0, $nominal - 500000);
                }

                if ($creatorId == $userId) {
                    $monthlyOmsetPribadi += $totalPaid;
                }
                if ($resellerMembersIds->contains($creatorId)) {
                    $monthlyOmsetReseller += $totalPaid;
                }
            }

            $carbonDate = \Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

            // Bonus Pribadi - berdasarkan jumlah closing di bulan pertama user mendaftar
            // Rumus: jumlah_closing × Rp 2.000.000 × tier% (5% jika ≥10jt, 10% jika ≥20jt)
            $userJoinMonth = $user->created_at ? $user->created_at->format('Y-m') : null;
            $bonusPribadi = 0;
            if ($userJoinMonth && $yearMonth === $userJoinMonth) {
                $closingCount = 0;
                foreach ($monthPlans as $plan) {
                    if ($plan->created_by == $userId) {
                        $closingCount++;
                    }
                }
                $baseOmset = $closingCount * 2000000;
                if ($baseOmset >= 20000000) {
                    $bonusPribadi = $baseOmset * 0.10;
                } elseif ($baseOmset >= 10000000) {
                    $bonusPribadi = $baseOmset * 0.05;
                }
            }

            if ($bonusPribadi > 0) {
                $transactions->push([
                    'created_at' => $carbonDate->copy(),
                    'type' => 'income',
                    'source' => 'Bonus Pribadi',
                    'description' => 'Bonus Pencapaian Closing Bulan Pertama (' . $yearMonth . ')',
                    'amount' => $bonusPribadi,
                    'status' => 'success',
                    'reference_no' => 'INC-BP-' . strtoupper(substr(md5($userId . '_' . $yearMonth . '_bp'), -6)),
                    'admin_note' => '-',
                ]);
            }

            // Bonus Tim
            $bonusTim = 0;
            $totalTeamSales = $monthlyOmsetPribadi + $monthlyOmsetReseller;
            if ($resellerMembersIds->isNotEmpty() && $totalTeamSales >= 30000000) {
                $bonusTim = $totalTeamSales * 0.10;
            }

            if ($bonusTim > 0) {
                $transactions->push([
                    'created_at' => $carbonDate->copy(),
                    'type' => 'income',
                    'source' => 'Bonus Tim Bulanan',
                    'description' => 'Bonus Pencapaian Omset Tim (' . $yearMonth . ')',
                    'amount' => $bonusTim,
                    'status' => 'success',
                    'reference_no' => 'INC-BT-' . strtoupper(substr(md5($userId . '_' . $yearMonth . '_bt'), -6)),
                    'admin_note' => '-',
                ]);
            }
        }

        // Apply filters if year/month is passed
        if ($year) {
            $transactions = $transactions->filter(function ($t) use ($year) {
                return $t['created_at']->year == $year;
            });
        }
        if ($month) {
            $transactions = $transactions->filter(function ($t) use ($month) {
                return $t['created_at']->month == $month;
            });
        }

        // Add default properties to prevent Blade errors
        $transactions = $transactions->map(function ($t) {
            $t['id'] = null;
            $t['proof_of_transfer'] = null;
            $t['bank_name'] = null;
            $t['account_number'] = null;
            $t['account_name'] = null;
            return $t;
        });

        return $transactions->sortByDesc('created_at')->values();
    }
}
