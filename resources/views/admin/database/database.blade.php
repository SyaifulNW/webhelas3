@extends('layouts.masteradmin')
@section('content')
    @php
        $userRole = strtolower(auth()->user()->role);
        $viewType = request('view_type');
        $isChapterView =
            $userRole === 'chapter' ||
            $userRole === 'reseller' ||
            (in_array($userRole, ['administrator', 'operasional']) && $viewType === 'chapter');
        $isAdminCSView = $userRole === 'administrator' && $viewType !== 'chapter';
        $isCSMBCView = $userRole === 'cs-mbc';

        $chapterList = \App\Models\User::whereIn('role', ['chapter', 'reseller', 'agen'])
            ->select('id', 'name', 'chapter', 'role')
            ->orderBy('role')
            ->orderBy('name')
            ->get();
    @endphp

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <style>
        /* Premium FullCalendar Customizer inside Modal */
        #modalZoomCalendar {
            font-size: 11px !important;
        }

        #modalZoomCalendar .fc-theme-standard .fc-scrollgrid {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e3e6f0;
        }

        #modalZoomCalendar .fc .fc-toolbar-title {
            font-weight: 800;
            color: #333333;
            font-size: 1rem !important;
        }

        #modalZoomCalendar .fc .fc-button-primary {
            background-color: #2a5298 !important;
            border-color: #2a5298 !important;
            font-weight: 700;
            border-radius: 8px;
            padding: 4px 10px !important;
            font-size: 11px !important;
            transition: all 0.2s;
        }

        #modalZoomCalendar .fc .fc-button-primary:hover {
            background-color: #1e3c72 !important;
            border-color: #1e3c72 !important;
        }

        #modalZoomCalendar .fc-daygrid-day-number {
            font-weight: 700;
            color: #555555;
            font-size: 0.75rem;
        }

        #modalZoomCalendar .fc-col-header-cell-cushion {
            font-weight: 800;
            color: #333333;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
        }

        #modalZoomCalendar .fc-event {
            cursor: pointer;
            padding: 1px 4px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.65rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        }
    </style>
    <style>
        @keyframes pulse-orange {
            0% {
                box-shadow: 0 0 0 0 rgba(230, 126, 34, 0.7);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(230, 126, 34, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(230, 126, 34, 0);
            }
        }

        .badge-pulse {
            animation: pulse-orange 2s infinite;
            background: #e67e22 !important;
            border: 2px solid #fff !important;
            border-radius: 6px !important;
            display: inline-block;
        }

        /* Contenteditable Placeholder */
        [contenteditable]:empty:before {
            content: attr(data-placeholder);
            color: #aaa;
            font-style: italic;
            pointer-events: none;
            display: block;
        }
    </style>

    <style>
        .hover-float {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .hover-float:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 10px 24px rgba(37, 121, 158, 0.22) !important;
        }

        .g-stat-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .g-stat-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.15) !important;
        }

        /* Hover borders for top cards */
        #cardDatabaseBaru {
            border: 3px solid #000 !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        #cardDatabaseBaru:hover {
            border: 3px solid #54b2d3 !important;
        }

        #cardTotalDatabase {
            border: 3px solid #000 !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        #cardTotalDatabase:hover {
            border: 3px solid #25799E !important;
        }

        #cardFiltersDatabase {
            border: 3px solid #000 !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        #cardFiltersDatabase:hover {
            border: 3px solid #25799E !important;
        }

        #cardJumlahPotensi {
            border: 3px solid #000 !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        #cardJumlahPotensi:hover {
            border: 3px solid #E0A800 !important;
        }

        /* Hover borders for bottom legend cards */
        #legendCold .legend-card-interactive {
            border: 3px solid #000 !important;
        }

        #legendCold .legend-card-interactive:hover {
            border: 3px solid #6c757d !important;
        }

        #legendTertarik .legend-card-interactive {
            border: 3px solid #000 !important;
        }

        #legendTertarik .legend-card-interactive:hover {
            border: 3px solid #e0b400 !important;
        }

        #legendMauTransfer .legend-card-interactive {
            border: 3px solid #000 !important;
        }

        #legendMauTransfer .legend-card-interactive:hover {
            border: 3px solid #28a745 !important;
        }

        #legendSudahTransfer .legend-card-interactive {
            border: 3px solid #000 !important;
        }

        #legendSudahTransfer .legend-card-interactive:hover {
            border: 3px solid #007bff !important;
        }

        #legendNo .legend-card-interactive {
            border: 3px solid #000 !important;
        }

        #legendNo .legend-card-interactive:hover {
            border: 3px solid #dc3545 !important;
        }

        #legendTotal .legend-card-interactive {
            border: 3px solid #000 !important;
        }

        #legendTotal .legend-card-interactive:hover {
            border: 3px solid #25799E !important;
        }

        .legend-card-interactive {
            cursor: pointer !important;
            transition: all 0.2s ease-in-out !important;
        }

        .legend-card-interactive:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 18px rgba(37, 121, 158, 0.2) !important;
        }

        .table-scroll-container {
            overflow-x: auto !important;
            width: 100%;
            position: relative;
        }

        #myTable thead th {
            position: sticky;
            top: 0px;
            /* Adjust since the topbar is NOT fixed */
            z-index: 1000;
            background-color: #25799E !important;
            color: white;
            border-bottom: 2px solid #fff !important;
        }

        /* Adjust for sub-header row if any */
        #myTable thead tr:nth-child(2) th {
            top: 65px;
            /* Fits better with dropdowns in first row */
        }

        #myTable,
        #myTable th,
        #myTable td {
            border: 2px solid #000 !important;
        }

        #myTable thead th {
            border-color: #fff !important;
            border-width: 2px !important;
        }

        .read-more-container {
            position: relative;
            max-height: 6em;
            /* Approx 4 lines */
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            line-height: 1.4;
        }

        .read-more-container:not(.expanded)::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2em;
            background: linear-gradient(transparent, rgba(255, 255, 255, 0.1));
            pointer-events: none;
        }

        .read-more-container.expanded {
            max-height: 2000px;
        }

        .btn-read-more {
            display: block;
            color: #0d6efd !important;
            cursor: pointer !important;
            font-size: 0.75rem;
            margin-top: 4px;
            font-weight: 700;
            text-decoration: underline !important;
            background: transparent;
            border: none;
            padding: 0;
        }

        .status-sudah_transfer .btn-read-more,
        .status-no .btn-read-more {
            color: #fff !important;
        }

        .btn-read-more:hover {
            color: #0056b3 !important;
            text-decoration: none !important;
        }

        .status-sudah_transfer .btn-read-more:hover,
        .status-no .btn-read-more:hover {
            color: #f8f9fa !important;
        }

        .text-wrap-normal {
            white-space: normal !important;
            word-break: break-word;
            min-width: 180px;
            vertical-align: top !important;
        }

        .editable {
            transition: background-color 0.2s ease;
            padding: 4px 6px;
            border-radius: 8px;
            border: 1px solid #000 !important;
            background-color: #fff !important;
            color: #000 !important;
        }

        .editable:hover {
            background-color: #f8f9fa !important;
            outline: 2px solid #007bff;
        }

        /* Style for selects in the table */
        #myTable select {
            border: 1px solid #000 !important;
            border-radius: 8px !important;
            background-color: #fff !important;
            color: #000 !important;
        }

        @if (strtolower(auth()->user()->role) === 'marketing')
            #myTable td {
                vertical-align: top;
                padding: 8px 6px;
                font-size: 0.95rem;
            }

            #myTable th {
                vertical-align: middle;
                text-align: center;
                text-transform: uppercase;
                font-size: 0.9rem;
                letter-spacing: 0.5px;
                padding: 10px 5px;
            }

            /* Make container fill more space */
            .container-fluid {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            .card {
                width: 100% !important;
            }

        @else
            #myTable td {
                vertical-align: top;
                padding: 4px 3px;
                font-size: 0.8rem;
            }

            #myTable th {
                vertical-align: middle;
                text-align: center;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0px;
                padding: 4px 2px;
            }

        @endif

        /* Floating Scrollbar Styles - Match Sales Plan */
        .floating-scroll-bar {
            position: fixed;
            bottom: 0;
            overflow-x: auto;
            z-index: 9999;
            background: #fff;
            border-top: 2px solid #25799E;
            height: 25px;
            display: none;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .floating-scroll-bar::-webkit-scrollbar {
            height: 10px;
        }

        .floating-scroll-bar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .floating-scroll-bar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .floating-scroll-bar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    <style>
        /* Precision Styling for Compact Columns */
        .col-bat {
            width: 35px !important;
            min-width: 35px !important;
            padding: 4px 0 !important;
            text-align: center !important;
        }

        .col-zoom {
            width: 85px !important;
            min-width: 85px !important;
            padding: 4px 2px !important;
        }

        .col-spin-header {
            width: 105px !important;
            min-width: 105px !important;
        }

        #myTable th.col-bat,
        #myTable td.col-bat {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .col-bat .custom-control.custom-checkbox,
        .col-zoom .custom-control.custom-checkbox {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-left: 0 !important;
            margin: 0 auto;
        }

        .col-bat .custom-control-label::before,
        .col-bat .custom-control-label::after,
        .col-zoom .custom-control-label::before,
        .col-zoom .custom-control-label::after {
            left: 50% !important;
            margin-left: -8px !important;
            /* Half of 1rem checkbox */
            top: 50% !important;
            margin-top: -8px !important;
        }
    </style>
    @php
        $user = auth()->user();
        $userRole = strtolower($user->role);
    @endphp

    @if (!in_array($userRole, ['administrator', 'operasional']))
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                @if ($userRole === 'chapter')
                    Database Calon Peserta M1T Chapter {{ $user->chapter }}
                @else
                    Database Calon Peserta{{ $userRole === 'cs-mbc' ? '' : ' M1T' }}
                @endif
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-light shadow-sm rounded-pill px-4 mb-0" style="border: 1px solid #e3e6f0;">
                    <li class="breadcrumb-item small"><a href="{{ route('home') }}"
                            class="text-secondary text-decoration-none"><i class="fas fa-home me-1"></i> Home</a></li>
                    <li class="breadcrumb-item active small text-primary fw-bold" aria-current="page">Database Calon
                        Peserta{{ $userRole === 'cs-mbc' ? '' : ' M1T' }}</li>
                </ol>
            </nav>
        </div>
    @endif



    </div>
    </form>

    {{-- ALERT MODE READ ONLY (ADMIN) --}}
    @if (isset($user) && ($readonly ?? false))
        <div class="alert alert-info d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
            <div>
                <strong>Database CS:</strong> <strong>{{ $user->name }} </strong> <br>
                <span class="text-muted small">Email: {{ $user->email }} | Role: {{ ucfirst($user->role) }}</span>
            </div>
            <div>
                <span class="text-white badge bg-primary p-2">Mode Read-Only</span>
            </div>
        </div>

        @if (auth()->user()->name !== 'Agus Setyo')
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-comments me-2"></i> Komentar untuk {{ $user->name }}
                </div>
                <div class="card-body">
                    {{-- Form Kirim Komentar --}}
                    <form id="formKomentar" method="POST" action="{{ route('komentar.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <div class="input-group mb-3">
                            <input type="text" name="pesan" class="form-control"
                                placeholder="Tulis komentar untuk CS ini..." required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Kirim
                            </button>
                        </div>
                    </form>
                    @if (session('success'))
                        <script>
                            Swal.fire({
                                title: 'Berhasil!',
                                text: '{{ session('success') }}',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        </script>
                    @endif


                    <button class="btn btn-outline-secondary btn-sm mb-2" data-toggle="modal" data-target="#modalKomentar">
                        <i class="fas fa-history"></i> Lihat Riwayat Komentar
                    </button>

                    <div class="modal fade" id="modalKomentar" tabindex="-1" role="dialog"
                        aria-labelledby="modalKomentarLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title" id="modalKomentarLabel">
                                        <i class="fas fa-comments me-2"></i> Riwayat Komentar
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @foreach ($komentar as $msg)
                                        <div
                                            class="alert alert-light border d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong>{{ $msg->admin->name ?? 'Admin' }}</strong><br>
                                                <span class="text-dark">{{ $msg->pesan }}</span><br>
                                                <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                                            </div>
                                            <i class="fas fa-comment-dots text-warning"></i>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif





    <div class="content">
        <div class="card card-info card-outline">
            @php
                use Carbon\Carbon;
                use App\Models\Data;

                // $currentUser used in logic below
                $currentUser = auth()->user();

                // Ensure variables are defined if not passed (fallback for edge cases)
                $now = Carbon::now();
                if (!isset($bulanLabel)) {
                    $bulanLabel = $now->isoFormat('MMMM YYYY');
                }
                if (!isset($databaseBaru)) {
                    $databaseBaru = 0;
                }
                if (!isset($totalDatabase)) {
                    $totalDatabase = 0;
                }
                if (!isset($target)) {
                    $target = strtolower(auth()->user()->role) === 'administrator' ? 250 : 50;
                }
                if (!isset($kurang)) {
                    $kurang = 0;
                }
                if (!isset($data)) {
                    $data = collect([]);
                }

            @endphp



            <div class="card-header">
                {{-- Stats Cards Section (Moved to Top) --}}
                <style>
                    .stat-card-group {
                        display: flex;
                        gap: 15px;
                        flex-wrap: wrap;
                    }

                    .g-stat-card {
                        display: flex;
                        align-items: center;
                        padding: 10px 15px;
                        border-radius: 12px;
                        color: white;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        transition: all 0.3s ease;
                        min-width: 140px;
                        /* Slightly reduced */
                        position: relative;
                        overflow: hidden;
                        flex: 1;
                        /* Allow growing */
                    }

                    .g-stat-card:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
                    }

                    .g-stat-card::after {
                        content: '';
                        position: absolute;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        left: 0;
                        background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.2), transparent);
                        pointer-events: none;
                    }

                    /* Gradients */
                    .g-sc-cyan {
                        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
                    }

                    .g-sc-blue {
                        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
                    }

                    .g-sc-yellow {
                        background: linear-gradient(135deg, #ffca2c 0%, #ffc107 100%);
                        color: #212529;
                    }

                    .g-sc-red {
                        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
                    }

                    .g-sc-content {
                        display: flex;
                        flex-direction: column;
                        z-index: 1;
                    }

                    .g-sc-label {
                        font-size: 0.75rem;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        opacity: 0.9;
                        font-weight: 700;
                        margin-bottom: 2px;
                    }

                    .g-sc-value {
                        font-size: 1.25rem;
                        font-weight: 800;
                        line-height: 1.1;
                    }

                    .g-sc-sub {
                        font-size: 0.65rem;
                        opacity: 0.9;
                        margin-top: 2px;
                    }

                    .g-sc-icon {
                        margin-left: auto;
                        font-size: 1.8rem;
                        opacity: 0.3;
                        z-index: 1;
                        margin-bottom: -5px;
                    }
                </style>


                @if ($userRole === 'administrator')
                    <!-- Database View Type Tabs (Panels) -->
                    <div class="mb-4">
                        <ul class="nav nav-pills shadow-sm p-1 bg-white rounded-pill border" style="width: fit-content;"
                            id="databaseTabs">
                            <li class="nav-item">
                                <button
                                    class="nav-link rounded-pill px-4 fw-bold {{ request('view_type') !== 'chapter' ? 'active' : '' }}"
                                    id="tab-cs" onclick="updateFilter('view_type', 'cs')">
                                    <i class="fas fa-building me-2"></i> DATABASE CS HELAS
                                </button>
                            </li>
                            <li class="nav-item">
                                <button
                                    class="nav-link rounded-pill px-4 fw-bold {{ request('view_type') === 'chapter' ? 'active' : '' }}"
                                    id="tab-chapter" onclick="updateFilter('view_type', 'chapter')">
                                    <i class="fas fa-university me-2"></i> DATABASE CHAPTER
                                </button>
                            </li>
                        </ul>
                    </div>
                @elseif($userRole === 'operasional')
                    <div class="mb-4">
                        <span class="badge bg-primary text-white rounded-pill px-4 py-2 fw-bold shadow-sm"
                            style="font-size: 0.9rem; background: linear-gradient(135deg, #25799E 0%, #1d617e 100%) !important;">
                            <i class="fas fa-university me-2"></i> DATABASE CHAPTER
                        </span>
                    </div>
                @endif

                @if (
                    !in_array($userRole, ['chapter', 'reseller', 'operasional']) &&
                        !($userRole === 'administrator' && $viewType === 'chapter'))
                    @php
                        $today = \Carbon\Carbon::now()->startOfDay();
                        $user = auth()->user();
                        $csList = collect();
                        if (
                            in_array(strtolower($user->role), [
                                'administrator',
                                'manager',
                                'marketing',
                                'operasional',
                            ]) ||
                            $user->name === 'Agus Setyo' ||
                            $user->name === 'Linda'
                        ) {
                            $csList = \App\Models\User::whereIn('role', ['cs-mbc', 'cs-smi', 'customer_service'])
                                ->where('is_active', 1)
                                ->where('name', 'not like', '%umum%')
                                ->select('id', 'name')
                                ->orderBy('name')
                                ->get();
                            $chapterList = \App\Models\User::whereIn('role', ['chapter', 'reseller', 'agen'])
                                ->select('id', 'name', 'chapter', 'role')
                                ->orderBy('role')
                                ->orderBy('name')
                                ->get();
                        }
                    @endphp
                    @if ($userRole === 'cs-mbc' || $isAdminCSView)
                        <div class="mb-5 d-flex flex-column gap-4">
                            <!-- Row 1: Header Stats & Filters -->
                            <div class="d-flex align-items-stretch flex-wrap gap-4">
                                {{-- Card Database Baru --}}
                                <div class="bg-white shadow-sm hover-float text-center" id="cardDatabaseBaru"
                                    style="border-radius: 12px; overflow: hidden; min-width: 140px; background: linear-gradient(135deg, #1d617e 0%, #25799E 100%); cursor: pointer;"
                                    onclick="filterByLegendStatus('database_baru')">
                                    <div class="px-4 py-3">
                                        <div class="text-white fw-bold mb-0"
                                            style="font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.9;">DATABASE BARU
                                        </div>
                                        <div class="text-white-50" style="font-size: 0.65rem; margin-top: -2px;">
                                            {{ $bulanLabel }}</div>
                                        <div class="text-white fw-bold mt-2" style="font-size: 1.25rem;"><span
                                                id="statDatabaseBaru">{{ $databaseBaru }}</span> <small
                                                style="font-size: 0.8rem; opacity: 0.7;">dari {{ $target }}</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card Total Database --}}
                                <div class="bg-white shadow-sm hover-float px-4 py-3 text-center d-flex flex-column justify-content-center"
                                    id="cardTotalDatabase" style="border-radius: 12px; min-width: 140px; cursor: pointer;"
                                    onclick="filterByLegendStatus('total_database')">
                                    <div class="text-dark fw-bold mb-0"
                                        style="font-size: 0.75rem; letter-spacing: 0.5px;">TOTAL DATABASE</div>
                                    <div class="fw-bold mt-2" style="font-size: 1.4rem; color: #25799E;"
                                        id="statTotalDatabase">{{ $totalDatabase }}</div>
                                </div>


                                {{-- Filters & Search (Integrated Style) --}}
                                <div class="d-flex align-items-end flex-wrap gap-3 p-3 bg-white shadow-sm"
                                    id="cardFiltersDatabase" style="border-radius: 12px;">
                                    {{-- Status Ikut Kelas --}}
                                    <div class="flex-column d-flex" style="gap: 4px;" id="filterIkutKelasContainer">
                                        <label class="text-dark fw-bold mb-0 ml-1"
                                            style="font-size: 0.75rem; text-transform: uppercase;">Status Ikut
                                            Kelas</label>
                                        <select id="filterIkutKelas" class="form-select form-select-sm modern-select"
                                            style="min-width: 150px; height: 38px; color: #000; font-weight: 600;"
                                            onchange="toggleDaftarKelas(this.value)">
                                            <option value="">ALL Status</option>
                                            <option value="1" {{ request('ikut_kelas') == '1' ? 'selected' : '' }}>
                                                Sudah Ikut</option>
                                            <option value="0" {{ request('ikut_kelas') == '0' ? 'selected' : '' }}>
                                                Belum Ikut</option>
                                        </select>
                                    </div>

                                    {{-- Pilih Kelas --}}
                                    <div id="containerDaftarKelas"
                                        class="flex-column {{ request('ikut_kelas') === '1' || request('ikut_kelas') === '0' ? 'd-flex' : 'd-none' }}"
                                        style="gap: 4px;">
                                        <label class="text-dark fw-bold mb-0 ml-1"
                                            style="font-size: 0.75rem; text-transform: uppercase;">Pilih Kelas</label>
                                        <select id="filterDaftarKelas" class="form-select form-select-sm modern-select"
                                            style="min-width: 200px; height: 38px; color: #000; font-weight: 600;">
                                            <option value="">Pilih Kelas</option>
                                            @foreach ($kelas as $k)
                                                @php
                                                    try {
                                                        $isUpcoming =
                                                            $k->tanggal_selesai &&
                                                            \Carbon\Carbon::parse($k->tanggal_selesai)
                                                                ->startOfDay()
                                                                ->greaterThanOrEqualTo($today)
                                                                ? '1'
                                                                : '0';
                                                    } catch (\Exception $e) {
                                                        $isUpcoming = '0';
                                                    }
                                                @endphp
                                                <option value="{{ $k->id }}" data-upcoming="{{ $isUpcoming }}"
                                                    {{ request('daftar_kelas') == $k->id ? 'selected' : '' }}>
                                                    {{ str_contains($k->nama_kelas, 'Muslim Indonesia') ? 'M1T' : $k->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Tim CS Filter specifically for Administrator --}}
                                    @if ($isAdminCSView)
                                        <div class="flex-column d-flex" style="gap: 4px;" id="filterCSContainer">
                                            <label class="text-dark fw-bold mb-0 ml-1"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Tim CS</label>
                                            <select id="filterCS" class="form-select form-select-sm modern-select"
                                                style="min-width: 150px; height: 38px; color: #000; font-weight: 600;">
                                                <option value="">ALL Tim CS</option>
                                                @foreach ($csList as $cs)
                                                    <option value="{{ $cs->name }}"
                                                        {{ request('cs_name') == $cs->name ? 'selected' : '' }}>
                                                        {{ $cs->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    {{-- Search Group --}}
                                    <div class="modern-search-group" style="height: 38px;">
                                        <input type="text" id="tableSearch"
                                            class="form-control form-control-sm modern-search-input"
                                            style="width: 220px; height: 38px; font-size: 0.85rem; color: #000; font-weight: 500;"
                                            placeholder="Cari nama..." value="{{ request('search') }}">
                                        <button class="btn btn-primary btn-sm modern-search-btn" type="button"
                                            onclick="applyAllDatabaseFilters()"
                                            style="height: 38px; font-size: 0.85rem; font-weight: 800; background: #25799E; border: none; padding-left: 20px; padding-right: 20px;">
                                            <i class="fas fa-search me-2"></i> TAMPILKAN
                                        </button>
                                    </div>
                                </div>

                                {{-- Jumlah Potensi (Ditaruh di Sebelah Kanan) --}}
                                <div class="bg-white shadow-sm hover-float px-4 py-3 text-center d-flex flex-column justify-content-center"
                                    style="border-radius: 12px; cursor: pointer; min-width: 140px; {{ request('ikut_kelas') === '1' ? 'display: none !important;' : 'display: flex !important;' }}"
                                    id="cardJumlahPotensi" onclick="filterByLegendStatus('potensi')">
                                    <div class="text-dark fw-bold mb-0"
                                        style="font-size: 0.75rem; letter-spacing: 0.5px;">JUMLAH POTENSI</div>
                                    <div class="fw-bold mt-2" style="font-size: 1.4rem; color: #E0A800;"
                                        id="statJumlahPotensi">{{ $jumlahPotensi ?? 0 }}</div>
                                </div>
                            </div>

                            <!-- Row 2: Legend (Simplified) -->
                            <div class="d-flex align-items-center flex-wrap mt-3" style="gap: 28px;" id="legendRow">
                                @php $ikutKelasVal = request('ikut_kelas'); @endphp

                                {{-- COLD: shown only when filter = Belum Ikut (0) or empty --}}
                                <div class="d-flex flex-column align-items-center" style="gap: 8px;" id="legendCold"
                                    {{ $ikutKelasVal === '1' ? 'style="display:none!important;"' : '' }}>
                                    <div class="legend-card-interactive d-flex align-items-center bg-white shadow-sm border"
                                        style="border-radius: 50px; min-width: auto; justify-content: center; padding: 10px 20px;"
                                        onclick="filterByLegendStatus('cold')">
                                        <div
                                            style="width: 14px; height: 14px; background: #ffffff; border: 1px solid #aaa; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                        </div>
                                        <span class="fw-bold text-dark"
                                            style="font-size: 0.8rem; letter-spacing: 0.5px;">Cold</span>
                                    </div>
                                    <div class="fw-bold text-dark text-center" style="font-size: 1.2rem;"
                                        id="statCountCold">{{ $ikutKelasVal === '1' ? 0 : $countCold ?? 0 }}</div>
                                </div>

                                {{-- TERTARIK: hidden when filter = Sudah Ikut (1) --}}
                                <div class="d-flex flex-column align-items-center" style="gap: 8px;" id="legendTertarik"
                                    {{ $ikutKelasVal === '1' ? 'style="display:none!important;"' : '' }}>
                                    <div class="legend-card-interactive d-flex align-items-center bg-white shadow-sm border"
                                        style="border-radius: 50px; min-width: 155px; justify-content: center; padding: 10px 20px;"
                                        onclick="filterByLegendStatus('tertarik')">
                                        <div
                                            style="width: 14px; height: 14px; background: #F2F527; border: 1px solid #ccc; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                        </div>
                                        <span class="fw-bold text-dark"
                                            style="font-size: 0.8rem; letter-spacing: 0.5px;">TERTARIK</span>
                                    </div>
                                    <div class="fw-bold text-dark text-center" style="font-size: 1.2rem;"
                                        id="statCountTertarik">
                                        {{ $ikutKelasVal !== null && $ikutKelasVal !== '' ? $countTertarik : 0 }}</div>
                                </div>

                                {{-- MAU TRANSFER: hidden when filter = Sudah Ikut (1) --}}
                                <div class="d-flex flex-column align-items-center" style="gap: 8px;"
                                    id="legendMauTransfer"
                                    {{ $ikutKelasVal === '1' ? 'style="display:none!important;"' : '' }}>
                                    <div class="legend-card-interactive d-flex align-items-center bg-white shadow-sm border"
                                        style="border-radius: 50px; min-width: 170px; justify-content: center; padding: 10px 20px;"
                                        onclick="filterByLegendStatus('mau_transfer')">
                                        <div
                                            style="width: 14px; height: 14px; background: #3CDE1D; border: 1px solid #ccc; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                        </div>
                                        <span class="fw-bold text-dark"
                                            style="font-size: 0.8rem; letter-spacing: 0.5px;">MAU TRANSFER</span>
                                    </div>
                                    <div class="fw-bold text-dark text-center" style="font-size: 1.2rem;"
                                        id="statCountMauTransfer">
                                        {{ $ikutKelasVal !== null && $ikutKelasVal !== '' ? $countMauTransfer : 0 }}</div>
                                </div>

                                {{-- SUDAH TRANSFER: hidden when filter = Belum Ikut (0) --}}
                                <div class="d-flex flex-column align-items-center" style="gap: 8px;"
                                    id="legendSudahTransfer"
                                    {{ $ikutKelasVal === '0' ? 'style="display:none!important;"' : '' }}>
                                    <div class="legend-card-interactive d-flex align-items-center bg-white shadow-sm border"
                                        style="border-radius: 50px; min-width: 185px; justify-content: center; padding: 10px 20px;"
                                        onclick="filterByLegendStatus('sudah_transfer')">
                                        <div
                                            style="width: 14px; height: 14px; background: #1786E6; border: 1px solid #ccc; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                        </div>
                                        <span class="fw-bold text-dark"
                                            style="font-size: 0.8rem; letter-spacing: 0.5px;">SUDAH TRANSFER</span>
                                    </div>
                                    <div class="fw-bold text-dark text-center" style="font-size: 1.2rem;"
                                        id="statCountSudahTransfer">
                                        {{ $ikutKelasVal !== null && $ikutKelasVal !== '' ? $countSudahTransfer : 0 }}
                                    </div>
                                </div>

                                {{-- NO: hidden when filter = Sudah Ikut (1) --}}
                                <div class="d-flex flex-column align-items-center" style="gap: 8px;" id="legendNo"
                                    {{ $ikutKelasVal === '1' ? 'style="display:none!important;"' : '' }}>
                                    <div class="legend-card-interactive d-flex align-items-center bg-white shadow-sm border"
                                        style="border-radius: 50px; min-width: 120px; justify-content: center; padding: 10px 20px;"
                                        onclick="filterByLegendStatus('no')">
                                        <div
                                            style="width: 14px; height: 14px; background: #E61717; border: 1px solid #ccc; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                        </div>
                                        <span class="fw-bold text-dark"
                                            style="font-size: 0.8rem; letter-spacing: 0.5px;">NO</span>
                                    </div>
                                    <div class="fw-bold text-dark text-center" style="font-size: 1.2rem;"
                                        id="statCountNo">
                                        {{ $ikutKelasVal !== null && $ikutKelasVal !== '' ? $countNo : 0 }}</div>
                                </div>

                                {{-- TOTAL card: label changes based on filter --}}
                                <div class="d-flex flex-column align-items-center" style="gap: 8px;" id="legendTotal">
                                    <div class="legend-card-interactive d-flex align-items-center bg-dark shadow-sm"
                                        style="border-radius: 50px; min-width: 160px; justify-content: center; padding: 10px 24px;"
                                        onclick="filterByLegendStatus('all_total')">
                                        <span class="fw-bold text-white" style="font-size: 0.8rem; letter-spacing: 1.5px;"
                                            id="totalLegendLabel">{{ $ikutKelasVal === '1' ? 'TOTAL SUDAH IKUT' : 'TOTAL BELUM IKUT' }}</span>
                                    </div>
                                    <div class="fw-bold text-dark text-center" style="font-size: 1.2rem;"
                                        id="statTotalFiltered">
                                        {{ $ikutKelasVal !== null && $ikutKelasVal !== '' ? $totalFiltered : 0 }}</div>
                                </div>

                            </div>
                        </div>
                    @else
                        <div class="stat-card-group mb-4">
                            <!-- Database Baru -->
                            <div class="g-stat-card g-sc-cyan" id="cardDatabaseBaruOther" style="cursor: pointer;"
                                onclick="filterByLegendStatus('database_baru')">
                                <div class="g-sc-content">
                                    <span class="g-sc-label">Database Baru</span>
                                    <span class="g-sc-value" id="statDatabaseBaru">{{ $databaseBaru }}</span>
                                    <span class="g-sc-sub" id="statBulanLabel">{{ $bulanLabel }}</span>
                                </div>
                                <div class="g-sc-icon"><i class="fas fa-database"></i></div>
                            </div>

                            <!-- Total Database -->
                            <div class="g-stat-card g-sc-blue" id="cardTotalDatabaseOther" style="cursor: pointer;"
                                onclick="filterByLegendStatus('total_database')">
                                <div class="g-sc-content">
                                    <span class="g-sc-label">Total Database</span>
                                    <span class="g-sc-value" id="statTotalDatabase">{{ $totalDatabase }}</span>
                                </div>
                                <div class="g-sc-icon"><i class="fas fa-layer-group"></i></div>
                            </div>

                            <!-- Target -->
                            <div class="g-stat-card g-sc-yellow">
                                <div class="g-sc-content">
                                    <span class="g-sc-label">Target Bulanan</span>
                                    <span class="g-sc-value">{{ $target }}</span>
                                </div>
                                <div class="g-sc-icon"><i class="fas fa-bullseye"></i></div>
                            </div>

                            <!-- Jumlah Prospek -->
                            <div class="g-stat-card g-sc-red text-white" style="position: relative;">
                                <div class="g-sc-content">
                                    <span class="g-sc-label text-white d-flex align-items-center" style="gap: 10px;">
                                        Jumlah Prospek
                                        @php
                                            $upcomingKelas = isset($kelas)
                                                ? $kelas->filter(function ($k) use ($today) {
                                                    if (!$k->tanggal_selesai) {
                                                        return true;
                                                    }
                                                    try {
                                                        return \Carbon\Carbon::parse($k->tanggal_selesai)
                                                            ->startOfDay()
                                                            ->greaterThanOrEqualTo($today);
                                                    } catch (\Exception $e) {
                                                        return true;
                                                    }
                                                })
                                                : collect();
                                        @endphp
                                        <select id="filterCardProspek" class="form-control form-control-sm"
                                            style="font-size: 0.7rem; color: #000; width: 120px; height: 24px; padding: 2px 5px;"
                                            onchange="updateCardProspek()">
                                            <option value="all">Semua Kelas</option>
                                            @foreach ($upcomingKelas as $k)
                                                <option value="{{ $k->id }}"
                                                    {{ request('prospek_kelas_id') == $k->id ? 'selected' : '' }}>
                                                    {{ str_contains($k->nama_kelas, 'Muslim Indonesia') ? 'M1T' : $k->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </span>
                                    <span class="g-sc-value"
                                        id="statJumlahProspek">{{ isset($totalProspek) ? $totalProspek : 0 }}</span>
                                </div>
                                <div class="g-sc-icon text-white"><i class="fas fa-users"></i></div>
                            </div>
                        </div>

                        {{-- Legend Footer for Other Roles: Diletakkan di bawah stat cards --}}
                        <div class="mb-4 p-2 d-flex align-items-center justify-content-center flex-wrap"
                            style="background: #ffffff; border: 2px solid #000; border-radius: 12px; gap: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: fit-content;">
                            <span class="fw-bold text-dark text-uppercase mr-2"
                                style="font-size: 0.75rem; letter-spacing: 1px;">
                                <i class="fas fa-map mr-1"></i> Panduan Warna Status:
                            </span>
                            <div class="d-flex align-items-center bg-light px-3 py-1 shadow-sm"
                                style="border-radius: 50px; border: 1px solid #000;">
                                <div
                                    style="width: 12px; height: 12px; background: #F2F527; border: 1px solid #000; border-radius: 50%; margin-right: 8px;">
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 0.75rem;">TERTARIK</span>
                            </div>
                            <div class="d-flex align-items-center bg-light px-3 py-1 shadow-sm"
                                style="border-radius: 50px; border: 1px solid #000;">
                                <div
                                    style="width: 12px; height: 12px; background: #3CDE1D; border: 1px solid #000; border-radius: 50%; margin-right: 8px;">
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 0.75rem;">MAU TRANSFER</span>
                            </div>
                            <div class="d-flex align-items-center bg-light px-3 py-1 shadow-sm"
                                style="border-radius: 50px; border: 1px solid #000;">
                                <div
                                    style="width: 12px; height: 12px; background: #1786E6; border: 1px solid #000; border-radius: 50%; margin-right: 8px;">
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 0.75rem;">SUDAH TRANSFER</span>
                            </div>
                            <div class="d-flex align-items-center bg-light px-3 py-1 shadow-sm"
                                style="border-radius: 50px; border: 1px solid #000;">
                                <div
                                    style="width: 12px; height: 12px; background: #E61717; border: 1px solid #000; border-radius: 50%; margin-right: 8px;">
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 0.75rem;">NO</span>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Toolbar Actions Row --}}
                <div class="d-flex justify-content-start align-items-center flex-nowrap gap-3 overflow-x-auto pb-2">
                    <!-- Kiri: Tombol Tambah -->
                    <div class="d-flex align-items-center">
                        @if (
                            !in_array($userRole, ['administrator', 'manager', 'marketing', 'operasional']) &&
                                !(auth()->user()->name === 'Linda' && request('view') !== 'me'))
                            @php
                                $slugName =
                                    $userRole === 'chapter' && !empty(auth()->user()->chapter)
                                        ? 'chapter-' . strtolower(str_replace(' ', '-', auth()->user()->chapter))
                                        : (!empty(auth()->user()->username)
                                            ? auth()->user()->username
                                            : strtolower(str_replace(' ', '-', auth()->user()->name)));
                            @endphp
                            <a href="#" class="btn btn-success mr-2" id="btnAddRow" onclick="createNewRow(event)">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </a>
                            <a href="{{ route('form.m1t', $slugName) }}" class="btn btn-success mr-2" target="_blank"
                                style="background-color: #20c997; border-color: #20c997;">
                                <i class="fa-solid fa-link"></i> Tambah Via Link
                            </a>
                        @endif
                        <button type="button" id="btnInteraksi"
                            class="btn btn-primary d-flex align-items-center gap-2 px-3 shadow-sm rounded-pill {{ request('bulan') && request('tahun') ? '' : 'd-none' }}"
                            onclick="exportPdfInteraksi()"
                            style="background: linear-gradient(45deg, #1d4ed8, #2563eb); border: none; font-weight: 600;">
                            <i class="fas fa-file-pdf"></i> Follow Up
                        </button>

                        @if (
                            !(auth()->user()->role === 'operasional' && stripos(auth()->user()->name, 'Rafi') !== false) &&
                                !($userRole === 'administrator' && request('view_type') == 'chapter'))
                            <button type="button" id="btnLihatJadwalZoomHariIni"
                                class="btn btn-info d-flex align-items-center gap-2 px-3 shadow-sm rounded-pill ml-2"
                                style="background: linear-gradient(45deg, #0dcaf0, #0bacce); border: none; font-weight: 600; color: #fff;">
                                <i class="fas fa-calendar-day"></i> Lihat Jadwal Zoom
                            </button>
                        @endif

                        @if (in_array($userRole, ['chapter', 'reseller']) || (request('view_type') == 'chapter' && $userRole == 'administrator'))
                            <div class="ml-2 px-2 py-1 bg-light border rounded-pill shadow-sm d-flex align-items-center">
                                <i class="fas fa-database text-info mr-1"></i>
                                <span class="text-xs font-weight-bold text-gray-800">Total Database: <span
                                        class="text-primary">{{ number_format($data->count() ?? 0, 0, ',', '.') }}</span></span>
                            </div>
                        @endif
                    </div>

                    <!-- Kanan: Toolbar Filter & Search -->
                    <div class="d-flex align-items-center justify-content-start gap-2">
                        <style>
                            .modern-filter-container {
                                display: flex;
                                align-items: flex-end;
                                gap: 10px;
                                flex-wrap: nowrap;
                            }

                            .modern-select {
                                border-radius: 50px !important;
                                border: 1px solid #e0e0e0;
                                background-color: #fff;
                                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
                                font-size: 0.75rem;
                                padding: 2px 22px 2px 8px;
                                transition: all 0.2s ease;
                                cursor: pointer;
                                min-height: 30px;
                            }

                            .modern-select:hover {
                                border-color: #b0c4de;
                                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
                                transform: translateY(-1px);
                            }

                            .modern-select:focus {
                                border-color: #86b7fe;
                                box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
                                outline: 0;
                            }

                            .modern-search-group {
                                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
                                border-radius: 50px;
                                overflow: hidden;
                                display: flex;
                            }

                            .modern-search-input {
                                border: 1px solid #e0e0e0;
                                border-right: none;
                                padding-left: 20px;
                                font-size: 0.9rem;
                                border-top-left-radius: 50px;
                                border-bottom-left-radius: 50px;
                            }

                            .modern-search-input:focus {
                                box-shadow: none;
                                border-color: #e0e0e0;
                            }

                            .modern-search-btn {
                                border-radius: 0 50px 50px 0 !important;
                                padding-left: 20px;
                                padding-right: 20px;
                                font-weight: 600;
                            }
                        </style>

                        @if ($userRole !== 'cs-mbc' && !$isAdminCSView)
                            {{-- Toolbar Atas: Filter (Presisi & Berfungsi) --}}
                            <div class="w-100 mb-3 d-flex align-items-center justify-content-end">
                                <div class="d-flex align-items-end flex-wrap" style="gap: 10px;">
                                    {{-- Status Ikut Kelas --}}
                                    @if (!in_array($userRole, ['reseller', 'chapter']))
                                        <div class="flex-column"
                                            style="gap: 2px; display: {{ request('view_type') === 'chapter' ? 'none' : 'flex' }};"
                                            id="filterIkutKelasContainer">
                                            <label class="text-xs fw-bold mb-0 ml-2"
                                                style="font-size: 0.65rem; color: #555; text-transform: uppercase;">Status
                                                Ikut Kelas</label>
                                            <select id="filterIkutKelas" class="form-select form-select-sm modern-select"
                                                onchange="toggleDaftarKelas(this.value)">
                                                <option value="">ALL Status</option>
                                                <option value="1"
                                                    {{ request('ikut_kelas') == '1' ? 'selected' : '' }}>Sudah Ikut
                                                </option>
                                                <option value="0"
                                                    {{ request('ikut_kelas') == '0' ? 'selected' : '' }}>Belum Ikut
                                                </option>
                                            </select>
                                        </div>

                                        {{-- Filter Daftar Kelas (Dinamis) --}}
                                        <div id="containerDaftarKelas"
                                            class="flex-column {{ request('ikut_kelas') === '1' || request('ikut_kelas') === '0' ? 'd-flex' : 'd-none' }}"
                                            style="gap: 2px;">
                                            <label class="text-xs fw-bold mb-0 ml-2"
                                                style="font-size: 0.65rem; color: #555; text-transform: uppercase;">Pilih
                                                Kelas</label>
                                            <select id="filterDaftarKelas"
                                                class="form-select form-select-sm modern-select">
                                                <option value="">Pilih Kelas</option>
                                                @foreach ($kelas as $k)
                                                    @php
                                                        try {
                                                            // Upcoming = ends today or in the future
                                                            $isUpcoming =
                                                                $k->tanggal_selesai &&
                                                                \Carbon\Carbon::parse($k->tanggal_selesai)
                                                                    ->startOfDay()
                                                                    ->greaterThanOrEqualTo($today)
                                                                    ? '1'
                                                                    : '0';
                                                        } catch (\Exception $e) {
                                                            $isUpcoming = '0'; // Default to past if date is weird
                                                        }
                                                    @endphp
                                                    <option value="{{ $k->id }}"
                                                        data-upcoming="{{ $isUpcoming }}"
                                                        {{ request('daftar_kelas') == $k->id ? 'selected' : '' }}>
                                                        {{ str_contains($k->nama_kelas, 'Muslim Indonesia') ? 'M1T' : $k->nama_kelas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    {{-- Potensi --}}
                                    @if (!in_array($userRole, ['operasional', 'cs-mbc']) && request('view_type') !== 'chapter')
                                        <div class="flex-column" style="gap: 2px; display: flex;">
                                            <label class="text-xs fw-bold mb-0 ml-2"
                                                style="font-size: 0.65rem; color: #555; text-transform: uppercase;">Potensi
                                                Kelas Selanjutnya</label>
                                            <select id="filterPotensi" class="form-select form-select-sm modern-select"
                                                onchange="toggleFilterKelas(this.value)">
                                                <option value="">ALL Potensi</option>
                                                <option value="MBC"
                                                    {{ request('potensi') == 'MBC' ? 'selected' : '' }}>MBC</option>
                                                <option value="SMI"
                                                    {{ request('potensi') == 'SMI' ? 'selected' : '' }}>M1T (SMI)</option>
                                            </select>
                                        </div>
                                    @endif

                                    {{-- Filter Nama Kelas (Dinamis jika MBC) --}}
                                    @if (!in_array($userRole, ['operasional', 'cs-mbc']) && request('view_type') !== 'chapter')
                                        <div id="containerFilterKelas"
                                            class="flex-column {{ request('potensi') == 'MBC' ? 'd-flex' : 'd-none' }}"
                                            style="gap: 2px;">
                                            <label class="text-xs fw-bold mb-0 ml-2"
                                                style="font-size: 0.65rem; color: #555; text-transform: uppercase;">Kelas
                                                MBC</label>
                                            <select id="filterKelasId" class="form-select form-select-sm modern-select">
                                                <option value="">Pilih Kelas</option>
                                                @php $today = \Carbon\Carbon::today(); @endphp
                                                @foreach ($kelas as $k)
                                                    @php $tglSelesai = $k->tanggal_selesai ? \Carbon\Carbon::parse($k->tanggal_selesai) : null; @endphp
                                                    @if (!str_contains($k->nama_kelas, 'Muslim Indonesia') && ($tglSelesai && $tglSelesai->gte($today)))
                                                        <option value="{{ $k->id }}"
                                                            {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                                            {{ $k->nama_kelas }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    {{-- Follow Up --}}
                                    @if ($userRole !== 'cs-mbc')
                                        <div class="flex-column" style="gap: 2px; display: flex;">
                                            <label class="text-xs fw-bold mb-0 ml-2"
                                                style="font-size: 0.65rem; color: #555; text-transform: uppercase;">Status
                                                Follow Up</label>
                                            <select id="filterStatus" class="form-select form-select-sm modern-select">
                                                <option value="">ALL Status</option>
                                                <option value="cold"
                                                    {{ request('status') == 'cold' ? 'selected' : '' }}>Cold</option>
                                                <option value="tertarik"
                                                    {{ request('status') == 'tertarik' ? 'selected' : '' }}>Tertarik
                                                </option>
                                                <option value="mau_transfer"
                                                    {{ request('status') == 'mau_transfer' ? 'selected' : '' }}>Mau
                                                    Transfer</option>
                                                <option value="sudah_transfer"
                                                    {{ request('status') == 'sudah_transfer' ? 'selected' : '' }}>Sudah
                                                    Transfer</option>
                                                <option value="no" {{ request('status') == 'no' ? 'selected' : '' }}>
                                                    No</option>
                                            </select>
                                        </div>
                                    @endif

                                    {{-- CS / Chapter Filter --}}
                                    @if (in_array(strtolower($user->role), ['administrator', 'manager', 'marketing', 'operasional']) ||
                                            $user->name === 'Agus Setyo')
                                        <div class="flex-column"
                                            style="gap: 2px; display: {{ request('view_type') === 'chapter' ? 'flex' : 'none' }};"
                                            id="filterChapterContainer">
                                            <label class="text-xs fw-bold mb-0 ml-2"
                                                style="font-size: 0.65rem; color: #555; text-transform: uppercase;">Chapter</label>
                                            <select id="filterChapter" class="form-select form-select-sm modern-select">
                                                <option value="">ALL Chapter</option>
                                                @if (isset($chapterList))
                                                    @foreach ($chapterList as $ch)
                                                        <option value="{{ $ch->id }}"
                                                            {{ request('chapter_id') == $ch->id ? 'selected' : '' }}>
                                                            {{ strtoupper($ch->name) }}
                                                            {{ in_array(strtolower($ch->role), ['reseller', 'agen']) ? '(AGEN)' : '(CHAPTER)' }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="flex-column"
                                            style="gap: 2px; display: {{ request('view_type') === 'chapter' ? 'none' : 'flex' }};"
                                            id="filterCSContainer">
                                            <label class="text-xs fw-bold mb-0 ml-2"
                                                style="font-size: 0.65rem; color: #555; text-transform: uppercase;">Tim
                                                CS</label>
                                            <select id="filterCS" class="form-select form-select-sm modern-select">
                                                <option value="">ALL Tim CS</option>
                                                @foreach ($csList as $cs)
                                                    <option value="{{ $cs->name }}"
                                                        {{ request('cs_name') == $cs->name ? 'selected' : '' }}>
                                                        {{ $cs->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    {{-- Search Group --}}
                                    <div class="modern-search-group" style="height: 32px;">
                                        <input type="text" id="tableSearch"
                                            class="form-control form-control-sm modern-search-input"
                                            style="width: 150px; height: 32px; font-size: 0.75rem;" placeholder="Cari..."
                                            value="{{ request('search') }}">
                                        <button class="btn btn-primary btn-sm modern-search-btn" type="button"
                                            onclick="applyAllDatabaseFilters()"
                                            style="height: 32px; font-size: 0.75rem; font-weight: 700;">
                                            <i class="fas fa-search"></i> TAMPILKAN
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @php
                $userRole = strtolower(auth()->user()->role);
                $isAdmin = $userRole === 'administrator';
            @endphp

            <script>
                function filterByLegendStatus(status) {
                    var url = new URL(window.location.href);
                    var currentStatus = url.searchParams.get('status');

                    if (currentStatus === status) {
                        // Toggle off if already selected
                        updateFilters({
                            status: ''
                        });
                    } else {
                        updateFilters({
                            status: status
                        });
                    }
                }

                function applyLegendHighlight() {
                    var url = new URL(window.location.href);
                    var currentStatus = url.searchParams.get('status');

                    const cards = {
                        'cold': document.getElementById('legendCold'),
                        'tertarik': document.getElementById('legendTertarik'),
                        'mau_transfer': document.getElementById('legendMauTransfer'),
                        'sudah_transfer': document.getElementById('legendSudahTransfer'),
                        'no': document.getElementById('legendNo')
                    };

                    for (const [status, el] of Object.entries(cards)) {
                        if (el) {
                            const innerDiv = el.querySelector('.legend-card-interactive');
                            if (innerDiv) {
                                if (currentStatus === status) {
                                    innerDiv.style.setProperty('border', '2.5px solid #25799E', 'important');
                                    innerDiv.style.setProperty('box-shadow', '0 6px 18px rgba(37, 121, 158, 0.3)', 'important');
                                    innerDiv.style.setProperty('background-color', '#f4fafe', 'important');
                                    innerDiv.style.setProperty('transform', 'translateY(-2px)', 'important');
                                } else {
                                    innerDiv.style.setProperty('border', '1px solid #dee2e6', 'important');
                                    innerDiv.style.setProperty('box-shadow', '0 .125rem .25rem rgba(0,0,0,.075)', 'important');
                                    innerDiv.style.setProperty('background-color', '#ffffff', 'important');
                                    innerDiv.style.setProperty('transform', 'none', 'important');
                                }
                            }
                        }
                    }

                    // Highlight cardJumlahPotensi when currentStatus is 'potensi'
                    const potensiEl = document.getElementById('cardJumlahPotensi');
                    if (potensiEl) {
                        if (currentStatus === 'potensi') {
                            potensiEl.style.setProperty('background-color', '#fff9e6', 'important');
                            potensiEl.style.setProperty('border', '2px solid #E0A800', 'important');
                            potensiEl.style.setProperty('box-shadow',
                                'inset 0 0 10px rgba(224, 168, 0, 0.15), 0 4px 12px rgba(224, 168, 0, 0.2)', 'important');
                        } else {
                            potensiEl.style.setProperty('background-color', '#ffffff', 'important');
                            potensiEl.style.setProperty('border', '1px solid #dee2e6', 'important');
                            potensiEl.style.setProperty('box-shadow', 'none', 'important');
                        }
                    }

                    // Highlight cardDatabaseBaru when currentStatus is 'database_baru'
                    const dbBaruEl = document.getElementById('cardDatabaseBaru');
                    if (dbBaruEl) {
                        if (currentStatus === 'database_baru') {
                            dbBaruEl.style.setProperty('box-shadow',
                                'inset 0 0 15px rgba(0, 0, 0, 0.4), 0 4px 12px rgba(37, 121, 158, 0.3)', 'important');
                            dbBaruEl.style.setProperty('background', 'linear-gradient(135deg, #16495f 0%, #1c5b76 100%)',
                                'important');
                        } else {
                            dbBaruEl.style.setProperty('box-shadow', 'none', 'important');
                            dbBaruEl.style.setProperty('background', 'linear-gradient(135deg, #1d617e 0%, #25799E 100%)',
                                'important');
                        }
                    }

                    // Highlight cardDatabaseBaruOther when currentStatus is 'database_baru'
                    const dbBaruOtherEl = document.getElementById('cardDatabaseBaruOther');
                    if (dbBaruOtherEl) {
                        if (currentStatus === 'database_baru') {
                            dbBaruOtherEl.style.setProperty('border', '2.5px solid #0aa2c0', 'important');
                            dbBaruOtherEl.style.setProperty('box-shadow', '0 6px 18px rgba(10, 162, 192, 0.4)', 'important');
                        } else {
                            dbBaruOtherEl.style.setProperty('border', 'none', 'important');
                            dbBaruOtherEl.style.setProperty('box-shadow', 'none', 'important');
                        }
                    }
                    // Highlight cardTotalDatabase when currentStatus is 'total_database'
                    const totalDbEl = document.getElementById('cardTotalDatabase');
                    if (totalDbEl) {
                        if (currentStatus === 'total_database') {
                            totalDbEl.style.setProperty('background-color', '#eaf2f8', 'important');
                            totalDbEl.style.setProperty('border', '2px solid #25799E', 'important');
                            totalDbEl.style.setProperty('box-shadow',
                                'inset 0 0 10px rgba(37, 121, 158, 0.1), 0 4px 12px rgba(37, 121, 158, 0.2)', 'important');
                        } else {
                            totalDbEl.style.setProperty('background-color', '#ffffff', 'important');
                            totalDbEl.style.setProperty('border', '1px solid #dee2e6', 'important');
                            totalDbEl.style.setProperty('box-shadow', 'none', 'important');
                        }
                    }

                    // Highlight cardTotalDatabaseOther when currentStatus is 'total_database'
                    const totalDbOtherEl = document.getElementById('cardTotalDatabaseOther');
                    if (totalDbOtherEl) {
                        if (currentStatus === 'total_database') {
                            totalDbOtherEl.style.setProperty('border', '2.5px solid #0056b3', 'important');
                            totalDbOtherEl.style.setProperty('box-shadow', '0 6px 18px rgba(0, 86, 179, 0.4)', 'important');
                        } else {
                            totalDbOtherEl.style.setProperty('border', 'none', 'important');
                            totalDbOtherEl.style.setProperty('box-shadow', 'none', 'important');
                        }
                    }
                }

                function updateFilter(key, val) {
                    var params = {};
                    params[key] = val;
                    updateFilters(params);
                }

                function updateFilters(params) {
                    var url = new URL(window.location.href);
                    for (const [key, val] of Object.entries(params)) {
                        if (val || val === '0') {
                            url.searchParams.set(key, val);
                        } else {
                            url.searchParams.delete(key);
                        }

                        if (key === 'ikut_kelas' && val === '') {
                            url.searchParams.delete('daftar_kelas');
                        }
                    }

                    if (params.view_type !== undefined) {
                        window.location.href = url.toString();
                        return;
                    }

                    url.searchParams.delete('page');

                    loadDataAjax(url);
                }

                function loadDataAjax(url) {
                    // Show loading state
                    var tableBody = document.getElementById('tableBody');
                    tableBody.style.opacity = '0.4';
                    tableBody.style.pointerEvents = 'none';

                    fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.html !== undefined) {
                                tableBody.innerHTML = data.html;
                            } else {
                                console.error('Data.html is undefined!', data);
                                tableBody.innerHTML =
                                    '<tr><td colspan="20" class="text-center text-danger">Gagal memuat data (Response Error)</td></tr>';
                            }
                            tableBody.style.opacity = '1';
                            tableBody.style.pointerEvents = 'auto';

                            // Update pagination
                            if (data.pagination !== undefined) {
                                document.getElementById('paginationContainer').innerHTML = data.pagination;
                            }

                            if (data.stats) {
                                if (document.getElementById('statDatabaseBaru')) document.getElementById('statDatabaseBaru')
                                    .innerText = data.stats.databaseBaru;
                                if (document.getElementById('statTotalDatabase')) document.getElementById('statTotalDatabase')
                                    .innerText = data.stats.totalDatabase;
                                if (document.getElementById('statJumlahPotensi')) document.getElementById('statJumlahPotensi')
                                    .innerText = data.stats.jumlahPotensi !== undefined ? data.stats.jumlahPotensi : 0;
                                var cardJumlahPotensi = document.getElementById('cardJumlahPotensi');
                                if (cardJumlahPotensi) {
                                    var urlParams = new URLSearchParams(url.search);
                                    var ikutKelas = urlParams.get('ikut_kelas');
                                    if (ikutKelas === '1') {
                                        cardJumlahPotensi.style.setProperty('display', 'none', 'important');
                                    } else {
                                        cardJumlahPotensi.style.setProperty('display', 'flex', 'important');
                                    }
                                }
                                if (document.getElementById('statBulanLabel')) document.getElementById('statBulanLabel')
                                    .innerText = data.stats.bulanLabel;

                                // CS-MBC Specific Stats
                                if (document.getElementById('statTotalFiltered')) document.getElementById('statTotalFiltered')
                                    .innerText = data.stats.totalFiltered;

                                var totalLegendLabel = document.getElementById('totalLegendLabel');
                                if (totalLegendLabel) {
                                    var urlParams = new URLSearchParams(url.search);
                                    var ikutKelas = urlParams.get('ikut_kelas');
                                    if (ikutKelas === '1') {
                                        totalLegendLabel.innerText = 'TOTAL SUDAH IKUT';
                                    } else {
                                        totalLegendLabel.innerText = 'TOTAL BELUM IKUT';
                                    }
                                }
                                if (document.getElementById('statCountCold')) document.getElementById('statCountCold')
                                    .innerText = data.stats.countCold;
                                if (document.getElementById('statCountTertarik')) document.getElementById('statCountTertarik')
                                    .innerText = data.stats.countTertarik;
                                if (document.getElementById('statCountMauTransfer')) document.getElementById(
                                    'statCountMauTransfer').innerText = data.stats.countMauTransfer;
                                if (document.getElementById('statCountSudahTransfer')) document.getElementById(
                                    'statCountSudahTransfer').innerText = data.stats.countSudahTransfer;
                                if (document.getElementById('statCountNo')) document.getElementById('statCountNo').innerText =
                                    data.stats.countNo;

                                // Update prospek summary card
                                if (data.stats.prospekCounts !== undefined) {
                                    window.globalProspekCounts = data.stats.prospekCounts;
                                    window.globalTotalProspek = data.stats.totalProspek;
                                    refreshCardProspekStats();
                                }
                            }

                            window.history.pushState({}, '', url.toString());
                            applyLegendHighlight();

                            // Update active tab visuals
                            const params = new URLSearchParams(url.search);
                            const currentViewType = params.get('view_type') || 'cs';
                            const tabCs = document.getElementById('tab-cs');
                            const tabChapter = document.getElementById('tab-chapter');

                            if (tabCs && tabChapter) {
                                if (currentViewType === 'chapter') {
                                    tabChapter.classList.add('active');
                                    tabCs.classList.remove('active');

                                    // Update filter visibility
                                    if (document.getElementById('filterIkutKelasContainer')) document.getElementById(
                                        'filterIkutKelasContainer').style.setProperty('display', 'none', 'important');
                                    if (document.getElementById('filterCSContainer')) document.getElementById(
                                        'filterCSContainer').style.setProperty('display', 'none', 'important');
                                    if (document.getElementById('filterChapterContainer')) document.getElementById(
                                        'filterChapterContainer').style.setProperty('display', 'flex', 'important');
                                } else {
                                    tabCs.classList.add('active');
                                    tabChapter.classList.remove('active');

                                    // Update filter visibility
                                    if (document.getElementById('filterIkutKelasContainer')) document.getElementById(
                                        'filterIkutKelasContainer').style.setProperty('display', 'flex', 'important');
                                    if (document.getElementById('filterCSContainer')) document.getElementById(
                                        'filterCSContainer').style.setProperty('display', 'flex', 'important');
                                    if (document.getElementById('filterChapterContainer')) document.getElementById(
                                        'filterChapterContainer').style.setProperty('display', 'none', 'important');
                                }
                            }

                            // Scroll to top of table
                            document.getElementById('tableContainer').scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching filtered data:', error);
                            tableBody.style.opacity = '1';
                            tableBody.style.pointerEvents = 'auto';
                        });
                }

                function updateFilter(key, val) {
                    let obj = {};
                    obj[key] = val;
                    updateFilters(obj);
                }

                function applyAllDatabaseFilters() {
                    const params = {
                        search: document.getElementById('tableSearch').value,
                        sumber: document.getElementById('filterSumber').value,
                        provinsi: document.getElementById('filterProvinsi').value,
                        kota: document.getElementById('filterKota').value,
                        status: document.getElementById('filterStatus') ? document.getElementById('filterStatus').value : (
                            new URLSearchParams(window.location.search).get('status') || ''),
                        potensi: document.getElementById('filterPotensi') ? document.getElementById('filterPotensi').value : '',
                        kelas_id: document.getElementById('filterKelasId') ? document.getElementById('filterKelasId').value : ''
                    };

                    // Add optional filters if they exist in DOM
                    const filterIkut = document.getElementById('filterIkutKelas');
                    if (filterIkut) params.ikut_kelas = filterIkut.value;

                    const filterDaftar = document.getElementById('filterDaftarKelas');
                    if (filterDaftar) params.daftar_kelas = filterDaftar.value;

                    const filterCardProspek = document.getElementById('filterCardProspek');
                    if (filterCardProspek && filterCardProspek.value !== 'all') {
                        params.prospek_kelas_id = filterCardProspek.value;
                    }

                    const filterCS = document.getElementById('filterCS');
                    if (filterCS) params.cs_name = filterCS.value;

                    const filterChapter = document.getElementById('filterChapter');
                    if (filterChapter) params.chapter_id = filterChapter.value;

                    updateFilters(params);
                }

                function toggleFilterKelas(val) {
                    const container = document.getElementById('containerFilterKelas');
                    if (!container) return;

                    if (val === 'MBC') {
                        container.classList.remove('d-none');
                        container.classList.add('d-flex');
                    } else {
                        container.classList.add('d-none');
                        container.classList.remove('d-flex');
                        const select = document.getElementById('filterKelasId');
                        if (select) select.value = '';
                    }
                }

                function updatePotensiHeader(val) {
                    let kelasFilter = document.getElementById('filterPotensiKelas');
                    if (val === 'MBC') {
                        if (kelasFilter) kelasFilter.classList.remove('d-none');
                    } else {
                        if (kelasFilter) {
                            kelasFilter.classList.add('d-none');
                            kelasFilter.value = '';
                        }
                    }

                    // If we just want to show the dropdown without immediate filtering, we can stop here.
                    // But usually header filters trigger immediate action.
                    updateFilters({
                        potensi: val,
                        potensi_kelas_id: val === 'MBC' ? (kelasFilter ? kelasFilter.value : '') : ''
                    });
                }

                function toggleDaftarKelas(val) {
                    let container = document.getElementById('containerDaftarKelas');
                    let select = document.getElementById('filterDaftarKelas');
                    if (!container || !select) return;

                    // Initialize the backup of options if it doesn't exist
                    if (!window.daftarKelasOptions) {
                        window.daftarKelasOptions = Array.from(select.querySelectorAll('option[data-upcoming]')).map(opt => ({
                            value: opt.value,
                            text: opt.text,
                            upcoming: opt.getAttribute('data-upcoming')
                        }));
                    }

                    if (val === '1' || val === '0') {
                        container.classList.remove('d-none');
                        container.classList.add('d-flex');

                        let showUpcoming = val === '0' ? '1' : '0';
                        let currentValue = select.value;

                        // Clear options but keep the default
                        select.innerHTML = '<option value="">Pilih Kelas</option>';

                        let hasSelected = false;
                        window.daftarKelasOptions.forEach(opt => {
                            if (opt.upcoming === showUpcoming) {
                                let newOpt = document.createElement('option');
                                newOpt.value = opt.value;
                                newOpt.text = opt.text;
                                if (opt.value === currentValue) {
                                    newOpt.selected = true;
                                    hasSelected = true;
                                }
                                select.appendChild(newOpt);
                            }
                        });

                        if (!hasSelected) {
                            select.value = '';
                        }

                        // Auto-trigger only if a class is already selected
                        if (select.value !== '') {
                            applyAllDatabaseFilters();
                        }
                    } else {
                        // ALL Status: hide class dropdown and reset value
                        container.classList.add('d-none');
                        container.classList.remove('d-flex');
                        select.value = '';

                        // Auto-trigger filter to refresh table (it will now show all data but 0 counts)
                        applyAllDatabaseFilters();
                    }
                }

                // Initial call to set correct options if loaded with a value
                document.addEventListener('DOMContentLoaded', function() {
                    let filterIkutKelas = document.getElementById('filterIkutKelas');

                    if (filterIkutKelas && filterIkutKelas.value !== '') {
                        toggleDaftarKelas(filterIkutKelas.value);
                        // Reselect if it was cleared
                        let select = document.getElementById('filterDaftarKelas');
                        let urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('daftar_kelas') && select) {
                            select.value = urlParams.get('daftar_kelas');
                        }
                    }

                    // Highlight selected legend status card on initial load
                    applyLegendHighlight();
                });

                // Intercept pagination link clicks for AJAX navigation
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('tableSearch');
                    if (searchInput) {
                        searchInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                applyAllDatabaseFilters();
                            }
                        });
                    }
                });

                document.addEventListener('click', function(e) {
                    var link = e.target.closest('#paginationContainer a');
                    if (link && link.href) {
                        e.preventDefault();
                        var url = new URL(link.href);
                        loadDataAjax(url);
                    }
                });
            </script>

            <div class="card-body position-relative">
                <div id="tableContainer" class="table-scroll-container" style="overflow-x: auto; width: 100%;">

                    <table id="myTable"
                        class="table table-bordered table-striped {{ strtolower(auth()->user()->role) === 'marketing' ? '' : 'nowrap' }}"
                        style="width: {{ in_array(strtolower(auth()->user()->role), ['marketing', 'cs-mbc']) ? '100%' : 'max-content' }};">
                        <thead>
                            <tr>
                                <th>No</th>

                                <th style="min-width: 150px;">Nama & No.WA</th>
                                <th style="width: 92.5px;">
                                    Sumber Leads <br>
                                    <select id="filterSumber" class="form-control form-control-sm"
                                        style="font-size: 0.75rem;" onchange="applyAllDatabaseFilters()">
                                        <option value="">-- Semua --</option>
                                        <option value="Ads" {{ request('sumber') == 'Ads' ? 'selected' : '' }}>Ads
                                        </option>
                                        <option value="Sosmed" {{ request('sumber') == 'Sosmed' ? 'selected' : '' }}>
                                            Sosmed</option>
                                        <option value="Zoom" {{ request('sumber') == 'Zoom' ? 'selected' : '' }}>Zoom
                                        </option>
                                        <option value="Open House"
                                            {{ request('sumber') == 'Open House' ? 'selected' : '' }}>Open House</option>
                                        <option value="Mandiri" {{ request('sumber') == 'Mandiri' ? 'selected' : '' }}>
                                            Mandiri</option>
                                        @if (!in_array($userRole, ['reseller', 'agen']))
                                            <option value="Alumni" {{ request('sumber') == 'Alumni' ? 'selected' : '' }}>
                                                Alumni</option>
                                        @endif
                                    </select>
                                </th>
                                <th style="width: 140px;">
                                    Prov/Kota <br>
                                    <div class="d-flex flex-column gap-1">
                                        <select id="filterProvinsi" class="form-control form-control-sm mb-1"
                                            style="font-size: 0.7rem; height: auto; padding: 2px;">
                                            <option value="">-- Prov --</option>
                                            @if (isset($provinsiList))
                                                @foreach ($provinsiList as $prov)
                                                    <option value="{{ $prov }}"
                                                        {{ request('provinsi') == $prov ? 'selected' : '' }}>
                                                        {{ $prov }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <select id="filterKota" class="form-control form-control-sm"
                                            style="font-size: 0.7rem; height: auto; padding: 2px;">
                                            <option value="">-- Kota --</option>
                                            @if (isset($kotaList))
                                                @foreach ($kotaList as $kota)
                                                    <option value="{{ $kota }}"
                                                        {{ request('kota') == $kota ? 'selected' : '' }}>
                                                        {{ $kota }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </th>
                                <th style="width: 140px;">Nama Bisnis</th>

                                @php
                                    // Use globally defined variables from top of file
                                @endphp

                                {{-- Header for Chapter/Reseller or Admin in Chapter Tab --}}
                                @if ($isChapterView)
                                    <th style="width: 220px;">Situasi Bisnis</th>
                                    <th style="width: 120px; text-align:center;">Rekap Penilaian</th>
                                    <th style="width: 110px; text-align:center;">Prospek</th>
                                    <th style="width: 160px; text-align:center;">Status Potensi</th>
                                    @if(stripos(auth()->user()->chapter ?? '', 'depok') !== false || (in_array(strtolower(auth()->user()->role), ['administrator', 'operasional']) && request('view_type') === 'chapter'))
                                        <th style="width: 120px; text-align:center;">Bukti Transfer</th>
                                    @endif
                                    @if (in_array($userRole, ['administrator', 'operasional']))
                                        <th style="width: 120px; text-align:center;">PIC</th>
                                    @endif

                                    {{-- Header for Admin in CS Helas Tab --}}
                                @elseif($isAdminCSView)
                                    <th style="width: 220px;">Situasi Bisnis</th>
                                    <th style="width: 150px; text-align:center;">Potensi Ikut Kelas</th>
                                    <th style="width: 120px; text-align:center;">CS PIC</th>

                                    {{-- Header for CS-MBC role --}}
                                @elseif($isCSMBCView)
                                    <th style="width: 220px;">Situasi Bisnis</th>
                                    <th style="width: 150px; text-align:center;">Potensi Ikut Kelas</th>
                                    {{-- <th style="min-width: 140px; text-align:center;">✅ Kelas yang Sudah Diikuti</th> --}}
                                    {{-- <th style="min-width: 140px; text-align:center;">🔔 Kelas yang Belum Diikuti</th> --}}
                                    <th style="width: 80px; text-align:center;">Action</th>

                                    {{-- Default / Marketing --}}
                                @else
                                    <th style="width: 80px; text-align:center;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="tableBody">

                            @foreach ($data as $item)
                                @include('admin.database.partials.row', [
                                    'item' => $item,
                                    'loop' => $loop,
                                    'kelas' => $kelas,
                                ])
                            @endforeach


                        </tbody>
                    </table>



                    <script>
                        // Horizontal Floating Scroll Logic (Hanging Scroll) - Ported from Sales Plan
                        $(document).ready(function() {
                            function initFloatingScroll() {
                                const tableContainer = document.getElementById('tableContainer');
                                if (!tableContainer) return;

                                // Remove existing if any
                                $('.floating-scroll-bar').remove();

                                // Create scrollbar element
                                const floatingScroll = document.createElement('div');
                                floatingScroll.className = 'floating-scroll-bar';

                                const inner = document.createElement('div');
                                inner.style.height = '1px';
                                floatingScroll.appendChild(inner);
                                document.body.appendChild(floatingScroll);

                                const syncScroll = () => {
                                    if (!tableContainer.isConnected) return;

                                    inner.style.width = tableContainer.scrollWidth + 'px';
                                    const rect = tableContainer.getBoundingClientRect();

                                    // Show if the table's own scrollbar is below the screen and top is visible
                                    const isVisible = rect.top < window.innerHeight && rect.bottom > window.innerHeight;
                                    const hasScroll = tableContainer.scrollWidth > tableContainer.clientWidth;

                                    floatingScroll.style.display = (isVisible && hasScroll) ? 'block' : 'none';

                                    // Sync position and width
                                    floatingScroll.style.left = rect.left + 'px';
                                    floatingScroll.style.width = rect.width + 'px';

                                    // Sync scroll position with debounce
                                    if (Math.abs(floatingScroll.scrollLeft - tableContainer.scrollLeft) > 1) {
                                        floatingScroll.scrollLeft = tableContainer.scrollLeft;
                                    }
                                };

                                let isSyncing = false;
                                floatingScroll.onscroll = () => {
                                    if (isSyncing) return;
                                    isSyncing = true;
                                    tableContainer.scrollLeft = floatingScroll.scrollLeft;
                                    requestAnimationFrame(() => {
                                        isSyncing = false;
                                    });
                                };

                                tableContainer.onscroll = () => {
                                    if (isSyncing) return;
                                    isSyncing = true;
                                    floatingScroll.scrollLeft = tableContainer.scrollLeft;
                                    requestAnimationFrame(() => {
                                        isSyncing = false;
                                    });
                                };

                                window.addEventListener('scroll', syncScroll, {
                                    passive: true
                                });
                                window.addEventListener('resize', syncScroll, {
                                    passive: true
                                });

                                setTimeout(syncScroll, 100);
                            }

                            // Initial Init
                            initFloatingScroll();

                            // Re-init after AJAX
                            $(document).ajaxSuccess(function() {
                                setTimeout(initFloatingScroll, 500);
                            });
                        });
                    </script>

                    <!-- Script FIlter -->
                    <script>
                        $(document).ready(function() {
                            $('#filterLeads, #filterProvinsi, #filterKota, #filterJenisBisnis, #filterInputOleh').on('change',
                                function() {
                                    let filters = {
                                        leads: $('#filterLeads').val(),
                                        provinsi: $('#filterProvinsi').val(),
                                        kota: $('#filterKota').val(),
                                        jenisbisnis: $('#filterJenisBisnis').val(),
                                        created_by: $('#filterInputOleh').val(),
                                    };

                                    $.ajax({
                                        url: "{{ route('admin.database.filter') }}",
                                        type: "GET",
                                        data: filters,
                                        success: function(response) {
                                            $('#tableData').html(response);
                                        },
                                        // error: function() {
                                        //     alert('Gagal memuat data filter');
                                        // }
                                    });
                                });
                        });
                    </script>


                    <!-- Script JQuery -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                        $(document).ready(function() {

                            // Untuk kolom text
                            // Konsolidasi handler untuk kolom text (.editable)
                            $(document).on('focus', '.editable', function() {
                                $(this).addClass('editing');
                            });

                            $(document).on('blur', '.editable', function() {
                                let $this = $(this);
                                let value = $this.text();
                                let field = $this.data('field');
                                let id = $this.closest('tr').data('id');

                                $this.removeClass('editing');

                                $.ajax({
                                    url: "{{ url('admin/database/update-inline') }}",
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id: id,
                                        field: field,
                                        value: value
                                    },
                                    success: function(res) {
                                        console.log('Updated:', field);
                                        showStatusIcon($this, true);
                                    },
                                    error: function(xhr) {
                                        console.error('Failed to update:', field, xhr);
                                        showStatusIcon($this, false);

                                        let msg = 'Gagal menyimpan perubahan.';
                                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON
                                            .message;

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Update Gagal',
                                            text: msg
                                        });
                                    }
                                });
                            });

                            // Konsolidasi handler untuk Potensi Kelas
                            $(document).on('change', '.select-potensi', function() {
                                let $this = $(this);
                                let id = $this.data('id');
                                let kelas_id = $this.val();

                                $.ajax({
                                    url: "{{ url('admin/database/update-potensi') }}/" + id,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        kelas_id: kelas_id
                                    },
                                    success: function(response) {
                                        console.log('Potensi kelas updated');
                                        showStatusIcon($this, true);
                                    },
                                    error: function() {
                                        console.log('Failed to update potensi kelas');
                                        showStatusIcon($this, false);
                                    }
                                });
                            });

                        });
                    </script>

                    <script>
                        // Delegated event for Potensi MBC/M1T Select
                        $(document).on('change', '.select-potensi-mbc-m1t', function() {
                            let $this = $(this);
                            let id = $this.data('id');
                            let value = $this.val();
                            let $kelasContainer = $this.siblings('.potensi-kelas-container');

                            // Update colors dynamically
                            if (value === 'MBC') {
                                $this.css({
                                    'background-color': '#8b0000',
                                    'color': '#fff'
                                });
                                $kelasContainer.removeClass('d-none');
                            } else if (value === 'SMI') {
                                $this.css({
                                    'background-color': '#28a745',
                                    'color': '#fff'
                                });
                                $kelasContainer.addClass('d-none');
                            } else {
                                $this.css({
                                    'background-color': '#fff',
                                    'color': 'inherit'
                                });
                                $kelasContainer.addClass('d-none');
                            }

                            let updateData = {
                                _token: '{{ csrf_token() }}',
                                id: id,
                                field: 'potensi',
                                value: value
                            };

                            // If SMI, we also want to set the default M1T class ID
                            if (value === 'SMI') {
                                @php
                                    $m1tClass = \App\Models\Kelas::where('nama_kelas', 'like', '%Muslim Indonesia%')->first();
                                    $m1tId = $m1tClass ? $m1tClass->id : null;
                                @endphp
                                @if ($m1tId)
                                    updateData.updates = {
                                        potensi: value,
                                        kelas_id: {{ $m1tId }}
                                    };
                                    delete updateData.field;
                                    delete updateData.value;
                                @endif
                            }

                            $.ajax({
                                url: '/admin/database/update-inline',
                                method: 'POST',
                                data: updateData,
                                success: function(res) {
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true
                                    });
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Potensi Updated'
                                    });
                                }
                            });
                        });

                        // Delegated event for Secondary Kelas Select
                        $(document).on('change', '.select-potensi-kelas', function() {
                            let $this = $(this);
                            let id = $this.data('id');
                            let value = $this.val();

                            $.ajax({
                                url: '/admin/database/update-inline',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    id: id,
                                    field: 'kelas_id',
                                    value: value
                                },
                                success: function(res) {
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true
                                    });
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Kelas Updated'
                                    });
                                }
                            });
                        });

                        // Delegated event for Checkboxes (Spin, Zoom, BANT components)
                        $(document).on('change', '.check-spin, .check-zoom, .check-bant-budget, .check-bant-authority, .check-bant-time',
                            function() {
                                let $this = $(this);
                                let id = $this.data('id');
                                let field = '';

                                if ($this.hasClass('check-spin')) field = 'berhasil_spin';
                                else if ($this.hasClass('check-zoom')) field = 'ikut_zoom';
                                else if ($this.hasClass('check-bant-budget')) field = 'bant_budget';
                                else if ($this.hasClass('check-bant-authority')) field = 'bant_authority';
                                else if ($this.hasClass('check-bant-time')) field = 'bant_time';

                                let value = $this.is(':checked') ? 1 : 0;
                                $this.prop('disabled', true);

                                $.ajax({
                                    url: '/admin/database/update-inline',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id: id,
                                        field: field,
                                        value: value
                                    },
                                    complete: function() {
                                        $this.prop('disabled', false);
                                    },
                                    success: function(res) {
                                        console.log('Updated checkbox:', field);

                                        // Toggle visibility of Potensi & SalesPlan columns based on any SPIN checkbox
                                        let $row = $this.closest('tr');
                                        let isBudget = $row.find('.check-bant-budget').is(':checked');
                                        let isAuthority = $row.find('.check-bant-authority').is(':checked');
                                        let isTime = $row.find('.check-bant-time').is(':checked');

                                        if (isBudget && isAuthority && isTime) {
                                            $row.find('.spin-content').removeClass('d-none');
                                        } else {
                                            $row.find('.spin-content').addClass('d-none');
                                        }

                                        // Show Toast Success
                                        const Toast = Swal.mixin({
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                        Toast.fire({
                                            icon: 'success',
                                            title: 'Status Updated'
                                        });
                                    },
                                    error: function(xhr) {
                                        console.error('Error updating checkbox:', xhr);
                                        let msg = 'Gagal update status.';
                                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Update Gagal',
                                            text: msg
                                        });
                                        // Revert checkbox state
                                        $this.prop('checked', !$this.is(':checked'));
                                    }
                                });
                            });

                        // Toggle Read More
                        $(document).on('click', '.btn-read-more', function(e) {
                            e.preventDefault();
                            const $this = $(this);
                            const $container = $this.siblings('.read-more-container');

                            if ($container.hasClass('expanded')) {
                                $container.removeClass('expanded');
                                $this.text('Baca Selengkapnya');
                            } else {
                                $container.addClass('expanded');
                                $this.text('Tutup');
                            }
                        });

                        function createNewRow(e) {
                            if (e) e.preventDefault();

                            $.ajax({
                                url: '{{ route('admin.database.createDraft') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        // Prepend to tbody
                                        $('#myTable tbody').prepend(response.html);

                                        let $newRow = $('#myTable tbody tr:first');

                                        // Populate Provinces for the new row
                                        if (window.populateProvinceRow) {
                                            window.populateProvinceRow($newRow);
                                        }

                                        // Optional: Highlight row or focus name
                                        $newRow.css('background-color', '#d4edda').animate({
                                            backgroundColor: '#fff'
                                        }, 2000);
                                    }
                                },
                                error: function(xhr) {
                                    let msg = 'Gagal menambah baris baru.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        msg += '\n' + xhr.responseJSON.message;
                                    }
                                    alert(msg);
                                }
                            });
                        }

                        // Toggle No Potential
                        $(document).on('click', '.btn-no-potensi', function() {
                            let id = $(this).data('id');
                            let $row = $(this).closest('tr');

                            Swal.fire({
                                title: 'Tandai Tidak Potensi?',
                                text: "Data akan ditandai merah dan dipindah ke urutan paling belakang.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#e74a3b',
                                cancelButtonColor: '#858796',
                                confirmButtonText: 'Ya, Tandai!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: "{{ url('admin/database') }}/" + id + "/toggle-no-potensi",
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}'
                                        },
                                        success: function(res) {
                                            if (res.success) {
                                                Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                                    // Reload page to apply new sorting
                                                    location.reload();
                                                });
                                            }
                                        },
                                        error: function() {
                                            Swal.fire('Gagal!', 'Terjadi kesalahan saat memperbarui status.',
                                                'error');
                                        }
                                    });
                                }
                            });
                        });
                    </script>
                    <style>
                        .editable {
                            cursor: pointer;
                        }

                        .editing {
                            background-color: #fff3cd !important;
                            /* kuning saat edit */
                        }

                        .status-icon {
                            margin-left: 5px;
                            font-size: 14px;
                        }

                        .status-success {
                            color: green;
                        }

                        .status-error {
                            color: red;
                        }
                    </style>

                    <script>
                        $(document).ready(function() {

                            // Handler sudah dikonsolidasi di atas (line 642)

                            // Untuk dropdown Potensi Kelas
                            $('.select-potensi').on('change', function() {
                                let $this = $(this);
                                let id = $this.data('id');
                                let kelas_id = $this.val();
                                let iconSpan = $this.next('.status-icon');

                                $.ajax({
                                    url: `/admin/database/update-potensi/${id}`,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        kelas_id: kelas_id
                                    },
                                    success: function() {
                                        iconSpan.html('<i class="fa fa-check status-success"></i>');
                                        setTimeout(() => iconSpan.html(''), 2000);
                                    },
                                    error: function() {
                                        iconSpan.html('<i class="fa fa-times status-error"></i>');
                                        setTimeout(() => iconSpan.html(''), 2000);
                                    }
                                });
                            });

                            // Fungsi tampil icon centang atau silang
                            function showStatusIcon($element, success) {
                                let iconHtml = success ?
                                    '<i class="fa fa-check status-success"></i>' :
                                    '<i class="fa fa-times status-error"></i>';

                                let iconSpan = $('<span class="status-icon">' + iconHtml + '</span>');
                                $element.after(iconSpan);

                                setTimeout(() => {
                                    iconSpan.fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                }, 2000);
                            }

                        });
                    </script>



                </div>

                <!-- Pagination -->
                <div id="paginationContainer" class="d-flex justify-content-center mt-4">
                    {{ $data->withQueryString()->links('pagination::bootstrap-4') }}
                </div>

            </div>


        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('tableSearch')?.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    updateFilter('search', this.value);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Global variables to cache default province list
            let cachedProvinces = [];

            // Helper: Populate specific select elements
            function populateProvinceSelect($elements) {
                if (cachedProvinces.length === 0) return;

                $elements.each(function() {
                    let $select = $(this);
                    // check if already populated to avoid potential overwrite issues if logic changes
                    if ($select.children('option').length > 1) return;

                    let currentNama = $select.data('nama');

                    // Keep existing "Pilih" if exists
                    let $default = $select.find('option:first');
                    $select.empty().append($default);

                    cachedProvinces.forEach(function(prov) {
                        let isSelected = (currentNama && currentNama.toUpperCase() === prov.name
                            .toUpperCase()) ? 'selected' : '';
                        $select.append(
                            `<option value="${prov.id}" data-name="${prov.name}" ${isSelected}>${prov.name}</option>`
                        );
                    });
                });
            }

            // 1. Fetch Provinces & Populate
            $.getJSON('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json', function(provinces) {
                // Sort: Alphabetical
                provinces.sort((a, b) => a.name.localeCompare(b.name));
                cachedProvinces = provinces;

                // Populate existing rows
                populateProvinceSelect($('.select-provinsi'));

                // Also populate Header Filter
                let $filterProv = $('#filterProvinsi');
                cachedProvinces.forEach(function(prov) {
                    // Avoid duplicate append if run multiple times
                    if ($filterProv.find(`option[value="${prov.name}"]`).length === 0) {
                        $filterProv.append(
                            `<option value="${prov.name}" data-id="${prov.id}">${prov.name}</option>`
                        );
                    }
                });
            });

            // Expose populate function purely for local usage pattern if needed, 
            // but better to attach a listener or just call it from createNewRow.

            // We attach it to window so createNewRow can access it if defined outside (though it is defined outside doc.ready)
            window.populateProvinceRow = function($row) {
                if (cachedProvinces.length > 0) {
                    populateProvinceSelect($row.find('.select-provinsi'));
                } else {
                    // retry if not yet loaded? usually loaded by the time user clicks add
                }
            };

            // 2. Change Province -> Find Cities & Save
            $(document).on('change', '.select-provinsi', function() {
                let $select = $(this);
                let id = $select.data('id');
                let provId = $select.val();
                let provName = $select.find(':selected').data('name');

                let $kotaSelect = $select.closest('tr').find('.select-kota');

                // Save to DB
                if (provId) {
                    $.post('/admin/database/update-location', {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        provinsi_id: provId,
                        provinsi_nama: provName
                    }).done(function() {
                        console.log('Provinsi saved');
                    });

                    // Load Cities
                    loadCities(provId, $kotaSelect);
                } else {
                    $kotaSelect.empty().append('<option value="">-- Pilih Kota --</option>');
                }
            });

            // 3. Change City -> Save
            $(document).on('change', '.select-kota', function() {
                let $select = $(this);
                let id = $select.data('id');
                let kotaId = $select.val();
                let kotaName = $select.find(':selected').data('name');

                if (kotaId) {
                    $.post('/admin/database/update-location', {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        kota_id: kotaId,
                        kota_nama: kotaName
                    }).done(function() {
                        console.log('Kota saved');
                    });
                }
            });

            // 3a. Change Potensi -> Save
            $(document).on('change', '.select-potensi', function() {
                let $select = $(this);
                let id = $select.data('id');
                let val = $select.val();

                // Update Class for Colors
                $select.removeClass('bg-success bg-danger bg-light text-white text-dark text-muted');
                if (val === 'SMI') $select.addClass('bg-success text-dark');
                else if (val === 'MBC') $select.addClass('bg-danger text-white');
                else $select.addClass('bg-light text-muted');

                $.post('/admin/database/update-inline', {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    updates: {
                        potensi: val
                    }
                }).done(function() {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Potensi berhasil diperbarui'
                    });
                });
            });

            // 4. Lazy Load Cities on Click (if not populated)
            $(document).on('click', '.select-kota', function() {
                let $kotaSelect = $(this);
                // Only load if we haven't loaded options yet (length <= 1 means only default option)
                // And ensure we have a province selected
                if ($kotaSelect.children('option').length <= 1) {
                    let $provSelect = $kotaSelect.closest('tr').find('.select-provinsi');
                    let provId = $provSelect.val();

                    if (provId) {
                        loadCities(provId, $kotaSelect);
                    } else {
                        // Try to resolve province ID from its text if user hasn't touched it? 
                        // Difficult because we haven't mapped ID to the initial text unless content matched.
                        if ($provSelect.find('option:selected').val()) {
                            loadCities($provSelect.find('option:selected').val(), $kotaSelect);
                        }
                    }
                }
            });

            function loadCities(provId, $targetSelect) {
                $targetSelect.empty().append('<option value="">Loading...</option>');

                $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provId}.json`, function(
                    cities) {
                    cities.sort((a, b) => a.name.localeCompare(b.name));

                    $targetSelect.empty().append('<option value="">-- Pilih Kota --</option>');

                    let currentKota = $targetSelect.data('nama');

                    cities.forEach(function(city) {
                        let isSelected = (currentKota && currentKota.toUpperCase() === city.name
                            .toUpperCase()) ? 'selected' : '';
                        $targetSelect.append(
                            `<option value="${city.id}" data-name="${city.name}" ${isSelected}>${city.name}</option>`
                        );
                    });
                });
            }


            // Note: older applyFilters function defined in document.ready above might conflict if not careful.
            // We are overriding or extending functionality. The previous script block used "applyFilters" name. 
            // Since we are inside the same doc.ready (effectively), we should be careful. 
            // To be safe, we'll assume the previous separate scripts might need consolidation, 
            // but typically later script specific listeners will run.
            // We explicitly attach applyTableFilters to the new inputs.
        });
    </script>
    @if (auth()->user()->role === 'administrator')
        <!-- Modal Detail Bisnis & Situasi -->
        <div class="modal fade" id="modalBisnis" tabindex="-1" role="dialog" aria-labelledby="modalBisnisLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="modal-header bg-info text-white" style="border-radius: 15px 15px 0 0;">
                        <h5 class="modal-title fw-bold" id="modalBisnisLabel"><i class="fas fa-business-time me-2"></i>
                            Bisnis &
                            Situasi: <span class="detailNama"></span></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="bg-light p-3 rounded shadow-sm border">
                            <label class="text-muted text-uppercase fw-bold mb-1 d-block"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">Nama Bisnis</label>
                            <h6 class="fw-bold mb-1 detailNamaBisnis" style="color: #1e293b;"></h6>
                            <p class="text-info small mb-0 detailJenisBisnis"></p>

                            <hr class="my-3" style="border-top: 1px dashed #cbd5e1;">

                            <label class="text-muted text-uppercase fw-bold mb-1 d-block"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">Situasi Bisnis</label>
                            <p class="mb-0 text-dark" style="white-space: pre-wrap; line-height: 1.6; font-size: 0.9rem;"
                                id="detailSituasiBisnis"></p>
                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary fw-bold px-4 rounded-pill"
                            data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail Kendala -->
        <div class="modal fade" id="modalKendala" tabindex="-1" role="dialog" aria-labelledby="modalKendalaLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="modal-header bg-info text-white" style="border-radius: 15px 15px 0 0;">
                        <h5 class="modal-title fw-bold" id="modalKendalaLabel"><i
                                class="fas fa-exclamation-circle me-2"></i>
                            Kendala: <span class="detailNama"></span></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="bg-light p-3 rounded shadow-sm border border-info" style="min-height: 120px;">
                            <p class="mb-0 text-dark" style="white-space: pre-wrap;" id="detailKendalaContent"></p>
                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary fw-bold px-4 rounded-pill"
                            data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).on('click', '.btn-view-bisnis', function() {
                const data = $(this).data();
                $('.detailNama').text(data.nama);
                $('.detailNamaBisnis').text(data.bisnis || '-');
                $('.detailJenisBisnis').text(data.jenis || '-');
                $('#detailSituasiBisnis').text(data.situasi || '-');
                $('#modalBisnis').modal('show');
            });

            $(document).on('click', '.btn-view-kendala', function() {
                const data = $(this).data();
                $('.detailNama').text(data.nama);
                $('#detailKendalaContent').text(data.kendala || '-');
                $('#modalKendala').modal('show');
            });
        </script>
    @endif

    {{-- MODAL MOVE TO SALES PLAN --}}
    <div class="modal fade" id="modalMoveSalesPlan" tabindex="-1" role="dialog"
        aria-labelledby="modalMoveSalesPlanLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalMoveSalesPlanLabel">
                        @if (in_array($userRole, ['reseller', 'chapter']))
                            Pindahkan ke Prospek
                        @else
                            Masukkan ke Sales Plan
                        @endif
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formMoveSalesPlan" method="POST" action="">
                    @csrf
                    <div class="modal-body">
                        <p>Anda akan memindahkan <strong id="modalNamaPeserta"></strong> ke @if (in_array($userRole, ['reseller', 'chapter']))
                                Prospek
                            @else
                                Sales Plan
                            @endif.
                            @if (!in_array($userRole, ['reseller', 'chapter']))
                                Silakan pilih <span id="textPotensi">Potensi Kelas Pertama</span>:
                            @endif
                        </p>
                        <div class="form-group">
                            <label class="font-weight-bold">Pilih Kelas :</label>
                            <div id="checkbox-kelas-container" class="border rounded p-3 bg-white shadow-sm text-left"
                                style="max-height: 250px; overflow-y: auto; border: 1px solid #e3e6f0 !important;">
                                @foreach ($kelas as $k)
                                    @php
                                        $isM1T = str_contains($k->nama_kelas, 'Muslim Indonesia');
                                        $showCheckbox = true;
                                        if (in_array($userRole, ['reseller', 'chapter']) && !$isM1T) {
                                            $showCheckbox = false;
                                        }
                                    @endphp
                                    @if ($showCheckbox)
                                        <div class="custom-control custom-checkbox mb-2 ml-1">
                                            <input type="checkbox" name="kelas_id[]" value="{{ $k->id }}"
                                                class="custom-control-input checkbox-kelas-item"
                                                id="kelas_{{ $k->id }}">
                                            <label class="custom-control-label fw-bold text-dark"
                                                for="kelas_{{ $k->id }}"
                                                style="cursor: pointer; font-size: 0.9rem;">{{ $isM1T ? 'M1T' : $k->nama_kelas }}</label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div id="joinedClassesContainer" class="mt-2">
                            <!-- Badges will be inserted here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            @if (in_array($userRole, ['reseller', 'chapter']))
                                Pindahkan ke Prospek
                            @else
                                Masukkan ke Salesplan
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).on('click', '.btn-trigger-salesplan', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let kelasId = $(this).data('kelas');
            let isSalesplan = $(this).data('is-salesplan');
            let joinedClasses = $(this).data('joined-classes');

            let url = `/admin/database/${id}/tambah-salesplan`;

            $('#modalMoveSalesPlan').modal('show');
            $('#modalNamaPeserta').text(nama);
            $('#formMoveSalesPlan').attr('action', url);
            $('#formMoveSalesPlan select[name="kelas_id"]').val(kelasId);

            // Update text based on isSalesplan
            if (isSalesplan == '1') {
                $('#textPotensi').text('Potensi Kelas Selanjutnya');
            } else {
                $('#textPotensi').text('Potensi Kelas Pertama');
            }

            // Reset all checkboxes first
            $('.checkbox-kelas-item').prop('checked', false);

            // Check the current class if it exists
            if (kelasId) {
                $(`#kelas_${kelasId}`).prop('checked', true);
            }

            // Display joined classes badges
            let container = $('#joinedClassesContainer');
            container.empty();
            if (joinedClasses) {
                container.append(
                    '<label class="d-block mb-1" style="font-size: 0.8rem; font-weight: 600; color: #666;">Sudah Ikut:</label>'
                );
                let classes = joinedClasses.split(',');
                classes.forEach(function(className) {
                    container.append(
                        `<span class="badge bg-success text-white mr-1 mb-1 shadow-sm" style="padding: 5px 8px;">${className}</span>`
                    );
                });
            }
        });
    </script>

    <!-- Modal Riwayat FU -->
    <div class="modal fade" id="modalRiwayat" tabindex="-1" role="dialog" aria-labelledby="modalRiwayatLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalRiwayatLabel">
                        <i class="fas fa-history me-2"></i> Riwayat Follow Up - <span id="namaPeserta"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-2 bg-light text-dark">
                    <input type="hidden" id="riwayat_data_id">
                    <input type="hidden" id="riwayat_salesplan_id">
                    <div class="d-flex flex-wrap pb-2" id="fuCardsContainer">
                        @for ($i = 1; $i <= 10; $i++)
                            <div class="px-1 mb-3 fu-card-container d-none" id="fu_card_{{ $i }}"
                                style="width: 20%; flex: 0 0 20%;" data-index="{{ $i }}">
                                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                                    <div class="bg-warning text-dark fw-bold px-2 py-1 d-flex justify-content-between align-items-center"
                                        style="font-size: 0.75rem;">
                                        <span class="text-uppercase" style="font-weight: 900 !important;">Follow Up
                                            {{ $i }}</span>
                                        <input type="text" class="fu-at-input border-0 rounded px-1 text-center"
                                            id="fu{{ $i }}_at"
                                            {{ auth()->user()->role === 'administrator' ? 'readonly' : '' }}
                                            style="font-size: 0.6rem; width: 85px; height: 18px; outline: none; background: rgba(255,255,255,0.8);"
                                            placeholder="-">
                                    </div>
                                    <div class="card-body p-2 bg-white">
                                        <div class="row no-gutters text-center mb-2 border rounded bg-light overflow-hidden"
                                            style="margin-left: -2px; margin-right: -2px;">
                                            <div class="col-6 py-1 border-right">
                                                <div class="fw-bold text-uppercase text-dark"
                                                    style="font-size: 0.6rem; letter-spacing: 0.5px; font-weight: 800 !important;">
                                                    WA</div>
                                                <input type="checkbox" id="fu{{ $i }}_wa"
                                                    class="fu-checkbox"
                                                    {{ auth()->user()->role === 'administrator' ? 'disabled' : '' }}
                                                    style="transform: scale(0.95);">
                                            </div>
                                            <div class="col-6 py-1">
                                                <div class="fw-bold text-uppercase text-dark"
                                                    style="font-size: 0.6rem; letter-spacing: 0.5px; font-weight: 800 !important;">
                                                    TELP</div>
                                                <input type="checkbox" id="fu{{ $i }}_telp"
                                                    class="fu-checkbox"
                                                    {{ auth()->user()->role === 'administrator' ? 'disabled' : '' }}
                                                    style="transform: scale(0.95);">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="fu{{ $i }}_hasil"
                                                class="fw-bold text-dark text-uppercase mb-0"
                                                style="font-size: 0.6rem; display: block; font-weight: 800 !important;">Hasil
                                                FU</label>
                                            <textarea class="form-control form-control-sm border bg-white shadow-sm p-1" id="fu{{ $i }}_hasil"
                                                rows="5" {{ auth()->user()->role === 'administrator' ? 'readonly' : '' }}
                                                style="font-size: 0.8rem; border-radius: 4px; resize: none; line-height: 1.2; font-weight: 700 !important; border-color: #dee2e6 !important;"
                                                placeholder="Hasil..."></textarea>
                                        </div>
                                        <div class="border-top border-secondary opacity-25 my-2"
                                            style="border-style: dashed !important;"></div>
                                        <div>
                                            <label for="fu{{ $i }}_tindak_lanjut"
                                                class="fw-bold text-dark text-uppercase mb-0"
                                                style="font-size: 0.6rem; display: block; font-weight: 800 !important;">Tindak
                                                Lanjut</label>
                                            <textarea class="form-control form-control-sm border bg-white shadow-sm p-1"
                                                id="fu{{ $i }}_tindak_lanjut" rows="4"
                                                {{ auth()->user()->role === 'administrator' ? 'readonly' : '' }}
                                                style="font-size: 0.8rem; border-radius: 4px; resize: none; line-height: 1.2; font-weight: 700 !important; border-color: #dee2e6 !important;"
                                                placeholder="Next..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor

                        @if (auth()->user()->role !== 'administrator')
                            <!-- Tombol Tambah SPIN -->
                            <div class="px-1 mb-3 d-flex align-items-center justify-content-center"
                                style="width: 20%; flex: 0 0 20%;">
                                <button type="button"
                                    class="btn btn-outline-primary border-dashed rounded-3 shadow-sm d-flex flex-column align-items-center justify-content-center p-3"
                                    id="btnTambahSpin"
                                    style="height: 100%; border: 2px dashed #4e73df; background: #f8f9fc; transition: all 0.3s; width: 100%; min-height: 200px;">
                                    <i class="fas fa-plus-circle fa-2x mb-2 text-primary"></i>
                                    <span class="fw-bold text-primary" style="font-size: 0.85rem;">Tambah Follow Up</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4 btn-sm" data-dismiss="modal">Tutup</button>
                    @if (auth()->user()->role !== 'administrator')
                        <button type="button" class="btn btn-primary px-4 btn-sm shadow-sm" id="btnSimpanRiwayat">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#modalRiwayat').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });
            $('#modalZoomBant').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });
        });

        $(document).on('click', '.btn-zoom-bant', function() {
            let $btn = $(this);
            let id = $btn.data('id');
            let nama = $btn.data('nama');
            let kelasNama = $btn.attr('data-kelas-nama') || $btn.data('kelas-nama') || '';
            let noWa = $btn.data('no-wa') || $btn.closest('tr').find('[data-field="no_wa"]').text().trim() || '-';
            let canEdit = $btn.data('can-edit') == 1;

            $('#zoomBant_data_id').val(id);
            $('#zoomBantNama').text(nama);
            $('#zoomBantNoWa').text(noWa);

            if (kelasNama) {
                let displayKelas = kelasNama;
                if (kelasNama.toLowerCase().includes('muslim indonesia')) {
                    displayKelas = 'M1T';
                }
                $('#zoomBantKelas').text(displayKelas).show();
                $('#zoomBantKelasPrefix').show();
            } else {
                $('#zoomBantKelas').text('').hide();
                $('#zoomBantKelasPrefix').hide();
            }

            // Detect if M1T / Startup Muslim Indonesia
            let isM1T = kelasNama.toLowerCase().includes('m1t') || kelasNama.toLowerCase().includes('muslim');
            if (isM1T) {
                // Wide Modal layout for M1T (Two Columns)
                $('#modalZoomBantDialog').css('max-width', '800px');
                $('#zoomBantZoomCol').removeClass('col-12').addClass('col-md-6 pr-md-3');
                $('#zoomBantBantCol').removeClass('col-12').addClass('col-md-6 pl-md-3');
                $('#bantHeader').removeClass('mt-4').addClass('mt-md-0');

                $('#standardZoomSection').hide();
                $('#m1tZoomSection').show();

                // Populate M1T form
                let scheduleDateRaw = $btn.attr('data-schedule-date') || '';
                if (scheduleDateRaw) {
                    let datePart = scheduleDateRaw.substring(0, 10);
                    let timePart = scheduleDateRaw.substring(11, 16);

                    $('#zoomM1tDate').val(datePart);
                    if (timePart.startsWith('09') || timePart.startsWith('9')) {
                        $('#zoomM1tSession').val('09:00');
                    } else if (timePart.startsWith('11')) {
                        $('#zoomM1tSession').val('11:00');
                    } else if (timePart.startsWith('13') || timePart.startsWith('1')) {
                        $('#zoomM1tSession').val('13:00');
                    } else if (timePart.startsWith('15') || timePart.startsWith('3')) {
                        $('#zoomM1tSession').val('15:00');
                    } else {
                        $('#zoomM1tSession').val('');
                    }
                } else {
                    $('#zoomM1tDate').val('');
                    $('#zoomM1tSession').val('');
                }

                $('#zoomM1tSalesplanId').val($btn.data('salesplan-id') || '');
                $('#zoomM1tDateTime').val(scheduleDateRaw);
                $('#zoomM1tLink').val($btn.attr('data-schedule-link') || '');
                $('#zoomM1tStatus').val($btn.attr('data-schedule-status') || 'scheduled');
                $('#zoomM1tNotes').val($btn.attr('data-schedule-notes') || '');
            } else {
                // Standard narrow Modal layout (Stacked)
                $('#modalZoomBantDialog').css('max-width', '420px');
                $('#zoomBantZoomCol').removeClass('col-md-6 pr-md-3').addClass('col-12');
                $('#zoomBantBantCol').removeClass('col-md-6 pl-md-3').addClass('col-12');
                $('#bantHeader').addClass('mt-4').removeClass('mt-md-0');

                $('#standardZoomSection').show();
                $('#m1tZoomSection').hide();
            }

            // Populate checkboxes
            document.getElementById('zoomBantIkutZoom').checked = $btn.attr('data-ikut-zoom') == '1';
            document.getElementById('zoomBantBudget').checked = $btn.attr('data-bant-budget') == '1';
            document.getElementById('zoomBantAuthority').checked = $btn.attr('data-bant-authority') == '1';
            document.getElementById('zoomBantTime').checked = $btn.attr('data-bant-time') == '1';

            // Enable/disable based on permissions
            document.getElementById('zoomBantIkutZoom').disabled = !canEdit;
            document.getElementById('zoomBantBudget').disabled = !canEdit;
            document.getElementById('zoomBantAuthority').disabled = !canEdit;
            document.getElementById('zoomBantTime').disabled = !canEdit;

            $('#modalZoomBant').modal('show');
        });

        // AJAX handler for saving One-on-One schedule
        $(document).on('click', '#btnSaveZoomM1t', function() {
            let dataId = $('#zoomBant_data_id').val();
            let salesplanId = $('#zoomM1tSalesplanId').val();

            let dateVal = $('#zoomM1tDate').val();
            let sessionVal = $('#zoomM1tSession').val();

            if (!dateVal) {
                alert('Silakan pilih Tanggal Sesi Zoom terlebih dahulu.');
                return;
            }
            if (!sessionVal) {
                alert('Silakan pilih Jam Sesi Zoom terlebih dahulu.');
                return;
            }

            let scheduledAt = dateVal + ' ' + sessionVal + ':00';
            let zoomLink = $('#zoomM1tLink').val() || '';
            let status = $('#zoomM1tStatus').val() || 'scheduled';
            let notes = $('#zoomM1tNotes').val();

            let $btn = $(this);
            $btn.prop('disabled', true).text('Menyimpan...');

            $.post('{{ route('zoom-schedule.store') }}', {
                _token: '{{ csrf_token() }}',
                data_id: dataId,
                salesplan_id: salesplanId,
                scheduled_at: scheduledAt,
                zoom_link: zoomLink,
                status: status,
                notes: notes
            }).done(function(r) {
                $btn.prop('disabled', false).text('Simpan Jadwal Zoom');
                if (r.success) {
                    showDetailToast('Jadwal Zoom berhasil disimpan!');
                    $('#modalZoomBant').modal('hide');

                    // Dynamic sync to trigger button
                    let $btnZoom = $(`.btn-zoom-bant[data-salesplan-id="${salesplanId}"]`);
                    if ($btnZoom.length) {
                        $btnZoom.attr('data-schedule-date', scheduledAt);
                        $btnZoom.attr('data-schedule-link', zoomLink);
                        $btnZoom.attr('data-schedule-status', status);
                        $btnZoom.attr('data-schedule-notes', notes);
                        if (status === 'done') {
                            $btnZoom.attr('data-ikut-zoom', '1');
                            $btnZoom.closest('tr').find('.checkbox-ikut-zoom').prop('checked', true);
                        }
                    }

                    // [USER_REQUEST] Dynamically update calendar events & jump to rescheduled date without full page reload
                    if (window.modalCalendar) {
                        window.modalCalendar.refetchEvents();
                        if (dateVal) {
                            window.modalCalendar.gotoDate(dateVal);
                        }
                    }
                } else {
                    alert(r.message || 'Gagal menyimpan jadwal');
                }
            }).fail(function() {
                $btn.prop('disabled', false).text('Simpan Jadwal Zoom');
                alert('Terjadi kesalahan jaringan, silakan coba lagi.');
            });
        });

        function saveZoomBantField(field, value) {
            let id = $('#zoomBant_data_id').val();
            if (!id) return;

            $.post('{{ route('admin.database.update-inline') }}', {
                _token: '{{ csrf_token() }}',
                id: id,
                field: field,
                value: value
            }).done(function(r) {
                if (r.success) {
                    showDetailToast('Tersimpan!');

                    // Sync the data attributes on the trigger buttons in that row!
                    let $btnZoom = $(`.btn-zoom-bant[data-id="${id}"]`);
                    if ($btnZoom.length) {
                        $btnZoom.attr('data-' + field.replace('_', '-'), value);
                    }

                    let $btnDetail = $(`.btn-detail-peserta[data-id="${id}"]`);
                    if ($btnDetail.length) {
                        $btnDetail.attr('data-' + field.replace('_', '-'), value);
                    }
                }
            });
        }

        $(document).on('click', '.btn-riwayat', function() {
            let $btn = $(this);
            let id = $btn.data('id');
            let nama = $btn.data('nama');
            let kelasNama = $btn.data('kelas-nama');
            let salesplanId = $btn.attr('data-salesplan-id') || '';

            $('#riwayat_data_id').val(id);
            $('#riwayat_salesplan_id').val(salesplanId);
            $('#namaPeserta').text((kelasNama ? 'Kelas ' + kelasNama + ' - ' : '') + nama);

            // Hide all cards first
            $('.fu-card-container').addClass('d-none');

            // Populate fields and show cards that have data
            let lastVisibleIndex = 1;
            for (let i = 1; i <= 10; i++) {
                let hasil = $btn.attr('data-fu' + i + '-hasil') || $btn.attr('data-fu' + i) || '';
                let tindak = $btn.attr('data-fu' + i + '-tindak-lanjut') || '';
                let wa = $btn.attr('data-fu' + i + '-wa') == 1;
                let telp = $btn.attr('data-fu' + i + '-telp') == 1;
                let dateVal = $btn.attr('data-fu' + i + '-at');

                $('#fu' + i + '_hasil').val(hasil);
                $('#fu' + i + '_tindak_lanjut').val(tindak);
                $('#fu' + i + '_wa').prop('checked', wa);
                $('#fu' + i + '_telp').prop('checked', telp);
                $('#fu' + i + '_at').val(dateVal ? dateVal : '');

                // Show card if it has data or if it's the first card
                if (hasil !== '' || tindak !== '' || wa || telp || i === 1) {
                    $('#fu_card_' + i).removeClass('d-none');
                    lastVisibleIndex = i;
                }
            }

            $('#modalRiwayat').modal('show');
        });

        // Event Tambah SPIN
        $('#btnTambahSpin').on('click', function() {
            // Find the next hidden card
            let $nextCard = $('.fu-card-container.d-none').first();
            if ($nextCard.length) {
                $nextCard.removeClass('d-none');
            } else {
                Swal.fire({
                    icon: 'info',
                    text: 'Maksimal 10 Follow Up tercapai.'
                });
            }
        });

        $('#btnSimpanRiwayat').on('click', function() {
            let id = $('#riwayat_data_id').val();
            let salesplanId = $('#riwayat_salesplan_id').val();
            let updates = {};
            for (let i = 1; i <= 10; i++) {
                updates['fu' + i + '_hasil'] = $('#fu' + i + '_hasil').val();
                updates['fu' + i + '_tindak_lanjut'] = $('#fu' + i + '_tindak_lanjut').val();
                updates['fu' + i + '_wa'] = $('#fu' + i + '_wa').is(':checked') ? 1 : 0;
                updates['fu' + i + '_telp'] = $('#fu' + i + '_telp').is(':checked') ? 1 : 0;
                updates['fu' + i + '_at'] = $('#fu' + i + '_at').val();
            }

            let $btnSimpan = $(this);
            $btnSimpan.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: '/admin/database/update-inline',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    salesplan_id: salesplanId,
                    updates: updates
                },
                success: function(res) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'Interaksi berhasil disimpan'
                    });

                    $('#modalRiwayat').modal('hide');

                    // Update data attributes in the trigger button
                    let $triggerBtn = salesplanId ?
                        $(`.btn-riwayat[data-id="${id}"][data-salesplan-id="${salesplanId}"]`) :
                        $(`.btn-riwayat[data-id="${id}"]`);

                    for (let i = 1; i <= 10; i++) {
                        $triggerBtn.attr('data-fu' + i + '-hasil', updates['fu' + i + '_hasil']);
                        $triggerBtn.attr('data-fu' + i + '-tindak-lanjut', updates['fu' + i +
                            '_tindak_lanjut']);
                        $triggerBtn.attr('data-fu' + i + '-wa', updates['fu' + i + '_wa']);
                        $triggerBtn.attr('data-fu' + i + '-telp', updates['fu' + i + '_telp']);

                        // Update the date attribute with the new timestamp from server
                        if (res.timestamps && res.timestamps['fu' + i + '_at']) {
                            $triggerBtn.attr('data-fu' + i + '-at', res.timestamps['fu' + i + '_at']);
                        }
                    }
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: msg
                    });
                },
                complete: function() {
                    $btnSimpan.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
                }
            });
        });
    </script>
    <!-- Modal Create -->


    <script>
        $('#createForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    alert('Berhasil disimpan!');
                    $('#createPesertaModal').modal('hide');
                    location.reload(); // atau refresh tabel data
                },
                error: function(err) {
                    alert('Gagal menyimpan.');
                }
            });
        });
    </script>

    <script>
        function create() {
            $('#createPesertaModal').modal('show');
        }

        $('#createForm').on('submit', function(e) {
            e.preventDefault();
            // Add your AJAX call here to save the data
            alert('Data saved successfully!');
            $('#createPesertaModal').modal('hide');
        });
    </script>
    {{--
    <script>
        $(document).ready(function () {
            $('#myTable').DataTable({
                responsive: true,
                autoWidth: false,
            });
        });
    </script> --}}



    <!-- Modal Create -->
    <div class="modal fade" id="createPesertaModal" tabindex="-1" role="dialog"
        aria-labelledby="createPesertaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPesertaModalLabel">Tambah Peserta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createForm" action="{{ route('admin.database.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        {{-- Nama Peserta --}}
                        <div class="form-group">
                            <label for="nama">Nama Peserta</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>

                        {{-- Potensi Kelas --}}
                        <div class="form-group">
                            <label for="kelas_id">Potensi Kelas</label>
                            <select name="kelas_id" id="kelas_id" class="form-control" required>
                                <option value="">Pilih Potensi Kelas</option>
                                @forelse($kelas as $item)
                                    <option value="{{ $item->id }}">
                                        {{ str_contains($item->nama_kelas, 'Muslim Indonesia') ? 'M1T' : $item->nama_kelas }}
                                    </option>
                                @empty
                                    <option disabled>Tidak ada kelas tersedia</option>
                                @endforelse
                            </select>
                        </div>

                        {{-- Sumber Leads --}}
                        <div class="form-group">
                            <label for="leads">Sumber Leads</label>
                            <select name="leads" id="leads" class="form-control">
                                <option value="Ads">ADS</option>
                                <option value="Sosmed">Sosial Media</option>
                                <option value="zoom">Zoom Preview</option>
                                <option value="Open House">Open House</option>
                                <option value="Mandiri">Mandiri</option>
                                <option value="Alumni">Alumni</option>
                            </select>
                        </div>

                        {{-- Provinsi --}}
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <select id="provinsi" class="form-control" name="provinsi_id" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                            <input type="hidden" name="provinsi_nama" id="provinsi_nama">
                        </div>

                        {{-- Kota --}}
                        <div class="form-group">
                            <label for="kota">Kota</label>
                            <select id="kota" class="form-control" name="kota_id" required>
                                <option value="">Pilih Kota</option>
                            </select>
                            <input type="hidden" name="kota_nama" id="kota_nama">
                        </div>

                        <script>
                            fetch('/wilayah/provinsi')
                                .then(res => res.json())
                                .then(data => {
                                    data.forEach(prov => {
                                        $('#provinsi').append(
                                            `<option value="${prov.id}" data-nama="${prov.name}">${prov.name}</option>`);
                                    });
                                });

                            $('#provinsi').on('change', function() {
                                const id = $(this).val();
                                const nama = $(this).find('option:selected').text();
                                $('#provinsi_nama').val(nama);

                                fetch(`/wilayah/kota/${id}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        $('#kota').html('<option value="">Pilih Kota</option>');
                                        data.forEach(kota => {
                                            $('#kota').append(
                                                `<option value="${kota.id}" data-nama="${kota.name}">${kota.name}</option>`
                                            );
                                        });
                                    });
                            });

                            $('#kota').on('change', function() {
                                const nama = $(this).find('option:selected').text();
                                $('#kota_nama').val(nama);
                            });
                        </script>

                        {{-- Nama Bisnis --}}
                        <div class="form-group">
                            <label for="nama_bisnis">Nama Bisnis</label>
                            <input type="text" class="form-control" id="nama_bisnis" name="nama_bisnis" required>
                        </div>

                        {{-- Jenis Bisnis --}}
                        <div class="form-group">
                            <label for="jenisbisnis">Jenis Bisnis</label>
                            <select name="jenisbisnis" id="jenisbisnis" class="form-control">
                                <option value="Bisnis Properti">Bisnis Properti</option>
                                <option value="Bisnis Manufaktur">Bisnis Manufaktur</option>
                                <option value="Bisnis F&B (Food & Beverage)">Bisnis F&B (Food & Beverage)</option>
                                <option value="Bisnis Jasa">Bisnis Jasa</option>
                                <option value="Bisnis Digital">Bisnis Digital</option>
                                <option value="Bisnis Online">Bisnis Online</option>
                                <option value="Bisnis Franchise">Bisnis Franchise</option>
                                <option value="Bisnis Edukasi & Pelatihan">Bisnis Edukasi & Pelatihan</option>
                                <option value="Bisnis Kreatif">Bisnis Kreatif</option>
                                <option value="Bisnis Agribisnis">Bisnis Agribisnis</option>
                                <option value="Bisnis Kesehatan & Kecantikan">Bisnis Kesehatan & Kecantikan</option>
                                <option value="Bisnis Keuangan">Bisnis Keuangan</option>
                                <option value="Bisnis Transportasi & Logistik">Bisnis Transportasi & Logistik</option>
                                <option value="Bisnis Pariwisata & Hospitality">Bisnis Pariwisata & Hospitality</option>
                                <option value="Bisnis Sosial (Social Enterprise)">Bisnis Sosial (Social Enterprise)
                                </option>
                            </select>
                        </div>

                        {{-- No WA --}}
                        <div class="form-group">
                            <label for="no_wa">No. WA</label>
                            <input type="text" class="form-control" id="no_wa" name="no_wa" required>
                        </div>

                        {{-- Situasi Bisnis --}}
                        <div class="form-group">
                            <label
                                for="situasi_bisnis">{{ in_array($userRole, ['chapter', 'reseller']) ? 'Situasi & Kendala Bisnis' : 'Situasi Bisnis' }}</label>
                            <textarea class="form-control" id="situasi_bisnis" name="situasi_bisnis" rows="3"></textarea>
                        </div>

                        {{-- Kendala --}}
                        <div class="form-group {{ $userRole === 'chapter' ? 'd-none' : '' }}">
                            <label for="kendala">Kendala</label>
                            <textarea class="form-control" id="kendala" name="kendala" rows="3"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Setting Pembayaran --}}
    <div class="modal fade" id="monthSelectionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header bg-info text-white"
                    style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title fw-bold m-0"><i class="fas fa-wallet me-2"></i> Setting Pembayaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3 pb-3 border-bottom">
                        <div class="col-12">
                            <label class="font-weight-bold d-block mb-0 text-muted" style="font-size: 0.7rem;">NAMA
                                PESERTA</label>
                            <span id="modalPlanNameDisplay" class="h6 fw-bold text-dark">-</span>
                            <span id="modalPlanLevelDisplay" class="d-none"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold d-block mb-2 text-uppercase text-secondary"
                            style="font-size: 0.75rem;">Metode Pembayaran</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-info btn-sm active flex-fill">
                                <input type="radio" name="pay_method" id="method-template" value="template" checked>
                                Pembayaran Template
                            </label>
                            <label class="btn btn-outline-info btn-sm flex-fill">
                                <input type="radio" name="pay_method" id="method-custom" value="custom"> Pembayaran
                                Custom
                            </label>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded mb-4" style="border: 1px solid #e0e0e0;">
                        <h6 class="font-weight-bold text-info mb-3" style="font-size: 0.85rem;"><i
                                class="fas fa-money-bill-wave me-1"></i> Detail Pembayaran Pertama</h6>
                        <div class="row">
                            <div class="col-sm-4 mb-2">
                                <label class="font-weight-bold d-block mb-1 text-uppercase text-secondary"
                                    style="font-size: 0.7rem;">Pendaftaran</label>
                                <input type="text" id="modalBiayaPendaftaran"
                                    class="form-control form-control-sm currency-input" style="border-radius: 8px;"
                                    placeholder="0">
                            </div>
                            <div class="col-sm-4 mb-2">
                                <label class="font-weight-bold d-block mb-1 text-uppercase text-secondary"
                                    style="font-size: 0.7rem;">Pembayaran SPP</label>
                                <input type="text" id="modalSppPertama"
                                    class="form-control form-control-sm currency-input" style="border-radius: 8px;"
                                    placeholder="0">
                            </div>
                            <div class="col-sm-4 mb-2">
                                <label class="font-weight-bold d-block mb-1 text-uppercase text-danger"
                                    style="font-size: 0.7rem;">Total Saja</label>
                                <input type="text" id="modalSppAwal"
                                    class="form-control form-control-sm bg-white fw-bold text-danger"
                                    style="border-radius: 8px; border-color: #ffcccc;" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="modalPlanId">
                    <input type="hidden" id="modalTanggalClosingHidden">
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <div class="d-flex align-items-center mb-2" style="gap: 15px;">
                                <label class="font-weight-bold mb-0 text-uppercase"
                                    style="font-size: 0.75rem; letter-spacing: 1px;">Bulan</label>
                                <div class="custom-control custom-checkbox" id="all-month-wrapper">
                                    <input type="checkbox" class="custom-control-input" id="btn-month-all">
                                    <label class="custom-control-label font-weight-bold text-info" for="btn-month-all"
                                        style="font-size: 0.7rem;">ALL</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input text-success" id="cb-pay-full">
                                    <label class="custom-control-label font-weight-bold text-success" for="cb-pay-full"
                                        style="font-size: 0.7rem;">Bayar Lunas Langsung</label>
                                </div>
                            </div>
                            <div class="text-muted italic mb-2" style="font-size: 0.65rem;">(Klik ALL jika langsung
                                membayar lunas)</div>
                            <div id="monthChecklist" style="max-height: 250px; overflow-y: auto;">
                                @php
                                    $monthsNames = [
                                        'Januari',
                                        'Februari',
                                        'Maret',
                                        'April',
                                        'Mei',
                                        'Juni',
                                        'Juli',
                                        'Agustus',
                                        'September',
                                        'Oktober',
                                        'November',
                                        'Desember',
                                    ];
                                @endphp
                                @foreach ($monthsNames as $index => $month)
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input month-cb"
                                            id="cb-month-{{ $index + 1 }}" data-value="{{ $index + 1 }}">
                                        <label class="custom-control-label font-weight-normal"
                                            for="cb-month-{{ $index + 1 }}">{{ $month }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6 pl-3">
                            <label class="font-weight-bold d-block mb-1 text-uppercase"
                                style="font-size: 0.75rem; letter-spacing: 1px;">Tahun</label>
                            <select id="yearSelect" class="form-control form-control-sm mb-3"
                                style="border-radius: 8px;">
                                <option value="">-- Pilih Tahun --</option>
                                @for ($y = 2026; $y <= 2030; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>

                            <div id="dateSelectionFields" class="mt-3">
                                <div class="mb-3">
                                    <label class="font-weight-bold d-block mb-1 text-uppercase text-info"
                                        style="font-size: 0.7rem;">Tanggal Masuk</label>
                                    <input type="date" id="modalTanggalMasuk" class="form-control form-control-sm"
                                        style="border-radius: 8px;">
                                </div>

                                <div class="mb-2" id="wrapperTanggalSelesai" style="display: none;">
                                    <label class="font-weight-bold d-block mb-1 text-uppercase text-info"
                                        style="font-size: 0.7rem;">Tanggal Selesai</label>
                                    <input type="date" id="modalTanggalSelesai" class="form-control form-control-sm"
                                        style="border-radius: 8px;">
                                </div>

                                <div id="customPaymentsContainer"
                                    class="d-none mt-3 p-3 bg-white rounded border border-info shadow-sm">
                                    <label
                                        class="font-weight-bold d-block mb-3 text-uppercase text-info border-bottom pb-2"
                                        style="font-size: 0.75rem;"><i class="fas fa-plus-circle me-1"></i> Pembayaran
                                        Selanjutnya</label>
                                    <div id="customPaymentsList"></div>
                                    <button type="button" class="btn btn-sm btn-outline-info w-100 mt-2 font-weight-bold"
                                        style="border-radius: 8px; border-style: dashed;" onclick="addCustomPaymentRow()">
                                        <i class="fas fa-plus mr-1"></i> Tambah Pembayaran
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light"
                    style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info rounded-pill px-4" onclick="saveSelectedMonths()">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- End Modal Create -->

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>


    <script>
        // Logic filter kelas sudah digabung di applyFilters()
    </script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- ✅ Tambahkan ini di atas tabel kamu -->


    <script>
        // --- Payment Setting Modal Logic (Copied from SalesPlan) ---
        let tempModalSelections = {};
        const monthsNamesArr = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
            'Oktober', 'November', 'Desember'
        ];

        window.showMonthSelectionModal = function(id, name, currentSelection, tglMasuk, tglSelesai, sppAwal,
            biayaPendaftaran, pembayaranSpp, totalPembayaran, level, tglClosing) {
            $('#modalPlanId').val(id);
            $('#modalPlanNameDisplay').text(name);
            $('#modalPlanLevelDisplay').text(level || '-');
            $('#modalTanggalClosingHidden').val(tglClosing || '');

            // Reset selections
            $('.month-cb').prop('checked', false);
            $('#cb-pay-full').prop('checked', false).trigger('change');
            $('#btn-month-all').prop('checked', false);
            tempModalSelections = {};
            $('#modalTanggalMasuk').val(tglMasuk || tglClosing || '');
            $('#modalTanggalSelesai').val(tglSelesai || '');
            $('#modalBiayaPendaftaran').val(formatCurrencyValue(biayaPendaftaran || ''));
            $('#modalSppPertama').val(formatCurrencyValue(pembayaranSpp || ''));
            $('#modalSppAwal').val(formatCurrencyValue(totalPembayaran || sppAwal || ''));

            // Default to Template method
            $('#method-template').prop('checked', true);
            $('input[name="pay_method"]:checked').trigger('change');

            let firstYear = '';
            if (currentSelection) {
                if (typeof currentSelection === 'string') {
                    try {
                        currentSelection = JSON.parse(currentSelection);
                    } catch (e) {}
                }
                if (typeof currentSelection === 'object' && !Array.isArray(currentSelection)) {
                    tempModalSelections = currentSelection;
                    let yearsInSelection = Object.keys(tempModalSelections);
                    if (yearsInSelection.length > 0) {
                        firstYear = yearsInSelection[0];
                    }
                }
            }

            if (!firstYear) {
                let today = new Date();
                firstYear = today.getFullYear();
                if (firstYear < 2026) firstYear = 2026;
            }

            $('#yearSelect').val(firstYear);
            loadMonthsForYear(firstYear);
            $('#dateSelectionFields').toggle(!!firstYear);
            $('#monthSelectionModal').modal('show');
        };

        function loadMonthsForYear(year) {
            $('.month-cb').prop('checked', false);
            if (year && tempModalSelections[year]) {
                tempModalSelections[year].forEach(m => {
                    $('#cb-month-' + m).prop('checked', true);
                });
            }
        }

        $(document).on('change', '#yearSelect', function() {
            let year = $(this).val();
            loadMonthsForYear(year);
            $('#dateSelectionFields').toggle(!!year);
        });

        $(document).on('change', 'input[name="pay_method"]', function() {
            let method = $(this).val();
            let level = $('#modalPlanLevelDisplay').text().toLowerCase();

            if (method === 'template') {
                $('#modalSppPertama').prop('readonly', true).addClass('bg-light');
                $('#modalBiayaPendaftaran').val(formatCurrencyValue('500000'));
                if (level.includes('grow up')) {
                    $('#modalSppPertama').val(formatCurrencyValue('1500000'));
                } else {
                    $('#modalSppPertama').val(formatCurrencyValue('1000000'));
                }
                $('#customPaymentsContainer').addClass('d-none');
            } else {
                $('#modalSppPertama').prop('readonly', false).removeClass('bg-light');
                $('#customPaymentsContainer').removeClass('d-none');
            }
            updateModalTotal();
        });

        $(document).on('change', '#cb-pay-full', function() {
            let isChecked = $(this).is(':checked');
            let level = $('#modalPlanLevelDisplay').text().toLowerCase();

            if (isChecked) {
                $('#monthChecklist').hide();
                $('#all-month-wrapper').hide();
                $('#wrapperTanggalSelesai').show();
                $('#modalSppPertama').val(formatCurrencyValue('15000000'));
                calculateEndDate();
            } else {
                $('#monthChecklist').show();
                $('#all-month-wrapper').show();
                $('#wrapperTanggalSelesai').hide();
                if (level.includes('grow up')) {
                    $('#modalSppPertama').val(formatCurrencyValue('1500000'));
                } else {
                    $('#modalSppPertama').val(formatCurrencyValue('1000000'));
                }
            }
            updateModalTotal();
        });

        function calculateEndDate() {
            let entryVal = $('#modalTanggalMasuk').val();
            if (entryVal && $('#cb-pay-full').is(':checked')) {
                let date = new Date(entryVal);
                date.setMonth(date.getMonth() + 11);
                let y = date.getFullYear();
                let m = String(date.getMonth() + 1).padStart(2, '0');
                let d = String(date.getDate()).padStart(2, '0');
                $('#modalTanggalSelesai').val(`${y}-${m}-${d}`);
            }
        }

        $(document).on('change', '#modalTanggalMasuk', function() {
            calculateEndDate();
        });

        $(document).on('input', '#modalBiayaPendaftaran, #modalSppPertama', function() {
            updateModalTotal();
        });

        function updateModalTotal() {
            let pendaftaran = parseInt($('#modalBiayaPendaftaran').val().replace(/[^0-9]/g, '')) || 0;
            let spp = parseInt($('#modalSppPertama').val().replace(/[^0-9]/g, '')) || 0;
            $('#modalSppAwal').val(formatCurrencyValue(pendaftaran + spp));
        }

        $(document).on('change', '.month-cb', function() {
            let year = $('#yearSelect').val();
            if (!year) {
                Swal.fire('Oops!', 'Pilih tahun terlebih dahulu.', 'warning');
                $(this).prop('checked', false);
                return;
            }
            if (!tempModalSelections[year]) tempModalSelections[year] = [];
            let checkedMonths = [];
            $('.month-cb:checked').each(function() {
                checkedMonths.push(parseInt($(this).data('value')));
            });
            tempModalSelections[year] = checkedMonths.sort((a, b) => a - b);
        });

        $(document).on('click', '#btn-month-all', function() {
            let isChecked = $(this).is(':checked');
            $('.month-cb').prop('checked', isChecked).trigger('change');
        });

        window.saveSelectedMonths = function() {
            let id = $('#modalPlanId').val();
            let tanggalMasuk = $('#modalTanggalMasuk').val();
            let tanggalSelesai = $('#modalTanggalSelesai').val();
            let pendaftaran = $('#modalBiayaPendaftaran').val().replace(/[^0-9]/g, '');
            let sppPertama = $('#modalSppPertama').val().replace(/[^0-9]/g, '');
            let sppAwal = $('#modalSppAwal').val().replace(/[^0-9]/g, '');

            let finalSelections = {};
            if ($('#cb-pay-full').is(':checked')) {
                if (!tanggalMasuk) {
                    Swal.fire('Oops!', 'Mohon isi Tanggal Masuk.', 'warning');
                    return;
                }
                let dateMasuk = new Date(tanggalMasuk);
                let startYear = dateMasuk.getFullYear();
                let startMonth = dateMasuk.getMonth() + 1;
                for (let i = 0; i < 12; i++) {
                    let current = new Date(startYear, (startMonth - 1) + i, 1);
                    let curY = current.getFullYear();
                    let curM = current.getMonth() + 1;
                    if (!finalSelections[curY]) finalSelections[curY] = [];
                    finalSelections[curY].push(curM);
                }
            } else {
                for (let y in tempModalSelections) {
                    if (tempModalSelections[y].length > 0) finalSelections[y] = tempModalSelections[y];
                }
            }

            let methodSelection = $('input[name="pay_method"]:checked').val();
            let customPayments = [];
            if (methodSelection === 'custom') {
                $('#customPaymentsList .custom-payment-row').each(function() {
                    let date = $(this).find('.cp-date').val();
                    let nominal = $(this).find('.cp-nominal').val().replace(/[^0-9]/g, '');
                    if (date && nominal) customPayments.push({
                        date: date,
                        nominal: nominal
                    });
                });
            }

            $.ajax({
                url: "{{ route('admin.salesplan.update-selected-months') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    selected_months: JSON.stringify(finalSelections),
                    tanggal_masuk: tanggalMasuk,
                    tanggal_selesai: tanggalSelesai,
                    spp_awal: sppAwal,
                    biaya_pendaftaran: pendaftaran,
                    pembayaran_spp: sppPertama,
                    custom_payments: customPayments
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Disimpan',
                        timer: 1500
                    });
                    $('#monthSelectionModal').modal('hide');
                }
            });
        };

        function formatCurrencyValue(value) {
            if (!value) return '';
            let num = value.toString().replace(/[^0-9]/g, '');
            if (!num) return '';
            return parseInt(num).toLocaleString('id-ID');
        }

        window.addCustomPaymentRow = function() {
            let count = $('#customPaymentsList .custom-payment-row').length + 1;
            let html = `
                <div class="custom-payment-row mb-2 border-bottom pb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="font-weight-bold text-secondary" style="font-size: 0.7rem;">Pembayaran ke-${count + 1}</span>
                        <i class="fas fa-times text-danger" onclick="$(this).closest('.custom-payment-row').remove();" style="cursor: pointer;"></i>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-1 pr-1"><input type="date" class="form-control form-control-sm cp-date"></div>
                        <div class="col-sm-6 mb-1 pl-1"><input type="text" class="form-control form-control-sm cp-nominal" oninput="this.value = formatCurrencyValue(this.value)"></div>
                    </div>
                </div>
            `;
            $('#customPaymentsList').append(html);
        };

        function exportPdfInteraksi() {
            let url = '{{ route('admin.database.export-pdf-interaksi') }}';
            let currentParams = new URLSearchParams(window.location.search);

            window.location.href = url + '?' + currentParams.toString();
        }

        // Direct status update function for Chapter/Reseller
        window.updateStatusDirect = function(dataId, status) {
            const $select = $(`.status-select[data-id="${dataId}"]`);

            $select.css('opacity', '0.5').prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.database.update-status-direct') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    data_id: dataId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Status Updated'
                        });

                        const row = $select.closest('tr');
                        const colors = {
                            'cold': {
                                bg: '#ffffff',
                                text: '#6c757d',
                                border: '#ddd',
                                rowBg: '#ffffff'
                            },
                            'tertarik': {
                                bg: '#fffceb',
                                text: '#856404',
                                border: '#ffeeba',
                                rowBg: '#fffceb'
                            },
                            'sudah_transfer': {
                                bg: '#e7f5ff',
                                text: '#1971c2',
                                border: '#74c0fc',
                                rowBg: '#e7f5ff'
                            },
                            'no': {
                                bg: '#fff5f5',
                                text: '#c92a2a',
                                border: '#ffa8a8',
                                rowBg: '#fff5f5'
                            },
                            'new': {
                                bg: '#ffffff',
                                text: '#495057',
                                border: '#dee2e6',
                                rowBg: '#ffffff'
                            }
                        };

                        const color = colors[status] || colors['new'];

                        // Update select styling
                        $select.css({
                            'background-color': color.bg,
                            'color': color.text,
                            'border-color': color.border,
                            'opacity': '1'
                        }).prop('disabled', false);

                        // Update entire row background
                        row.css('background-color', color.rowBg);

                        // Toggle nominal display visibility
                        if (status === 'sudah_transfer') {
                            row.find('.nominal-display').removeClass('d-none');
                        } else {
                            row.find('.nominal-display').addClass('d-none');
                        }
                        // Trigger Payment Modal if status is Sudah Transfer
                        if (status === 'sudah_transfer') {
                            const nama = $select.attr('data-nama');
                            const level = $select.attr('data-level');
                            const planId = response.plan_id;
                            const kelasNama = $select.attr('data-kelas-nama') || '';
                            const isM1T = kelasNama.toUpperCase().includes('M1T') || kelasNama.toUpperCase()
                                .includes('MUSLIM INDONESIA');

                            // Get potensi value from the same row
                            const $row = $select.closest('tr');

                            if (!isM1T) {
                                // Simplified popup for MBC
                                Swal.fire({
                                    title: 'Setting Pembayaran MBC',
                                    text: 'Masukkan Nominal Pembayaran untuk ' + nama,
                                    input: 'text',
                                    inputPlaceholder: 'Contoh: 1.500.000',
                                    showCancelButton: true,
                                    confirmButtonText: 'Simpan',
                                    cancelButtonText: 'Batal',
                                    customClass: {
                                        confirmButton: 'btn btn-primary rounded-pill px-4',
                                        cancelButton: 'btn btn-secondary rounded-pill px-4'
                                    },
                                    buttonsStyling: false,
                                    didOpen: () => {
                                        const input = Swal.getInput();
                                        $(input).on('input', function() {
                                            let val = this.value.replace(/[^0-9]/g, '');
                                            this.value = val ? parseInt(val)
                                                .toLocaleString('id-ID') : '';
                                        });
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        let nominal = result.value.replace(/[^0-9]/g, '');
                                        if (!nominal) {
                                            Swal.fire('Error', 'Nominal harus diisi', 'error');
                                            return;
                                        }

                                        // Save via AJAX
                                        $.ajax({
                                            url: "{{ route('admin.salesplan.update-selected-months') }}",
                                            type: "POST",
                                            data: {
                                                _token: "{{ csrf_token() }}",
                                                id: planId,
                                                spp_awal: nominal,
                                                nominal: nominal, // Also sync to SalesPlan nominal
                                                selected_months: JSON.stringify({}),
                                                tanggal_masuk: new Date().toISOString()
                                                    .split('T')[0]
                                            },
                                            success: function(res) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Berhasil Disimpan',
                                                    timer: 1500
                                                });
                                                // Update UI
                                                $row.find('.nominal-display').text(
                                                        'Rp ' + parseInt(nominal)
                                                        .toLocaleString('id-ID'))
                                                    .removeClass('d-none');
                                            }
                                        });
                                    }
                                });
                            } else {
                                // Detailed modal for M1T (SMI) and others
                                window.showMonthSelectionModal(planId, nama, {}, null, null, null, null,
                                    null, null, level, null);
                            }
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Update status failed:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Gagal',
                        text: 'Terjadi kesalahan saat memperbarui status.'
                    });
                    $select.css('opacity', '1').prop('disabled', false);
                }
            });
        };

        // --- Reuse Logic ---

        // Reuse Data (Create New SalesPlan Instance)
        $(document).on('click', '.btn-reuse', function() {
            let id = $(this).data('id');
            let $btn = $(this);

            Swal.fire({
                title: 'Reuse Data?',
                text: 'Ini akan membuat riwayat penawaran baru (Cold) untuk data ini tanpa menambah jumlah total database.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Segarkan',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary rounded-pill px-4',
                    cancelButton: 'btn btn-secondary rounded-pill px-4'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin" style="font-size: 0.7rem;"></i>');

                    $.ajax({
                        url: "{{ route('admin.database.reuse-data') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data Berhasil Disegarkan',
                                    text: 'Status terbaru kini kembali ke COLD.',
                                    timer: 2000
                                }).then(() => {
                                    location
                                        .reload(); // Reload to see the new status and updated history
                                });
                            }
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Terjadi kesalahan sistem', 'error');
                            $btn.prop('disabled', false).html(
                                '<i class="fas fa-retweet" style="color: #fff; font-size: 0.7rem;"></i>'
                            );
                        }
                    });
                }
            });
        });
        // Delete Confirmation for Linda & Yasmin (cs-mbc)
        $(document).on('submit', '.delete-form', function(e) {
            @php
                $userName = auth()->user()->name;
                $userRole = strtolower(auth()->user()->role);
                $isTargetUser = (in_array($userName, ['Linda', 'Yasmin']) && $userRole === 'cs-mbc') || $userRole === 'administrator';
            @endphp

            @if ($isTargetUser)
                e.preventDefault();
                let form = this;
                let nama = $(this).data('nama');
                Swal.fire({
                    title: 'Apakah anda yakin menghapus data dengan nama "' + nama + '",',
                    text: "data yang di hapus tidak dapat di kembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            @endif
        });
    </script>

    {{-- ============================================================
     MODAL DETAIL PESERTA
     ============================================================ --}}
    <div class="modal fade" id="modalDetailPeserta" tabindex="-1" role="dialog"
        aria-labelledby="modalDetailPesertaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content"
                style="border-radius:14px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25);">

                {{-- Header --}}
                <div class="modal-header border-0 pb-2"
                    style="background:linear-gradient(135deg,#25799E,#1a5475); color:#fff;">
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="modalDetailPesertaLabel">
                            <i class="fas fa-user-circle mr-2"></i>
                            <span id="detailNama">-</span>
                        </h5>
                        <small class="d-block mt-1 opacity-75">
                            <i class="fas fa-phone-alt mr-1"></i><span id="detailNoWa"></span>
                            &nbsp;|&nbsp;
                            <i class="fas fa-user mr-1"></i><span id="detailInputOleh"></span>
                            &nbsp;|&nbsp;
                            <i class="fas fa-clock mr-1"></i>Update: <span id="detailUpdatedAt"></span>
                        </small>
                    </div>
                    <div class="d-flex align-items-center" style="gap:15px;">
                        <button type="button"
                            class="btn btn-warning btn-sm font-weight-bold shadow-sm text-dark d-flex align-items-center"
                            style="border-radius:20px; font-size:0.7rem; border:1px solid #fff; padding: 4px 12px; height: 28px;"
                            onclick="refreshProspekData()">
                            <i class="fas fa-sync-alt mr-1"></i> REUSE / REFRESH
                        </button>
                        <button type="button" class="close text-white p-0 m-0" data-dismiss="modal" aria-label="Tutup"
                            style="opacity:1; font-size:1.4rem; line-height:1;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="modal-body p-3" style="background:#f4f6f9;">

                    {{-- Row 1: Potensi + Status (Layout Disesuaikan) --}}
                    <div class="row mb-3">
                        {{-- Potensi --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                                <div class="card-body py-3">
                                    <p class="text-muted small font-weight-bold mb-2" id="detailPotensiLabel"
                                        style="text-transform:uppercase; letter-spacing:.5px;">Potensi Kelas Selanjutnya
                                    </p>
                                    <select id="detailPotensiSelect"
                                        class="form-control form-control-sm font-weight-bold"
                                        style="border-radius:8px; font-size:0.85rem; cursor:pointer;"
                                        onchange="saveDetailPotensi()">
                                        <option value="">- Pilih -</option>
                                        <option value="MBC">MBC</option>
                                        <option value="SMI">M1T (SMI)</option>
                                    </select>
                                    <div id="detailPotensiKelasWrap" class="mt-2 d-none">
                                        <select id="detailKelasSelect" class="form-control form-control-sm"
                                            style="font-size:0.8rem; border-radius:8px;" onchange="saveDetailKelas()">
                                            <option value="">- Pilih Kelas MBC -</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                                <div class="card-body py-3">
                                    <p class="text-muted small font-weight-bold mb-2"
                                        style="text-transform:uppercase; letter-spacing:.5px;">Status Follow Up</p>
                                    <select id="detailStatusSelect"
                                        class="form-control form-control-sm font-weight-bold"
                                        style="border-radius:8px; font-size:0.85rem; cursor:pointer;"
                                        onchange="saveDetailStatus()">
                                        <option value="cold">⚪ Cold</option>
                                        <option value="tertarik">🟡 Tertarik</option>
                                        <option value="mau_transfer">🟢 Mau Transfer</option>
                                        <option value="sudah_transfer">🔵 Sudah Transfer</option>
                                        <option value="no">🔴 No</option>
                                    </select>

                                    {{-- Input Nominal (Hidden by default) --}}
                                    <div id="wrapperNominalBayar" class="mt-2 d-none">
                                        <label class="small font-weight-bold text-success mb-1 text-uppercase"
                                            style="letter-spacing: 0.5px;">Nominal Bayar (Rp)</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-success text-white border-0"
                                                    style="border-radius:10px 0 0 10px;">Rp</span>
                                            </div>
                                            <input type="text" id="detailNominalInput"
                                                class="form-control form-control-sm shadow-sm"
                                                style="border-radius:0 10px 10px 0; height:35px; font-weight:700;"
                                                placeholder="0" onkeyup="formatRupiah(this)"
                                                onblur="saveDetailStatus()">
                                        </div>
                                    </div>

                                    <div id="detailNominalDisplay" class="mt-2 d-none"
                                        style="font-size:0.85rem; font-weight:800; color:#1971c2; background:rgba(25,113,194,.08); padding:4px 8px; border-radius:6px; border:1px solid rgba(25,113,194,.2);">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Kualifikasi + Ikut Zoom + Tgl Update --}}
                    <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
                        <div class="card-header py-2 border-0" style="background:#e9ecef; border-radius:10px 10px 0 0;">
                            <span class="font-weight-bold text-dark" style="font-size:0.85rem;">
                                <i class="fas fa-check-circle mr-1 text-primary"></i> Kualifikasi (B, A, T) &amp; Zoom
                            </span>
                        </div>
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap gap-3 align-items-center" style="gap:20px;">
                                {{-- Budget --}}
                                <div class="d-flex flex-column align-items-center" style="min-width:70px;">
                                    <span class="small text-muted mb-1 font-weight-bold">Budget (B)</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="detailBantBudget"
                                            onchange="saveDetailBant('bant_budget', this.checked ? 1 : 0)">
                                        <label class="custom-control-label" for="detailBantBudget"></label>
                                    </div>
                                </div>
                                {{-- Authority --}}
                                <div class="d-flex flex-column align-items-center" style="min-width:70px;">
                                    <span class="small text-muted mb-1 font-weight-bold">Authority (A)</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="detailBantAuthority"
                                            onchange="saveDetailBant('bant_authority', this.checked ? 1 : 0)">
                                        <label class="custom-control-label" for="detailBantAuthority"></label>
                                    </div>
                                </div>
                                {{-- Time --}}
                                <div class="d-flex flex-column align-items-center" style="min-width:70px;">
                                    <span class="small text-muted mb-1 font-weight-bold">Time (T)</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="detailBantTime"
                                            onchange="saveDetailBant('bant_time', this.checked ? 1 : 0)">
                                        <label class="custom-control-label" for="detailBantTime"></label>
                                    </div>
                                </div>
                                {{-- Divider --}}
                                <div style="width:1px; height:40px; background:#dee2e6;"></div>
                                {{-- Ikut Zoom --}}
                                <div class="d-flex flex-column align-items-center" style="min-width:80px;">
                                    <span class="small text-muted mb-1 font-weight-bold">Ikut Zoom</span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="detailIkutZoom"
                                            onchange="saveDetailBant('ikut_zoom', this.checked ? 1 : 0)">
                                        <label class="custom-control-label" for="detailIkutZoom"></label>
                                    </div>
                                </div>
                                {{-- Tgl Update --}}
                                <div class="ml-auto text-right">
                                    <span class="small text-muted d-block">Terakhir Update</span>
                                    <span id="detailTglUpdate" class="font-weight-bold text-dark"
                                        style="font-size:0.9rem;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: History SalesPlan (Terintegrasi dengan Follow Up) --}}
                    <div class="card border-0 shadow-sm" style="border-radius:10px;">
                        <div class="card-header py-2 border-0 d-flex justify-content-between align-items-center"
                            style="background:#e9ecef; border-radius:10px 10px 0 0;">
                            <span class="font-weight-bold text-dark" style="font-size:0.85rem;">
                                <i class="fas fa-history mr-1 text-success"></i> Riwayat Kelas (SalesPlan)
                            </span>
                        </div>
                        <div class="card-body py-2">
                            {{-- List Kelas --}}
                            <div id="detailSalesplanHistory">
                                <span class="text-muted small">Tidak ada riwayat kelas.</span>
                            </div>

                            {{-- Section Expandable Follow Up --}}
                            <div id="detailFollowupHistoryWrapper" class="mt-3 pt-2 border-top d-none">
                                <p class="text-info font-weight-bold mb-2"
                                    style="font-size:0.75rem; text-transform:uppercase;">
                                    <i class="fas fa-comment-dots mr-1"></i> Catatan Follow Up
                                </p>
                                <div id="detailFollowupHistory" style="max-height:200px; overflow-y:auto;">
                                    <span class="text-muted small">Tidak ada riwayat follow up.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- end modal-body --}}

                <div class="modal-footer border-0" style="background:#f4f6f9;">
                    <button type="button" class="btn btn-success btn-sm px-4 shadow-sm"
                        onclick="handleDetailSimpan()" style="border-radius: 8px; font-weight: bold;">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL JADWAL ZOOM ONE-ON-ONE (CALENDAR) -->
    <div class="modal fade" id="modalJadwalZoomHariIni" tabindex="-1" role="dialog"
        aria-labelledby="modalJadwalZoomHariIniLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1280px; width: 98%;">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius: 16px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;">
                <!-- Modal Header -->
                <div class="modal-header border-0 text-white p-4"
                    style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h5 class="modal-title font-weight-bold mb-1 d-flex align-items-center"
                                id="modalJadwalZoomHariIniLabel" style="font-size: 1.25rem; letter-spacing: 0.5px;">
                                <i class="fas fa-video mr-2"></i> Monitoring Jadwal Zoom One-on-One -
                                {{ auth()->user()->name }}
                            </h5>
                            <p class="mb-0 text-white-50 small">Pantau pencapaian target harian dan sebaran jadwal Zoom
                                seluruh tim CS secara real-time.</p>
                        </div>
                        <button type="button" class="close text-white p-0 m-0" data-dismiss="modal"
                            aria-label="Close"
                            style="outline: none; background: transparent; border: none; opacity: 0.85;">
                            <span aria-hidden="true" style="font-size: 1.8rem; color: #fff;">&times;</span>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4 bg-light">
                    <!-- Legend Row -->
                    <div class="row align-items-center mb-4">
                        <input type="hidden" id="modalFilterCs" value="{{ auth()->id() }}">
                        <div class="col-12 d-flex justify-content-end align-items-center"
                            style="gap: 15px; font-size: 0.8rem; font-weight: 700; color: #333;">
                            <span class="d-flex align-items-center" style="gap: 5px;"><span class="rounded-circle"
                                    style="width: 10px; height: 10px; background-color: #25799E; display: inline-block;"></span>
                                Scheduled</span>
                            <span class="d-flex align-items-center" style="gap: 5px;"><span class="rounded-circle"
                                    style="width: 10px; height: 10px; background-color: #3CDE1D; display: inline-block;"></span>
                                Done / Sukses</span>
                            <span class="d-flex align-items-center" style="gap: 5px;"><span class="rounded-circle"
                                    style="width: 10px; height: 10px; background-color: #E61717; display: inline-block;"></span>
                                Cancelled</span>
                        </div>
                    </div>

                    <!-- Calendar Area -->
                    <div class="card border-0 shadow-sm"
                        style="border-radius: 12px; overflow: hidden; border: 1px solid #e3e6f0;">
                        <div class="card-body p-3">
                            <div id="modalZoomCalendar"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 bg-white p-3 d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm px-4 font-weight-bold shadow-sm"
                        data-dismiss="modal" style="border-radius: 8px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SUB-MODAL DETAIL JADWAL ZOOM -->
    <div class="modal fade" id="modalModalEventDetail" tabindex="-1" role="dialog"
        aria-labelledby="modalModalEventDetailLabel" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius: 16px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.3) !important;">
                <div class="modal-header border-0 pb-3 text-white"
                    style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 16px 20px;">
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="modalModalEventDetailLabel"
                            style="font-size: 1.1rem; letter-spacing: 0.5px;">
                            <i class="fas fa-calendar-day mr-2"></i> Rincian Jadwal Zoom
                        </h5>
                        <small class="d-block mt-1 opacity-75" style="font-size: 0.78rem;">
                            Status: <span id="modalDetailStatusBadge" class="badge text-white px-2 py-0.5 ml-1"></span>
                        </small>
                    </div>
                    <button type="button" class="close text-white p-0 m-0"
                        onclick="$('#modalModalEventDetail').modal('hide')" aria-label="Close"
                        style="outline:none; background: transparent; border:none; opacity:1; font-size: 1.4rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 bg-light text-dark">
                    <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #fff;">
                        <div class="mb-3">
                            <small class="text-muted d-block text-uppercase font-weight-bold"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">NAMA CUSTOMER / PESERTA</small>
                            <span id="modalDetailParticipant" class="font-weight-bold text-dark"
                                style="font-size: 0.95rem;">-</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block text-uppercase font-weight-bold"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">TIM CS PENANGGUNG JAWAB</small>
                            <span id="modalDetailCs" class="font-weight-bold text-dark"
                                style="font-size: 0.9rem;">-</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block text-uppercase font-weight-bold"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">WAKTU PELAKSANAAN</small>
                            <span id="modalDetailTime" class="font-weight-bold text-primary"
                                style="font-size: 0.9rem;">-</span>
                        </div>
                    </div>

                    <button type="button" id="btnRescheduleZoom"
                        class="btn btn-primary btn-block border-0 shadow-sm py-2 font-weight-bold mb-2 btn-zoom-bant"
                        style="border-radius: 10px; font-size: 0.85rem; background: linear-gradient(45deg, #1e3c72, #2a5298); display: none;">
                        <i class="fas fa-calendar-alt mr-1"></i> Reschedule Jadwal
                    </button>
                    <button type="button" class="btn btn-secondary btn-block border-0 shadow-sm py-2 font-weight-bold"
                        onclick="$('#modalModalEventDetail').modal('hide')"
                        style="border-radius: 10px; font-size: 0.85rem;">
                        Tutup Rincian
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
     MODAL ZOOM & BANT (BAT)
     ============================================================ --}}
    <div class="modal fade" id="modalZoomBant" tabindex="-1" role="dialog" aria-labelledby="modalZoomBantLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" id="modalZoomBantDialog" role="document"
            style="max-width: 420px; transition: max-width 0.3s ease;">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius: 16px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;">
                {{-- Header --}}
                <div class="modal-header border-0 pb-3"
                    style="background: linear-gradient(135deg, #2D8CFF, #1570E0); color: #fff; padding: 16px 20px;">
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="modalZoomBantLabel"
                            style="font-size: 1.15rem; letter-spacing: 0.5px;">
                            <i class="fas fa-video mr-2"></i> Zoom & BANT<span id="zoomBantKelasPrefix"
                                style="display:none;"> - </span><span id="zoomBantKelas" style="display:none;"></span>
                        </h5>
                        <small class="d-block mt-1 opacity-75" style="font-size: 0.8rem;">
                            <span id="zoomBantNama" class="font-weight-bold">-</span> &nbsp;|&nbsp; <span
                                id="zoomBantNoWa">-</span>
                        </small>
                    </div>
                    <button type="button" class="close text-white p-0 m-0" data-dismiss="modal" aria-label="Tutup"
                        style="opacity: 1; font-size: 1.4rem; line-height: 1; outline: none; border: none; background: transparent;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-4 bg-light text-dark">
                    <input type="hidden" id="zoomBant_data_id">

                    <div class="row" id="zoomBantRowContainer">
                        <!-- Kolom Kiri: Zoom Section -->
                        <div id="zoomBantZoomCol" class="col-12">
                            {{-- Standard Zoom Section --}}
                            <div id="standardZoomSection" class="card border-0 shadow-sm mb-3"
                                style="border-radius: 12px;">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm"
                                            style="width: 36px; height: 36px; flex-shrink: 0;">
                                            <i class="fas fa-video" style="font-size: 1rem;"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold text-dark d-block"
                                                style="font-size: 0.9rem; line-height: 1.2;">Ikut Zoom</span>
                                            <small class="text-muted d-block"
                                                style="font-size: 0.72rem; line-height: 1.2;">Status keikutsertaan webinar
                                                Zoom</small>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="zoomBantIkutZoom"
                                            onchange="saveZoomBantField('ikut_zoom', this.checked ? 1 : 0)">
                                        <label class="custom-control-label" for="zoomBantIkutZoom"
                                            style="cursor: pointer;"></label>
                                    </div>
                                </div>
                            </div>

                            {{-- M1T One-on-One Zoom Scheduling Section --}}
                            <div id="m1tZoomSection" class="card border-0 shadow-sm mb-3"
                                style="border-radius: 12px; display: none; background: #f8f9fc; border: 1px dashed #2a5298 !important;">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-3" style="gap: 10px;">
                                        <div class="d-flex align-items-center justify-content-center bg-gradient-info text-white rounded-circle shadow-sm"
                                            style="width: 32px; height: 32px; flex-shrink: 0; background: linear-gradient(45deg, #36b9cc, #1a8a99);">
                                            <i class="fas fa-calendar-alt" style="font-size: 0.85rem;"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold text-dark d-block"
                                                style="font-size: 0.85rem; line-height: 1.2;">Jadwal Zoom
                                                One-on-One</span>
                                            <small class="text-muted d-block"
                                                style="font-size: 0.68rem; line-height: 1.2;">Jadwalkan Zoom khusus
                                                M1T</small>
                                        </div>
                                    </div>

                                    <!-- Hidden salesplan id -->
                                    <input type="hidden" id="zoomM1tSalesplanId">

                                    <!-- Hidden fields to ensure system compatibility -->
                                    <input type="hidden" id="zoomM1tDateTime">
                                    <input type="hidden" id="zoomM1tLink" value="">
                                    <input type="hidden" id="zoomM1tStatus" value="scheduled">

                                    <!-- Date Picker -->
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-dark mb-1">Pilih Tanggal Sesi
                                            Zoom</label>
                                        <input type="date" class="form-control form-control-sm" id="zoomM1tDate"
                                            style="border-radius: 6px;" required>
                                    </div>

                                    <!-- Session Picker -->
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-dark mb-1">Pilih Jam Sesi Zoom</label>
                                        <select class="form-control form-control-sm" id="zoomM1tSession"
                                            style="border-radius: 6px; font-weight: 600;" required>
                                            <option value="">-- Pilih Sesi --</option>
                                            <option value="09:00">Jam 9.00 - 10.00 WIB</option>
                                            <option value="11:00">Jam 11.00 - 12.00 WIB</option>
                                            <option value="13:00">Jam 13.00 - 14.00 WIB</option>
                                            <option value="15:00">Jam 15.00 - 16.00 WIB</option>
                                        </select>
                                    </div>

                                    <!-- Notes / Tindak Lanjut -->
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-dark mb-1">Catatan / Tindak
                                            Lanjut</label>
                                        <textarea class="form-control form-control-sm" id="zoomM1tNotes" rows="2"
                                            placeholder="Tulis hasil komunikasi/kesepakatan..." style="border-radius: 6px; font-size: 0.75rem;"></textarea>
                                    </div>

                                    <!-- Save button -->
                                    <button type="button" id="btnSaveZoomM1t"
                                        class="btn btn-primary btn-sm btn-block font-weight-bold py-2 shadow-sm border-0 text-white"
                                        style="border-radius: 8px; background: linear-gradient(45deg, #1e3c72, #2a5298);">
                                        Simpan Jadwal Zoom
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: BANT Section -->
                        <div id="zoomBantBantCol" class="col-12">
                            {{-- BANT Section --}}
                            <h6 id="bantHeader" class="font-weight-bold text-uppercase text-secondary mb-3 mt-4 mt-md-0"
                                style="font-size: 0.7rem; letter-spacing: 1px;">Kualifikasi Prospek (BANT)</h6>

                            {{-- Budget --}}
                            <div class="card border-0 shadow-sm mb-2" style="border-radius: 12px;">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                        <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-sm"
                                            style="width: 36px; height: 36px; flex-shrink: 0;">
                                            <i class="fas fa-wallet" style="font-size: 0.95rem;"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold text-dark d-block"
                                                style="font-size: 0.9rem; line-height: 1.2;">Budget (B)</span>
                                            <small class="text-muted d-block"
                                                style="font-size: 0.72rem; line-height: 1.2;">Dana / Anggaran sesuai
                                                kriteria</small>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="zoomBantBudget"
                                            onchange="saveZoomBantField('bant_budget', this.checked ? 1 : 0)">
                                        <label class="custom-control-label" for="zoomBantBudget"
                                            style="cursor: pointer;"></label>
                                    </div>
                                </div>
                            </div>

                            {{-- Authority --}}
                            <div class="card border-0 shadow-sm mb-2" style="border-radius: 12px;">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                        <div class="d-flex align-items-center justify-content-center bg-info text-white rounded-circle shadow-sm"
                                            style="width: 36px; height: 36px; flex-shrink: 0;">
                                            <i class="fas fa-user-shield" style="font-size: 0.95rem;"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold text-dark d-block"
                                                style="font-size: 0.9rem; line-height: 1.2;">Authority (A)</span>
                                            <small class="text-muted d-block"
                                                style="font-size: 0.72rem; line-height: 1.2;">Pengambil keputusan
                                                utama</small>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="zoomBantAuthority"
                                            onchange="saveZoomBantField('bant_authority', this.checked ? 1 : 0)">
                                        <label class="custom-control-label" for="zoomBantAuthority"
                                            style="cursor: pointer;"></label>
                                    </div>
                                </div>
                            </div>

                            {{-- Time --}}
                            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                        <div class="d-flex align-items-center justify-content-center bg-warning text-white rounded-circle shadow-sm"
                                            style="width: 36px; height: 36px; flex-shrink: 0;">
                                            <i class="fas fa-clock" style="font-size: 0.95rem;"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold text-dark d-block"
                                                style="font-size: 0.9rem; line-height: 1.2;">Time (T)</span>
                                            <small class="text-muted d-block"
                                                style="font-size: 0.72rem; line-height: 1.2;">Waktu / Timeline
                                                kebutuhan</small>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="zoomBantTime"
                                            onchange="saveZoomBantField('bant_time', this.checked ? 1 : 0)">
                                        <label class="custom-control-label" for="zoomBantTime"
                                            style="cursor: pointer;"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer bg-light border-0 justify-content-between py-3" style="padding: 12px 20px;">
                    <div class="d-flex align-items-center text-success" style="gap: 6px; font-size: 0.78rem;">
                        <i
                            class="fas fa-check-circle animate__animated animate__flash animate__infinite animate__slower"></i>
                        <span class="font-weight-bold">Auto-Save Aktif</span>
                    </div>
                    <button type="button" class="btn btn-success btn-sm px-4 shadow-sm" data-dismiss="modal"
                        style="border-radius: 20px; font-weight: bold; font-size: 0.75rem;">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // MODAL DETAIL PESERTA — JS Handler
        // ============================================================
        var _detailCurrentId = null;
        var _detailCanEdit = false;

        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.btn-detail-peserta');
            if (!btn) return;

            _detailCurrentId = btn.dataset.id;
            _detailCanEdit = btn.dataset.canEdit === '1';

            // --- Populate header info ---
            document.getElementById('detailNama').textContent = btn.dataset.nama || '-';
            document.getElementById('detailNoWa').textContent = btn.dataset.noWa || '-';
            document.getElementById('detailInputOleh').textContent = btn.dataset.inputOleh || '-';
            document.getElementById('detailUpdatedAt').textContent = btn.dataset.updatedAt || '-';
            document.getElementById('detailTglUpdate').textContent = btn.dataset.updatedAt || '-';

            // --- Riwayat Follow Up (Catatan) ---
            var fuHtml = '';
            var hasFu = false;
            for (var i = 1; i <= 10; i++) {
                var hasil = btn.dataset['fu' + i + 'Hasil'];
                var at = btn.dataset['fu' + i + 'At'];
                var tl = btn.dataset['fu' + i + 'TindakLanjut'];

                if (hasil && hasil !== '' && hasil !== 'null') {
                    hasFu = true;
                    fuHtml += '<div class="mb-3 pb-2 border-bottom">' +
                        '<div class="d-flex justify-content-between align-items-center mb-1">' +
                        '<span class="badge badge-info" style="font-size:0.65rem;">Follow Up ' + i + '</span>' +
                        '<small class="text-muted font-weight-bold">' + (at || '-') + '</small>' +
                        '</div>' +
                        '<div class="small font-weight-bold text-dark mb-1">Hasil: ' + hasil + '</div>' +
                        (tl ? '<div class="small text-muted italic">Tindak Lanjut: ' + tl + '</div>' : '') +
                        '</div>';
                }
            }

            // Add Archived History if exists
            var spinHistory = btn.dataset.keteranganSpin || '';
            if (spinHistory && spinHistory !== '' && spinHistory !== 'null' && spinHistory !== 'undefined') {
                hasFu = true;
                fuHtml +=
                    '<div class="mt-2 p-2 bg-light border-left border-info" style="border-width: 3px !important; white-space: pre-wrap; font-size: 0.75rem; border-radius: 4px;">' +
                    '<i class="fas fa-archive mr-1 text-info"></i> <strong>ARSIP CATATAN LAMA:</strong><br>' +
                    spinHistory +
                    '</div>';
            }

            if (!hasFu) {
                fuHtml = '<span class="text-muted small">Tidak ada riwayat follow up.</span>';
            }
            document.getElementById('detailFollowupHistory').innerHTML = fuHtml;

            // --- BANT + Zoom ---
            document.getElementById('detailBantBudget').checked = btn.dataset.bantBudget === '1';
            document.getElementById('detailBantAuthority').checked = btn.dataset.bantAuthority === '1';
            document.getElementById('detailBantTime').checked = btn.dataset.bantTime === '1';
            document.getElementById('detailIkutZoom').checked = btn.dataset.ikutZoom === '1';

            var disabled = !_detailCanEdit;
            document.getElementById('detailBantBudget').disabled = disabled;
            document.getElementById('detailBantAuthority').disabled = disabled;
            document.getElementById('detailBantTime').disabled = disabled;
            document.getElementById('detailIkutZoom').disabled = disabled;

            // --- Potensi ---
            var spData = JSON.parse(btn.dataset.salesplan || '[]');
            var hasParticipated = spData.some(function(sp) {
                return sp.status === 'sudah_transfer';
            });
            var labelElement = document.getElementById('detailPotensiLabel');
            if (labelElement) {
                labelElement.textContent = hasParticipated ? 'POTENSI KELAS SELANJUTNYA' : 'POTENSI KELAS PERTAMA';
            }

            var potensi = (btn.dataset.potensi || '').toUpperCase();
            var potensiSelect = document.getElementById('detailPotensiSelect');
            potensiSelect.value = potensi;
            potensiSelect.disabled = disabled;
            toggleDetailKelasDropdown(potensi, btn.dataset.kelasId, JSON.parse(btn.dataset.kelas || '[]'));


            // --- Status ---
            var statusSelect = document.getElementById('detailStatusSelect');
            var statusVal = btn.dataset.status || 'cold';
            statusSelect.value = statusVal;
            statusSelect.disabled = disabled;

            var nominalVal = parseInt(btn.dataset.nominal || '0');
            var nomInput = document.getElementById('detailNominalInput');
            var nomWrap = document.getElementById('wrapperNominalBayar');

            if (statusVal === 'sudah_transfer') {
                nomWrap.classList.remove('d-none');
                nomInput.value = nominalVal > 0 ? nominalVal.toLocaleString('id-ID') : '';
            } else {
                nomWrap.classList.add('d-none');
                nomInput.value = '';
            }

            // Still update display if needed
            var nomDiv = document.getElementById('detailNominalDisplay');
            if (statusVal === 'sudah_transfer' && nominalVal > 0) {
                nomDiv.textContent = 'Rp ' + nominalVal.toLocaleString('id-ID');
                nomDiv.classList.remove('d-none');
            } else {
                nomDiv.classList.add('d-none');
            }

            updateModalStatusAndNominalFromSelectedClass();

            // --- Salesplan history ---
            var spData = JSON.parse(btn.dataset.salesplan || '[]');
            var spHtml = '';
            if (spData.length > 0) {
                spData.forEach(function(sp) {
                    var statusColors = {
                        cold: '#6c757d',
                        tertarik: '#f08c00',
                        mau_transfer: '#0ca678',
                        sudah_transfer: '#1971c2',
                        no: '#c92a2a'
                    };
                    var spColor = statusColors[sp.status] || '#6c757d';
                    spHtml +=
                        '<div class="d-flex align-items-center justify-content-between py-1 border-bottom">' +
                        '<div class="d-flex align-items-center gap-2">' +
                        '<span class="small font-weight-bold text-dark">' + sp.kelas + '</span>' +
                        '<button type="button" class="btn btn-link p-0 text-info" style="font-size:0.65rem; text-decoration:none;" onclick="toggleDetailFollowup()">' +
                        '<i class="fas fa-comment-dots"></i> Riwayat FU' +
                        '</button>' +
                        '</div>' +
                        '<div class="d-flex align-items-center gap-2">' +
                        '<span class="badge" style="background:' + spColor +
                        '; color:#fff; border-radius:6px; font-size:0.7rem; padding:3px 8px;">' + sp.status
                        .replace('_', ' ').toUpperCase() + '</span>';
                    if (sp.nominal > 0) {
                        spHtml += '<span class="small text-primary font-weight-bold ml-1">Rp ' + parseInt(sp
                            .nominal).toLocaleString('id-ID') + '</span>';
                    }
                    spHtml += '</div></div>';
                });
            } else {
                spHtml = '<span class="text-muted small">Tidak ada riwayat kelas.</span>';
            }
            document.getElementById('detailSalesplanHistory').innerHTML = spHtml;

            // Reset Followup Wrapper (Hide by default when opening new person)
            document.getElementById('detailFollowupHistoryWrapper').classList.add('d-none');

            // Show modal
            $('#modalDetailPeserta').modal('show');
        });

        function refreshProspekData() {
            if (!_detailCurrentId) return;
            refreshProspekDataDirect(_detailCurrentId);
        }

        function refreshProspekDataDirect(id) {
            if (!id) return;

            Swal.fire({
                title: 'Reset & Arsip Data?',
                text: 'Data kualifikasi (BANT, Potensi, Status) akan di-reset dan kartu Follow Up akan dikosongkan. Catatan saat ini akan otomatis masuk ke Riwayat (Arsip).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset & Arsip!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.post('{{ route('admin.database.reuse-data') }}', {
                        _token: '{{ csrf_token() }}',
                        id: id
                    }).done(function(r) {
                        if (r.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil di-reset!',
                                text: 'Halaman akan dimuat ulang.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', r.message, 'error');
                        }
                    }).fail(function() {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                    });
                }
            });
        }

        function toggleDetailFollowup() {
            var wrapper = document.getElementById('detailFollowupHistoryWrapper');
            wrapper.classList.toggle('d-none');
        }

        function toggleDetailKelasDropdown(potensi, selectedKelasId, kelasList) {
            var wrap = document.getElementById('detailPotensiKelasWrap');
            var select = document.getElementById('detailKelasSelect');
            if (potensi === 'MBC') {
                wrap.classList.remove('d-none');
                select.innerHTML = '<option value="">- Pilih Kelas MBC -</option>';
                kelasList.forEach(function(k) {
                    var opt = document.createElement('option');
                    opt.value = k.id;
                    opt.textContent = k.nama;
                    if (String(k.id) === String(selectedKelasId)) opt.selected = true;
                    select.appendChild(opt);
                });
            } else {
                wrap.classList.add('d-none');
            }
        }

        function updateModalStatusAndNominalFromSelectedClass() {
            var select = document.getElementById('detailKelasSelect');
            var selectedKelasId = select ? select.value : '';

            // Find the status and nominal from spData
            var statusVal = 'cold';
            var nominalVal = 0;

            var spData = [];
            try {
                var btn = document.querySelector('.btn-detail-peserta[data-id="' + _detailCurrentId + '"]');
                if (btn) spData = JSON.parse(btn.dataset.salesplan || '[]');
            } catch (e) {}

            // Search for a matching class_id in spData
            var matchedSp = spData.find(function(sp) {
                return String(sp.kelas_id) === String(selectedKelasId);
            });

            if (matchedSp) {
                statusVal = matchedSp.status || 'cold';
                nominalVal = parseInt(matchedSp.nominal || '0');
            }

            // Update the modal status input
            var statusSelect = document.getElementById('detailStatusSelect');
            if (statusSelect) {
                statusSelect.value = statusVal;
            }

            // Update nominal wrapper and input
            var nomInput = document.getElementById('detailNominalInput');
            var nomWrap = document.getElementById('wrapperNominalBayar');
            var nomDiv = document.getElementById('detailNominalDisplay');

            if (nomInput && nomWrap) {
                if (statusVal === 'sudah_transfer') {
                    nomWrap.classList.remove('d-none');
                    nomInput.value = nominalVal > 0 ? nominalVal.toLocaleString('id-ID') : '';
                    if (nomDiv) {
                        nomDiv.textContent = 'Rp ' + nominalVal.toLocaleString('id-ID');
                        nomDiv.classList.remove('d-none');
                    }
                } else {
                    nomWrap.classList.add('d-none');
                    nomInput.value = '';
                    if (nomDiv) {
                        nomDiv.classList.add('d-none');
                    }
                }
            }
        }

        function saveDetailBant(field, value) {
            if (!_detailCurrentId) return;
            $.post('{{ route('admin.database.update-inline') }}', {
                _token: '{{ csrf_token() }}',
                id: _detailCurrentId,
                field: field,
                value: value
            }).done(function(r) {
                if (r.success) showDetailToast('Tersimpan!');
            });
        }

        function saveDetailPotensi() {
            if (!_detailCurrentId) return;
            var val = document.getElementById('detailPotensiSelect').value;
            var kelasList = [];
            try {
                var btn = document.querySelector('.btn-detail-peserta[data-id="' + _detailCurrentId + '"]');
                if (btn) kelasList = JSON.parse(btn.dataset.kelas || '[]');
            } catch (e) {}
            toggleDetailKelasDropdown(val.toUpperCase(), '', kelasList);

            $.post('{{ route('admin.database.update-inline') }}', {
                _token: '{{ csrf_token() }}',
                id: _detailCurrentId,
                field: 'potensi',
                value: val
            }).done(function(r) {
                if (r.success) showDetailToast('Potensi tersimpan!');
            });
        }

        function saveDetailKelas() {
            if (!_detailCurrentId) return;
            var kelasId = document.getElementById('detailKelasSelect').value;

            // Instantly update status/nominal UI to match selected class
            updateModalStatusAndNominalFromSelectedClass();

            $.post('{{ route('admin.database.update-inline') }}', {
                _token: '{{ csrf_token() }}',
                id: _detailCurrentId,
                field: 'kelas_id',
                value: kelasId
            }).done(function(r) {
                if (r.success) showDetailToast('Kelas tersimpan!');
            });
        }

        function saveDetailStatus() {
            if (!_detailCurrentId) return;
            var status = document.getElementById('detailStatusSelect').value;
            var nominal = document.getElementById('detailNominalInput').value;
            var kelasId = document.getElementById('detailKelasSelect').value;

            // Toggle Nominal Wrapper
            var wrap = document.getElementById('wrapperNominalBayar');
            if (status === 'sudah_transfer') {
                wrap.classList.remove('d-none');
            } else {
                wrap.classList.add('d-none');
            }

            $.post('{{ route('admin.database.update-status-direct') }}', {
                _token: '{{ csrf_token() }}',
                data_id: _detailCurrentId,
                kelas_id: kelasId,
                status: status,
                nominal: nominal
            }).done(function(r) {
                if (r.success) {
                    showDetailToast('Status & Nominal tersimpan!');

                    // Sync status/badge in the main table row in real-time
                    var selectInTable = document.querySelector(
                        `.inline-status-select[data-data-id="${_detailCurrentId}"][data-kelas-id="${kelasId}"]`);
                    if (selectInTable) {
                        selectInTable.value = status;
                        var statusConfig = {
                            'cold': {
                                bg: '#ffffff',
                                text: '#6c757d'
                            },
                            'tertarik': {
                                bg: '#F2F527',
                                text: '#000000'
                            },
                            'mau_transfer': {
                                bg: '#3CDE1D',
                                text: '#000000'
                            },
                            'sudah_transfer': {
                                bg: '#1786E6',
                                text: '#ffffff'
                            },
                            'no': {
                                bg: '#E61717',
                                text: '#ffffff'
                            }
                        };
                        var cfg = statusConfig[status] || statusConfig['cold'];
                        selectInTable.style.backgroundColor = cfg.bg;
                        selectInTable.style.color = cfg.text;

                        var badge = selectInTable.previousElementSibling;
                        if (badge && badge.classList.contains('badge')) {
                            badge.style.background = cfg.bg;
                            badge.style.color = cfg.text;
                            let baseText = badge.textContent.replace(' ✓', '').trim();
                            if (status === 'sudah_transfer') {
                                badge.innerHTML = baseText + ' ✓';
                            } else {
                                badge.innerHTML = baseText;
                            }
                        }
                    }

                    // --- AUTO OPEN SETTING PEMBAYARAN UNTUK M1T / MBC ---
                    if (status === 'sudah_transfer') {
                        var kelasSelect = document.getElementById('detailKelasSelect');
                        if (kelasSelect && kelasSelect.selectedIndex >= 0) {
                            var selectedText = kelasSelect.options[kelasSelect.selectedIndex].text.toUpperCase();
                            var name = document.getElementById('detailNama').textContent;

                            // Tutup modal detail agar tidak tumpang tindih
                            $('#modalDetailPeserta').modal('hide');

                            if (selectedText.includes('M1T') || selectedText.includes('MUSLIM INDONESIA')) {
                                // Tunggu sebentar agar modal pertama benar-benar tertutup baru buka yang kedua
                                setTimeout(function() {
                                    if (window.showMonthSelectionModal) {
                                        window.showMonthSelectionModal(r.plan_id, name, '', '', '',
                                            '2.000.000', '500.000', '1.500.000', '2.000.000', 'Grow Up',
                                            '');
                                    }
                                }, 500);
                            } else {
                                // Non-M1T (MBC classes) -> show nominal popup
                                setTimeout(function() {
                                    Swal.fire({
                                        title: 'Setting Pembayaran MBC',
                                        text: 'Masukkan Nominal Pembayaran untuk ' + name,
                                        input: 'text',
                                        inputPlaceholder: 'Contoh: 1.500.000',
                                        showCancelButton: true,
                                        confirmButtonText: 'Simpan',
                                        cancelButtonText: 'Batal',
                                        customClass: {
                                            confirmButton: 'btn btn-primary rounded-pill px-4',
                                            cancelButton: 'btn btn-secondary rounded-pill px-4'
                                        },
                                        buttonsStyling: false,
                                        didOpen: () => {
                                            const input = Swal.getInput();
                                            $(input).on('input', function() {
                                                let val = this.value.replace(/[^0-9]/g,
                                                    '');
                                                this.value = val ? parseInt(val)
                                                    .toLocaleString('id-ID') : '';
                                            });
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            let nominalVal = result.value.replace(/[^0-9]/g, '');
                                            if (!nominalVal) {
                                                Swal.fire('Error', 'Nominal harus diisi', 'error');
                                                return;
                                            }

                                            // Save nominal via AJAX
                                            $.ajax({
                                                url: "{{ route('admin.salesplan.update-selected-months') }}",
                                                type: "POST",
                                                data: {
                                                    _token: "{{ csrf_token() }}",
                                                    id: r.plan_id,
                                                    spp_awal: nominalVal,
                                                    nominal: nominalVal, // Also sync to SalesPlan nominal
                                                    selected_months: JSON.stringify({}),
                                                    tanggal_masuk: new Date().toISOString()
                                                        .split('T')[0]
                                                },
                                                success: function(res) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Berhasil Disimpan',
                                                        timer: 1500
                                                    });
                                                }
                                            });
                                        }
                                    });
                                }, 500);
                            }
                        }
                    }

                    // Update display if needed
                    if (status !== 'sudah_transfer') {
                        document.getElementById('detailNominalDisplay').classList.add('d-none');
                    }
                }
            });
        }

        function formatRupiah(input) {
            let value = input.value.replace(/[^,\d]/g, '');
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            input.value = rupiah;
        }

        // Tracks and updates the legend counters in real-time when prospect status changes
        function adjustLegendCounts(oldStatus, newStatus) {
            if (!oldStatus || !newStatus || oldStatus === newStatus) return;

            const statusMap = {
                'cold': 'statCountCold',
                'tertarik': 'statCountTertarik',
                'mau_transfer': 'statCountMauTransfer',
                'sudah_transfer': 'statCountSudahTransfer',
                'no': 'statCountNo'
            };

            // Decrement old status count
            const oldId = statusMap[oldStatus];
            if (oldId) {
                const oldEl = document.getElementById(oldId);
                if (oldEl) {
                    let val = parseInt(oldEl.innerText) || 0;
                    oldEl.innerText = Math.max(0, val - 1);
                }
            }

            // Increment new status count
            const newId = statusMap[newStatus];
            if (newId) {
                const newEl = document.getElementById(newId);
                if (newEl) {
                    let val = parseInt(newEl.innerText) || 0;
                    newEl.innerText = val + 1;
                }
            }

            // Update Jumlah Potensi if status changes from/to potential categories
            // Potential categories: cold, tertarik, mau_transfer
            const oldIsPotensi = ['cold', 'tertarik', 'mau_transfer'].includes(oldStatus);
            const newIsPotensi = ['cold', 'tertarik', 'mau_transfer'].includes(newStatus);

            if (oldIsPotensi && !newIsPotensi) {
                const potEl = document.getElementById('statJumlahPotensi');
                if (potEl) {
                    let val = parseInt(potEl.innerText) || 0;
                    potEl.innerText = Math.max(0, val - 1);
                }
            } else if (!oldIsPotensi && newIsPotensi) {
                const potEl = document.getElementById('statJumlahPotensi');
                if (potEl) {
                    let val = parseInt(potEl.innerText) || 0;
                    potEl.innerText = val + 1;
                }
            }

            // Update Total Belum/Sudah Ikut filter counts
            const urlParams = new URLSearchParams(window.location.search);
            const ikutKelas = urlParams.get('ikut_kelas');

            if (ikutKelas === '0') {
                // Belum Ikut view (Total Belum Ikut includes all except sudah_transfer)
                if (oldStatus !== 'sudah_transfer' && newStatus === 'sudah_transfer') {
                    const totEl = document.getElementById('statTotalFiltered');
                    if (totEl) {
                        let val = parseInt(totEl.innerText) || 0;
                        totEl.innerText = Math.max(0, val - 1);
                    }
                } else if (oldStatus === 'sudah_transfer' && newStatus !== 'sudah_transfer') {
                    const totEl = document.getElementById('statTotalFiltered');
                    if (totEl) {
                        let val = parseInt(totEl.innerText) || 0;
                        totEl.innerText = val + 1;
                    }
                }
            } else if (ikutKelas === '1') {
                // Sudah Ikut view (Total Sudah Ikut includes only sudah_transfer)
                if (oldStatus !== 'sudah_transfer' && newStatus === 'sudah_transfer') {
                    const totEl = document.getElementById('statTotalFiltered');
                    if (totEl) {
                        let val = parseInt(totEl.innerText) || 0;
                        totEl.innerText = val + 1;
                    }
                } else if (oldStatus === 'sudah_transfer' && newStatus !== 'sudah_transfer') {
                    const totEl = document.getElementById('statTotalFiltered');
                    if (totEl) {
                        let val = parseInt(totEl.innerText) || 0;
                        totEl.innerText = Math.max(0, val - 1);
                    }
                }
            }
        }

        // Delegate focus listener to keep track of the original status before the user changes it
        $(document).on('focus', '.status-direct-select, .inline-status-select', function() {
            if ($(this).data('previous-val') === undefined) {
                $(this).data('previous-val', this.value);
            }
        });

        function updateStatusDirectTable(dataId, el) {
            var status = el.value;
            var name = el.dataset.nama || 'Peserta';
            var kelasNama = el.dataset.kelasNama || '';
            var isM1T = kelasNama.toUpperCase().includes('M1T') || kelasNama.toUpperCase().includes('MUSLIM INDONESIA');

            // Read and default previous status value
            var oldStatus = $(el).data('previous-val');
            if (oldStatus === undefined) {
                oldStatus = 'cold';
            }

            // Config warna sama dengan row.blade.php
            var statusConfig = {
                'cold': {
                    bg: '#ffffff',
                    text: '#6c757d'
                },
                'tertarik': {
                    bg: '#F2F527',
                    text: '#000000'
                },
                'sudah_transfer': {
                    bg: '#1786E6',
                    text: '#ffffff'
                },
                'no': {
                    bg: '#E61717',
                    text: '#ffffff'
                }
            };

            var cfg = statusConfig[status] || statusConfig['cold'];
            el.style.backgroundColor = cfg.bg;
            el.style.color = cfg.text;

            $.post('{{ route('admin.database.update-status-direct') }}', {
                _token: '{{ csrf_token() }}',
                data_id: dataId,
                status: status,
                nominal: 0 // Default, akan diupdate di modal payment jika sudah_transfer
            }).done(function(r) {
                if (r.success) {
                    showDetailToast('Status diperbarui!');

                    // Adjust legend counts in real-time without refresh
                    adjustLegendCounts(oldStatus, status);
                    $(el).data('previous-val', status);

                    if (status === 'sudah_transfer') {
                        if (isM1T) {
                            setTimeout(function() {
                                if (window.showMonthSelectionModal) {
                                    // Buka modal pembayaran M1T (Default nominal & level Grow Up as previously requested/implemented)
                                    window.showMonthSelectionModal(r.plan_id, name, '', '', '', '2.000.000',
                                        '500.000', '1.500.000', '2.000.000', 'Grow Up', '');
                                }
                            }, 500);
                        } else {
                            // Non-M1T (MBC classes) -> show nominal popup
                            setTimeout(function() {
                                Swal.fire({
                                    title: 'Setting Pembayaran MBC',
                                    text: 'Masukkan Nominal Pembayaran untuk ' + name,
                                    input: 'text',
                                    inputPlaceholder: 'Contoh: 1.500.000',
                                    showCancelButton: true,
                                    confirmButtonText: 'Simpan',
                                    cancelButtonText: 'Batal',
                                    customClass: {
                                        confirmButton: 'btn btn-primary rounded-pill px-4',
                                        cancelButton: 'btn btn-secondary rounded-pill px-4'
                                    },
                                    buttonsStyling: false,
                                    didOpen: () => {
                                        const input = Swal.getInput();
                                        $(input).on('input', function() {
                                            let val = this.value.replace(/[^0-9]/g, '');
                                            this.value = val ? parseInt(val)
                                                .toLocaleString('id-ID') : '';
                                        });
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        let nominalVal = result.value.replace(/[^0-9]/g, '');
                                        if (!nominalVal) {
                                            Swal.fire('Error', 'Nominal harus diisi', 'error');
                                            return;
                                        }

                                        // Save nominal via AJAX
                                        $.ajax({
                                            url: "{{ route('admin.salesplan.update-selected-months') }}",
                                            type: "POST",
                                            data: {
                                                _token: "{{ csrf_token() }}",
                                                id: r.plan_id,
                                                spp_awal: nominalVal,
                                                nominal: nominalVal, // Also sync to SalesPlan nominal
                                                selected_months: JSON.stringify({}),
                                                tanggal_masuk: new Date().toISOString()
                                                    .split('T')[0]
                                            },
                                            success: function(res) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Berhasil Disimpan',
                                                    timer: 1500
                                                });
                                            }
                                        });
                                    }
                                });
                            }, 500);
                        }
                    }
                } else {
                    Swal.fire('Gagal!', r.message || 'Gagal update status', 'error');
                }
            }).fail(function() {
                Swal.fire('Error!', 'Terjadi kesalahan koneksi.', 'error');
            });
        }

        function updateInlineStatusFromTable(selectEl) {
            var dataId = selectEl.dataset.dataId;
            var kelasId = selectEl.dataset.kelasId;
            var kelasNama = selectEl.dataset.kelasNama || '';
            var newStatus = selectEl.value;
            var isM1T = kelasNama.toUpperCase().includes('M1T') || kelasNama.toUpperCase().includes('MUSLIM INDONESIA');

            // Read and default previous status value
            var oldStatus = $(selectEl).data('previous-val');
            if (oldStatus === undefined) {
                oldStatus = 'cold';
            }

            var statusConfig = {
                'cold': {
                    bg: '#ffffff',
                    text: '#6c757d'
                },
                'tertarik': {
                    bg: '#F2F527',
                    text: '#000000'
                },
                'mau_transfer': {
                    bg: '#3CDE1D',
                    text: '#000000'
                },
                'sudah_transfer': {
                    bg: '#1786E6',
                    text: '#ffffff'
                },
                'no': {
                    bg: '#E61717',
                    text: '#ffffff'
                }
            };
            var cfg = statusConfig[newStatus] || statusConfig['cold'];
            selectEl.style.backgroundColor = cfg.bg;
            selectEl.style.color = cfg.text;
            selectEl.style.opacity = '0.5';

            $.post('{{ route('admin.database.update-status-direct') }}', {
                _token: '{{ csrf_token() }}',
                data_id: dataId,
                kelas_id: kelasId,
                status: newStatus,
                nominal: ''
            }).done(function(r) {
                selectEl.style.opacity = '1';
                if (r.success) {
                    showDetailToast('Status prospek berhasil diperbarui!');

                    // Adjust legend counts in real-time without refresh
                    adjustLegendCounts(oldStatus, newStatus);
                    $(selectEl).data('previous-val', newStatus);

                    // Sync status/badge styling in real-time
                    var badge = selectEl.previousElementSibling;
                    if (badge && badge.classList.contains('badge')) {
                        badge.style.background = cfg.bg;
                        badge.style.color = cfg.text;
                        let baseText = badge.textContent.replace(' ✓', '').trim();
                        if (newStatus === 'sudah_transfer') {
                            badge.innerHTML = baseText + ' ✓';
                        } else {
                            badge.innerHTML = baseText;
                        }
                    }

                    // Auto open setting pembayaran jika status sudah_transfer
                    if (newStatus === 'sudah_transfer') {
                        var row = selectEl.closest('tr');
                        var name = row ? (row.querySelector('[data-field="nama"]')?.textContent?.trim() ||
                            'Peserta') : 'Peserta';

                        if (isM1T) {
                            setTimeout(function() {
                                if (window.showMonthSelectionModal) {
                                    window.showMonthSelectionModal(r.plan_id, name, '', '', '', '2.000.000',
                                        '500.000', '1.500.000', '2.000.000', 'Grow Up', '');
                                }
                            }, 500);
                        } else {
                            // Non-M1T (MBC classes) -> show nominal popup
                            setTimeout(function() {
                                Swal.fire({
                                    title: 'Setting Pembayaran MBC',
                                    text: 'Masukkan Nominal Pembayaran untuk ' + name,
                                    input: 'text',
                                    inputPlaceholder: 'Contoh: 1.500.000',
                                    showCancelButton: true,
                                    confirmButtonText: 'Simpan',
                                    cancelButtonText: 'Batal',
                                    customClass: {
                                        confirmButton: 'btn btn-primary rounded-pill px-4',
                                        cancelButton: 'btn btn-secondary rounded-pill px-4'
                                    },
                                    buttonsStyling: false,
                                    didOpen: () => {
                                        const input = Swal.getInput();
                                        $(input).on('input', function() {
                                            let val = this.value.replace(/[^0-9]/g, '');
                                            this.value = val ? parseInt(val)
                                                .toLocaleString('id-ID') : '';
                                        });
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        let nominalVal = result.value.replace(/[^0-9]/g, '');
                                        if (!nominalVal) {
                                            Swal.fire('Error', 'Nominal harus diisi', 'error');
                                            return;
                                        }

                                        // Save nominal via AJAX
                                        $.ajax({
                                            url: "{{ route('admin.salesplan.update-selected-months') }}",
                                            type: "POST",
                                            data: {
                                                _token: "{{ csrf_token() }}",
                                                id: r.plan_id,
                                                spp_awal: nominalVal,
                                                nominal: nominalVal, // Also sync to SalesPlan nominal
                                                selected_months: JSON.stringify({}),
                                                tanggal_masuk: new Date().toISOString()
                                                    .split('T')[0]
                                            },
                                            success: function(res) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Berhasil Disimpan',
                                                    timer: 1500
                                                });
                                            }
                                        });
                                    }
                                });
                            }, 500);
                        }
                    }
                } else {
                    Swal.fire('Gagal!', r.message || 'Gagal update status', 'error');
                }
            }).fail(function() {
                selectEl.style.opacity = '1';
                Swal.fire('Error!', 'Terjadi kesalahan koneksi.', 'error');
            });
        }

        function showInlineAddClassDropdown(btn) {
            var container = btn.closest('.inline-add-class-wrapper');
            if (container) {
                var select = container.querySelector('.inline-add-class-select');
                if (select) {
                    select.classList.remove('d-none');
                    select.focus();
                }
                btn.classList.add('d-none');
            }
        }

        function saveNewProspectFromTable(selectEl, dataId) {
            if (!selectEl.value) {
                // User cancelled, toggle back to + button
                selectEl.classList.add('d-none');
                var container = selectEl.closest('.inline-add-class-wrapper');
                if (container) {
                    var btn = container.querySelector('.btn-success');
                    if (btn) btn.classList.remove('d-none');
                }
                return;
            }

            var kelasId = selectEl.value;
            var namaKls = selectEl.options[selectEl.selectedIndex].text;
            var shortKls = namaKls.includes('Muslim Indonesia') ? 'M1T' : (namaKls.includes('Muda Indonesia') ?
                'Start-Up Muda' : namaKls);

            // Ambil nama & no_wa peserta dari btn-detail-peserta di baris yang sama
            var currentRow = selectEl.closest('tr');
            var detailBtn = currentRow ? currentRow.querySelector('.btn-detail-peserta[data-id="' + dataId + '"]') : null;
            var namaPeserta = detailBtn ? (detailBtn.dataset.nama || '') : '';
            var noWaPeserta = detailBtn ? (detailBtn.dataset.noWa || '') : '';

            selectEl.style.opacity = '0.5';
            $.post('{{ route('admin.database.update-inline') }}', {
                _token: '{{ csrf_token() }}',
                id: dataId,
                field: 'kelas_id',
                value: kelasId
            }).done(function(r) {
                selectEl.style.opacity = '1';
                if (r.success) {
                    showDetailToast('Prospek kelas baru berhasil ditambahkan!');

                    var salesplanId = r.salesplan_id || '';

                    // Build new row element — including Zoom & Follow Up buttons
                    var newElem = document.createElement('div');
                    newElem.className = 'd-flex align-items-center justify-content-center mb-1';
                    newElem.style.gap = '8px';
                    newElem.innerHTML = `
                <button type="button"
                    class="btn btn-zoom-bant p-0 d-flex align-items-center justify-content-center border-0 shadow-sm text-white"
                    style="width: 24px; height: 24px; border-radius: 6px; background: linear-gradient(45deg, #2D8CFF, #1570E0); transition: all 0.2s; flex-shrink:0;"
                    data-id="${dataId}"
                    data-nama="${namaPeserta}"
                    data-kelas-nama="${namaKls}"
                    data-salesplan-id="${salesplanId}"
                    data-schedule-date=""
                    data-schedule-link=""
                    data-schedule-status=""
                    data-schedule-notes=""
                    data-no-wa="${noWaPeserta}"
                    data-can-edit="1"
                    data-bant-budget=""
                    data-bant-authority=""
                    data-bant-time=""
                    data-ikut-zoom=""
                    title="Zoom & BANT (${shortKls})">
                    <i class="fas fa-video" style="font-size: 0.7rem;"></i>
                </button>

                <button type="button"
                    class="btn btn-primary btn-sm btn-riwayat shadow-sm border-0 px-2"
                    style="height: 24px; line-height: 1; font-size: 0.65rem; font-weight: 700; background: linear-gradient(45deg, #4e73df, #224abe); border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; flex-shrink:0;"
                    data-kelas-nama="${namaKls}"
                    data-salesplan-id="${salesplanId}"
                    data-id="${dataId}"
                    data-nama="${namaPeserta}">
                    Follow Up
                </button>

                <span class="badge shadow-sm"
                    style="background:#ffffff;color:#6c757d;font-size:0.75rem;border-radius:8px;padding:6px 12px;border:1px solid #ccc; text-wrap: normal; word-break: break-word; min-width: 110px;">
                    ${shortKls}
                </span>

                <select class="form-control form-control-sm inline-status-select shadow-sm"
                    data-data-id="${dataId}"
                    data-kelas-id="${kelasId}"
                    data-kelas-nama="${namaKls}"
                    style="width: 125px; font-size: 0.75rem; font-weight: bold; border-radius: 8px; padding: 2px 6px; height: auto; cursor: pointer; background-color: #ffffff; color: #6c757d; border: 1px solid #ccc;"
                    onchange="updateInlineStatusFromTable(this)">
                    <option value="cold" selected style="background:#ffffff; color:#6c757d;">Cold</option>
                    <option value="tertarik" style="background:#F2F527; color:#000000;">Tertarik</option>
                    <option value="mau_transfer" style="background:#3CDE1D; color:#000000;">Mau Transfer</option>
                    <option value="sudah_transfer" style="background:#1786E6; color:#ffffff;">Sudah Transfer</option>
                </select>

                <button type="button" class="btn btn-sm btn-outline-danger p-0"
                    style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; margin-left: 2px; transition: all 0.2s;"
                    onclick="deleteProspectFromTable(this, ${dataId}, ${kelasId})"
                    title="Hapus Prospek Kelas Ini">
                    <i class="fas fa-trash-alt" style="font-size: 0.65rem;"></i>
                </button>
            `;

                    // Insert it before the wrapper
                    var wrapper = selectEl.closest('.inline-add-class-wrapper');
                    if (wrapper) {
                        wrapper.parentNode.insertBefore(newElem, wrapper);
                    }

                    // Reset & toggle back the add class wrapper inputs
                    selectEl.value = '';
                    selectEl.classList.add('d-none');
                    if (wrapper) {
                        var addBtn = wrapper.querySelector('.btn-success');
                        if (addBtn) addBtn.classList.remove('d-none');
                    }
                } else {
                    Swal.fire('Gagal!', r.message || 'Gagal menambahkan kelas', 'error');
                }
            }).fail(function() {
                selectEl.style.opacity = '1';
                Swal.fire('Error!', 'Terjadi kesalahan koneksi.', 'error');
            });
        }

        function deleteProspectFromTable(btn, dataId, kelasId) {
            Swal.fire({
                title: 'Hapus Prospek Kelas?',
                text: 'Apakah Anda yakin ingin menghapus prospek kelas ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.style.opacity = '0.5';
                    btn.disabled = true;

                    $.post('{{ route('admin.database.delete-prospect-direct') }}', {
                        _token: '{{ csrf_token() }}',
                        data_id: dataId,
                        kelas_id: kelasId
                    }).done(function(r) {
                        if (r.success) {
                            showDetailToast('Prospek kelas berhasil dihapus!');
                            // Seamlessly remove the DOM row item container!
                            var rowContainer = btn.closest(
                                '.d-flex.align-items-center.justify-content-center.mb-1');
                            if (rowContainer) {
                                rowContainer.remove();
                            }
                        } else {
                            Swal.fire('Gagal!', r.message || 'Gagal menghapus prospek', 'error');
                            btn.style.opacity = '1';
                            btn.disabled = false;
                        }
                    }).fail(function() {
                        Swal.fire('Error!', 'Terjadi kesalahan koneksi.', 'error');
                        btn.style.opacity = '1';
                        btn.disabled = false;
                    });
                }
            });
        }

        function showDetailToast(msg) {
            var toast = document.createElement('div');
            toast.style.cssText =
                'position:fixed;bottom:80px;right:20px;background:#28a745;color:#fff;padding:8px 18px;border-radius:8px;font-size:0.85rem;font-weight:700;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,.2);transition:opacity .4s;';
            toast.textContent = '✓ ' + msg;
            document.body.appendChild(toast);
            setTimeout(function() {
                toast.style.opacity = '0';
                setTimeout(function() {
                    toast.remove();
                }, 400);
            }, 1500);
        }

        function handleDetailSimpan() {
            // Force blur on any active input/select to trigger AJAX auto-saves
            if (document.activeElement) {
                document.activeElement.blur();
            }

            showDetailToast('Semua data berhasil disimpan!');

            // Close the modal
            $('#modalDetailPeserta').modal('hide');

            // Reload page to reflect stats and table updates instantly
            setTimeout(function() {
                window.location.reload();
            }, 450);
        }

        // --- Filter Potensi Dynamic Dropdown ---
        $('#filterPotensi').on('change', function() {
            if ($(this).val() === 'MBC') {
                $('#filterKelasWrap').removeClass('d-none');
            } else {
                $('#filterKelasWrap').addClass('d-none');
                $('#filterKelasId').val('');
            }
        });

        // --- Prospek Card Logic ---
        window.globalProspekCounts = @json($prospekCounts ?? []);
        window.globalTotalProspek = {{ $totalProspek ?? 0 }};

        function updateCardProspek() {
            var select = document.getElementById('filterCardProspek');
            if (!select) return;

            // Trigger table filtering via the unified function
            updateFilters({
                prospek_kelas_id: select.value === 'all' ? '' : select.value
            });
        }

        function refreshCardProspekStats() {
            var select = document.getElementById('filterCardProspek');
            var stat = document.getElementById('statJumlahProspek');
            if (!select || !stat) return;

            if (select.value === 'all' || select.value === '') {
                stat.innerText = window.globalTotalProspek || 0;
            } else {
                var count = window.globalProspekCounts[select.value] || 0;
                stat.innerText = count;
            }
        }

        // Handler for showing Today's Zoom Schedule Modal
        window.modalCalendar = null;

        $(document).on('click', '#btnLihatJadwalZoomHariIni', function() {
            $('#modalJadwalZoomHariIni').modal('show');
        });

        // Initialize calendar only after modal is fully shown to ensure correct sizes
        $('#modalJadwalZoomHariIni').on('shown.bs.modal', function() {
            let calendarEl = document.getElementById('modalZoomCalendar');
            if (!calendarEl) return;

            if (!modalCalendar) {
                modalCalendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    height: 480,
                    aspectRatio: 2.1,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listMonth'
                    },
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        list: 'Daftar'
                    },
                    events: function(info, successCallback, failureCallback) {
                        let params = {
                            start: info.startStr,
                            end: info.endStr
                        };

                        let csFilter = document.getElementById('modalFilterCs');
                        if (csFilter && csFilter.value) {
                            params.cs_id = csFilter.value;
                        }

                        $.getJSON('{{ route('zoom-schedule.events') }}', params)
                            .done(function(data) {
                                successCallback(data);
                            })
                            .fail(function() {
                                failureCallback();
                            });
                    },
                    eventClick: function(info) {
                        let props = info.event.extendedProps;

                        $('#modalDetailParticipant').text(props.participant || '-');
                        $('#modalDetailCs').text(props.cs || '-');

                        let timeStr = '-';
                        if (info.event.start) {
                            timeStr = info.event.start.toLocaleDateString('id-ID', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            }) + ' @ ' + (props.time || '');
                        }
                        $('#modalDetailTime').text(timeStr);

                        // [USER_REQUEST] Populate and show Reschedule Button with proper data properties
                        let $resBtn = $('#btnRescheduleZoom');
                        if (props.data_id) {
                            $resBtn.data('id', props.data_id);
                            $resBtn.data('nama', props.participant || '');
                            $resBtn.attr('data-kelas-nama', props.kelas_nama || '');
                            $resBtn.data('no-wa', props.no_wa || '');
                            $resBtn.data('can-edit', 1);
                            $resBtn.attr('data-schedule-date', props.scheduled_at || '');
                            $resBtn.attr('data-schedule-link', props.zoom_link || '');
                            $resBtn.attr('data-schedule-status', (props.status || 'scheduled')
                                .toLowerCase());
                            $resBtn.attr('data-schedule-notes', props.notes || '');
                            $resBtn.data('salesplan-id', props.salesplan_id || '');
                            $resBtn.attr('data-ikut-zoom', props.ikut_zoom || 0);
                            $resBtn.attr('data-bant-budget', props.bant_budget || 0);
                            $resBtn.attr('data-bant-authority', props.bant_authority || 0);
                            $resBtn.attr('data-bant-time', props.bant_time || 0);
                            $resBtn.show();
                        } else {
                            $resBtn.hide();
                        }

                        let status = (props.status || 'Scheduled').toLowerCase();
                        let badge = $('#modalDetailStatusBadge');
                        badge.text(props.status || 'Scheduled');

                        if (status === 'done') {
                            badge.removeClass().addClass('badge badge-success px-2 py-1 ml-1');
                        } else if (status === 'cancelled') {
                            badge.removeClass().addClass('badge badge-danger px-2 py-1 ml-1');
                        } else {
                            badge.removeClass().addClass('badge badge-primary px-2 py-1 ml-1');
                        }

                        $('#modalModalEventDetail').modal('show');
                    }
                });

                modalCalendar.render();

                // Re-fetch events when Filter CS changes inside modal
                let csFilter = document.getElementById('modalFilterCs');
                if (csFilter) {
                    csFilter.addEventListener('change', function() {
                        modalCalendar.refetchEvents();
                    });
                }
            } else {
                modalCalendar.updateSize();
                modalCalendar.refetchEvents();
            }
        });

        $(document).on('click', '#btnRescheduleZoom', function() {
            $('#modalModalEventDetail').modal('hide');
        });
    </script>
@endsection
