<?php

namespace App\Http\Controllers\Admin\Core;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\LabaRugi;
use PDF;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function operasional()
    {
        // Real data calculations for Operasional
        $stats = [
            'proyek_berjalan' => \App\Models\Inisiatif::where('status', 'Progress')->count(),
            'karyawan_aktif' => \App\Models\User::where('is_active', 1)->count(),
            'tiket_pending' => \App\Models\MonitoringPerbaikan::where('progress', '!=', '100%')->count(),
            'inventory' => (int) \App\Models\InventarisKantor::sum('jumlah')
        ];

        return view('admin.Core.operasional', compact('stats'));
    }

    public function keuangan()
    {
        $bulan = date('m');
        $tahun = date('Y');

        // 1. Pemasukan (Manual LabaRugi + SalesPlan)
        $pemasukanManual = LabaRugi::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('type', 'pendapatan')
            ->sum('jumlah');

        $pemasukanSales = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->whereYear('updated_at', $tahun)
            ->whereMonth('updated_at', $bulan)
            ->sum('nominal');

        $pemasukanTotal = $pemasukanManual + $pemasukanSales;

        // 2. Pengeluaran (Manual LabaRugi)
        $pengeluaranTotal = LabaRugi::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('type', 'biaya')
            ->sum('jumlah');

        // 3. Profit
        $profit = $pemasukanTotal - $pengeluaranTotal;

        // 4. Pending Invoice (mau_transfer)
        $pendingInvoice = \App\Models\SalesPlan::where('status', 'mau_transfer')
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->count();

        $stats = [
            'pemasukan' => 'Rp ' . number_format($pemasukanTotal, 0, ',', '.'),
            'pengeluaran' => 'Rp ' . number_format($pengeluaranTotal, 0, ',', '.'),
            'profit' => 'Rp ' . number_format($profit, 0, ',', '.'),
            'pending_invoice' => $pendingInvoice
        ];

        return view('admin.Core.keuangan', compact('stats'));
    }

    public function labaRugi(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Fetch manual entries
        $manualQuery = LabaRugi::query();
        if ($bulan !== 'all')
            $manualQuery->where('bulan', $bulan);
        if ($tahun !== 'all')
            $manualQuery->where('tahun', $tahun);
        $manualData = $manualQuery->get();

        $pendapatan = $manualData->where('type', 'pendapatan');
        $biaya = $manualData->where('type', 'biaya');

        $buildDateFilter = function ($query, $y, $m) {
            $query->where(function ($q) use ($y, $m) {
                $q->where(function ($qM1T) use ($y, $m) {
                    $qM1T->whereHas('kelas', fn($k) => $k->where('nama_kelas', 'like', '%Start-Up Muslim Indonesia%'))
                         ->where(function ($qDate) use ($y, $m) {
                             if ($y !== 'all') { $qDate->whereYear('tanggal_closing', $y); }
                             if ($m !== 'all') { $qDate->whereMonth('tanggal_closing', $m); }
                         });
                })->orWhere(function ($qMBC) use ($y, $m) {
                    $qMBC->whereHas('kelas', function($k) use ($y, $m) {
                        $k->where('nama_kelas', 'not like', '%Start-Up Muslim Indonesia%')
                          ->where(function($kSub) use ($y, $m) {
                              $kSub->where('nama_kelas', 'like', '%Zoom Privat%')->orWhere('nama_kelas', 'like', '%Start-Up Muda Indonesia%');
                              if ($m !== 'all') {
                                  $kSub->orWhere(function($kMul) use ($y, $m) {
                                      if ($y !== 'all') { $kMul->whereYear('tanggal_mulai', $y); }
                                      $kMul->whereMonth('tanggal_mulai', $m);
                                  });
                              } else {
                                  if ($y !== 'all') { $kSub->orWhereYear('tanggal_mulai', $y); }
                              }
                          });
                    })->where(function ($qDate) use ($y, $m) {
                        if ($y !== 'all') { $qDate->whereYear('updated_at', $y); }
                        if ($m !== 'all') { $qDate->whereMonth('updated_at', $m); }
                    });
                });
            });
        };

        $applySalesPlanDateFilter = function ($query) use ($bulan, $tahun, $buildDateFilter) {
            $buildDateFilter($query, $tahun, $bulan);
        };

        $excludePartnerSales = function ($query) {
            $query->where(function ($q) {
                $q->whereDoesntHave('createdBy')
                  ->orWhereHas('createdBy', function ($sq) {
                      $sq->whereNotIn('role', ['chapter', 'reseller', 'agen']);
                  });
            });
        };

        // 1. SMI (System Auto Data with Fallback)
        $smiQuery = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->whereHas('kelas', function ($q) {
                $q->where('nama_kelas', 'like', '%Muslim Indonesia%')
                    ->orWhere('nama_kelas', 'like', 'SMI - %');
            });
        $excludePartnerSales($smiQuery);
        $applySalesPlanDateFilter($smiQuery);

        $smiBreakdown = $smiQuery->with('kelas:id,nama_kelas')
            ->get()
            ->groupBy('kelas.nama_kelas')
            ->map(fn($group) => $group->sum('nominal'));

        // REVISION: SPP and Tunggakan are now fully automated based on PesertaSmi checkboxes
        $totalSmiPendaftaran = (clone $smiQuery)->with('pesertaSmi')->get()->sum(function ($plan) {
            // [USER_REQUEST] Exclude status 'Cuti' from Pendaftaran revenue stats
            if ($plan->pesertaSmi && $plan->pesertaSmi->status === 'Cuti') {
                return 0;
            }

            $nominalAwal = $plan->nominal;
            if ($plan->pesertaSmi) {
                $calc = (float) $plan->pesertaSmi->spp_awal + (float) $plan->pesertaSmi->pembayaran_spp;
                if ($calc > 0) {
                    return $calc;
                }
                if ($plan->pesertaSmi->total_pembayaran) {
                    return (float) $plan->pesertaSmi->total_pembayaran;
                }
                if ($plan->pesertaSmi->spp_awal) {
                    return (float) $plan->pesertaSmi->spp_awal;
                }
            }
            return (float) $nominalAwal;
        });

        $calcSpp = $this->calculateSppData($bulan, $tahun);
        $totalSmiSpp = $calcSpp['spp'];
        $totalSmiTunggakan = $calcSpp['tunggakan'];

        $totalSmi = $totalSmiPendaftaran + $totalSmiSpp;

        // 2. MBC (Everything else except SMI and Private)
        $mbcQuery = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->where(function ($q) {
                $q->whereDoesntHave('kelas')
                    ->orWhereHas('kelas', function ($sub) {
                        $sub->where('nama_kelas', 'not like', '%Muslim Indonesia%')
                            ->where('nama_kelas', 'not like', 'SMI - %')
                            ->where('nama_kelas', 'not like', '%Privat%')
                            ->where('nama_kelas', 'not like', '%Coaching%');
                    });
            });
        $excludePartnerSales($mbcQuery);
        $applySalesPlanDateFilter($mbcQuery);

        $totalMbc = (clone $mbcQuery)->sum('nominal');
        $mbcBreakdown = $mbcQuery->with('kelas:id,nama_kelas')
            ->get()
            ->groupBy(function ($item) {
                return $item->kelas ? $item->kelas->nama_kelas : 'Tanpa Kelas / Lainnya';
            })
            ->map(fn($group) => $group->sum('nominal'));

        // 3. Private Coaching (Auto Fetch)
        $privateQuery = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->whereHas('kelas', function ($q) {
                $q->where('nama_kelas', 'like', '%Privat%')
                    ->orWhere('nama_kelas', 'like', '%Coaching%');
            });
        $excludePartnerSales($privateQuery);
        $applySalesPlanDateFilter($privateQuery);
        $totalPrivate = $privateQuery->sum('nominal');

        // calculate auto chapter and reseller sales
        $chapters = \App\Models\User::where('role', 'chapter')->orderBy('name')->get();
        $agens = \App\Models\User::where('role', 'reseller')->orderBy('name')->get();

        $chapterAutoSales = [];
        foreach ($chapters as $ch) {
            $chapterAutoSales[$ch->id] = [
                'personal' => 0,
                'agents' => 0,
                'total' => 0
            ];
        }

        $agenAutoSales = [];
        foreach ($agens as $ag) {
            $agenAutoSales[$ag->id] = 0;
        }

        $allPartnerSales = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->where(function ($q) use ($buildDateFilter, $tahun, $bulan) {
                $buildDateFilter($q, $tahun, $bulan);
            })
            ->whereHas('createdBy', function ($q) {
                $q->whereIn('role', ['chapter', 'reseller', 'agen']);
            })
            ->with(['pesertaSmi', 'createdBy'])
            ->get();

        $calculateOmset = function ($plan) {
            if ($plan->pesertaSmi) {
                return (float) str_replace('.', '', $plan->pesertaSmi->total_pembayaran ?: ($plan->pesertaSmi->pembayaran_spp ?: $plan->pesertaSmi->spp_awal ?: 0));
            }
            return (float) str_replace('.', '', $plan->nominal ?: 0);
        };

        foreach ($allPartnerSales as $sale) {
            $creator = $sale->createdBy;
            if (!$creator) continue;

            $amount = $calculateOmset($sale);

            if ($creator->role === 'chapter') {
                if (isset($chapterAutoSales[$creator->id])) {
                    $chapterAutoSales[$creator->id]['personal'] += $amount;
                }
            } else {
                $parentId = $creator->created_by;
                $chapterName = $creator->chapter;
                $matchedChId = null;

                if (isset($chapterAutoSales[$parentId])) {
                    $matchedChId = $parentId;
                } elseif (!empty($chapterName)) {
                    foreach ($chapters as $ch) {
                        if (trim(strtolower($ch->chapter)) === trim(strtolower($chapterName))) {
                            $matchedChId = $ch->id;
                            break;
                        }
                    }
                }

                if ($matchedChId) {
                    $chapterAutoSales[$matchedChId]['agents'] += $amount;
                } else {
                    if (isset($agenAutoSales[$creator->id])) {
                        $agenAutoSales[$creator->id] += $amount;
                    }
                }
            }
        }

        foreach ($chapterAutoSales as $chId => &$data) {
            $data['total'] = $data['personal'] + $data['agents'];
        }
        unset($data);

        $chapterTotal = 0;
        foreach ($chapters as $ch) {
            $chKeterangan = $ch->name;
            $subRow = $pendapatan->filter(fn($r) => trim($r->keterangan ?? '') === $chKeterangan && trim($r->parent_keterangan ?? '') === 'Pendapatan Chapter')->first();
            if ($subRow) {
                $chapterTotal += $subRow->jumlah;
            } else {
                $chapterTotal += $chapterAutoSales[$ch->id]['total'] ?? 0;
            }
        }

        $agenTotal = 0;
        foreach ($agens as $ag) {
            $agKeterangan = $ag->name;
            $subRow = $pendapatan->filter(fn($r) => trim($r->keterangan ?? '') === $agKeterangan && trim($r->parent_keterangan ?? '') === 'Pendapatan Agen')->first();
            if ($subRow) {
                $agenTotal += $subRow->jumlah;
            } else {
                $agenTotal += $agenAutoSales[$ag->id] ?? 0;
            }
        }

        // 4. Approved Budget Proposals (Pengajuan Anggaran)
        $approvedAnggaran = \App\Models\PengajuanAnggaran::where('status', 'approved')
            ->when($bulan !== 'all', function ($q) use ($bulan) {
                $q->whereMonth('tanggal_pengajuan', $bulan); })
            ->when($tahun !== 'all', function ($q) use ($tahun) {
                $q->whereYear('tanggal_pengajuan', $tahun); })
            ->get();

        $coachItems = [
            'Cicilan mobil Coach',
            'Cicilan mobil teh Lia',
            'Uang bulanan Fathin',
            'Gaji ART',
            'Uang bulanan teh Lia',
            'Cicilan 2 kartu kredit',
            'Paket paket ustad',
            'Hutang Tajirw',
            'Hutang pak Yusron',
            'Biaya program Dela',
            'Biaya Pengeluaran Coach'
        ];

        $anggaranMapped = $approvedAnggaran->map(function ($item) use ($coachItems) {
            $category = 'Biaya Lain-lain';
            $name = strtolower($item->nama_pengajuan);

            if (in_array($item->nama_pengajuan, $coachItems)) {
                $category = 'Pengeluaran Coach';
            } elseif (strpos($name, 'kuota') !== false || strpos($name, 'pulsa') !== false) {
                $category = 'Biaya Kuota';
            } elseif (strpos($name, 'listrik') !== false || strpos($name, 'token') !== false) {
                $category = 'Biaya Listrik';
            } elseif (strpos($name, 'air') !== false) {
                $category = 'Biaya Air';
            } elseif (strpos($name, 'bpjs') !== false) {
                $category = 'Biaya BPJS';
            } elseif (strpos($name, 'wifi') !== false || strpos($name, 'internet') !== false || strpos($name, 'indihome') !== false) {
                $category = 'Biaya Internet & Wifi';
            } elseif (strpos($name, 'maintenance') !== false || strpos($name, 'website') !== false) {
                $category = 'Biaya Maintenance Web';
            } elseif (strpos($name, 'gaji') !== false || strpos($name, 'upah') !== false) {
                $category = 'Biaya Gaji Karyawan';
            } elseif (strpos($name, 'iklan') !== false || strpos($name, 'ads') !== false || strpos($name, 'facebook') !== false || strpos($name, 'instagram') !== false) {
                $category = 'Biaya Iklan';
            } elseif (strpos($name, 'kebersihan') !== false || strpos($name, 'sampah') !== false || strpos($name, 'keamanan') !== false) {
                $category = 'Biaya Kebersihan & Keamanan';
            }

            return (object) [
                'id' => 'anggaran-' . $item->id,
                'tanggal' => $item->tanggal_pengajuan ? $item->tanggal_pengajuan->format('Y-m-d') : null,
                'type' => 'biaya',
                'parent_keterangan' => $category,
                'keterangan' => $item->nama_pengajuan,
                'jumlah' => $item->biaya_disetujui ?? $item->jumlah_biaya,
                'is_auto' => true
            ];
        });

        $biaya = $biaya->concat($anggaranMapped);

        // Fetch Classes for the selected month for auto-population of "Biaya Event Kelas"
        $kelasBulanIni = \App\Models\Kelas::when($bulan !== 'all' || $tahun !== 'all', function ($q) use ($bulan, $tahun) {
            $q->where(function ($sq) use ($bulan, $tahun) {
                if ($bulan !== 'all' && $tahun !== 'all') {
                    $sq->whereMonth('tanggal_mulai', $bulan)->whereYear('tanggal_mulai', $tahun)
                        ->orWhereMonth('tanggal_selesai', $bulan)->whereYear('tanggal_selesai', $tahun);
                } elseif ($bulan !== 'all') {
                    $sq->whereMonth('tanggal_mulai', $bulan)->orWhereMonth('tanggal_selesai', $bulan);
                } elseif ($tahun !== 'all') {
                    $sq->whereYear('tanggal_mulai', $tahun)->orWhereYear('tanggal_selesai', $tahun);
                }
            });
        })->get();

        // Fetch All Classes for "Biaya Iklan" expand/collapse
        $semuaKelas = \App\Models\Kelas::orderBy('nama_kelas')->get();

        return view('admin.Finance.keuangan.laba-rugi', compact('pendapatan', 'biaya', 'bulan', 'tahun', 'totalSmi', 'totalSmiPendaftaran', 'totalSmiSpp', 'totalSmiTunggakan', 'smiBreakdown', 'totalMbc', 'mbcBreakdown', 'totalPrivate', 'kelasBulanIni', 'semuaKelas', 'chapterAutoSales', 'agenAutoSales', 'chapterTotal', 'agenTotal'));
    }

    public function zakat(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $buildDateFilter = function ($query, $y, $m) {
            $query->where(function ($q) use ($y, $m) {
                $q->where(function ($qM1T) use ($y, $m) {
                    $qM1T->whereHas('kelas', fn($k) => $k->where('nama_kelas', 'like', '%Start-Up Muslim Indonesia%'))
                         ->where(function ($qDate) use ($y, $m) {
                             if ($y !== 'all') { $qDate->whereYear('tanggal_closing', $y); }
                             if ($m !== 'all') { $qDate->whereMonth('tanggal_closing', $m); }
                         });
                })->orWhere(function ($qMBC) use ($y, $m) {
                    $qMBC->whereHas('kelas', function($k) use ($y, $m) {
                        $k->where('nama_kelas', 'not like', '%Start-Up Muslim Indonesia%')
                          ->where(function($kSub) use ($y, $m) {
                              $kSub->where('nama_kelas', 'like', '%Zoom Privat%')->orWhere('nama_kelas', 'like', '%Start-Up Muda Indonesia%');
                              if ($m !== 'all') {
                                  $kSub->orWhere(function($kMul) use ($y, $m) {
                                      if ($y !== 'all') { $kMul->whereYear('tanggal_mulai', $y); }
                                      $kMul->whereMonth('tanggal_mulai', $m);
                                  });
                              } else {
                                  if ($y !== 'all') { $kSub->orWhereYear('tanggal_mulai', $y); }
                              }
                          });
                    })->where(function ($qDate) use ($y, $m) {
                        if ($y !== 'all') { $qDate->whereYear('updated_at', $y); }
                        if ($m !== 'all') { $qDate->whereMonth('updated_at', $m); }
                    });
                });
            });
        };

        $applySalesPlanDateFilter = function ($query) use ($bulan, $tahun, $buildDateFilter) {
            $buildDateFilter($query, $tahun, $bulan);
        };

        // 1. Fetch Automated Data from SalesPlan (All Classes)
        $autoQuery = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->with('kelas:id,nama_kelas');
        $applySalesPlanDateFilter($autoQuery);

        $autoRecords = $autoQuery->get()
            ->groupBy('kelas.nama_kelas')
            ->map(function ($group, $className) {
                $sum = $group->sum('nominal');
                return (object) [
                    'id' => 'auto-' . md5($className),
                    'kelas' => $className ?? 'Kelas Tidak Terdefinisi',
                    'omset' => $sum,
                    'beban_zakat' => $sum * 0.025,
                    'is_auto' => true
                ];
            })->values();

        // 2. Fetch Manual Data from LabaRugi where type='zakat'
        $manualQuery = \App\Models\LabaRugi::where('type', 'zakat');
        if ($bulan !== 'all')
            $manualQuery->where('bulan', $bulan);
        if ($tahun !== 'all')
            $manualQuery->where('tahun', $tahun);

        $manualRecords = $manualQuery->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'kelas' => $item->keterangan,
                'omset' => $item->jumlah,
                'beban_zakat' => $item->jumlah * 0.025,
                'is_auto' => false
            ];
        });

        // 3. Fetch Manual Data from LabaRugi where type='zakat_fitra'
        $fitraQuery = \App\Models\LabaRugi::where('type', 'zakat_fitra');
        if ($bulan !== 'all')
            $fitraQuery->where('bulan', $bulan);
        if ($tahun !== 'all')
            $fitraQuery->where('tahun', $tahun);

        $zakatFitraRecords = $fitraQuery->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'keterangan' => $item->keterangan,
                'nominal' => $item->jumlah,
                'is_auto' => false
            ];
        });

        // Combine
        $zakatRecords = $autoRecords->concat($manualRecords);

        return view('admin.Finance.keuangan.zakat', compact('zakatRecords', 'zakatFitraRecords', 'bulan', 'tahun'));
    }

    public function getSmiDetails(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $namaKelas = $request->get('kelas');

        // 1. Peserta Baru (from SalesPlan closing this month with fallback)
        $salesQuery = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->whereHas('kelas', function ($q) use ($namaKelas) {
                $q->where('nama_kelas', $namaKelas);
            });

        // Date Fallback Logic
        $salesQuery->where(function ($q) use ($tahun, $bulan) {
            $q->where(function ($qM1T) use ($tahun, $bulan) {
                $qM1T->whereHas('kelas', fn($k) => $k->where('nama_kelas', 'like', '%Start-Up Muslim Indonesia%'))
                     ->where(function ($qDate) use ($tahun, $bulan) {
                         if ($tahun !== 'all') { $qDate->whereYear('tanggal_closing', $tahun); }
                         if ($bulan !== 'all') { $qDate->whereMonth('tanggal_closing', $bulan); }
                     });
            })->orWhere(function ($qMBC) use ($tahun, $bulan) {
                $qMBC->whereHas('kelas', function($k) use ($tahun, $bulan) {
                    $k->where('nama_kelas', 'not like', '%Start-Up Muslim Indonesia%')
                      ->where(function($kSub) use ($tahun, $bulan) {
                          $kSub->where('nama_kelas', 'like', '%Zoom Privat%')->orWhere('nama_kelas', 'like', '%Start-Up Muda Indonesia%');
                          if ($bulan !== 'all') {
                              $kSub->orWhere(function($kMul) use ($tahun, $bulan) {
                                  if ($tahun !== 'all') { $kMul->whereYear('tanggal_mulai', $tahun); }
                                  $kMul->whereMonth('tanggal_mulai', $bulan);
                              });
                          } else {
                              if ($tahun !== 'all') { $kSub->orWhereYear('tanggal_mulai', $tahun); }
                          }
                      });
                })->where(function ($qDate) use ($tahun, $bulan) {
                    if ($tahun !== 'all') { $qDate->whereYear('updated_at', $tahun); }
                    if ($bulan !== 'all') { $qDate->whereMonth('updated_at', $bulan); }
                });
            });
        });
        $baru = $salesQuery->select('id', 'nama', 'nominal')->get();

        // 2. SPP (from PesertaSmi checklist)
        $spp = collect();
        if ($bulan !== 'all' && $tahun !== 'all') {
            $calcPeriod = (int) ($tahun . str_pad($bulan, 2, '0', STR_PAD_LEFT));
            $currentPeriod = (int) date('Ym');

            // Assume SPP currently is mostly for 'Start-Up Muslim Indonesia' or matching class names
            if ($calcPeriod < $currentPeriod && (str_contains($namaKelas, 'Start-Up') || str_contains($namaKelas, 'Muslim Indonesia'))) {
                $colSpp = 'spp_' . (int) $bulan;
                $dateStart = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d');
                $dateEnd = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');

                $spp = \App\Models\PesertaSmi::where($colSpp, '>', 0)
                    ->whereRaw("PERIOD_DIFF(?, EXTRACT(YEAR_MONTH FROM tanggal_masuk)) BETWEEN 0 AND 5", [$calcPeriod])
                    ->whereDate('tanggal_selesai', '>=', $dateStart)
                    ->whereNotIn('sales_plan_id', $baru->pluck('id'))
                    ->select('nama', "{$colSpp} as nominal")
                    ->get();
            }
        }

        return response()->json([
            'success' => true,
            'kelas' => $namaKelas,
            'baru' => $baru,
            'spp' => $spp,
            'total_baru' => $baru->sum('nominal'),
            'total_spp' => $spp->sum('nominal')
        ]);
    }

    public function exportLabaRugiPdf(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Fetch manual entries
        $manualQuery = LabaRugi::query();
        if ($bulan !== 'all')
            $manualQuery->where('bulan', $bulan);
        if ($tahun !== 'all')
            $manualQuery->where('tahun', $tahun);
        $manualData = $manualQuery->get();

        $pendapatan = $manualData->where('type', 'pendapatan');
        $biaya = $manualData->where('type', 'biaya');

        // Helper closure to apply the complex date filter used in SalesPlanController
        $buildDateFilter = function ($query, $y, $m) {
            $query->where(function ($q) use ($y, $m) {
                $q->where(function ($qM1T) use ($y, $m) {
                    $qM1T->whereHas('kelas', fn($k) => $k->where('nama_kelas', 'like', '%Start-Up Muslim Indonesia%'))
                         ->where(function ($qDate) use ($y, $m) {
                             if ($y !== 'all') { $qDate->whereYear('tanggal_closing', $y); }
                             if ($m !== 'all') { $qDate->whereMonth('tanggal_closing', $m); }
                         });
                })->orWhere(function ($qMBC) use ($y, $m) {
                    $qMBC->whereHas('kelas', function($k) use ($y, $m) {
                        $k->where('nama_kelas', 'not like', '%Start-Up Muslim Indonesia%')
                          ->where(function($kSub) use ($y, $m) {
                              $kSub->where('nama_kelas', 'like', '%Zoom Privat%')->orWhere('nama_kelas', 'like', '%Start-Up Muda Indonesia%');
                              if ($m !== 'all') {
                                  $kSub->orWhere(function($kMul) use ($y, $m) {
                                      if ($y !== 'all') { $kMul->whereYear('tanggal_mulai', $y); }
                                      $kMul->whereMonth('tanggal_mulai', $m);
                                  });
                              } else {
                                  if ($y !== 'all') { $kSub->orWhereYear('tanggal_mulai', $y); }
                              }
                          });
                    })->where(function ($qDate) use ($y, $m) {
                        if ($y !== 'all') { $qDate->whereYear('updated_at', $y); }
                        if ($m !== 'all') { $qDate->whereMonth('updated_at', $m); }
                    });
                });
            });
        };

        $applyDateFilter = function ($query) use ($bulan, $tahun, $buildDateFilter) {
            $buildDateFilter($query, $tahun, $bulan);
        };

        $excludePartnerSales = function ($query) {
            $query->where(function ($q) {
                $q->whereDoesntHave('createdBy')
                  ->orWhereHas('createdBy', function ($sq) {
                      $sq->whereNotIn('role', ['chapter', 'reseller', 'agen']);
                  });
            });
        };

        // 1. SMI
        $smiQuery = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->whereHas('kelas', function ($q) {
                $q->where('nama_kelas', 'like', '%Muslim Indonesia%')
                    ->orWhere('nama_kelas', 'like', 'SMI - %');
            });
        $excludePartnerSales($smiQuery);
        $applyDateFilter($smiQuery);

        $smiBreakdown = $smiQuery->with('kelas:id,nama_kelas')
            ->get()
            ->groupBy('kelas.nama_kelas')
            ->map(fn($group) => $group->sum('nominal'));

        $totalSmiPendaftaran = (clone $smiQuery)->with('pesertaSmi')->get()->sum(function ($plan) {
            $nominalAwal = $plan->nominal;
            if ($plan->pesertaSmi) {
                $calc = (float) $plan->pesertaSmi->spp_awal + (float) $plan->pesertaSmi->pembayaran_spp;
                if ($calc > 0) {
                    return $calc;
                }
                if ($plan->pesertaSmi->total_pembayaran) {
                    return (float) $plan->pesertaSmi->total_pembayaran;
                }
                if ($plan->pesertaSmi->spp_awal) {
                    return (float) $plan->pesertaSmi->spp_awal;
                }
            }
            return (float) $nominalAwal;
        });

        $calcSpp = $this->calculateSppData($bulan, $tahun);
        $totalSmiSpp = $calcSpp['spp'];
        $totalSmiTunggakan = $calcSpp['tunggakan'];

        $totalSmi = $totalSmiPendaftaran + $totalSmiSpp;

        // 2. MBC
        $mbcQuery = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->whereHas('kelas', function ($q) {
                $q->where('nama_kelas', 'not like', '%Muslim Indonesia%')
                    ->where('nama_kelas', 'not like', 'SMI - %')
                    ->where('nama_kelas', 'not like', '%Privat%');
            });
        $excludePartnerSales($mbcQuery);
        $applyDateFilter($mbcQuery);

        $totalMbc = (clone $mbcQuery)->sum('nominal');
        $mbcBreakdown = $mbcQuery->with('kelas:id,nama_kelas')
            ->get()
            ->groupBy('kelas.nama_kelas')
            ->map(fn($group) => $group->sum('nominal'));

        $privateQuery = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->whereHas('kelas', function ($q) {
                $q->where('nama_kelas', 'like', '%Privat%');
            });
        $excludePartnerSales($privateQuery);
        $applyDateFilter($privateQuery);
        $totalPrivate = $privateQuery->sum('nominal');

        // calculate auto chapter and reseller sales for PDF
        $chapters = \App\Models\User::where('role', 'chapter')->orderBy('name')->get();
        $agens = \App\Models\User::where('role', 'reseller')->orderBy('name')->get();

        $chapterAutoSales = [];
        foreach ($chapters as $ch) {
            $chapterAutoSales[$ch->id] = [
                'personal' => 0,
                'agents' => 0,
                'total' => 0
            ];
        }

        $agenAutoSales = [];
        foreach ($agens as $ag) {
            $agenAutoSales[$ag->id] = 0;
        }

        $allPartnerSales = \App\Models\SalesPlan::where('status', 'sudah_transfer')
            ->where(function ($q) use ($buildDateFilter, $tahun, $bulan) {
                $buildDateFilter($q, $tahun, $bulan);
            })
            ->whereHas('createdBy', function ($q) {
                $q->whereIn('role', ['chapter', 'reseller', 'agen']);
            })
            ->with(['pesertaSmi', 'createdBy'])
            ->get();

        $calculateOmset = function ($plan) {
            if ($plan->pesertaSmi) {
                return (float) str_replace('.', '', $plan->pesertaSmi->total_pembayaran ?: ($plan->pesertaSmi->pembayaran_spp ?: $plan->pesertaSmi->spp_awal ?: 0));
            }
            return (float) str_replace('.', '', $plan->nominal ?: 0);
        };

        foreach ($allPartnerSales as $sale) {
            $creator = $sale->createdBy;
            if (!$creator) continue;

            $amount = $calculateOmset($sale);

            if ($creator->role === 'chapter') {
                if (isset($chapterAutoSales[$creator->id])) {
                    $chapterAutoSales[$creator->id]['personal'] += $amount;
                }
            } else {
                $parentId = $creator->created_by;
                $chapterName = $creator->chapter;
                $matchedChId = null;

                if (isset($chapterAutoSales[$parentId])) {
                    $matchedChId = $parentId;
                } elseif (!empty($chapterName)) {
                    foreach ($chapters as $ch) {
                        if (trim(strtolower($ch->chapter)) === trim(strtolower($chapterName))) {
                            $matchedChId = $ch->id;
                            break;
                        }
                    }
                }

                if ($matchedChId) {
                    $chapterAutoSales[$matchedChId]['agents'] += $amount;
                } else {
                    if (isset($agenAutoSales[$creator->id])) {
                        $agenAutoSales[$creator->id] += $amount;
                    }
                }
            }
        }

        foreach ($chapterAutoSales as $chId => &$data) {
            $data['total'] = $data['personal'] + $data['agents'];
        }
        unset($data);

        $chapterTotal = 0;
        foreach ($chapters as $ch) {
            $chKeterangan = $ch->name;
            $subRow = $pendapatan->filter(fn($r) => trim($r->keterangan ?? '') === $chKeterangan && trim($r->parent_keterangan ?? '') === 'Pendapatan Chapter')->first();
            if ($subRow) {
                $chapterTotal += $subRow->jumlah;
            } else {
                $chapterTotal += $chapterAutoSales[$ch->id]['total'] ?? 0;
            }
        }

        $agenTotal = 0;
        foreach ($agens as $ag) {
            $agKeterangan = $ag->name;
            $subRow = $pendapatan->filter(fn($r) => trim($r->keterangan ?? '') === $agKeterangan && trim($r->parent_keterangan ?? '') === 'Pendapatan Agen')->first();
            if ($subRow) {
                $agenTotal += $subRow->jumlah;
            } else {
                $agenTotal += $agenAutoSales[$ag->id] ?? 0;
            }
        }

        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        $namaBulan = $bulan === 'all' ? 'Semua Bulan' : ($months[$bulan] ?? 'Unknown');
        $tahunDisplay = $tahun === 'all' ? 'Semua Tahun' : $tahun;

        // Fetch Classes for the selected month
        $kelasBulanIni = \App\Models\Kelas::when($bulan !== 'all' || $tahun !== 'all', function ($q) use ($bulan, $tahun) {
            $q->where(function ($sq) use ($bulan, $tahun) {
                if ($bulan !== 'all' && $tahun !== 'all') {
                    $sq->whereMonth('tanggal_mulai', $bulan)->whereYear('tanggal_mulai', $tahun)
                        ->orWhereMonth('tanggal_selesai', $bulan)->whereYear('tanggal_selesai', $tahun);
                } elseif ($bulan !== 'all') {
                    $sq->whereMonth('tanggal_mulai', $bulan)->orWhereMonth('tanggal_selesai', $bulan);
                } elseif ($tahun !== 'all') {
                    $sq->whereYear('tanggal_mulai', $tahun)->orWhereYear('tanggal_selesai', $tahun);
                }
            });
        })->get();

        // 4. Approved Budget Proposals (Pengajuan Anggaran)
        $approvedAnggaran = \App\Models\PengajuanAnggaran::where('status', 'approved')
            ->when($bulan !== 'all', function ($q) use ($bulan) {
                $q->whereMonth('tanggal_pengajuan', $bulan); })
            ->when($tahun !== 'all', function ($q) use ($tahun) {
                $q->whereYear('tanggal_pengajuan', $tahun); })
            ->get();

        $coachItems = [
            'Cicilan mobil Coach',
            'Cicilan mobil teh Lia',
            'Uang bulanan Fathin',
            'Gaji ART',
            'Uang bulanan teh Lia',
            'Cicilan 2 kartu kredit',
            'Paket paket ustad',
            'Hutang Tajirw',
            'Hutang pak Yusron',
            'Biaya program Dela',
            'Biaya Pengeluaran Coach'
        ];

        $anggaranMapped = $approvedAnggaran->map(function ($item) use ($coachItems) {
            $category = 'Biaya Lain-lain';
            $name = strtolower($item->nama_pengajuan);

            if (in_array($item->nama_pengajuan, $coachItems)) {
                $category = 'Pengeluaran Coach';
            } elseif (strpos($name, 'kuota') !== false) {
                $category = 'Biaya Kuota';
            } elseif (strpos($name, 'listrik') !== false) {
                $category = 'Biaya Listrik';
            } elseif (strpos($name, 'air') !== false) {
                $category = 'Biaya Air';
            } elseif (strpos($name, 'bpjs') !== false) {
                $category = 'Biaya BPJS';
            } elseif (strpos($name, 'wifi') !== false || strpos($name, 'internet') !== false) {
                $category = 'Biaya Internet & Wifi';
            } elseif (strpos($name, 'maintenance') !== false) {
                $category = 'Biaya Maintenance Web';
            } elseif (strpos($name, 'gaji') !== false) {
                $category = 'Biaya Gaji Karyawan';
            } elseif (strpos($name, 'iklan') !== false) {
                $category = 'Biaya Iklan';
            }

            return (object) [
                'id' => 'anggaran-' . $item->id,
                'tanggal' => $item->tanggal_pengajuan ? $item->tanggal_pengajuan->format('Y-m-d') : null,
                'type' => 'biaya',
                'parent_keterangan' => $category,
                'keterangan' => $item->nama_pengajuan,
                'jumlah' => $item->biaya_disetujui ?? $item->jumlah_biaya,
                'is_auto' => true
            ];
        });

        $biaya = $biaya->concat($anggaranMapped);

        // Fetch All Classes for PDF consistency
        $semuaKelas = \App\Models\Kelas::orderBy('nama_kelas')->get();

        $pdf = PDF::loadView('admin.Finance.keuangan.laba-rugi-pdf', compact(
            'pendapatan',
            'biaya',
            'bulan',
            'tahun',
            'totalSmi',
            'totalSmiPendaftaran',
            'totalSmiSpp',
            'totalSmiTunggakan',
            'smiBreakdown',
            'totalMbc',
            'mbcBreakdown',
            'totalPrivate',
            'namaBulan',
            'kelasBulanIni',
            'semuaKelas',
            'tahunDisplay',
            'chapterAutoSales',
            'agenAutoSales',
            'chapterTotal',
            'agenTotal'
        ));

        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('LaporanLabaRugi_' . $namaBulan . '_' . $tahunDisplay . '.pdf');
    }

    public function storeLabaRugi(Request $request)
    {
        // Allow Linda even if role is administrator (might be a mismatch on server)
        $user = Auth::user();
        $isAdmin = strtolower($user->role ?? '') === 'administrator';
        $isLinda = stripos($user->name ?? '', 'Linda') !== false;
        $isYasmin = stripos($user->name ?? '', 'Yasmin') !== false;

        if ($isAdmin && !$isLinda && !$isYasmin) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menambah data.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah data.');
        }

        // Clean input: trim strings and convert empty to null for parent_keterangan
        $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT); // Ensure '01' instead of '1'
        $tahun = $request->tahun;
        $type = $request->type;
        $keterangan = trim($request->keterangan);
        $parent = $request->has('parent_keterangan') ? trim($request->parent_keterangan) : null;
        if ($parent === '')
            $parent = null;

        // Fallback for type if not set by AJAX
        if (!$type && $keterangan) {
            $pendapatanCats = ['Pendapatan Lainnya', 'Pendapatan Chapter', 'Pendapatan Agen'];
            $type = in_array($keterangan, $pendapatanCats) || strpos($keterangan, 'Pendapatan') !== false ? 'pendapatan' : 'biaya';
        }

        try {
            $request->validate([
                'bulan' => 'required',
                'tahun' => 'required',
                'tanggal' => 'nullable|date',
                'type' => 'required|in:pendapatan,biaya,zakat,zakat_fitra',
                'keterangan' => 'required',
                'jumlah' => 'required|numeric'
            ]);

            // Robust search to handle NULL vs ""
            $query = LabaRugi::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('keterangan', $keterangan)
                ->where('type', $type);

            if (empty($parent)) {
                $query->where(function ($q) {
                    $q->whereNull('parent_keterangan')->orWhere('parent_keterangan', '');
                });
            } else {
                $query->where('parent_keterangan', $parent);
            }

            $labaRugi = $query->first();

            if ($labaRugi) {
                $labaRugi->update([
                    'tanggal' => $request->tanggal,
                    'jumlah' => $request->jumlah,
                    'parent_keterangan' => $parent, // Sync it to what we have now
                    'created_by' => Auth::id()
                ]);
            } else {
                $labaRugi = LabaRugi::create([
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'tanggal' => $request->tanggal,
                    'type' => $type,
                    'keterangan' => $keterangan,
                    'parent_keterangan' => $parent,
                    'jumlah' => $request->jumlah,
                    'created_by' => Auth::id()
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan',
                    'data' => $labaRugi
                ]);
            }

            return redirect()->back()->with('success', 'Data berhasil disimpan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors()))
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyLabaRugi($id)
    {
        $userName = Auth::user()->name;
        $isYasmin = stripos($userName, 'Yasmin') !== false;
        if (strtolower(Auth::user()->role) === 'administrator' && $userName !== 'Linda' && !$isYasmin) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus data.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data.');
        }

        $labaRugi = LabaRugi::findOrFail($id);
        $labaRugi->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        }

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function kas(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $kas = \App\Models\Kas::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('admin.Finance.keuangan.kas', compact('kas', 'bulan', 'tahun'));
    }

    public function storeKas(Request $request)
    {
        $user = Auth::user();
        // Check if user is Linda or similar permission logic
        $isLinda = stripos($user->name ?? '', 'Linda') !== false;
        $isManager = strtolower($user->role ?? '') === 'manager';
        $isAdmin = strtolower($user->role ?? '') === 'administrator';
        $isYasmin = $user->name === 'Yasmin';

        if (!$isLinda && !$isManager && !$isAdmin && !$isYasmin) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah data.');
        }

        try {
            $request->validate([
                'tanggal' => 'required|date',
                'deskripsi' => 'required|string',
                'nominal' => 'required|numeric',
                'type' => 'required|in:masuk,keluar'
            ]);

            \App\Models\Kas::create([
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->deskripsi,
                'nominal' => $request->nominal,
                'type' => $request->type,
                'created_by' => Auth::id()
            ]);

            return redirect()->back()->with('success', 'Kas berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan kas: ' . $e->getMessage());
        }
    }

    public function destroyKas($id)
    {
        $user = Auth::user();
        $isLinda = stripos($user->name ?? '', 'Linda') !== false;
        $isYasmin = $user->name === 'Yasmin';

        if (!$isLinda && !$isYasmin && strtolower($user->role) === 'administrator') {
            return redirect()->back()->with('error', 'Hanya Linda dan Yasmin yang dapat menghapus data kas.');
        }

        $kas = \App\Models\Kas::findOrFail($id);
        $kas->delete();

        return redirect()->back()->with('success', 'Kas berhasil dihapus');
    }

    private function calculateSppData($bulan, $tahun)
    {
        $totalSpp = 0;
        $totalTunggakan = 0;

        $m1tClasses = \App\Models\Kelas::where('nama_kelas', 'like', '%Muslim Indonesia%')
            ->orWhere('nama_kelas', 'like', 'SMI - %')
            ->pluck('id')->toArray();

        // Fetch all participants with relations
        $pesertas = \App\Models\PesertaSmi::with(['salesPlan.createdBy', 'closingCs', 'createdBy'])->get();

        if ($bulan !== 'all') {
            $months = [(int) $bulan];
        } else {
            $months = range(1, 12);
        }

        $currentYear = (int) date('Y');
        $filterYearStr = $tahun;
        $filterYear = ($tahun !== 'all') ? (int) $tahun : $currentYear;

        foreach ($pesertas as $p) {
            // 1. Class Filter Alignment
            if (!$p->salesPlan || !in_array($p->salesPlan->kelas_id, $m1tClasses)) {
                continue;
            }

            // 2. Approval Status Filter Alignment
            $creatorRole = strtolower($p->closingCs->role ?? $p->createdBy->role ?? $p->salesPlan->createdBy->role ?? '');
            $needsApproval = in_array($creatorRole, ['reseller', 'chapter', 'agen']);
            if ($needsApproval && $p->approval_status !== 'Approved') {
                continue;
            }

            // 3. Lunas Badge calculation (Same logic)
            $paidMonthsCount = 0;
            for ($i = 1; $i <= 12; $i++) {
                if ((float) ($p->{"spp_$i"} ?? 0) >= 1000000) {
                    $paidMonthsCount++;
                }
            }
            $isLunasBadge = ($p->is_lunas == 1 || $paidMonthsCount >= 6);

            if ($isLunasBadge) {
                continue; // Skip this participant entirely
            }

            // Determine level-based nominal
            $pLevel = strtolower($p->level ?: ($p->salesPlan->level ?? ''));
            $levelNominal = str_contains($pLevel, 'grow') ? 1500000 : 1000000;

            foreach ($months as $m) {
                // 4. Active Period Filter Alignment
                $dateStart = \Carbon\Carbon::createFromDate($filterYear, $m, 1)->startOfMonth()->format('Y-m-d');
                $dateEnd = \Carbon\Carbon::createFromDate($filterYear, $m, 1)->endOfMonth()->format('Y-m-d');

                $isActive = ($p->tanggal_masuk <= $dateEnd) && ($p->tanggal_selesai >= $dateStart || is_null($p->tanggal_selesai));
                if (!$isActive) {
                    continue;
                }

                // 5. Payment check
                $val = 0;
                $tglSpp = $p->{"tanggal_spp_$m"};
                $paymentYear = $tglSpp ? \Carbon\Carbon::parse($tglSpp)->format('Y') : null;
                if (($p->{"spp_$m"} ?? 0) > 0 && (!$paymentYear || $paymentYear == $filterYear)) {
                    $val = (float) $p->{"spp_$m"};
                }

                // 6. Blue Checklist Logic (Closing or Planned)
                // Priority: tanggal_closing -> tanggal_masuk -> updated_at
                $effectiveDate = null;
                if ($p->salesPlan) {
                    if ($p->salesPlan->tanggal_closing) {
                        $effectiveDate = \Carbon\Carbon::parse($p->salesPlan->tanggal_closing);
                    } else {
                        $effectiveDate = $p->tanggal_masuk ? \Carbon\Carbon::parse($p->tanggal_masuk) : $p->salesPlan->updated_at;
                    }
                } else {
                    $effectiveDate = $p->tanggal_masuk ? \Carbon\Carbon::parse($p->tanggal_masuk) : $p->created_at;
                }

                $effM = (int)$effectiveDate->month;
                $effY = (int)$effectiveDate->year;
                $isClosing = ($effM == $m && $effY == $filterYear);

                $customSch = $p->spp_custom_schedule ?? [];
                $isPlanned = false;
                foreach ($customSch as $sch) {
                    if ((int)$sch['month'] === $m && (int)($sch['year'] ?? $filterYear) === (int)$filterYear) {
                        $isPlanned = true;
                        break;
                    }
                }

                if (!$isPlanned && $p->salesPlan) {
                    $selectedMonths = $p->salesPlan->selected_months;
                    if (is_string($selectedMonths)) {
                        $selectedMonths = json_decode($selectedMonths, true) ?? [];
                    }
                    if (isset($selectedMonths[$filterYear]) && is_array($selectedMonths[$filterYear])) {
                        if (in_array($m, $selectedMonths[$filterYear])) {
                            $isPlanned = true;
                        }
                    }
                }

                $isBlue = $isClosing || $isPlanned;

                // 7. SPP Revenue Calculation
                // Exclude OFF status from SPP count just like dashboard card
                if (!in_array($p->status, ['Cuti', 'OFF', 'off']) && !$isBlue && $val > 0) {
                    $totalSpp += $val;
                }

                // 8. Tunggakan / Arrears Calculation
                // Consistently use the same logic as "nominal_belum" card
                if ($filterYearStr !== 'all') {
                    if ($p->status === 'Aktif' && !$isBlue && $val <= 0) {
                        $totalTunggakan += $levelNominal;
                    }
                }
            }
        }

        return ['spp' => $totalSpp, 'tunggakan' => $totalTunggakan];
    }
}
