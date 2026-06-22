<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Kelas; // Ensure you import the Kelas model
use App\Models\Data;
use App\Models\Alumni; // Ensure you import the Alumni model
use App\Models\SalesPlan; // Ensure you import the Salesplan model

    use App\Models\User;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Support\Facades\Cache;
    use App\Imports\DataImport;
    use Barryvdh\DomPDF\Facade\Pdf;

    class DataController extends Controller
    {
        public function __construct()
        {
            $this->middleware('auth')->except(['formM1t', 'storeFormM1t']);
        }

        public function createDraft(Request $request)
    {
        try {
            $user = Auth::user();
            $newData = new Data();
            $newData->nama = '';
            $newData->status_peserta = 'peserta_baru';
            $newData->leads = 'Ads';
            
            // Find M1T (Start-Up Muslim Indonesia) class ID
            $m1tClass = Kelas::where('nama_kelas', 'like', '%Muslim Indonesia%')->first();
            if ($m1tClass) {
                $newData->kelas_id = $m1tClass->id;
            }
            
            $newData->created_by = $user->name;
            $newData->created_by_role = $user->role;
            
            // Set default chapter city for Chapter/Reseller roles so the data is visible in their view
            if (in_array(strtolower($user->role), ['chapter', 'reseller', 'agen']) && $user->chapter) {
                $newData->kota_nama = $user->chapter;
            }

            // Deteksi apakah ini Rafi (operasional)
            $isRafi = $user->hasHakAkses('operasional_rafi');

            // Set status_data: ADD OPS jika yang tambah adalah Operasional (Rafi)
            if ($isRafi) {
                $newData->status_data = 'ADD OPS';
            } else {
                $newData->status_data = 'CHAPTER';
            }
            
            $newData->save();

            // Create SalesPlan for M1T class
            if ($m1tClass) {
                $plan = new SalesPlan();
                $plan->data_id = $newData->id;
                $plan->kelas_id = $m1tClass->id;
                $plan->nama = $newData->nama;
                $plan->created_by = $user->id;
                $plan->status = 'cold';
                $plan->level = 'Grow Up';
                $plan->save();
            }

            // Load relasi salesplan agar tampil dengan benar di partial
            $newData->load('salesplan');

            $kelas = Kelas::select('id', 'nama_kelas', 'tanggal_selesai')->orderBy('nama_kelas')->get();

            // Tentukan partial: chapter view untuk chapter/reseller/Rafi
            $viewType = $request->input('view_type', '');
            $isChapterUser = in_array(strtolower($user->role), ['chapter', 'reseller', 'agen']);
            $isChapterView = $isChapterUser || $isRafi || $viewType === 'chapter';

            if ($isChapterView) {
                // Render row_chapter langsung untuk chapter/Rafi agar konsisten
                $html = view('admin.Sales.database.partials.row_chapter', [
                    'item' => $newData,
                    'loop' => (object)['iteration' => 'New', 'index' => 0],
                    'kelas' => $kelas
                ])->render();
            } else {
                // Untuk admin/cs/mbc — gunakan dispatcher row.blade.php
                // Inject view_type ke request agar dispatcher berjalan benar
                if ($viewType) {
                    $request->merge(['view_type' => $viewType]);
                }
                $html = view('admin.Sales.database.partials.row', [
                    'item' => $newData,
                    'loop' => (object)['iteration' => 'New', 'index' => 0],
                    'kelas' => $kelas
                ])->render();
            }

            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $userRole = strtolower($user->role);

        // --- Admin MBC Khusus ---
<<<<<<< Updated upstream
        $adminMbcIds = User::whereJsonContains('hak_akses', 'cs_supervisor')->pluck('id')->toArray();
        $allowedCsNames = User::whereJsonContains('hak_akses', 'cs_pusat')->pluck('name')->toArray();
=======
        $adminMbcIds = User::whereJsonContains('subrole', 'hrd')->pluck('id')->toArray();
        $allowedCsNames = User::whereJsonContains('subrole', 'cs_pusat')->pluck('name')->toArray();
>>>>>>> Stashed changes

        // --- Ambil daftar CS sesuai role ---
        $csQuery = \App\Models\User::query();

<<<<<<< Updated upstream
        if (auth()->user()->hasHakAkses('cs_supervisor')) {
=======
        if (auth()->user()->hasSubrole('hrd')) {
>>>>>>> Stashed changes
            // Admin MBC hanya bisa lihat CS tertentu
            $csQuery->whereIn('name', $allowedCsNames);
        } elseif ($userRole === 'manager') {
            // Manager hanya boleh lihat CS Pusat & CS MBC/SMI
            $managerCsNames = User::whereIn('role', ['cs-smi', 'cs-mbc'])->orWhereJsonContains('hak_akses', 'cs_pusat')->pluck('name')->toArray();
            $csQuery->whereIn('name', $managerCsNames);
        } elseif ($userRole === 'administrator' || $userRole === 'marketing' || $userRole === 'operasional') {
            // Administrator & Linda & Marketing boleh lihat daftar CS
            if ($userRole === 'marketing') {
                $csQuery->where('role', 'cs-mbc');
            } else {
                $csQuery->whereIn('role', ['cs', 'CS', 'customer_service', 'cs-mbc', 'cs-smi', 'chapter', 'reseller', 'agen']);
            }
        } else {
            // CS biasa hanya bisa lihat dirinya sendiri
            $csQuery->where('id', $userId);
        }

        $csQuery->where('name', 'not like', '%umum%');

        $csList = $csQuery->select('id', 'name')->orderBy('name')->get();

        // --- Ambil filter ---
        $kelasFilter = $request->input('kelas');
        $csFilter    = $request->input('cs_name');
        $chapterFilter = $request->input('chapter_id');
        $bulanFilter = $request->input('bulan');
        $tahunFilter = $request->input('tahun');
        $statusFilter = $request->input('status');
        $potensiFilter = $request->input('potensi');
        $kelasIdFilter = $request->input('kelas_id');
        $searchFilter = $request->input('search');
        $perPage     = 50;

        // --- Query utama ---
        $sortByParam = $request->input('sort_by', 'created_at');
        $sortOrderParam = $request->input('order', 'desc');
        
        // Whitelist columns
        $allowedSorts = ['created_at', 'created_by', 'nama', 'status_peserta', 'follow_up', 'zoom_done', 'zoom_unscheduled', 'zoom_scheduled']; 
        if (!in_array($sortByParam, $allowedSorts)) {
            $sortByParam = 'created_at';
        }
        
        // Base Query: Show "Calon Peserta" (Peserta Baru) and those moved to Sales Plan
        $query = \App\Models\Data::with(['kelas', 'salesplan' => function($q) {
                $q->orderBy('updated_at', 'desc');
            }, 'salesplan.kelas', 'createdBy'])
            ->whereIn('status_peserta', ['peserta_baru', 'pindah_salesplan']);

        $viewType = $request->input('view_type');
        if (empty($viewType) && $userRole === 'administrator') {
            $viewType = 'cs';
        }
        if ($userRole === 'operasional') {
            $request->merge(['view_type' => 'chapter']);
            $viewType = 'chapter';
        }
        // Adjust view type role filters to respect cs and chapter specific filters
        if ($viewType === 'cs') {
            $query->where('created_by_role', 'cs-mbc');
        } elseif ($viewType === 'chapter') {
            // Only apply chapter role restriction when no specific user or chapter filter is set
            if (empty($csFilter) && empty($chapterFilter)) {
                $query->where(function($q) {
                    $q->whereIn('created_by_role', ['chapter', 'reseller', 'agen'])
                      ->orWhere('created_by_role', 'operasional')
                      ->orWhereIn('status_data', ['ADD OPS', 'EDIT OPS']);
                });
            }
        } elseif ($userRole === 'cs-mbc') {
            // Default behavior for CS-MBC role
            $query->whereNotIn('created_by_role', ['chapter', 'reseller', 'agen', 'operasional']);
        }

        // CS biasa → hanya datanya sendiri (Moved higher to capture absolute total correctly)
        $forceMyData = $request->input('view') === 'me';
        if ($userRole === 'marketing') {
            if ($user->hasAnyHakAkses(['activity_marketing_offline', 'activity_marketing'])) {
                $query->whereIn('leads', ['Event', 'Open House']);
            } elseif ($user->hasAnyHakAkses(['activity_marketing_online', 'activity_intake'])) {
                $query->whereIn('leads', ['Online', 'Sosmed']);
            } else {
                $query->whereIn('leads', ['Marketing', 'Ads', 'Sosmed', 'Zoom', 'Open House']);
            }
            $query->where('created_by_role', 'cs-mbc');
<<<<<<< Updated upstream
        } elseif (!in_array($userRole, ['administrator', 'manager', 'chapter', 'reseller', 'agen', 'operasional']) && !$user->hasAnyHakAkses(['smi_class_only', 'cs_manager_smi'])) {
=======
        } elseif (!in_array($userRole, ['administrator', 'manager', 'chapter', 'reseller', 'agen', 'operasional']) && !$user->hasSubrole('manager_smi')) {
>>>>>>> Stashed changes
            $query->where('created_by', $user->name);
        }

        // Apply regional/role restrictions before calculating absolute total database
        // Chapter Role – filter by user's chapter city OR own created data, and exclude certain CS-MBC names
        if ($userRole === 'chapter') {
<<<<<<< Updated upstream
            $excludeNames = \App\Models\User::whereNotIn('role', ['chapter', 'reseller', 'agen'])
                ->pluck('name')
                ->toArray();
=======
            $excludeNames = \App\Models\User::whereNotIn('role', ['chapter', 'reseller', 'agen'])->pluck('name')->toArray();
>>>>>>> Stashed changes
            $query->where(function($q) use ($user, $excludeNames) {
                $q->where('created_by', $user->name)
                  ->orWhere(function($subQ) use ($user, $excludeNames) {
                      $subQ->where('kota_nama', 'like', '%' . $user->chapter . '%')
                           ->whereNotIn('created_by', $excludeNames)
                           ->where('created_by_role', '!=', 'cs-mbc');
                  });
            });
        } elseif (in_array($userRole, ['reseller', 'agen'])) {
            // Reseller/Agen: See own data + downline data
            $downlineNames = \App\Models\User::where('created_by', $user->id)->pluck('name')->toArray();
            $viewNames = array_merge([$user->name], $downlineNames);
            
            if ($user->hasHakAkses('share_depok_leads')) {
                $viewNames[] = 'AGUNG H. WIBOWO';
            }
            
            $query->whereIn('created_by', $viewNames);
        }

<<<<<<< Updated upstream
        if ($user->hasHakAkses('smi_class_only')) {
=======
        // Khusus Agus Setyo: Hanya kelas Start-Up Muslim/Muda Indonesia
        if ($user->hasSubrole('manager_smi')) {
>>>>>>> Stashed changes
            $query->whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'Start-Up Muda Indonesia')
                ->orWhere('nama_kelas', 'Start-Up Muslim Indonesia');
            });
        }

        // [NEW] Capture Absolute Total Database for the current context (CS/Role)
        $totalAbsoluteDatabase = (clone $query)->count();
        $totalAbsoluteBelumIkut = (clone $query)->whereDoesntHave('salesplan', function($q) {
            $q->where('status', 'sudah_transfer');
        })->count();

        // Filter Search
        if (!empty($searchFilter)) {
            $query->where(function($q) use ($searchFilter) {
                $q->where('nama', 'LIKE', '%'.$searchFilter.'%')
                ->orWhere('leads', 'LIKE', '%'.$searchFilter.'%')
                ->orWhere('nama_bisnis', 'LIKE', '%'.$searchFilter.'%')
                ->orWhere('no_wa', 'LIKE', '%'.$searchFilter.'%')
                ->orWhere('keterangan_spin', 'LIKE', '%'.$searchFilter.'%');
            });
        }

        if (in_array($sortByParam, ['follow_up', 'zoom_done', 'zoom_unscheduled', 'zoom_scheduled'])) {
            $query->select('data.*');

            $targetKelasId = !empty($request->input('prospek_kelas_id')) && $request->input('prospek_kelas_id') !== 'all' 
                ? $request->input('prospek_kelas_id') 
                : ($request->input('daftar_kelas') ?: $request->input('kelas_id'));

            $kelasCond = '';
            if (!empty($targetKelasId)) {
                $kelasCond = "AND salesplans.kelas_id = " . intval($targetKelasId);
            }

            if ($sortByParam === 'follow_up') {
                $followUpCountSql = "(SELECT (
                    CASE WHEN fu1_at IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN fu2_at IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN fu3_at IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN fu4_at IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN fu5_at IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN fu6_at IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN fu7_at IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN fu8_at IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN fu9_at IS NOT NULL THEN 1 ELSE 0 END +
                    CASE WHEN fu10_at IS NOT NULL THEN 1 ELSE 0 END
                ) FROM salesplans WHERE salesplans.data_id = data.id AND salesplans.deleted_at IS NULL {$kelasCond} ORDER BY salesplans.id DESC LIMIT 1)";
                
                $query->selectSub($followUpCountSql, 'fu_count_sort');
                $query->orderBy('is_no_potensi', 'asc')
                      ->orderBy('fu_count_sort', 'desc')
                      ->orderBy('data.id', 'desc');
            } elseif ($sortByParam === 'zoom_done') {
                $zoomDoneSql = "(CASE WHEN data.ikut_zoom = 1 THEN 1 ELSE (
                    CASE WHEN EXISTS (
                        SELECT 1 FROM zoom_schedules 
                        JOIN salesplans ON salesplans.id = zoom_schedules.salesplan_id
                        WHERE zoom_schedules.data_id = data.id 
                          AND zoom_schedules.status = 'done'
                          AND salesplans.deleted_at IS NULL
                          {$kelasCond}
                    ) THEN 1 ELSE 0 END
                ) END)";
                
                $query->selectSub($zoomDoneSql, 'zoom_done_sort');
                $query->orderBy('is_no_potensi', 'asc')
                      ->orderBy('zoom_done_sort', 'desc')
                      ->orderBy('data.id', 'desc');
            } elseif ($sortByParam === 'zoom_scheduled') {
                $zoomScheduledSql = "(CASE WHEN EXISTS (
                    SELECT 1 FROM zoom_schedules 
                    JOIN salesplans ON salesplans.id = zoom_schedules.salesplan_id
                    WHERE zoom_schedules.data_id = data.id 
                      AND zoom_schedules.status = 'scheduled'
                      AND salesplans.deleted_at IS NULL
                      {$kelasCond}
                ) THEN 1 ELSE 0 END)";
                
                $query->selectSub($zoomScheduledSql, 'zoom_scheduled_sort');
                $query->orderBy('is_no_potensi', 'asc')
                      ->orderBy('zoom_scheduled_sort', 'desc')
                      ->orderBy('data.id', 'desc');
            } elseif ($sortByParam === 'zoom_unscheduled') {
                $zoomUnscheduledSql = "(CASE WHEN data.ikut_zoom = 1 THEN 0 ELSE (
                    CASE WHEN EXISTS (
                        SELECT 1 FROM zoom_schedules 
                        JOIN salesplans ON salesplans.id = zoom_schedules.salesplan_id
                        WHERE zoom_schedules.data_id = data.id 
                          AND zoom_schedules.status IN ('done', 'scheduled')
                          AND salesplans.deleted_at IS NULL
                          {$kelasCond}
                    ) THEN 0 ELSE 1 END
                ) END)";
                
                $query->selectSub($zoomUnscheduledSql, 'zoom_unscheduled_sort');
                $query->orderBy('is_no_potensi', 'asc')
                      ->orderBy('zoom_unscheduled_sort', 'desc')
                      ->orderBy('data.id', 'desc');
            }
        } else {
            $query->orderBy('is_no_potensi', 'asc')
                  ->orderBy($sortByParam, $sortOrderParam); // Order By must be after search conditions if any
        }

        // Jika admin MBC → hanya 6 CS tertentu (DISABLED/ADJUSTED: User reported CS seeing shared data is undesirable)
        // if (in_array($userId, $adminMbcIds)) {
        //     $query->whereIn('created_by', $allowedCsNames);
        // }

        // Manager → hanya bisa lihat data CS Pusat & CS MBC/SMI
        if ($userRole === 'manager') {
            $managerCsNames = User::whereIn('role', ['cs-smi', 'cs-mbc'])->orWhereJsonContains('hak_akses', 'cs_pusat')->pluck('name')->toArray();
            $query->whereIn('created_by', $managerCsNames);
        }

        // Filter User
        if (!empty($csFilter)) {
            $query->where('created_by', $csFilter);
        }

        // Filter Chapter: Only include records created by the selected Chapter user
        if (!empty($chapterFilter)) {
            $selectedChapter = \App\Models\User::find($chapterFilter);
            if ($selectedChapter) {
                // Directly filter by the creator's name (or could use ID if stored)
                $query->where('created_by', $selectedChapter->name);
            }
        }

        // Filter kelas & bulan & tahun
        if (!empty($kelasFilter)) {
            $query->where('kelas_id', $kelasFilter);
        }

        if (!empty($bulanFilter)) {
            $query->whereMonth('created_at', $bulanFilter);
        }

        if (!empty($tahunFilter)) {
            $query->whereYear('created_at', $tahunFilter);
        }


        // Filter Potensi Kelas
        if (!empty($potensiFilter)) {
            $query->where('potensi', $potensiFilter);
        }

        // Filter Nama Kelas (dari dropdown dinamis MBC)
        if (!empty($kelasIdFilter)) {
            $query->where('kelas_id', $kelasIdFilter);
        }

        // New Filters (Server Side)
        $sumberFilter = $request->input('sumber');
        $kotaFilter = $request->input('kota');
        $provinsiFilter = $request->input('provinsi');

        if (!empty($sumberFilter)) {
            $query->where('leads', $sumberFilter);
        }

        if (!empty($kotaFilter)) {
            // Kota is stored as 'kota_nama' or linked via ID. Checking view logic, it seems to be 'kota_nama'.
            // Let's verify view usage. In row.blade.php it uses $item->kota_nama.
            $query->where('kota_nama', $kotaFilter);
        }


        if (!empty($provinsiFilter)) {
            $query->where('provinsi_nama', $provinsiFilter);
        }

        // Filter Spin (ALL = semua B,A,T tercentang, NOT_ALL = belum semua tercentang)
        $spinFilter = $request->input('filter_spin');
        if (!empty($spinFilter)) {
            if ($spinFilter === 'ALL') {
                $query->where('bant_budget', 1)
                      ->where('bant_authority', 1)
                      ->where('bant_time', 1);
            } elseif ($spinFilter === 'NOT_ALL') {
                $query->where(function($q) {
                    $q->where('bant_budget', '!=', 1)
                      ->orWhereNull('bant_budget')
                      ->orWhere('bant_authority', '!=', 1)
                      ->orWhereNull('bant_authority')
                      ->orWhere('bant_time', '!=', 1)
                      ->orWhereNull('bant_time');
                });
            }
        }

        // Filter Zoom
        $zoomFilter = $request->input('zoom');
        if ($zoomFilter !== null && $zoomFilter !== '') {
            $query->where('ikut_zoom', $zoomFilter);
        }

        // Filter Ikut Kelas (SalesPlan)
        $ikutKelasFilter = $request->input('ikut_kelas');
        $daftarKelasFilter = $request->input('daftar_kelas');

        if ($ikutKelasFilter !== null && $ikutKelasFilter !== '') {
            if ($ikutKelasFilter == '1') {
                // Sudah Pernah Ikut
                $query->whereHas('salesplan', function($q) use ($daftarKelasFilter) {
                    $q->where('status', 'sudah_transfer');
                    if (!empty($daftarKelasFilter)) {
                        $q->where('kelas_id', $daftarKelasFilter);
                    }
                });
            } else {
                // Belum Pernah Ikut
                if (!empty($daftarKelasFilter)) {
                    // Belum pernah ikut kelas spesifik (harus terdaftar di salesplan untuk kelas tersebut dengan status bukan sudah_transfer)
                    $query->whereHas('salesplan', function($q) use ($daftarKelasFilter) {
                        $q->where('kelas_id', $daftarKelasFilter)
                          ->where('status', '!=', 'sudah_transfer');
                    });
                } else {
                    // Belum pernah ikut kelas APAPUN
                    $query->whereDoesntHave('salesplan', function($q) {
                        $q->where('status', 'sudah_transfer');
                    });
                }
            }
        }

        // Filter BANT
        $bantFilter = $request->input('bant');
        if ($bantFilter !== null && $bantFilter !== '') {
            $query->where('bant', $bantFilter);
        }

        // Filter Potensi
        $potensiFilter = $request->input('potensi');
        $potensiKelasFilter = $request->input('potensi_kelas_id');

        if (!empty($potensiFilter) && $potensiFilter !== 'all') {
            if (in_array(strtoupper($potensiFilter), ['MBC', 'SMI'])) {
                $query->where('potensi', strtoupper($potensiFilter));
                if (!empty($potensiKelasFilter)) {
                    $query->where('kelas_id', $potensiKelasFilter);
                }
            } else {
                $query->where(function($q) use ($potensiFilter) {
                    $q->where('potensi', $potensiFilter)
                      ->orWhere('potensi', strtolower($potensiFilter))
                      ->orWhere('potensi', strtoupper($potensiFilter))
                      ->orWhere('situasi_bisnis', 'like', '%Kategori: ' . strtoupper($potensiFilter) . '%');
                });
            }
        }

        // Filter Prospek by Class (from the Jumlah Prospek Card)
        $prospekKelasId = $request->input('prospek_kelas_id');
        if (!empty($prospekKelasId) && $prospekKelasId !== 'all') {
            $query->whereHas('salesplan', function($q) use ($prospekKelasId) {
                $q->where('kelas_id', $prospekKelasId);
            });
        }

        // Filter Status is now applied later after stats calculation to keep counts accurate.



        // Regional/role restrictions already applied higher up to ensure correct database counts.

        // --- Stats Calculation for Dashboard Headers ---
        // KPI Query: Targets ALL data input (ignoring status_peserta) to reflect Acquisition Performance
        $kpiQuery = \App\Models\Data::query();

        // Filter by view_type or CS-MBC role for stats
        if ($viewType === 'cs' || $userRole === 'cs-mbc') {
            $kpiQuery->where('created_by_role', 'cs-mbc');
        } elseif ($viewType === 'chapter') {
            $kpiQuery->where(function($q) {
                $q->whereIn('created_by_role', ['chapter', 'reseller', 'agen'])
                  ->orWhere('created_by_role', 'operasional')
                  ->orWhereIn('status_data', ['ADD OPS', 'EDIT OPS']);
            });
        }
        
        // Re-apply Permission/Ownership Logic to KPI Query
        // Manager
        if ($userRole === 'manager') {
            $managerCsNames = User::whereIn('role', ['cs-smi', 'cs-mbc'])->orWhereJsonContains('hak_akses', 'cs_pusat')->pluck('name')->toArray();
            $kpiQuery->whereIn('created_by', $managerCsNames);
        }
        // Filter User (Dropdown)
        if (!empty($csFilter)) {
            $kpiQuery->where('created_by', $csFilter);
        }
        // KPI Query Chapter filter: only include records created by the selected Chapter user
        if (!empty($chapterFilter)) {
            $selectedChapter = \App\Models\User::find($chapterFilter);
            if ($selectedChapter) {
                $kpiQuery->where('created_by', $selectedChapter->name);
            }
        }
        // Strict CS View
        // Strict CS View
<<<<<<< Updated upstream
        if (($user->hasHakAkses('spp_admin') && $forceMyData) || (!in_array($userRole, ['administrator', 'manager', 'chapter', 'reseller', 'agen', 'operasional']) && !$user->hasAnyHakAkses(['spp_admin', 'sales_admin', 'smi_class_only', 'sales_full_view', 'cs_manager_smi']))) {
=======
        if (($user->hasSubrole('spp_admin') && $forceMyData) || ($user->hasAnySubrole(['sales_marketing', 'keuangan']) && $forceMyData) || (!in_array($userRole, ['administrator', 'manager', 'chapter', 'reseller', 'agen', 'operasional']) && !$user->hasAnySubrole(['spp_admin', 'sales_admin']) && !$user->hasSubrole('manager_smi') && !$user->hasAnySubrole(['sales_marketing', 'keuangan']))) {
>>>>>>> Stashed changes
            $kpiQuery->where('created_by', $user->name);
        }
        
        // Re-apply Permission/Ownership Logic to KPI Query for Regional Roles
        if ($userRole === 'chapter') {
            $chapterName = $user->chapter;
            $kpiQuery->where(function($q) use ($user, $chapterName) {
                $q->where('created_by', $user->name)
                  ->orWhere('kota_nama', 'like', '%' . $chapterName . '%');
            });
        } elseif (in_array($userRole, ['reseller', 'agen'])) {
            $downlineNames = \App\Models\User::where('created_by', $user->id)->pluck('name')->toArray();
            $viewNames = array_merge([$user->name], $downlineNames);
            if ($user->hasHakAkses('share_depok_leads')) {
                $viewNames[] = 'AGUNG H. WIBOWO';
            }
            $kpiQuery->whereIn('created_by', $viewNames);
        }
        // Agus Setyo
        // Marketing Role specific KPI filter
        if ($userRole === 'marketing') {
            if ($user->hasAnyHakAkses(['activity_marketing_offline', 'activity_marketing'])) {
                $kpiQuery->where('leads', 'Event');
            } else {
                $kpiQuery->whereIn('leads', ['Marketing', 'Event']);
            }
            $kpiQuery->where('created_by_role', 'cs-mbc');
        }

<<<<<<< Updated upstream
        if ($user->hasHakAkses('smi_class_only')) {
=======
        if ($user->hasSubrole('manager_smi')) {
>>>>>>> Stashed changes
            $kpiQuery->whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'Start-Up Muda Indonesia')
                ->orWhere('nama_kelas', 'Start-Up Muslim Indonesia');
            });
        }


        $now = \Carbon\Carbon::now();
        $statsYear = $tahunFilter ? $tahunFilter : $now->year;
        $statsMonth = $bulanFilter ? $bulanFilter : $now->month;
        
        $bulanLabel = \Carbon\Carbon::createFromDate($statsYear, $statsMonth, 1)->isoFormat('MMMM YYYY');

        // Total Database: Count of current table (Queue Size)
        // We use the original $query which has 'status' & 'time' & 'search' filters applied.
        $totalDatabase = (clone $query)->count();

        // Database Baru: Performance Metric (Count of ALL inputs in period)
        $databaseBaru = $kpiQuery
            ->whereYear('created_at', $statsYear)
            ->whereMonth('created_at', $statsMonth)
            ->count();

        $targetAdmin = \App\Models\Setting::where('key', 'target_database_admin')->value('value') ?? 250;
        $targetCs = \App\Models\Setting::where('key', 'target_database_cs')->value('value') ?? 50;
        $target = in_array($userRole, ['administrator', 'operasional']) ? $targetAdmin : $targetCs;
        $kurang = max($target - $databaseBaru, 0);

        // Compute Prospek Counts based on current query
        $dataIds = (clone $query)->pluck('id');
        $prospekCounts = \App\Models\SalesPlan::whereNotNull('kelas_id')
            ->whereIn('data_id', $dataIds)
            ->select('kelas_id', \DB::raw('count(DISTINCT data_id) as total'))
            ->groupBy('kelas_id')
            ->pluck('total', 'kelas_id')
            ->toArray();
        $totalProspek = array_sum($prospekCounts);

        // [NEW] Status Counts for Legend (Based on filtered query)
        $dataFilteredIds = (clone $query)->pluck('id');
        
        $statusCountsQuery = \App\Models\SalesPlan::whereIn('data_id', $dataFilteredIds);
        
        // If a specific class is selected, only count statuses for that class
        $activeKelasId = $request->input('daftar_kelas') ?: $request->input('kelas_id');
        if (!empty($activeKelasId)) {
            $statusCountsQuery->where('kelas_id', $activeKelasId);
        }

        $statusCounts = $statusCountsQuery->select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        
        $countTertarik = $statusCounts['tertarik'] ?? 0;
        $countMauTransfer = $statusCounts['mau_transfer'] ?? 0;
        $countSudahTransfer = $statusCounts['sudah_transfer'] ?? 0;
        $countNo = $statusCounts['no'] ?? 0;
        
        // totalFiltered depends on filter context:
        // - Sudah Ikut (1): total sudah transfer (the ones who actually joined)
        // - Belum Ikut (0): total who haven't transferred yet
        if ($ikutKelasFilter === '1') {
            $totalFiltered = $countSudahTransfer;
        } else {
            $totalFiltered = (clone $query)->count();
        }

        // Calculate countCold as all other participants who are not Tertarik, Mau Transfer, Sudah Transfer, or No
        $activeKelasId = $request->input('daftar_kelas') ?: $request->input('kelas_id');
        if (!empty($activeKelasId)) {
            $countCold = $statusCounts['cold'] ?? 0;
        } else {
            if ($ikutKelasFilter === '0') {
                $countCold = max($totalFiltered - $countTertarik - $countMauTransfer - $countNo, 0);
            } else {
                $countCold = $statusCounts['cold'] ?? 0;
            }
        }

        // If no ikut_kelas filter and no active class filter, force legend counts to 0 for UI clarity as requested
        $prospekKelasId = $request->input('prospek_kelas_id');
        if (empty($ikutKelasFilter) && $ikutKelasFilter !== '0' && empty($activeKelasId) && (empty($prospekKelasId) || $prospekKelasId === 'all')) {
            $countTertarik = 0;
            $countMauTransfer = 0;
            $countSudahTransfer = 0;
            $countNo = 0;
            $countCold = 0;
            $totalFiltered = 0;
        }

        // Apply Status Filter to main query for pagination (after stats have been calculated)
        $statusFilter = $request->input('status');
        if (!empty($statusFilter)) {
            if ($statusFilter === 'total_database') {
                // Do not apply any status filter (shows all)
            } elseif ($statusFilter === 'database_baru') {
                $query->whereYear('created_at', $statsYear)
                      ->whereMonth('created_at', $statsMonth);
            } elseif ($statusFilter === 'potensi') {
                // Show entries with any potential status (cold, tertarik, mau_transfer)
                $query->whereHas('salesplan', function($q) {
                    $q->whereIn('status', ['cold', 'tertarik', 'mau_transfer']);
                });
            } else {
                // Filter by specific salesplan status
                $query->whereHas('salesplan', function($q) use ($statusFilter) {
                    $q->where('status', $statusFilter);
                });
            }
        }

        $data = $query->paginate($perPage)->withQueryString();
        $kelas = \App\Models\Kelas::select('id', 'nama_kelas', 'tanggal_selesai')->orderBy('nama_kelas')->get();

        // Calculate Jumlah Potensi (only show if status filter is active or class filter is active, otherwise default to 0)
        $prospekKelasId = $request->input('prospek_kelas_id');
        if (empty($ikutKelasFilter) && $ikutKelasFilter !== '0' && empty($activeKelasId) && (empty($prospekKelasId) || $prospekKelasId === 'all')) {
            $jumlahPotensi = 0;
        } else {
            $jumlahPotensiQuery = \App\Models\SalesPlan::whereIn('data_id', $dataFilteredIds)
                ->whereIn('status', ['cold', 'tertarik', 'mau_transfer']);
                
            if (!empty($activeKelasId)) {
                $jumlahPotensiQuery->where('kelas_id', $activeKelasId);
            }
            
            $jumlahPotensi = $jumlahPotensiQuery->count();
        }


        // Calculate Zoom Status Counts
        $zoomCounts = \App\Models\ZoomSchedule::whereIn('data_id', $dataFilteredIds)
            ->select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $countZoomScheduled = $zoomCounts['scheduled'] ?? 0;
        $countZoomDone = $zoomCounts['done'] ?? 0;

        $scheduledDataIds = \App\Models\ZoomSchedule::whereIn('data_id', $dataFilteredIds)
            ->where('status', 'scheduled')
            ->pluck('data_id')
            ->toArray();

        $doneDataIds = \App\Models\ZoomSchedule::whereIn('data_id', $dataFilteredIds)
            ->where('status', 'done')
            ->pluck('data_id')
            ->toArray();

        $ikutZoomDataIds = \App\Models\Data::whereIn('id', $dataFilteredIds)
            ->where('ikut_zoom', 1)
            ->pluck('id')
            ->toArray();

        $scheduledOrDoneIds = array_unique(array_merge($scheduledDataIds, $doneDataIds, $ikutZoomDataIds));
        
        $countZoomUnscheduled = count($dataFilteredIds) - count(array_intersect($dataFilteredIds->toArray(), $scheduledOrDoneIds));

        // Zoom legend counts are no longer forced to 0
        // as requested by the user, they will now always show the actual numbers.



        // Fetch lists for filters
        $provinsiList = \App\Models\Data::select('provinsi_nama')
            ->whereNotNull('provinsi_nama')
            ->where('provinsi_nama', '!=', '')
            ->distinct()
            ->orderBy('provinsi_nama')
            ->pluck('provinsi_nama');

        $kotaQuery = \App\Models\Data::select('kota_nama')
            ->whereNotNull('kota_nama')
            ->where('kota_nama', '!=', '')
            ->distinct()
            ->orderBy('kota_nama');

        if (!empty($provinsiFilter)) {
            $kotaQuery->where('provinsi_nama', $provinsiFilter);
        }

        $kotaList = $kotaQuery->pluck('kota_nama');

        if ($request->ajax()) {
            $paginationHtml = $data->withQueryString()->links('pagination::bootstrap-4')->toHtml();
            return response()->json([
                'html' => view('admin.Sales.database.partials.table', [
                    'data' => $data,
                    'kelas' => $kelas,
                    'csList' => $csList,
                    'provinsiList' => $provinsiList,
                    'kotaList' => $kotaList,
                    'databaseBaru' => $databaseBaru,
                    'totalDatabase' => $totalDatabase,
                    'target' => $target,
                    'kurang' => $kurang,
                    'bulanLabel' => $bulanLabel,
                ])->render(),
                'pagination' => $paginationHtml,
                'stats' => [
                    'databaseBaru' => $databaseBaru,
                    'totalDatabase' => $totalAbsoluteDatabase, // Use absolute total here
                    'totalFiltered' => $totalFiltered,
                    'countTertarik' => $countTertarik,
                    'countMauTransfer' => $countMauTransfer,
                    'countSudahTransfer' => $countSudahTransfer,
                    'countNo' => $countNo,
                    'countCold' => $countCold,
                    'kurang' => $kurang,
                    'bulanLabel' => $bulanLabel,
                    'prospekCounts' => $prospekCounts,
                    'totalProspek' => $totalProspek,
                    'jumlahPotensi' => $jumlahPotensi,
                    'countZoomScheduled' => $countZoomScheduled,
                    'countZoomDone' => $countZoomDone,
                    'countZoomUnscheduled' => $countZoomUnscheduled,
                ]
            ]);
        }

        return view('admin.Sales.database.database', [
            'data' => $data,
            'kelas' => $kelas,
            'csList' => $csList,
            'provinsiList' => $provinsiList,
            'kotaList' => $kotaList,
            'databaseBaru' => $databaseBaru,
            'totalDatabase' => $totalAbsoluteDatabase,
            'totalFiltered' => $totalFiltered,
            'countTertarik' => $countTertarik,
            'countMauTransfer' => $countMauTransfer,
            'countSudahTransfer' => $countSudahTransfer,
            'countNo' => $countNo,
            'countCold' => $countCold,
            'target' => $target,
            'kurang' => $kurang,
            'bulanLabel' => $bulanLabel,
            'prospekCounts' => $prospekCounts,
            'totalProspek' => $totalProspek,
            'jumlahPotensi' => $jumlahPotensi,
            'countZoomScheduled' => $countZoomScheduled,
            'countZoomDone' => $countZoomDone,
            'countZoomUnscheduled' => $countZoomUnscheduled,
        ]);

    }







        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            // Return a view to create a new resource
            return view('admin.Sales.database.create');
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function updateInline(Request $request)
        {
            try {
                $salesplanId = $request->input('salesplan_id');
                $targetModel = null;
                if (!empty($salesplanId)) {
                    $targetModel = \App\Models\SalesPlan::findOrFail($salesplanId);
                } else {
                    $targetModel = Data::findOrFail($request->id);
                }
                
                if ($request->has('updates')) {
                    $updates = $request->updates;
                    
                    // Auto-update timestamps for FU ONLY if content changed
                    for ($i = 1; $i <= 10; $i++) {
                        $hasilField = "fu{$i}_hasil";
                        $tindakField = "fu{$i}_tindak_lanjut";
                        $waField = "fu{$i}_wa";
                        $telpField = "fu{$i}_telp";
                        $atField = "fu{$i}_at";

                        $hasChanged = false;

                        // Compare with current data (normalize null/empty string)
                        $newHasil = $updates[$hasilField] ?? '';
                        $oldHasil = $targetModel->$hasilField ?? '';
                        if ($newHasil !== $oldHasil) $hasChanged = true;

                        $newTindak = $updates[$tindakField] ?? '';
                        $oldTindak = $targetModel->$tindakField ?? '';
                        if ($newTindak !== $oldTindak) $hasChanged = true;

                        if (isset($updates[$waField]) && (int)$updates[$waField] !== (int)$targetModel->$waField) $hasChanged = true;
                        if (isset($updates[$telpField]) && (int)$updates[$telpField] !== (int)$targetModel->$telpField) $hasChanged = true;

                        // Handle follow-up timestamp (explicit user choice, no automatic defaults)
                        if (array_key_exists($atField, $updates)) {
                            if (empty($updates[$atField])) {
                                $updates[$atField] = null;
                            } else {
                                $manualDate = null;
                                try {
                                    if (strpos($updates[$atField], 'T') !== false) {
                                        $manualDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $updates[$atField]);
                                    } else {
                                        $manualDate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $updates[$atField]);
                                    }
                                } catch (\Exception $e) {
                                    try {
                                        $manualDate = \Carbon\Carbon::parse($updates[$atField]);
                                    } catch (\Exception $ex) {}
                                }

                                if ($manualDate) {
                                    $updates[$atField] = $manualDate;
                                } else {
                                    unset($updates[$atField]);
                                }
                            }
                        } else {
                            unset($updates[$atField]);
                        }
                    }
                    
                    $targetModel->update($updates);
                    $targetModel->refresh(); // Ensure we have the updated timestamps

                    // Recalculate Daily Activity for all affected dates
                    try {
                        $affectedDates = [];
                        for ($i = 1; $i <= 10; $i++) {
                            $atField = "fu{$i}_at";
                            if ($targetModel->$atField && $targetModel->$atField instanceof \Carbon\Carbon) {
                                $affectedDates[] = $targetModel->$atField->toDateString();
                            }
                        }
                        // Also include today's date just in case
                        $affectedDates[] = now()->toDateString();
                        
                        $userId = auth()->id();
                        foreach (array_unique($affectedDates) as $date) {
                            \App\Models\DailyActiviti::updateAutomated($userId, $date);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Failed to update daily activity: " . $e->getMessage());
                    }

                    // Return the updated timestamps for frontend sync
                    $timestamps = [];
                    for ($i = 1; $i <= 10; $i++) {
                        $field = "fu{$i}_at";
                        $timestamps[$field] = $targetModel->$field ? $targetModel->$field->format('d/m/Y H:i') : null;
                    }
                    return response()->json(['success' => true, 'timestamps' => $timestamps]);
                } else {
                    $data = Data::findOrFail($request->id);
                    $field = $request->field;
                    if ($field) {
                        // Normalize field name (some parts of the app use jenisbisnis)
                        if ($field === 'jenisbisnis') {
                            $field = 'jenis_bisnis';
                        }
                        
                        // Check if the column exists in the 'data' table
                        if (!\Schema::hasColumn('data', $field)) {
                             // Fallback to jenisbisnis if jenis_bisnis doesn't exist (unlikely but safe)
                             if ($field === 'jenis_bisnis') $field = 'jenisbisnis';
                        }
                        
                        // Direct assignment and save to bypass mass-assignment issues if any
                        if ($field === 'ikut_zoom' && !empty($request->input('salesplan_id'))) {
                            // Don't save to data table to avoid marking other classes as done
                        } else {
                            $data->$field = $request->value;
                        }

                        if ($field === 'ikut_zoom') {
                            $salesplanId = $request->input('salesplan_id');
                            if (!empty($salesplanId)) {
                                $schedule = \App\Models\ZoomSchedule::where('salesplan_id', $salesplanId)->first();
                                if (!$schedule && $request->value == 1) {
                                    $schedule = new \App\Models\ZoomSchedule();
                                    $schedule->data_id = $data->id;
                                    $schedule->salesplan_id = $salesplanId;
                                    $schedule->cs_id = auth()->id() ?: $data->pic;
                                    $schedule->scheduled_at = now();
                                    $schedule->zoom_link = '';
                                }
                                if ($schedule) {
                                    $schedule->status = ($request->value == 1) ? 'done' : 'scheduled';
                                    $schedule->save();
                                }
                            } else {
                                if ($request->value == 1) {
                                    // Find any schedule for this participant and update it to 'done'
                                    $schedule = \App\Models\ZoomSchedule::where('data_id', $data->id)->first();
                                    if ($schedule) {
                                        $schedule->status = 'done';
                                        $schedule->save();
                                    }
                                } else {
                                    // If they set ikut_zoom = 0, find any 'done' schedule and change it back to 'scheduled'
                                    $schedule = \App\Models\ZoomSchedule::where('data_id', $data->id)
                                        ->where('status', 'done')
                                        ->first();
                                    if ($schedule) {
                                        $schedule->status = 'scheduled';
                                        $schedule->save();
                                    }
                                }
                            }
                        }

                        // Update status_data jika yang edit adalah Operasional (Rafi) dan data asli dari Chapter
                        $authUser = Auth::user();
                        if (
                            $authUser->hasHakAkses('operasional_rafi') &&
                            $data->status_data === 'CHAPTER'
                        ) {
                            $data->status_data = 'EDIT OPS';
                        }

                        $data->save();

                        // If class is being changed, ensure a SalesPlan exists for this new class
                        if ($field === 'kelas_id' && !empty($request->value)) {
                            $kelasId = $request->value;
                            $existingPlan = \App\Models\SalesPlan::where('data_id', $data->id)
                                ->where('kelas_id', $kelasId)
                                ->first();
                            if (!$existingPlan) {
                                $plan = new \App\Models\SalesPlan();
                                $plan->data_id = $data->id;
                                $plan->kelas_id = $kelasId;
                                $plan->nama = $data->nama;
                                $plan->created_by = auth()->id();
                                $plan->status = 'cold';
                                $plan->level = 'Grow Up';
                                $plan->save();
                                $newSalesplanId = $plan->id;
                            } else {
                                $newSalesplanId = $existingPlan->id;
                            }
                        }
                        
                        // Debug log to confirm reaching this point
                        \Log::info("Update successful", ['id' => $data->id, 'field' => $field, 'value' => $request->value]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'salesplan_id' => $newSalesplanId ?? null,
                ]);
            } catch (\Exception $e) {
                \Log::error('Update Inline Error: ' . $e->getMessage(), [
                    'id' => $request->id,
                    'field' => $request->field,
                    'value' => $request->value,
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        public function updateLocation(Request $request)
        {
            $data = Data::findOrFail($request->id);
            
            if ($request->has('provinsi_id')) {
                $data->provinsi_id = $request->provinsi_id;
                $data->provinsi_nama = $request->provinsi_nama;
                // Reset kota jika provinsi berubah
                $data->kota_id = null;
                $data->kota_nama = null;
            }

            if ($request->has('kota_id')) {
                $data->kota_id = $request->kota_id;
                $data->kota_nama = $request->kota_nama;
            }

            // Update status_data jika yang edit adalah Operasional (Rafi) dan data asli dari Chapter
            $authUser = Auth::user();
            if (
                $authUser->hasHakAkses('operasional_rafi') &&
                $data->status_data === 'CHAPTER'
            ) {
                $data->status_data = 'EDIT OPS';
            }

            $data->save();

            return response()->json(['success' => true]);
        }

        public function store(Request $request)
        {
            $data = new Data();
            $data->nama = $request->input('nama');
            $data->status_peserta = $request->input('status_peserta','peserta_baru');
            // Enum field
            $data->leads = $request->input('leads'); // Assuming 'leads' is an enum field
            // Custom field
            if ($request->input('leads_custom') === null) {
                $data->leads_custom = ''; // Set to empty string if null
            } else {
                $data->leads_custom = $request->input('leads_custom');
            }
            $data->provinsi_id = $request->input('provinsi_id');
            $data->provinsi_nama = $request->input('provinsi_nama');
            $data->kota_id = $request->input('kota_id');
            $data->kota_nama = $request->input('kota_nama');
            $data->jenis_bisnis = $request->input('jenis_bisnis') ?? $request->input('jenisbisnis');
            $data->nama_bisnis = $request->input('nama_bisnis');
            $data->no_wa = $request->input('no_wa');
            $data->situasi_bisnis = $request->input('situasi_bisnis');
            $data->kendala = $request->input('kendala');

            // Ya atau tidak
            // Enum Peserta Baru


            // Role
            $data->created_by = Auth::user()->name;
            $data->created_by_role = Auth::user()->role;
            $data->save();
            
            // Trigger auto update for Daily Activity
            \App\Models\DailyActiviti::updateAutomated(auth()->id(), now()->toDateString());

            return redirect()->route('admin.database.database')->with('success', 'Data has been added successfully.');
        }

        /**
         * Display the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */

        public function updatePotensi(Request $request, $id)
        {
            $data = data::findOrFail($id);
            $data->kelas_id = $request->kelas_id;
            $data->save();

            return response()->json(['success' => true]);
        }
        
            public function updateSumberLeads(Request $request, $id)
    {
        $data = data::findOrFail($id);
        $data->leads = $request->leads;

        // Update status_data jika yang edit adalah Operasional (Rafi) dan data asli dari Chapter
        $authUser = Auth::user();
        if (
            $authUser->hasHakAkses('operasional_rafi') &&
            $data->status_data === 'CHAPTER'
        ) {
            $data->status_data = 'EDIT OPS';
        }

        $data->save();

        return response()->json(['success' => true]);
    }



        public function show($id)
        {
            // Fetch the data by ID
            $data = data::findOrFail($id);
            $kelas = Kelas::all(); // Fetch all classes for the sidebar
            // Return a view to show the data
            return view('admin.Sales.database.show', compact('data', 'kelas'));
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function edit($id)
        {
            // Fetch the data by ID
            $data = data::findOrFail($id);

            $kelas = Kelas::all(); // Fetch all classes for the sidebar
            // Return a view to edit the data
            return view('admin.Sales.database.edit', compact('data', 'kelas'));
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, $id)
        {
            // Validate the request data
            $data = data::findOrFail($id);
            $data->nama = $request->input('nama');
            $data->status_peserta = $request->input('status_peserta', 'Peserta Baru');
            // Enum field
            $data->leads = $request->input('leads'); // Assuming 'leads' is an enum field
            // Custom field
            if ($request->input('leads_custom') === null) {
                $data->leads_custom = ''; // Set to empty string if null
            } else {
                $data->leads_custom = $request->input('leads_custom');
            }
            $data->provinsi_id = $request->input('provinsi_id');

            $data->kota_nama = $request->input('kota_nama');
            $data->jenis_bisnis = $request->input('jenis_bisnis') ?? $request->input('jenisbisnis');
            $data->nama_bisnis = $request->input('nama_bisnis');
            $data->no_wa = $request->input('no_wa');
            $data->situasi_bisnis = $request->input('situasi_bisnis');
            $data->kendala = $request->input('kendala');

            // Ya atau tidak

            $data->save();



            // Redirect to the index page with a success message
            return redirect()->route('admin.database.database')->with('success', 'Data has been updated successfully.');
        }


        /**
         * Remove the specified resource from storage.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function destroy($id)
        {
            // Fetch the data by ID
            $data = Data::findOrFail($id);
            // Delete the data
            $data->delete();
            // Redirect to the index page with a success message
            return redirect()->route('admin.database.database')->with('success', 'Data has been deleted successfully.');
        }


        // app/Http/Controllers/DatabaseController.php

        public function peserta_baru()
        {
            // Subrole 'alumni_admin' dapat melihat semua data tanpa filter
            if (Auth::user()->hasHakAkses('alumni_admin')) {
                $data = data::whereIn('status_peserta', ['peserta_baru', 'pindah_salesplan'])->paginate(50);
            } else {
                $data = data::whereIn('status_peserta', ['peserta_baru', 'pindah_salesplan'])
                    ->where('created_by', Auth::user()->name)
                    ->paginate(50);
            }
            return view('admin.Sales.database.database', compact('data'));
        }

        public function alumni()
        {
            // Subrole 'alumni_admin' dapat melihat semua data alumni tanpa filter
            if (Auth::user()->hasHakAkses('alumni_admin')) {
                $data = data::where('status_peserta', 'alumni')->paginate(50);
            } else {
                $data = data::where('status_peserta', 'alumni')
                    ->where('created_by', Auth::user()->name)
                    ->paginate(50);
            }
            return view('admin.Sales.database.database', compact('data'));
        }


    private function filterKelasByUser($user)
    {
        // Jika Administrator atau subrole view_all_classes / spp_admin / sales_full_view: tampil semua
        if (strtolower($user->role) == 'administrator' || $user->hasAnyHakAkses(['view_all_classes', 'spp_admin', 'sales_full_view'])) {
            return Kelas::all();
        }

        // Jika subrole muda_class_only → hanya Start-Up Muda Indonesia
        if ($user->hasHakAkses('muda_class_only')) {
            return Kelas::where('nama_kelas', 'Start-Up Muda Indonesia')->get();
        }

        // Jika subrole kaya_class_only → hanya Sekolah Kaya
        if ($user->hasHakAkses('kaya_class_only')) {
            return Kelas::where('nama_kelas', 'Sekolah Kaya')->get();
        }

        // Jika subrole no_muda_class → semua kecuali Start-Up Muda Indonesia
        if ($user->hasHakAkses('no_muda_class')) {
            return Kelas::where('nama_kelas', '!=', 'Start-Up Muda Indonesia')->get();
        }

        // Selain itu → semua kecuali Sekolah Kaya dan Start-Up Muda Indonesia
        return Kelas::whereNotIn('nama_kelas', ['Sekolah Kaya', 'Start-Up Muda Indonesia'])->get();
    }

        public function pindahkesalesplan(Request $request, $id)
        {
            // Ambil data peserta dari tabel data
            $data = Data::findOrFail($id);
            $kelasIds = $request->input('kelas_id', []);

            if (empty($kelasIds)) {
                return redirect()->back()->with('error', 'Silakan pilih minimal satu kelas.');
            }

            // Update status ke 'pindah_salesplan' agar bisa dibedakan, 
            // namun tetap muncul di list (karena index query include pindah_salesplan)
            // Simpan kelas pertama sebagai referensi utama di tabel data
            $data->status_peserta = 'pindah_salesplan';
            $data->kelas_id = $kelasIds[0];
            $data->save();

            foreach ($kelasIds as $kId) {
                // Cek apakah sudah ada salesplan untuk peserta ini di kelas ini
                $existing = SalesPlan::where('data_id', $data->id)
                    ->where('kelas_id', $kId)
                    ->first();
                
                if (!$existing) {
                    $salesPlan = new SalesPlan();
                    $salesPlan->nama = $data->nama;          // dari tabel peserta
                    $salesPlan->situasi_bisnis      = $data->situasi_bisnis; // dari tabel peserta
                    $salesPlan->kendala      = $data->kendala;       // dari tabel peserta
                    $salesPlan->kelas_id     = $kId;
                    $salesPlan->data_id      = $data->id; // Link ke data asli 
                    $salesPlan->created_by   = auth()->id();
                    $salesPlan->status       = 'cold'; // default awal
                    $salesPlan->level        = 'Grow Up'; // Forced grow up default
                    $salesPlan->save();
                }
            }

            $userRole = strtolower(auth()->user()->role);
            $message = 'Peserta berhasil dipindahkan ke ' . count($kelasIds) . ' kelas di ' . (in_array($userRole, ['reseller', 'chapter', 'agen']) ? 'Prospek' : 'Sales Plan') . '.';

            if (in_array($userRole, ['reseller', 'chapter', 'agen'])) {
                return redirect()->route('admin.salesplan.index', ['type' => 'smi'])->with('success', $message);
            }

            return redirect()->back()->with('success', $message);
            
        }

        /**
         * Update status directly from database view (Chapter/Reseller)
         */
        public function updateStatusDirect(Request $request)
        {
            $dataId = $request->data_id;
            $newStatus = $request->status;
            $nominalVal = $request->nominal; // New parameter
            
            $data = Data::findOrFail($dataId);
            $userRole = strtolower(auth()->user()->role);
            
            // Assume default class if none set
            $kelasId = $request->input('kelas_id') ?: $data->kelas_id;
            
            // For Chapter/Reseller, strictly use M1T
            if (in_array($userRole, ['chapter', 'reseller', 'agen'])) {
                $m1tClass = Kelas::where('nama_kelas', 'like', '%Muslim Indonesia%')->first();
                $kelasId = $m1tClass ? $m1tClass->id : ($kelasId ?: 1);
            } elseif (!$kelasId) {
                // Find M1T (Start-Up Muslim Indonesia) class ID as fallback
                $m1tClass = Kelas::where('nama_kelas', 'like', '%Muslim Indonesia%')->first();
                $kelasId = $m1tClass ? $m1tClass->id : 1; 
            }

            // Create or Update SalesPlan
            // Find existing plan for this data strictly matching the selected kelas_id
            $plan = SalesPlan::where('data_id', $dataId)
                ->where('kelas_id', $kelasId)
                ->first();

            if (!$plan) {
                $plan = new SalesPlan();
                $plan->data_id = $dataId;
                $plan->kelas_id = $kelasId;
                $plan->nama = $data->nama;
                $plan->created_by = auth()->id();
                $plan->level = 'Grow Up';
            }

            // If we are setting this plan to 'sudah_transfer', ensure other plans for this data_id 
            // are NOT 'sudah_transfer' to avoid appearing in both M1T and MBC lists.
            // Note: Removed logic that resets other 'sudah_transfer' statuses to allow 
            // a single lead to be registered as a paid participant in multiple classes.
            
            $oldStatus = $plan->status ?? 'new';
            $plan->status = $newStatus;
            
            // Save nominal if provided
            if ($nominalVal !== null) {
                $cleanNominal = preg_replace('/[^0-9]/', '', $nominalVal);
                $plan->nominal = (int)$cleanNominal;
            }
            
            $plan->save();

            // Auto update data status
            if ($data->status_peserta === 'peserta_baru') {
                $data->status_peserta = 'pindah_salesplan';
                $data->kelas_id = $kelasId;
                $data->save();
            }

            // Commission logic (copied from SalesPlanController)
            if ($newStatus === 'sudah_transfer' && $oldStatus !== 'sudah_transfer') {
                $creator = $plan->createdBy;
                $isChapter = $creator && str_contains(strtolower($creator->role), 'chapter');
                
                if (!$isChapter) {
                    $nominal = $plan->nominal;
                    if ($nominal > 0) {
                        \App\Services\WalletService::creditCommission(
                            $plan->created_by,
                            $nominal * 0.1, // 10% Commission
                            'Closing MMA - ' . $plan->nama,
                            'Program ' . ($plan->kelas ? $plan->kelas->nama_kelas : 'MMA')
                        );
                    }
                }
            }

            return response()->json(['success' => true, 'plan_id' => $plan->id]);
        }

        /**
         * Delete prospect directly from database view
         */
        public function deleteProspectDirect(Request $request)
        {
            $dataId = $request->data_id;
            $kelasId = $request->kelas_id;

            $deleted = SalesPlan::where('data_id', $dataId)
                ->where('kelas_id', $kelasId)
                ->delete();

            if ($deleted) {
                // If candidate has no more prospects left, reset status back to peserta_baru
                $remainingPlansCount = SalesPlan::where('data_id', $dataId)->count();
                if ($remainingPlansCount === 0) {
                    $data = Data::find($dataId);
                    if ($data) {
                        $data->status_peserta = 'peserta_baru';
                        $data->kelas_id = null;
                        $data->save();
                    }
                }
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Prospek tidak ditemukan.']);
        }
        public function getStatistik(Request $request)
        {
            $user = Auth::user();
            $userRole = strtolower($user->role);
            $filterUser = $request->input('user');
            
            $query = Data::query();

            // Admin & Manager Logic
<<<<<<< Updated upstream
            if (in_array($userRole, ['administrator', 'manager']) || $user->hasHakAkses('cs_manager_smi')) {
=======
            if (in_array($userRole, ['administrator', 'manager']) || $user->hasSubrole('manager_smi')) {
>>>>>>> Stashed changes
                if (!empty($filterUser)) {
                    $query->where('created_by', $filterUser);
                }
            } else {
                // CS Biasa
                $query->where('created_by', $user->name);
            }

<<<<<<< Updated upstream
            if ($user->hasHakAkses('smi_class_only')) {
=======
            // Agus Setyo Filter
            if ($user->hasSubrole('manager_smi')) {
>>>>>>> Stashed changes
                $query->whereHas('kelas', function($q) {
                    $q->where('nama_kelas', 'Start-Up Muda Indonesia')
                    ->orWhere('nama_kelas', 'Start-Up Muslim Indonesia');
                });
            }

            // Calculate Stats
            $now = \Carbon\Carbon::now();
            $bulanLabel = $now->isoFormat('MMMM YYYY');
            
            $databaseBaru = (clone $query)
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->count();
                
            $totalDatabase = $query->count();
            $targetAdmin = \App\Models\Setting::where('key', 'target_database_admin')->value('value') ?? 250;
            $targetCs = \App\Models\Setting::where('key', 'target_database_cs')->value('value') ?? 50;
            $target = in_array(strtolower($user->role), ['administrator', 'operasional']) ? $targetAdmin : $targetCs;
            $kurang = max($target - $databaseBaru, 0);

            return response()->json([
                'bulanLabel' => $bulanLabel,
                'databaseBaru' => $databaseBaru,
                'totalDatabase' => $totalDatabase,
                'target' => $target,
                'kurang' => $kurang
        ]);
    }

    public function exportPdfInteraksi(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $query = $this->getFilteredQuery($request);

        // Fetch inputs for follow up period filtering
        $bulanFilter = $request->input('bulan');
        $tahunFilter = $request->input('tahun');

        // Display values for PDF header
        $displayBulan = $bulanFilter ?: (int) date('m');
        $displayTahun = $tahunFilter ?: (int) date('Y');

        $daftarKelasFilter = $request->input('daftar_kelas');
        $prospekKelasId = $request->input('prospek_kelas_id');
        $kelasIdFilter = $request->input('kelas_id');
        $kelasFilter = $request->input('kelas');
        $targetKelasId = $daftarKelasFilter ?: ($prospekKelasId ?: ($kelasIdFilter ?: $kelasFilter));

        // --- FILTER HANYA YANG SUDAH DI FOLLOW UP ---
        $query->where(function($q) use ($bulanFilter, $tahunFilter, $targetKelasId) {
            if (!empty($bulanFilter) && !empty($tahunFilter)) {
                // Check in data table
                $q->where(function($subq) use ($bulanFilter, $tahunFilter) {
                    for($i=1; $i<=10; $i++) {
                        $subq->orWhere(function($subq2) use ($i, $bulanFilter, $tahunFilter) {
                            $subq2->whereMonth("fu{$i}_at", $bulanFilter)
                                  ->whereYear("fu{$i}_at", $tahunFilter);
                        });
                    }
                })
                // OR check in salesplans table
                ->orWhereHas('salesplan', function($subq) use ($bulanFilter, $tahunFilter, $targetKelasId) {
                    if (!empty($targetKelasId) && $targetKelasId !== 'all') {
                        $subq->where('kelas_id', $targetKelasId);
                    }
                    $subq->where(function($subq2) use ($bulanFilter, $tahunFilter) {
                        for($i=1; $i<=10; $i++) {
                            $subq2->orWhere(function($subq3) use ($i, $bulanFilter, $tahunFilter) {
                                $subq3->whereMonth("fu{$i}_at", $bulanFilter)
                                      ->whereYear("fu{$i}_at", $tahunFilter);
                            });
                        }
                    });
                });
            } else {
                // Check in data table for any follow-up
                $q->where(function($subq) {
                    for($i=1; $i<=10; $i++) {
                        $subq->orWhereNotNull("fu{$i}_at")
                              ->orWhere("fu{$i}_hasil", '!=', '')
                              ->orWhereNotNull("fu{$i}_hasil")
                              ->orWhere("fu{$i}_tindak_lanjut", '!=', '')
                              ->orWhereNotNull("fu{$i}_tindak_lanjut")
                              ->orWhere("fu{$i}_wa", 1)
                              ->orWhere("fu{$i}_telp", 1);
                    }
                })
                // OR check in salesplans table for any follow-up
                ->orWhereHas('salesplan', function($subq) use ($targetKelasId) {
                    if (!empty($targetKelasId) && $targetKelasId !== 'all') {
                        $subq->where('kelas_id', $targetKelasId);
                    }
                    $subq->where(function($subq2) {
                        for($i=1; $i<=10; $i++) {
                            $subq2->orWhereNotNull("fu{$i}_at")
                                  ->orWhere("fu{$i}_hasil", '!=', '')
                                  ->orWhereNotNull("fu{$i}_hasil")
                                  ->orWhere("fu{$i}_tindak_lanjut", '!=', '')
                                  ->orWhereNotNull("fu{$i}_tindak_lanjut")
                                  ->orWhere("fu{$i}_wa", 1)
                                  ->orWhere("fu{$i}_telp", 1);
                        }
                    });
                });
            }
        });

        // Sorting
        $sortByParam = $request->input('sort_by', 'created_at');
        if ($sortByParam === 'follow_up') {
            $kelasCond = '';
            if (!empty($targetKelasId) && $targetKelasId !== 'all') {
                $kelasCond = "AND salesplans.kelas_id = " . intval($targetKelasId);
            }

            $followUpCountSql = "(SELECT (
                CASE WHEN fu1_at IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN fu2_at IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN fu3_at IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN fu4_at IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN fu5_at IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN fu6_at IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN fu7_at IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN fu8_at IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN fu9_at IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN fu10_at IS NOT NULL THEN 1 ELSE 0 END
            ) FROM salesplans WHERE salesplans.data_id = data.id AND salesplans.deleted_at IS NULL {$kelasCond} ORDER BY salesplans.id DESC LIMIT 1)";
            
            $query->select('data.*');
            $query->selectSub($followUpCountSql, 'fu_count_sort');
            $query->orderBy('is_no_potensi', 'asc')
                  ->orderBy('fu_count_sort', 'desc')
                  ->orderBy('data.id', 'desc');
        } else {
            $query->orderBy('data.id', 'desc');
        }

        $items = $query->get();

        // Get class name if filtered
        $kelasName = null;
        if (!empty($targetKelasId) && $targetKelasId !== 'all') {
            $kelasObj = Kelas::find($targetKelasId);
            if ($kelasObj) {
                $kelasName = str_contains($kelasObj->nama_kelas, 'Muslim Indonesia') ? 'M1T' : $kelasObj->nama_kelas;
            }
        }

        // Get status name if filtered
        $statusFilter = $request->input('status');
        $statusName = null;
        if (!empty($statusFilter) && $statusFilter !== 'total_database') {
            $statusName = ucfirst(str_replace('_', ' ', $statusFilter));
        }

        // Get CS name
        $csFilter = $request->input('cs_name');
        $user = Auth::user();
        $userRole = strtolower($user->role);
        if (!$csFilter && !in_array($userRole, ['administrator', 'manager', 'marketing'])) {
            $csFilter = $user->name;
        }

        $pdf = Pdf::loadView('admin.Sales.database.pdf-interaksi', [
            'items' => $items,
            'bulan' => $displayBulan,
            'tahun' => $displayTahun,
            'csName' => $csFilter ?: 'Semua CS',
            'kelasName' => $kelasName,
            'statusName' => $statusName
        ])->setPaper('a4', 'landscape');

        $fileName = 'Rekap_Interaksi_' . ($csFilter ?: 'Semua') . '_' . ($displayBulan ?: 'Semua') . '_' . $displayTahun . '.pdf';
        return $pdf->download($fileName);
    }

    private function getFilteredQuery(Request $request)
    {
        $user = Auth::user();
        $userRole = strtolower($user->role);

        // Fetch inputs
        $kelasFilter = $request->input('kelas');
        $csFilter    = $request->input('cs_name');
        $chapterFilter = $request->input('chapter_id');
        $bulanFilter = $request->input('bulan');
        $tahunFilter = $request->input('tahun');
        $statusFilter = $request->input('status');
        $potensiFilter = $request->input('potensi');
        $kelasIdFilter = $request->input('kelas_id');
        $searchFilter = $request->input('search');
        
        $ikutKelasFilter = $request->input('ikut_kelas');
        $daftarKelasFilter = $request->input('daftar_kelas');
        $zoomFilter = $request->input('zoom');
        $bantFilter = $request->input('bant');
        $sumberFilter = $request->input('sumber');
        $kotaFilter = $request->input('kota');
        $provinsiFilter = $request->input('provinsi');
        $spinFilter = $request->input('filter_spin');
        $prospekKelasId = $request->input('prospek_kelas_id');

        // If regular CS (not admin/manager/marketing) and no filter selected, use their own name
        if (!$csFilter && !in_array($userRole, ['administrator', 'manager', 'marketing'])) {
            $csFilter = $user->name;
        }

        // Base Query
        $query = Data::with(['kelas', 'salesplan' => function($q) use ($daftarKelasFilter, $prospekKelasId, $kelasIdFilter, $kelasFilter) {
                $targetKelasId = $daftarKelasFilter ?: ($prospekKelasId ?: ($kelasIdFilter ?: $kelasFilter));
                if (!empty($targetKelasId) && $targetKelasId !== 'all') {
                    $q->where('kelas_id', $targetKelasId);
                }
                $q->orderBy('updated_at', 'desc');
            }, 'salesplan.kelas', 'createdBy'])
            ->whereIn('status_peserta', ['peserta_baru', 'pindah_salesplan']);

        $viewType = $request->input('view_type');
        if (empty($viewType) && $userRole === 'administrator') {
            $viewType = 'cs';
        }
        if ($userRole === 'operasional') {
            $viewType = 'chapter';
        }

        if ($viewType === 'cs') {
            $query->where('created_by_role', 'cs-mbc');
        } elseif ($viewType === 'chapter') {
            if (empty($csFilter) && empty($chapterFilter)) {
                $query->where(function($q) {
                    $q->whereIn('created_by_role', ['chapter', 'reseller', 'agen'])
                      ->orWhere('created_by_role', 'operasional')
                      ->orWhereIn('status_data', ['ADD OPS', 'EDIT OPS']);
                });
            }
        } elseif ($userRole === 'cs-mbc') {
            $query->whereNotIn('created_by_role', ['chapter', 'reseller', 'agen', 'operasional']);
        }

        // CS biasa -> hanya datanya sendiri
        $forceMyData = $request->input('view') === 'me';
        if ($userRole === 'marketing') {
            if ($user->hasAnyHakAkses(['activity_marketing_offline', 'activity_marketing'])) {
                $query->whereIn('leads', ['Event', 'Open House']);
            } elseif ($user->hasAnyHakAkses(['activity_marketing_online', 'activity_intake'])) {
                $query->whereIn('leads', ['Online', 'Sosmed']);
            } else {
                $query->whereIn('leads', ['Marketing', 'Ads', 'Sosmed', 'Zoom', 'Open House']);
            }
            $query->where('created_by_role', 'cs-mbc');
<<<<<<< Updated upstream
        } elseif (!in_array($userRole, ['administrator', 'manager', 'chapter', 'reseller', 'agen', 'operasional']) && !$user->hasAnyHakAkses(['smi_class_only', 'cs_manager_smi'])) {
=======
        } elseif (!in_array($userRole, ['administrator', 'manager', 'chapter', 'reseller', 'agen', 'operasional']) && !$user->hasSubrole('manager_smi')) {
>>>>>>> Stashed changes
            $query->where('created_by', $user->name);
        }

        // Manager
        if ($userRole === 'manager') {
            $managerCsNames = User::whereIn('role', ['cs-smi', 'cs-mbc'])->orWhereJsonContains('hak_akses', 'cs_pusat')->pluck('name')->toArray();
            $query->whereIn('created_by', $managerCsNames);
        }

        // Filter User
        if (!empty($csFilter)) {
            $query->where('created_by', $csFilter);
        }

        // Filter Chapter
        if (!empty($chapterFilter)) {
            $selectedChapter = User::find($chapterFilter);
            if ($selectedChapter) {
                $query->where('created_by', $selectedChapter->name);
            }
        }

        // Filter Kelas & Bulan & Tahun (Created At)
        if (!empty($kelasFilter)) {
            $query->where('kelas_id', $kelasFilter);
        }
        if (!empty($bulanFilter)) {
            $query->whereMonth('created_at', $bulanFilter);
        }
        if (!empty($tahunFilter)) {
            $query->whereYear('created_at', $tahunFilter);
        }

        // Filter Potensi Kelas
        if (!empty($potensiFilter)) {
            $query->where('potensi', $potensiFilter);
        }

        // Filter Nama Kelas (dari dropdown dinamis MBC)
        if (!empty($kelasIdFilter)) {
            $query->where('kelas_id', $kelasIdFilter);
        }

        // Filter Search
        if (!empty($searchFilter)) {
            $query->where(function($q) use ($searchFilter) {
                $q->where('nama', 'LIKE', '%'.$searchFilter.'%')
                ->orWhere('leads', 'LIKE', '%'.$searchFilter.'%')
                ->orWhere('nama_bisnis', 'LIKE', '%'.$searchFilter.'%')
                ->orWhere('no_wa', 'LIKE', '%'.$searchFilter.'%')
                ->orWhere('keterangan_spin', 'LIKE', '%'.$searchFilter.'%');
            });
        }

        // Filter Sumber, Kota, Provinsi
        if (!empty($sumberFilter)) {
            $query->where('leads', $sumberFilter);
        }
        if (!empty($kotaFilter)) {
            $query->where('kota_nama', $kotaFilter);
        }
        if (!empty($provinsiFilter)) {
            $query->where('provinsi_nama', $provinsiFilter);
        }

        // Filter Spin
        if (!empty($spinFilter)) {
            if ($spinFilter === 'ALL') {
                $query->where('bant_budget', 1)
                      ->where('bant_authority', 1)
                      ->where('bant_time', 1);
            } elseif ($spinFilter === 'NOT_ALL') {
                $query->where(function($q) {
                    $q->where('bant_budget', '!=', 1)
                      ->orWhereNull('bant_budget')
                      ->orWhere('bant_authority', '!=', 1)
                      ->orWhereNull('bant_authority')
                      ->orWhere('bant_time', '!=', 1)
                      ->orWhereNull('bant_time');
                });
            }
        }

        // Filter Zoom
        if ($zoomFilter !== null && $zoomFilter !== '') {
            $query->where('ikut_zoom', $zoomFilter);
        }

        // Filter Ikut Kelas (SalesPlan)
        if ($ikutKelasFilter !== null && $ikutKelasFilter !== '') {
            if ($ikutKelasFilter == '1') {
                $query->whereHas('salesplan', function($q) use ($daftarKelasFilter) {
                    $q->where('status', 'sudah_transfer');
                    if (!empty($daftarKelasFilter)) {
                        $q->where('kelas_id', $daftarKelasFilter);
                    }
                });
            } else {
                if (!empty($daftarKelasFilter)) {
                    $query->whereHas('salesplan', function($q) use ($daftarKelasFilter) {
                        $q->where('kelas_id', $daftarKelasFilter)
                          ->where('status', '!=', 'sudah_transfer');
                    });
                } else {
                    $query->whereDoesntHave('salesplan', function($q) {
                        $q->where('status', 'sudah_transfer');
                    });
                }
            }
        }

        // Filter BANT
        if ($bantFilter !== null && $bantFilter !== '') {
            $query->where('bant', $bantFilter);
        }

        // Filter Potensi
        $potensiVal = $request->input('potensi');
        $potensiKelasVal = $request->input('potensi_kelas_id');
        if (!empty($potensiVal) && $potensiVal !== 'all') {
            if (in_array(strtoupper($potensiVal), ['MBC', 'SMI'])) {
                $query->where('potensi', strtoupper($potensiVal));
                if (!empty($potensiKelasVal)) {
                    $query->where('kelas_id', $potensiKelasVal);
                }
            } else {
                $query->where(function($q) use ($potensiVal) {
                    $q->where('potensi', $potensiVal)
                      ->orWhere('potensi', strtolower($potensiVal))
                      ->orWhere('potensi', strtoupper($potensiVal))
                      ->orWhere('situasi_bisnis', 'like', '%Kategori: ' . strtoupper($potensiVal) . '%');
                });
            }
        }

        // Filter Prospek by Class
        if (!empty($prospekKelasId) && $prospekKelasId !== 'all') {
            $query->whereHas('salesplan', function($q) use ($prospekKelasId) {
                $q->where('kelas_id', $prospekKelasId);
            });
        }

        // Chapter Role
        if ($userRole === 'chapter') {
<<<<<<< Updated upstream
            $excludeNames = User::whereNotIn('role', ['chapter', 'reseller', 'agen'])
                ->pluck('name')
                ->toArray();
=======
            $excludeNames = User::whereNotIn('role', ['chapter', 'reseller', 'agen'])->pluck('name')->toArray();
>>>>>>> Stashed changes
            $query->where(function($q) use ($user, $excludeNames) {
                $q->where('created_by', $user->name)
                  ->orWhere(function($subQ) use ($user, $excludeNames) {
                      $subQ->where('kota_nama', 'like', '%' . $user->chapter . '%')
                           ->whereNotIn('created_by', $excludeNames)
                           ->where('created_by_role', '!=', 'cs-mbc');
                  });
            });
        } elseif (in_array($userRole, ['reseller', 'agen'])) {
            $downlineNames = User::where('created_by', $user->id)->pluck('name')->toArray();
            $viewNames = array_merge([$user->name], $downlineNames);
            if ($user->hasHakAkses('share_depok_leads')) {
                $viewNames[] = 'AGUNG H. WIBOWO';
            }
            $query->whereIn('created_by', $viewNames);
        }

<<<<<<< Updated upstream
        if ($user->hasHakAkses('smi_class_only')) {
=======
        // Khusus Agus Setyo
        if ($user->hasSubrole('manager_smi')) {
>>>>>>> Stashed changes
            $query->whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'Start-Up Muda Indonesia')
                ->orWhere('nama_kelas', 'Start-Up Muslim Indonesia');
            });
        }

        // Filter Status (Legend Status)
        if (!empty($statusFilter)) {
            if ($statusFilter === 'total_database') {
                // No status filter
            } elseif ($statusFilter === 'database_baru') {
                $now = \Carbon\Carbon::now();
                $statsYear = $request->input('tahun', $now->year);
                $statsMonth = $request->input('bulan', $now->month);
                $query->whereYear('created_at', $statsYear)
                      ->whereMonth('created_at', $statsMonth);
            } elseif ($statusFilter === 'potensi') {
                $query->whereHas('salesplan', function($q) use ($daftarKelasFilter) {
                    $q->whereIn('status', ['cold', 'tertarik', 'mau_transfer']);
                    if (!empty($daftarKelasFilter)) {
                        $q->where('kelas_id', $daftarKelasFilter);
                    }
                });
            } else {
                $query->whereHas('salesplan', function($q) use ($statusFilter, $daftarKelasFilter) {
                    $q->where('status', $statusFilter);
                    if (!empty($daftarKelasFilter)) {
                        $q->where('kelas_id', $daftarKelasFilter);
                    }
                });
            }
        }

        return $query;
    }

    public function toggleNoPotensi($id)
    {
        try {
            $data = Data::findOrFail($id);
            $data->is_no_potensi = !$data->is_no_potensi;
            $data->save();

            return response()->json([
                'success' => true,
                'is_no_potensi' => $data->is_no_potensi,
                'message' => 'Status potensi diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function formM1t($identifier)
    {
        if (strtolower($identifier) === 'cs-mbc') {
            // Equal Rotator from users with cs_rotasi subrole
            $sequence = \App\Models\User::whereJsonContains('hak_akses', 'cs_rotasi')
                ->where('is_active', 1)
                ->pluck('name')
                ->toArray();
            
            if (empty($sequence)) {
<<<<<<< Updated upstream
                $sequence = \App\Models\User::where('role', 'cs-mbc')->where('is_active', 1)->pluck('name')->toArray();
=======
                $sequence = \App\Models\User::where('role', 'cs-mbc')
                    ->where('is_active', 1)
                    ->pluck('name')
                    ->toArray();
>>>>>>> Stashed changes
            }
            
            // Get the last lead submitted by one of these CSs to find who was assigned last
            $lastLead = \App\Models\Data::whereIn('created_by', $sequence)
                ->orderBy('id', 'desc')
                ->first();
                
            $nextIndex = 0;
            if ($lastLead) {
                $lastCsName = $lastLead->created_by;
                $lastIndex = array_search($lastCsName, $sequence);
                if ($lastIndex !== false) {
                    $nextIndex = ($lastIndex + 1) % count($sequence);
                }
            }
            
            $selectedCsName = $sequence[$nextIndex];
            $user = \App\Models\User::where('name', 'LIKE', '%' . $selectedCsName . '%')->first();
            
            // Fallback to any cs-mbc if not found
            if (!$user) {
                $user = \App\Models\User::where('role', 'cs-mbc')->first();
            }
        } else {
            $original = str_replace('-', ' ', $identifier);
            
            if (strpos($identifier, 'chapter-') === 0) {
                $chapterName = str_replace('chapter-', '', $identifier);
                $chapterName = str_replace('-', ' ', $chapterName);
                $user = \App\Models\User::where('role', 'chapter')
                    ->where('chapter', 'LIKE', '%' . $chapterName . '%')
                    ->first();
            } else {
                // Check by username, or by name if username is null
                $user = \App\Models\User::where('username', $identifier)
                    ->orWhere('name', 'LIKE', '%' . $original . '%')
                    ->first();
            }
        }

        // Fallback to ID if somehow it was a numeric ID
        if (!$user && is_numeric($identifier)) {
            $user = \App\Models\User::find($identifier);
        }

        if (!$user) {
            abort(404, 'User/Chapter tidak ditemukan.');
        }

        return response()
            ->view('admin.Sales.database.form_m1t', compact('user'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    public function storeFormM1t(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = \App\Models\User::findOrFail($user_id);

        $data = new Data();
        $data->nama = $request->input('nama');
        $data->no_wa = $request->input('no_wa');
        $data->provinsi_id = $request->input('provinsi');
        $data->provinsi_nama = $request->input('provinsi_nama');
        $data->kota_id = $request->input('kota');
        $data->kota_nama = $request->input('kota_nama'); 
        $data->nama_bisnis = $request->input('nama_usaha');
        
        $answers = [
            "Lama bisnis: " . $request->input('lama_bisnis_label'),
            "Omset: " . $request->input('omset_label'),
            "Tantangan: " . $request->input('tantangan_label'),
            "Target: " . $request->input('target_label'),
            "Alasan: " . $request->input('alasan_label'),
            "Posisi: " . $request->input('posisi_label'),
            "Investasi: " . $request->input('investasi_label'),
            "Kesiapan: " . $request->input('kesiapan_hadir_label'),
            "Keputusan: " . $request->input('keputusan_label'),
            "Jumlah Karyawan: " . $request->input('jumlah_karyawan_label'),
            "Mengenal Coach: " . $request->input('mengenal_coach_label')
        ];

        if ($request->filled('jadwal_zoom_tanggal') && $request->filled('jadwal_zoom_jam')) {
            $answers[] = "Jadwal Zoom: " . $request->input('jadwal_zoom_tanggal') . " " . $request->input('jadwal_zoom_jam');
        }

        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $subFolder = 'uploads/bukti_transfer';
            
            $destPath = public_path($subFolder);
            if (!file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }
            $file->move($destPath, $filename);
            $buktiPath = $subFolder . '/' . $filename;
            $answers[] = "Bukti Transfer: " . asset($buktiPath);
        }

        $totalScore = $request->input('total_score');

        // Determine Category/Potensi
        $category = 'Cold';
        if ($totalScore >= 41) {
            $category = 'Hot';
        } elseif ($totalScore >= 25) {
            $category = 'Warm';
        }

        $data->situasi_bisnis = implode("\n", $answers) . "\n\nTotal Skor Form: " . $totalScore . " / 51\nKategori: " . strtoupper($category);
        $data->potensi = $category;
        $data->status_peserta = 'peserta_baru';
        
        $role = strtolower($user->role);
        if (in_array($role, ['chapter', 'reseller', 'agen']) || str_starts_with($role, 'chapter_')) {
            $data->leads = 'Open House';
        } else {
            $data->leads = 'Ads';
        }
        
        // Find M1T (Start-Up Muslim Indonesia) class ID
        $m1tClass = Kelas::where('nama_kelas', 'like', '%Muslim Indonesia%')->first();
        if ($m1tClass) {
            $data->kelas_id = $m1tClass->id;
        }

        $data->created_by = $user->name;
        $data->created_by_role = $user->role;
        $data->save();

        // Create SalesPlan for M1T class
        if ($m1tClass) {
            $plan = new SalesPlan();
            $plan->data_id = $data->id;
            $plan->kelas_id = $m1tClass->id;
            $plan->nama = $data->nama;
            $plan->created_by = $user->id;
            $plan->status = 'cold';
            $plan->level = 'Grow Up';
            $plan->save();
        }

        // Create ZoomSchedule if date & time are provided
        if ($request->filled('jadwal_zoom_tanggal') && $request->filled('jadwal_zoom_jam')) {
            try {
                $scheduledAt = \Carbon\Carbon::parse($request->input('jadwal_zoom_tanggal') . ' ' . $request->input('jadwal_zoom_jam'));
                
                $schedule = new \App\Models\ZoomSchedule();
                $schedule->data_id = $data->id;
                $schedule->salesplan_id = isset($plan) ? $plan->id : null;
                $schedule->cs_id = $user->id;
                $schedule->scheduled_at = $scheduledAt;
                $schedule->zoom_link = ''; // Default empty
                $schedule->status = 'scheduled'; // Default status
                $schedule->notes = 'Dibuat otomatis dari Google Form M1T';
                $schedule->save();
            } catch (\Exception $e) {
                // Ignore formatting exceptions
            }
        }

        // Send WhatsApp Notification to the assigned CS (Rotator or Specific Form Owner)
        // Nomor WA CS diambil dari kolom 'wa' di tabel users (diisi melalui profil/settings)
        $waUrl = null;
        try {
            
            $isChapterOrAgent = in_array(strtolower($user->role ?? ''), ['chapter', 'reseller', 'agen']);
            
            // Ambil nomor WA dari kolom 'wa' milik user yang bersangkutan
            // Tidak perlu hardcode — setiap CS mengisi nomor WA-nya sendiri di profil
            if ($isChapterOrAgent) {
                $waNumber = !empty($user->wa) ? $user->wa : null;
            } else {
                // Gunakan nomor WA dari profil user (kolom 'wa')
                // Jika belum diisi, kirim notifikasi ke nomor admin via env
                $waNumber = !empty($user->wa) ? $user->wa : env('ADMIN_WA_FALLBACK');
            }
            
            $zoomTanggal = $request->input('jadwal_zoom_tanggal') ?? '-';
            $zoomJam = $request->input('jadwal_zoom_jam') ?? '-';
            $namaUsaha = $data->nama_bisnis ?? '-';

            if (strtolower($user->role ?? '') === 'cs-mbc' && !empty($waNumber)) {
                $formattedWa = preg_replace('/[^0-9]/', '', $waNumber);
                if (strpos($formattedWa, '0') === 0) {
                    $formattedWa = '62' . substr($formattedWa, 1);
                }
                
                $clientMessage = "*Halo CS M1T Helas Corp,*\n"
                               . "Saya sudah mengisi Form Pendaftaran & Penjadwalan Konsultasi Program M1T.\n\n"
                               . "*Berikut Data Saya:*\n"
                               . "- *Nama Lengkap:* " . $data->nama . "\n"
                               . "- *No. WhatsApp:* " . $data->no_wa . "\n"
                               . "- *Nama Usaha:* " . $namaUsaha . "\n"
                               . "- *Jadwal Sesi Zoom:* " . $zoomTanggal . " @ " . $zoomJam . " WIB\n\n"
                               . "Mohon dikonfirmasi jadwal konsultasi saya. Terima kasih!";
                
                $waUrl = "https://api.whatsapp.com/send?phone=" . $formattedWa . "&text=" . urlencode($clientMessage);
            }
            
            $message = "*Notifikasi ADS Masuk*\n"
                     . "*Database Calon Peserta M1T*\n\n"
                     . "*A. DATA DIRI*\n"
                     . "- *Nama Lengkap:* " . $data->nama . "\n"
                     . "- *No. WhatsApp:* " . $data->no_wa . "\n"
                     . "- *Nama Usaha:* " . $namaUsaha . "\n\n"
                     . "*B. JADWAL SESI ZOOM*\n"
                     . "- *Tanggal:* " . $zoomTanggal . "\n"
                     . "- *Jam:* " . $zoomJam . "\n\n"
                     . "----------------------------------------\n"
                     . "_Data ini masuk otomatis dari Form M1T " . $user->name . "_";

            $token = env('FONNTE_TOKEN');
            if ($token && !empty($waNumber)) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.fonnte.com/send',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => array(
                    'target' => $waNumber,
                    'message' => $message,
                    'countryCode' => '62',
                  ),
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token
                  ),
                ));
                
                $response = curl_exec($curl);
                curl_close($curl);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send WhatsApp notification: " . $e->getMessage());
        }

        if ($waUrl) {
            return redirect()->back()->with('success', 'Data Open House M1T berhasil disubmit.')->with('wa_url', $waUrl);
        }

        return redirect()->back()->with('success', 'Data Open House M1T berhasil disubmit.');
    }
    public function reuseData(Request $request)
    {
        try {
            $dataId = $request->id;
            $data = Data::findOrFail($dataId);
            
            // 1. Clear current Data model's potential tracking & BAT so view resets
            $data->potensi = null;
            $data->kelas_id = null;
            $data->bant_budget = false;
            $data->bant_authority = false;
            $data->bant_time = false;
            $data->ikut_zoom = false;
            $data->status_peserta = 'peserta_baru'; // Reset to fresh lead status
            
            // Archive existing FU history to keterangan_spin before clearing
            $oldHistory = $data->keterangan_spin ? $data->keterangan_spin . "\n\n" : "";
            $hasOldLogs = false;
            $archiveLogs = "--- ARSIP FOLLOW UP (" . date('d/m/Y H:i') . ") ---\n";
            
            for ($i = 1; $i <= 10; $i++) {
                $hasil = $data->{"fu{$i}_hasil"};
                $at    = $data->{"fu{$i}_at"};
                $tl    = $data->{"fu{$i}_tindak_lanjut"};
                
                if ($hasil) {
                    $hasOldLogs = true;
                    $archiveLogs .= "FU{$i} [" . ($at ? $at->format('d/m/Y H:i') : '-') . "]: {$hasil}";
                    if ($tl) $archiveLogs .= " | TL: {$tl}";
                    $archiveLogs .= "\n";
                }
                
                // Clear the actual fields for the new cycle
                $data->{"fu{$i}_hasil"} = null;
                $data->{"fu{$i}_tindak_lanjut"} = null;
                $data->{"fu{$i}_wa"} = 0;
                $data->{"fu{$i}_telp"} = 0;
                $data->{"fu{$i}_at"} = null;
            }
            
            if ($hasOldLogs) {
                $data->keterangan_spin = $oldHistory . $archiveLogs;
            }
            
            $data->save();

            // 2. Create a new SalesPlan (History entry + new active status)
            $plan = new SalesPlan();
            $plan->data_id = $dataId;
            $plan->kelas_id = null; // Clear class selection for the new cycle
            $plan->nama = $data->nama;
            $plan->created_by = auth()->id();
            $plan->status = 'cold'; // Start fresh as cold
            $plan->level = 'Grow Up';
            $plan->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function noPotensi($id)
    {
        try {
            $data = Data::findOrFail($id);
            $data->is_no_potensi = true;
            $data->save();
            return redirect()->back()->with('success', 'Data berhasil ditandai sebagai Tidak Potensi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses data.');
        }
    }

    public function getRealtimeCount(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }
        $userRole = strtolower($user->role);
        
        $query = \App\Models\Data::whereIn('status_peserta', ['peserta_baru', 'pindah_salesplan']);
        
        $viewType = $request->input('view_type');
        if (empty($viewType) && $userRole === 'administrator') {
            $viewType = 'cs';
        }
        if ($userRole === 'operasional') {
            $viewType = 'chapter';
        }
        
        if ($viewType === 'cs') {
            $query->where('created_by_role', 'cs-mbc');
        } elseif ($viewType === 'chapter') {
            $query->where(function($q) {
                $q->whereIn('created_by_role', ['chapter', 'reseller', 'agen'])
                  ->orWhere('created_by_role', 'operasional')
                  ->orWhereIn('status_data', ['ADD OPS', 'EDIT OPS']);
            });
        } elseif ($userRole === 'cs-mbc') {
            $query->whereNotIn('created_by_role', ['chapter', 'reseller', 'agen', 'operasional']);
        }

        if ($userRole === 'marketing') {
            if ($user->hasAnyHakAkses(['activity_marketing_offline', 'activity_marketing'])) {
                $query->whereIn('leads', ['Event', 'Open House']);
            } elseif ($user->hasAnyHakAkses(['activity_marketing_online', 'activity_intake'])) {
                $query->whereIn('leads', ['Online', 'Sosmed']);
            } else {
                $query->whereIn('leads', ['Marketing', 'Ads', 'Sosmed', 'Zoom', 'Open House']);
            }
            $query->where('created_by_role', 'cs-mbc');
<<<<<<< Updated upstream
        } elseif (!in_array($userRole, ['administrator', 'manager', 'chapter', 'reseller', 'agen', 'operasional']) && !$user->hasAnyHakAkses(['smi_class_only', 'cs_manager_smi'])) {
=======
        } elseif (!in_array($userRole, ['administrator', 'manager', 'chapter', 'reseller', 'agen', 'operasional']) && !$user->hasSubrole('manager_smi')) {
>>>>>>> Stashed changes
            $query->where('created_by', $user->name);
        }

        if ($userRole === 'manager') {
            $managerCsNames = User::whereIn('role', ['cs-smi', 'cs-mbc'])->orWhereJsonContains('hak_akses', 'cs_pusat')->pluck('name')->toArray();
            $query->whereIn('created_by', $managerCsNames);
        }

        $count = $query->count();
        return response()->json(['count' => $count]);
    }
}
