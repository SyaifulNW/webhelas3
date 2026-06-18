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
        $baseQuery = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
            ->where('salesplans.status', 'sudah_transfer')
            ->where('peserta_smis.approval_status', 'Approved')
            ->when($year, function ($q) use ($year) {
                $q->whereYear('salesplans.updated_at', $year);
            })
            ->when($month, function ($q) use ($month) {
                $q->whereMonth('salesplans.updated_at', $month);
            });

        $sumSql = 'CAST(COALESCE(NULLIF(COALESCE(peserta_smis.pembayaran_spp, 0) + COALESCE(peserta_smis.spp_1, 0) + COALESCE(peserta_smis.spp_2, 0) + COALESCE(peserta_smis.spp_3, 0) + COALESCE(peserta_smis.spp_4, 0) + COALESCE(peserta_smis.spp_5, 0) + COALESCE(peserta_smis.spp_6, 0) + COALESCE(peserta_smis.spp_7, 0) + COALESCE(peserta_smis.spp_8, 0) + COALESCE(peserta_smis.spp_9, 0) + COALESCE(peserta_smis.spp_10, 0) + COALESCE(peserta_smis.spp_11, 0) + COALESCE(peserta_smis.spp_12, 0), 0), GREATEST(0, COALESCE(salesplans.nominal, 0) - 500000), 0) AS DECIMAL(15,2))';
        $sppSql = $sumSql;

        // 1. Omset Pribadi (includes pendaftaran)
        $omsetPribadi = (clone $baseQuery)
            ->where('salesplans.created_by', $userId)
            ->sum(DB::raw($sumSql));

        // 2. Omset Reseller (includes pendaftaran)
        $omsetReseller = 0;
        if ($resellerMembersIds->isNotEmpty()) {
            $omsetReseller = (clone $baseQuery)
                ->whereIn('salesplans.created_by', $resellerMembersIds)
                ->sum(DB::raw($sumSql));
        }

        // SPP Pribadi (excludes pendaftaran) for Komisi
        $sppPribadi = (clone $baseQuery)
            ->where('salesplans.created_by', $userId)
            ->sum(DB::raw($sppSql));

        // SPP Reseller (excludes pendaftaran) for Royalti
        $sppReseller = 0;
        if ($resellerMembersIds->isNotEmpty()) {
            $sppReseller = (clone $baseQuery)
                ->whereIn('salesplans.created_by', $resellerMembersIds)
                ->sum(DB::raw($sppSql));
        }

        // 3. Komisi (10% of Personal SPP)
        $komisi = $sppPribadi * 0.10;

        // 4. Direct Fee (Rp 500.000 per Approved regional participant for Chapter)
        $directFee = 0;
        if ($isChapter) {
            $totalParticipantsCount = (clone $baseQuery)
                ->whereIn('salesplans.created_by', $regionalTeamIds)
                ->count();
            $directFee = $totalParticipantsCount * 500000;
        }

        // 5. Royalty (5% of Reseller SPP)
        $royalti = $sppReseller * 0.05;

        // 6. Bonus Pribadi (Tiered: ≥10jt → 5%, ≥20jt → 10%)
        // HANYA dihitung pada bulan pertama peserta mendaftar, BUKAN setiap bulan.
        $bonusPribadi = 0;
        $userJoinMonth = $user->created_at ? $user->created_at->format('Y-m') : null;

        if ($userJoinMonth) {
            // Skip if filter doesn't include the join month
            if ($year) {
                $joinYear = (int) substr($userJoinMonth, 0, 4);
                if ($joinYear != $year) {
                    $userJoinMonth = null;
                }
            }
            if ($month && $userJoinMonth) {
                $joinMonth = (int) substr($userJoinMonth, 5, 2);
                if ($joinMonth != $month) {
                    $userJoinMonth = null;
                }
            }
        }

        if ($userJoinMonth) {
            $firstMonthOmset = (clone $baseQuery)
                ->where('salesplans.created_by', $userId)
                ->whereRaw("DATE_FORMAT(salesplans.updated_at, '%Y-%m') = ?", [$userJoinMonth])
                ->sum(DB::raw($sumSql));
            $firstMonthOmset = (float) $firstMonthOmset;

            if ($firstMonthOmset >= 20000000) {
                $bonusPribadi = $firstMonthOmset * 0.10;
            } elseif ($firstMonthOmset >= 10000000) {
                $bonusPribadi = $firstMonthOmset * 0.05;
            }
        }

        // 7. Bonus Tim (10% if Team Sales >= 30,000,000)
        $bonusTim = 0;
        $totalTeamSales = $omsetPribadi + $omsetReseller;
        if ($resellerMembersIds->isNotEmpty() && $totalTeamSales >= 30000000) {
            $bonusTim = $totalTeamSales * 0.10;
        }

        return (float) ($komisi + $directFee + $royalti + $bonusPribadi + $bonusTim);
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
                // Initial SPP Commission (10%)
                if ($baseSpp > 0) {
                    $transactions->push([
                        'created_at' => \Carbon\Carbon::parse($tanggalMasuk),
                        'type' => 'income',
                        'source' => 'Komisi Closing - ' . $pesertaNama,
                        'description' => $isFallback ? 'Komisi 10% SPP Awal (Estimasi)' : 'Komisi 10% SPP Awal',
                        'amount' => $baseSpp * 0.10,
                        'status' => 'success',
                        'reference_no' => 'INC-KM-' . strtoupper(substr(md5($plan->id . '_init_km'), -6)),
                        'admin_note' => '-',
                    ]);
                }

                // Monthly SPP Commissions (10%)
                for ($i = 1; $i <= 12; $i++) {
                    $sppVal = (float)$plan->{"spp_$i"};
                    if ($sppVal > 0) {
                        $sppDate = $plan->{"tanggal_spp_$i"} ?: $tanggalMasuk;
                        $transactions->push([
                            'created_at' => \Carbon\Carbon::parse($sppDate),
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

            // 3. Royalty from Reseller (direct downline)
            if ($resellerMembersIds->contains($creatorId)) {
                // Initial SPP Royalty (5%)
                if ($baseSpp > 0) {
                    $transactions->push([
                        'created_at' => \Carbon\Carbon::parse($tanggalMasuk),
                        'type' => 'income',
                        'source' => 'Royalti Closing - ' . $pesertaNama,
                        'description' => 'Royalti 5% SPP Awal (CS: ' . $creatorName . ')',
                        'amount' => $baseSpp * 0.05,
                        'status' => 'success',
                        'reference_no' => 'INC-RY-' . strtoupper(substr(md5($plan->id . '_init_ry'), -6)),
                        'admin_note' => '-',
                    ]);
                }

                // Monthly SPP Royalties (5%)
                for ($i = 1; $i <= 12; $i++) {
                    $sppVal = (float)$plan->{"spp_$i"};
                    if ($sppVal > 0) {
                        $sppDate = $plan->{"tanggal_spp_$i"} ?: $tanggalMasuk;
                        $transactions->push([
                            'created_at' => \Carbon\Carbon::parse($sppDate),
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

            // Bonus Pribadi - HANYA di bulan pertama peserta mendaftar (bukan setiap bulan)
            $userJoinMonth = $user->created_at ? $user->created_at->format('Y-m') : null;
            $bonusPribadi = 0;
            if ($userJoinMonth && $yearMonth === $userJoinMonth) {
                if ($monthlyOmsetPribadi >= 20000000) {
                    $bonusPribadi = $monthlyOmsetPribadi * 0.10;
                } elseif ($monthlyOmsetPribadi >= 10000000) {
                    $bonusPribadi = $monthlyOmsetPribadi * 0.05;
                }
            }

            if ($bonusPribadi > 0) {
                $transactions->push([
                    'created_at' => $carbonDate->copy(),
                    'type' => 'income',
                    'source' => 'Bonus Pribadi',
                    'description' => 'Bonus Pencapaian Omset Pribadi Bulan Pertama (' . $yearMonth . ')',
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
