@extends('layouts.masteradmin')

@section('content')
    @php
        $monthsRaw = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
    @endphp
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="sppFilterForm" method="GET" action="{{ route('peserta-smi.index') }}">
        <!-- Hidden Form for Filters -->
    </form>

    {{-- Hidden Forms for Update --}}
    @foreach($data as $item)
        <form id="form-update-{{ $item->id }}" action="{{ route('peserta-smi.update', $item->id) }}" method="POST"
            style="display: none;">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="sink-status-{{ $item->id }}" value="{{ $item->status }}">
        </form>
    @endforeach

    <div class="row">
        {{-- Filter Section --}}
        {{-- Filter Section Moved to Table Header --}}

        <style>
            .spp-menunggak-border {
                border: 2px solid #e74a3b !important;
                border-radius: 4px;
                background-color: #fdf2f2 !important;
                box-shadow: 0 0 4px rgba(231, 74, 59, 0.2);
            }

            /* Status Badges */
            .badge-status {
                padding: 5px 10px;
                border-radius: 50px;
                font-size: 0.75rem;
                font-weight: 700;
                color: white !important;
                display: inline-block;
                width: 80px;
                text-align: center;
                border: none;
                cursor: pointer;
            }

            .status-aktif {
                background-color: #1cc88a !important;
            }

            .status-cuti {
                background-color: #e74a3b !important;
            }

            .status-lulus {
                background-color: #4e73df !important;
            }

            .status-off {
                background-color: #858796 !important;
            }

            .status-pending {
                background-color: #f6c23e !important;
            }

            .status-approved {
                background-color: #1cc88a !important;
            }

            .status-rejected {
                background-color: #e74a3b !important;
            }

            .badge-status {
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                padding: 4px 12px;
                border-radius: 50px;
                font-size: 0.85rem;
                font-weight: 800;
                color: white !important;
                text-align: center;
                border: none;
                cursor: pointer;
                width: 90px;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .badge-status option {
                color: white !important;
                font-weight: bold;
            }

            .badge-status option[value="Pending"] { background-color: #f6c23e !important; }
            .badge-status option[value="Approved"] { background-color: #1cc88a !important; }
            .badge-status option[value="Rejected"] { background-color: #e74a3b !important; }

            .badge-status option:checked {
                background-color: inherit !important;
                color: white !important;
            }

            /* Styling Table */
            .table-hover tbody tr:hover {
                background-color: #f1f7fd;
                /* Soft blue on hover */
            }

            /* 📌 Sticky Header & Single Scroll Logic */
            .sticky-header-top {
                position: sticky !important;
                top: 85px !important; /* Offset for SB-Admin-2 topbar + marquee height */
                z-index: 1040 !important;
                background-color: #4e73df !important;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                border-bottom: 2px solid #2e59d9 !important;
            }

            /* Sticky table header styles (Freeze Pane) */
            #dataTable thead th {
                position: sticky !important;
                background-color: #2e59d9 !important; /* Navy Blue */
                z-index: 1035 !important;
                border: 1px solid rgba(255,255,255,0.1) !important;
                box-shadow: 0 2px 2px rgba(0,0,0,0.1);
            }

            /* No and Name main row */
            #dataTable thead tr:nth-child(1) th {
                top: 0px !important; 
            }

            /* Secondary Month row sticks below No/Name row */
            #dataTable thead tr:nth-child(2) th {
                top: 47px !important; /* Height of the first row header */
                z-index: 1034 !important;
            }

            /* Prevent parent containers from hiding the sticky elements */
            .card, .col-xl-12, .row {
                overflow: visible !important;
            }

            #content-wrapper, #wrapper {
                overflow: visible !important;
            }

            /* Ensure table container allows horizontal scroll AND vertical freeze pane */
            .table-responsive {
                overflow: auto !important;
                max-height: 65vh; /* Constraints height so vertical scroll happens INSIDE the table */
                border-bottom: 1px solid #e3e6f0;
            }

            .dummy-scroll-content {
                height: 1px;
            }

            /* SPP Expand/Collapse */
            .spp-col {
                transition: all 0.3s ease;
            }

            .spp-hidden {
                display: none !important;
            }

            #btn-toggle-spp {
                padding: 0.1rem 0.4rem;
                font-size: 0.7rem;
            }

            /* SPP Checkbox and Input Combo */
            .spp-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 2px;
                padding: 4px 2px;
            }

            .spp-checkbox {
                width: 16px;
                height: 16px;
                cursor: pointer;
                margin: 0;
                accent-color: #1cc88a;
            }

            .spp-input-small {
                font-size: 0.7rem !important;
                padding: 2px !important;
                min-width: 55px !important;
                text-align: center;
                height: 22px;
            }

            .btn-add-participant {
                font-size: 0.65rem;
                padding: 0px 4px;
                margin-left: 2px;
                opacity: 0.6;
                transition: opacity 0.2s;
            }

            .btn-add-participant:hover {
                opacity: 1;
            }

            .participant-2-hidden {
                display: none;
            }

            /* Input Styling in Table for Inline Editing */
            .table-input {
                border: 1px solid transparent;
                border-radius: 4px;
                padding: 4px 6px;
                transition: all 0.2s;
                background: transparent;
                color: #2d2d2d;
                width: 100%;
                font-weight: 500;
            }
            .table-input:hover {
                background: #f8f9fc;
                border-color: #d1d3e2;
                cursor: pointer;
            }
            .table-input:focus {
                background: #fff;
                border-color: #4e73df;
                border-width: 2px !important;
                box-shadow: 0 0 0 0.15rem rgba(78, 115, 223, 0.25);
            }
            .active-stat-row {
                background-color: rgba(78, 115, 223, 0.08) !important;
                border-left: 4px solid #4e73df !important;
            }
            .active-stat-row td {
                font-weight: 700 !important;
                color: #2e59d9 !important;
            }
            .stat-table {
                border-collapse: collapse !important;
                border: 1px solid #000000 !important;
            }
            .stat-table th, .stat-table td {
                padding: 6px 12px !important;
                vertical-align: middle !important;
                font-size: 0.82rem;
                border: 1px solid #000000 !important;
            }
            .stat-card .card-header {
                padding: 6px 16px !important;
            }
            .stat-card .card-header h6 {
                font-size: 0.88rem !important;
            }
            .stat-table thead th {
                padding-top: 6px !important;
                padding-bottom: 6px !important;
                font-size: 0.68rem !important;
                border: 1px solid #000000 !important;
                border-bottom: 2px solid #000000 !important;
            }
        </style>

        <style>
            .stat-card-premium {
                border: none;
                border-radius: 12px;
                transition: all 0.3s ease;
                overflow: hidden;
            }
            .stat-card-premium:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
            }
            .stat-icon-bg {
                position: absolute;
                right: -10px;
                bottom: -10px;
                font-size: 4rem;
                opacity: 0.1;
                transform: rotate(-15deg);
            }
            .stat-label-mini {
                font-size: 0.65rem;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                font-weight: 800;
                opacity: 0.8;
            }
            .stat-value-main {
                font-size: 1.4rem;
                line-height: 1;
            }
            .stat-value-sub {
                font-size: 0.85rem;
                font-weight: 700;
            }
            .stat-card-premium {
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, border-width 0.2s ease;
            }
            .stat-card-premium .card-body {
                padding: 0.75rem !important;
            }
            .bg-gradient-blue { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
            .bg-gradient-green { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
            .bg-gradient-red { background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); }
            .bg-gradient-info { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); }
            .bg-gradient-yellow { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); }
            .col-stat-7 {
                flex: 0 0 14.28%;
                max-width: 14.28%;
                padding-left: 6px;
                padding-right: 6px;
            }
            .stat-label-mini {
                font-size: 0.6rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .stat-value-main {
                font-size: 1.25rem;
            }
            .stat-value-sub {
                font-size: 0.8rem;
            }
            .stat-card-premium .card-body {
                padding: 0.6rem 0.75rem !important;
            }
            #smi_search::placeholder {
                color: rgba(255, 255, 255, 0.75);
            }
            #smi_search:focus {
                background: rgba(255, 255, 255, 0.25) !important;
                outline: none;
                box-shadow: none;
                color: white;
            }
            #dataTable thead th.active-spp-month {
                background-color: #f6c23e !important;
                box-shadow: inset 0 0 0 2px #f6c23e;
            }
            #dataTable thead th.active-spp-month a {
                color: #111111 !important;
                font-weight: 800 !important;
            }
        </style>

        @if(!in_array(strtolower(auth()->user()->role), ['chapter', 'reseller', 'agen']))
        <!-- Filter Card Section (At the Very Top) -->
        <div class="col-12 mb-3 @if(auth()->check() && (strtolower(auth()->user()->role) === 'administrator' || in_array(auth()->user()->name, ['Linda', 'Yasmin']))) d-none @endif">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header py-2 d-flex flex-row align-items-center justify-content-start bg-primary text-white" style="gap: 20px;">
                    <div class="d-flex align-items-center mr-2">
                        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-filter mr-2"></i>Filter Pencarian</h6>
                    </div>
                    <div class="d-flex align-items-end flex-wrap p-2 rounded w-100" style="gap: 10px; background: rgba(255,255,255,0.07);">
                        {{-- Chapter, CS Pusat & Status Peserta filters removed per user request --}}
                        {{-- Hidden inputs to keep filter values for backend if needed --}}
                        @if(strtolower(auth()->user()->role) === 'administrator' || in_array(auth()->user()->name, ['Linda', 'Yasmin']))
                        <input type="hidden" form="sppFilterForm" name="filter_chapter" id="smi_filter_chapter" value="{{ request('filter_chapter', 'all') }}">
                        <input type="hidden" form="sppFilterForm" name="filter_cs_pusat" id="smi_filter_cs_pusat" value="{{ request('filter_cs_pusat', 'all') }}">
                        @endif
                        <input type="hidden" form="sppFilterForm" name="filter_status" id="smi_filter_status" value="{{ request('filter_status', 'all') }}">

                        {{-- Approval Filter --}}
                        <div class="d-flex flex-column" style="gap: 2px;">
                            <label class="mb-0 text-white font-weight-bold" style="font-size: 0.65rem; margin-left: 2px; letter-spacing: 0.5px;">APPROVE STATUS</label>
                            <select form="sppFilterForm" name="filter_approval" id="smi_filter_approval" onchange="updateSmiFilters()" class="form-control form-control-sm border-0 bg-light text-primary font-weight-bold" style="font-size: 0.75rem; height: 30px; width: 110px;">
                                <option value="all" {{ request('filter_approval', 'all') == 'all' ? 'selected' : '' }}>ALL</option>
                                <option value="Pending" {{ request('filter_approval') == 'Pending' ? 'selected' : '' }}>PENDING</option>
                                <option value="Approved" {{ request('filter_approval') == 'Approved' ? 'selected' : '' }}>APPROVED</option>
                                <option value="Rejected" {{ request('filter_approval') == 'Rejected' ? 'selected' : '' }}>REJECTED</option>
                            </select>
                        </div>

                        {{-- SPP Bulan --}}
                        <div class="d-flex flex-column" style="gap: 2px;">
                            <label class="mb-0 text-white font-weight-bold" style="font-size: 0.65rem; margin-left: 2px; letter-spacing: 0.5px;">PEMBAYARAN SPP</label>
                            <select form="sppFilterForm" name="filter_spp_month" id="smi_filter_spp_month" onchange="updateSmiFilters()" class="form-control form-control-sm border-0 bg-light" style="width: 110px; font-size: 0.75rem; height: 30px;">
                                <option value="all" {{ request('filter_spp_month') == 'all' ? 'selected' : '' }}>ALL</option>
                                @foreach($monthsRaw as $key => $val)
                                    <option value="{{ $key }}" {{ request('filter_spp_month', date('n')) == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SPP Status --}}
                        <div class="d-flex flex-column" style="gap: 2px;">
                            <label class="mb-0 text-white font-weight-bold" style="font-size: 0.65rem; margin-left: 2px; letter-spacing: 0.5px;">STATUS SPP</label>
                            <select form="sppFilterForm" name="filter_spp_status" id="smi_filter_spp_status" onchange="updateSmiFilters()" class="form-control form-control-sm border-0 bg-light" style="width: 125px; font-size: 0.75rem; height: 30px;">
                                <option value="all" {{ request('filter_spp_status', 'all') == 'all' ? 'selected' : '' }}>ALL</option>
                                <option value="1" {{ request('filter_spp_status') === '1' ? 'selected' : '' }}>Lunas</option>
                                <option value="0" {{ request('filter_spp_status') === '0' ? 'selected' : '' }}>Belum</option>
                                <option value="blue" {{ request('filter_spp_status') === 'blue' ? 'selected' : '' }}>Closing Baru</option>
                                <option value="menunggak" {{ request('filter_spp_status') === 'menunggak' ? 'selected' : '' }}>Menunggak</option>
                                <option value="total_month" {{ request('filter_spp_status') === 'total_month' ? 'selected' : '' }}>Total Keseluruhan</option>
                            </select>
                        </div>

                        {{-- Tahun --}}
                        <div class="d-flex flex-column" style="gap: 2px;">
                            <label class="mb-0 text-white font-weight-bold" style="font-size: 0.65rem; margin-left: 2px; letter-spacing: 0.5px;">TAHUN</label>
                            <select form="sppFilterForm" name="filter_year" id="smi_filter_year" onchange="updateSmiFilters()" class="form-control form-control-sm border-0 bg-light" style="width: 80px; font-size: 0.75rem; height: 30px;">
                                <option value="all" {{ request('filter_year', 'all') == 'all' ? 'selected' : '' }}>ALL</option>
                                @for($y = date('Y') + 1; $y >= 2024; $y--)
                                    <option value="{{ $y }}" {{ request('filter_year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Level Filter (Linda Only) --}}
                        @if(auth()->user()->name === 'Linda')
                        <div class="d-flex flex-column" style="gap: 2px;">
                            <label class="mb-0 text-white font-weight-bold" style="font-size: 0.65rem; margin-left: 2px; letter-spacing: 0.5px;">LEVEL</label>
                            <select form="sppFilterForm" name="filter_level" id="smi_filter_level" onchange="updateSmiFilters()" class="form-control form-control-sm border-0 bg-light text-primary font-weight-bold" style="width: 110px; font-size: 0.75rem; height: 30px;">
                                <option value="all" {{ request('filter_level', 'all') == 'all' ? 'selected' : '' }}>ALL</option>
                                <option value="Grow Up" {{ request('filter_level') == 'Grow Up' ? 'selected' : '' }}>GROW UP</option>
                                <option value="Start Up" {{ request('filter_level') == 'Start Up' ? 'selected' : '' }}>START UP</option>
                            </select>
                        </div>
                        @endif

                        <a href="javascript:void(0)" onclick="resetSmiFilters()" class="text-white ml-1 mb-1" title="Reset Filter">
                            <i class="fas fa-sync-alt"></i>
                        </a>

                        {{-- Vertical Divider --}}
                        <div class="mx-1" style="width: 1px; height: 30px; background: rgba(255,255,255,0.2);"></div>

                        {{-- Search Block --}}
                        <div class="d-flex flex-column" style="gap: 2px;">
                            <label class="mb-0 text-white font-weight-bold" style="font-size: 0.65rem; margin-left: 2px; letter-spacing: 0.5px;">CARI NAMA</label>
                            <input type="text" name="search" id="smi_search_old" class="form-control form-control-sm border-0 bg-light smi-search-input" style="width: 150px; height: 30px; border-radius: 5px; font-size: 0.75rem;" placeholder="Cari nama..." value="{{ request('search') }}">
                        </div>
                        <button type="button" class="btn btn-warning btn-sm font-weight-bold shadow-sm px-3" onclick="updateSmiFilters()" style="height: 30px; border-radius: 5px; font-size: 0.7rem; color: #2e59d9;">
                            <i class="fas fa-search mr-1"></i> TAMPILKAN DATA
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Hidden inputs to maintain filter state for chapter/reseller/agen -->
        <input type="hidden" id="smi_filter_chapter" value="all">
        <input type="hidden" id="smi_filter_cs_pusat" value="all">
        <input type="hidden" id="smi_filter_status" value="all">
        <input type="hidden" id="smi_filter_approval" value="all">
        <input type="hidden" id="smi_filter_spp_month" value="{{ request('filter_spp_month', date('n')) }}">
        <input type="hidden" id="smi_filter_spp_status" value="all">
        <input type="hidden" id="smi_filter_year" value="{{ request('filter_year', date('Y')) }}">
        <input type="hidden" id="smi_filter_level" value="all">
        @endif


        <!-- Card Stats Section (Reorganized into 2 Tables) -->
        <div class="col-12 mb-3">
            <div class="row">
                <!-- Table 1: Membership Stats -->
                <div class="col-lg-5 mb-3">
                    <div class="card stat-card shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-gradient-primary text-white py-2 px-3 border-0 d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-users mr-2"></i>Statistik Keanggotaan</h6>
                        </div>
                        <div class="table-responsive h-100">
                            <table class="table table-bordered stat-table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light text-uppercase" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="pl-3">Status Peserta</th>
                                        <th class="text-center" style="width: 120px;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="row-stat-all" onclick="filterByStat && filterByStat('all')" style="cursor: pointer;">
                                        <td class="pl-3 font-weight-bold text-gray-800">
                                            <i class="fas fa-users text-primary mr-2" style="width: 16px;"></i>Total Peserta Keseluruhan (Aktif & OFF)
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary font-weight-bold px-2.5 py-1" id="stat-total" style="font-size: 0.85rem; border-radius: 4px;">{{ number_format($stats['total']) }}</span>
                                        </td>
                                    </tr>
                                    <tr id="row-stat-aktif" onclick="filterByStat && filterByStat('aktif')" style="cursor: pointer;">
                                        <td class="pl-3 font-weight-bold text-gray-800">
                                            <i class="fas fa-user-check text-success mr-2" style="width: 16px;"></i>Total Peserta Aktif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-success font-weight-bold px-2.5 py-1" id="stat-aktif" style="font-size: 0.85rem; border-radius: 4px;">{{ number_format($stats['aktif']) }}</span>
                                        </td>
                                    </tr>
                                    <tr id="row-stat-cuti" onclick="filterByStat && filterByStat('cuti')" style="cursor: pointer;">
                                        <td class="pl-3 font-weight-bold text-gray-800">
                                            <i class="fas fa-user-slash text-danger mr-2" style="width: 16px;"></i>Total Peserta OFF
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-danger font-weight-bold px-2.5 py-1" id="stat-cuti" style="font-size: 0.85rem; border-radius: 4px;">{{ number_format($stats['cuti']) }}</span>
                                        </td>
                                    </tr>
                                    <tr id="row-stat-lunas" onclick="filterByStat && filterByStat('lunas')" style="cursor: pointer;">
                                        <td class="pl-3 font-weight-bold text-gray-800">
                                            <i class="fas fa-check-double text-info mr-2" style="width: 16px;"></i>Peserta Lunas Keseluruhan
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info font-weight-bold px-2.5 py-1" id="stat-lunas" style="font-size: 0.85rem; border-radius: 4px;">{{ number_format($stats['lunas'] ?? 0) }}</span>
                                        </td>
                                    </tr>
                                    <tr id="row-stat-pending" onclick="filterByStat && filterByStat('pending')" style="cursor: pointer;">
                                        <td class="pl-3 font-weight-bold text-gray-800">
                                            <i class="fas fa-clock text-warning mr-2" style="width: 16px;"></i>Peserta Belum Approve
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-warning font-weight-bold px-2.5 py-1" id="stat-pending" style="font-size: 0.85rem; border-radius: 4px;">{{ number_format($stats['pending'] ?? 0) }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Table 2: Financial Stats -->
                @php
                    $monthName = $stats['filter_month_name'] ?? '';
                @endphp
                <div class="col-lg-7 mb-3">
                    <div class="card stat-card shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
                        <div class="card-header bg-gradient-info text-white py-2 px-3 border-0 d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-wallet mr-2"></i>Keuangan Keanggotaan
                            </h6>
                        </div>
                        <div class="table-responsive" style="flex: 1;">
                            <table class="table table-bordered stat-table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                <thead class="bg-light text-uppercase" style="font-size: 0.68rem; font-weight: 800; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="pl-3">Kategori</th>
                                        <th class="text-center" style="width: 90px;">Jumlah</th>
                                        <th class="text-right pr-3" style="width: 150px;">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="row-stat-closing" onclick="filterByStat && filterByStat('closing')" style="cursor: pointer;">
                                        <td class="pl-3 font-weight-bold text-gray-800">
                                            <i class="fas fa-check-double text-primary mr-2" style="width: 16px;"></i>Peserta Baru Closing <span class="stat-month-label badge badge-success text-white font-weight-bold px-2 py-0.5 ml-1" style="font-size: 0.72rem; background-color: #1cc88a; border-radius: 4px; display: {{ $monthName ? 'inline-block' : 'none' }};">{{ $monthName }}</span>
                                        </td>
                                        <td class="text-center font-weight-bold text-gray-800" id="stat-count-closing">
                                            {{ number_format($stats['count_closing'] ?? 0) }}
                                        </td>
                                        <td class="text-right pr-3 font-weight-bold text-primary" id="stat-nom-closing" style="font-size: 0.88rem;">
                                            Rp {{ number_format($stats['nominal_closing'] ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr id="row-stat-sudah_bayar" onclick="filterByStat && filterByStat('sudah_bayar')" style="cursor: pointer;">
                                        <td class="pl-3 font-weight-bold text-gray-800">
                                            <i class="fas fa-check-circle text-success mr-2" style="width: 16px;"></i>Peserta Lama Bayar SPP <span class="stat-month-label badge badge-success text-white font-weight-bold px-2 py-0.5 ml-1" style="font-size: 0.72rem; background-color: #1cc88a; border-radius: 4px; display: {{ $monthName ? 'inline-block' : 'none' }};">{{ $monthName }}</span>
                                        </td>
                                        <td class="text-center font-weight-bold text-gray-800" id="stat-count-spp">
                                            {{ number_format($stats['count_spp'] ?? 0) }}
                                        </td>
                                        <td class="text-right pr-3 font-weight-bold text-success" id="stat-nom-spp" style="font-size: 0.88rem;">
                                            Rp {{ number_format($stats['nominal_spp'] ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr style="background-color: rgba(78, 115, 223, 0.08); border-bottom: 2px solid #e3e6f0;">
                                        <td class="pl-3 font-weight-bold text-primary">
                                            <i class="fas fa-coins text-primary mr-2" style="width: 16px;"></i>Total Pembayaran Masuk
                                        </td>
                                        <td class="text-center font-weight-bold text-primary" id="stat-count-masuk">
                                            {{ number_format(($stats['count_closing'] ?? 0) + ($stats['count_spp'] ?? 0)) }}
                                        </td>
                                        <td class="text-right pr-3 font-weight-bold text-primary" id="stat-nom-masuk" style="font-size: 0.88rem;">
                                            Rp {{ number_format(($stats['nominal_closing'] ?? 0) + ($stats['nominal_spp'] ?? 0), 0, ',', '.') }}
                                        </td>
                                    </tr>

                                    <tr id="row-stat-belum_bayar" onclick="filterByStat && filterByStat('belum_bayar')" style="cursor: pointer; border-bottom: 2px solid #e3e6f0;">
                                        <td class="pl-3 font-weight-bold text-gray-800">
                                            <i class="fas fa-exclamation-circle text-warning mr-2" style="width: 16px;"></i>Potensi Belum Bayar <span class="stat-month-label badge badge-success text-white font-weight-bold px-2 py-0.5 ml-1" style="font-size: 0.72rem; background-color: #1cc88a; border-radius: 4px; display: {{ $monthName ? 'inline-block' : 'none' }};">{{ $monthName }}</span>
                                        </td>
                                        <td class="text-center font-weight-bold text-gray-800" id="stat-count-belum">
                                            {{ number_format($stats['count_belum'] ?? 0) }}
                                        </td>
                                        <td class="text-right pr-3 font-weight-bold text-warning" id="stat-nom-belum" style="font-size: 0.88rem;">
                                            Rp {{ number_format($stats['nominal_belum'] ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>

                                    <tr id="row-stat-menunggak" onclick="filterByStat && filterByStat('menunggak')" style="cursor: pointer;">
                                        <td class="pl-3 font-weight-bold text-gray-800">
                                            <i class="fas fa-history text-danger mr-2" style="width: 16px;"></i>Peserta Menunggak
                                        </td>
                                        <td class="text-center font-weight-bold text-gray-800" id="stat-count-menunggak">
                                            {{ number_format($stats['count_menunggak'] ?? 0) }}
                                        </td>
                                        <td class="text-right pr-3 font-weight-bold text-danger" id="stat-nom-menunggak" style="font-size: 0.88rem;">
                                            Rp {{ number_format($stats['nominal_menunggak'] ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @if(!in_array(strtolower(auth()->user()->role), ['chapter', 'reseller', 'agen']))
        <!-- Legend Section -->
        <div class="col-12 mb-3">
            <div class="card shadow-sm border-0 bg-white p-2">
                <div class="d-flex align-items-center flex-wrap" style="gap: 25px; font-size: 0.82rem; padding: 0 10px;">
                    <div class="d-flex align-items-center">
                        <div
                            style="width: 20px; height: 20px; background-color: #4e73df; border-radius: 4px; margin-right: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(78,115,223,0.2);">
                            <i class="fas fa-check-double text-white" style="font-size: 0.7rem;"></i>
                        </div>
                        <span class="text-dark"><b style="color: #4e73df;">Checklist Biru:</b> Pembayaran Awal (Closing) / Status LUNAS</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div
                            style="width: 20px; height: 20px; background-color: #1cc88a; border-radius: 4px; margin-right: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(28,200,138,0.2);">
                            <i class="fas fa-check-circle text-white" style="font-size: 0.7rem;"></i>
                        </div>
                        <span class="text-dark"><b style="color: #1cc88a;">Checklist Hijau:</b> Pembayaran Bulanan (Manual)</span>
                    </div>
                    
                    <button type="button" onclick="exportSmiPdf()" class="btn btn-danger btn-sm font-weight-bold ml-auto shadow-sm d-flex align-items-center" style="border-radius: 6px; font-size: 0.8rem; background-color: #e74a3b; border-color: #e74a3b; padding: 6px 12px; gap: 6px; transition: all 0.2s ease;">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
        @endif

        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-2 d-flex align-items-center bg-primary text-white sticky-header-top justify-content-between">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-list-alt mr-2"></i>Daftar Peserta M1T</h6>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <div class="position-relative" style="width: 200px;">
                            <input type="text" id="smi_search" class="form-control form-control-sm border-0 smi-search-input" 
                                style="border-radius: 20px; padding-left: 15px; padding-right: 15px; height: 32px; font-size: 0.8rem; background: rgba(255,255,255,0.15); color: white;" 
                                placeholder="Cari nama..." value="{{ request('search') }}">
                        </div>
                        <button type="button" class="btn btn-warning btn-sm font-weight-bold shadow-sm d-flex align-items-center justify-content-center" 
                            onclick="updateSmiFilters()" 
                            style="height: 32px; border-radius: 20px; font-size: 0.75rem; color: #2e59d9; padding: 0 15px;">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="dataTable" width="100%"
                            cellspacing="0" style="font-size: 0.85rem;">
                            <thead class="thead-dark-blue text-white">
                                <tr>
                                    <th rowspan="2" class="align-middle text-center border-right" style="width: 3%">No</th>
                                    <th rowspan="2" class="align-middle text-center border-right" style="min-width: 200px;">
                                        Nama Closing
                                    </th>
                                    <th rowspan="2" class="align-middle text-center border-right d-none"
                                        style="width: 8%">
                                        Status</th>
                                    <th rowspan="2" class="align-middle text-center border-right d-none"
                                        style="width: 10%">
                                        Level</th>
                                    <th rowspan="2" class="align-middle text-center border-right" style="width: 10%;">
                                        Biaya Closing Awal
                                    </th>
                                    <th rowspan="2" class="align-middle text-center border-right" style="width: 10%">
                                        @if(in_array(strtolower(auth()->user()->role), ['reseller', 'chapter']))
                                            Agen Closing
                                        @else
                                            PIC
                                        @endif
                                        <div class="mt-1">
                                            <select form="sppFilterForm" name="filter_cs_pusat" onchange="updateSmiFilters()" class="form-control form-control-sm border-0 text-center mx-auto" style="font-size: 0.6rem; height: 18px; width: 80px; background: rgba(255,255,255,0.2); color: white; border-radius: 4px; padding: 0;">
                                                <option value="all" class="text-dark">ALL</option>
                                                @foreach($listCs as $csName)
                                                    <option value="{{ $csName }}" {{ request('filter_cs_pusat') == $csName ? 'selected' : '' }} class="text-dark">{{ $csName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>

                                    <th colspan="12" id="sppMainHeader"
                                        class="text-center font-weight-bold text-uppercase border-bottom px-2">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 15px;">
                                            <!-- Left Month Navigation Button -->
                                            <button type="button" class="btn btn-link text-white p-0" onclick="navigateSppMonth(-1)" title="Bulan Sebelumnya" style="font-size: 1rem; text-decoration: none; transition: all 0.2s; cursor: pointer; opacity: 0.85;" onmouseover="this.style.opacity='1'; this.style.transform='scale(1.2)';" onmouseout="this.style.opacity='0.85'; this.style.transform='scale(1)';">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            
                                            <a href="{{ route('admin.keuangan.laba-rugi', ['bulan' => str_pad(request('filter_spp_month', 'all'), 2, '0', STR_PAD_LEFT), 'tahun' => request('filter_year', 'all')]) }}"
                                                class="text-white text-decoration-none font-weight-bold"
                                                id="sppHeaderTitle" title="Buka Laporan Laba Rugi M1T" style="letter-spacing: 0.5px;">
                                                Monitoring SPP
                                                @if(request('filter_spp_month') == 'all' || request('filter_year') == 'all')
                                                    Seluruh Periode
                                                @else
                                                    Bulan {{ $monthsRaw[request('filter_spp_month', (int) date('m'))] ?? '' }}
                                                    {{ request('filter_year', date('Y')) }}
                                                @endif
                                            </a>
                                            
                                            <!-- Right Month Navigation Button -->
                                            <button type="button" class="btn btn-link text-white p-0" onclick="navigateSppMonth(1)" title="Bulan Berikutnya" style="font-size: 1rem; text-decoration: none; transition: all 0.2s; cursor: pointer; opacity: 0.85;" onmouseover="this.style.opacity='1'; this.style.transform='scale(1.2)';" onmouseout="this.style.opacity='0.85'; this.style.transform='scale(1)';">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </th>
                                    <th rowspan="2" class="align-middle text-center border-left" style="width: 5%">Aksi</th>
                                </tr>
                                <tr>
                                    @php
                                        $bulan = [
                                            1 => 'Jan',
                                            2 => 'Feb',
                                            3 => 'Mar',
                                            4 => 'Apr',
                                            5 => 'Mei',
                                            6 => 'Jun',
                                            7 => 'Jul',
                                            8 => 'Ags',
                                            9 => 'Sep',
                                            10 => 'Okt',
                                            11 => 'Nov',
                                            12 => 'Des'
                                        ];
                                    @endphp
                                    @for($i = 1; $i <= 12; $i++)
                                        @php
                                            $activeMonth = request('filter_spp_month', date('n'));
                                            $isActive = ($activeMonth !== 'all' && (int)$activeMonth === $i);
                                        @endphp
                                        <th id="th-month-{{ $i }}" class="text-center spp-col {{ $i > 1 ? 'spp-extra' : '' }} {{ $isActive ? 'active-spp-month' : '' }}"
                                            style="min-width: 45px; width: 45px; font-size: 11px; cursor: pointer;"
                                            onclick="clickSppMonth({{ $i }})">
                                            <a href="javascript:void(0)"
                                                class="{{ $isActive ? 'text-primary' : 'text-white' }} text-decoration-none col-spp-label-{{ $i }}"
                                                title="Tampilkan Pembayaran {{ $bulan[$i] }} {{ request('filter_year', date('Y')) }}"
                                                style="display: block; width: 100%; height: 100%;">
                                                {{ $bulan[$i] }}
                                            </a>
                                        </th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody id="smiTableBody">
                                @include('admin.peserta-smi.table-rows')
                            </tbody>
                        </table>
                    </div>

                    <!-- Dummy Horizontal Scrollbar yang akan mengambang di bawah -->
                    <div id="floating-scrollbar" class="floating-scrollbar-wrapper">
                        <div id="floating-scrollbar-content" class="dummy-scroll-content"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tableContainer = document.querySelector('.table-responsive');
            const table = tableContainer.querySelector('table');
            const floatScroll = document.getElementById('floating-scrollbar');
            const floatContent = document.getElementById('floating-scrollbar-content');

            function syncFloatingScrollbar() {
                const rect = tableContainer.getBoundingClientRect();
                const windowHeight = window.innerHeight;

                // Hitung apakah tabel lebih lebar dari layar (butuh horizontal scroll)
                const needsHorizontalScroll = table.offsetWidth > tableContainer.clientWidth;

                // Tampilkan floating scrollbar JIKA:
                // 1. Bagian atas tabel sudah terlihat (top < windowHeight)
                // 2. Bagian bawah tabel masih belum terlihat / tertutup layar (bottom > windowHeight)
                // 3. Memang butuh scroll (needsHorizontalScroll)
                if (needsHorizontalScroll && rect.top < windowHeight && rect.bottom > windowHeight) {
                    floatScroll.style.display = 'block';
                    floatScroll.style.left = rect.left + 'px';
                    floatScroll.style.width = rect.width + 'px';
                    floatContent.style.width = table.offsetWidth + 'px';

                    // Supaya sinkronnya update saat muncul
                    floatScroll.scrollLeft = tableContainer.scrollLeft;
                } else {
                    floatScroll.style.display = 'none';
                }
            }

            // Sync scrolling
            floatScroll.addEventListener('scroll', function () {
                tableContainer.scrollLeft = floatScroll.scrollLeft;
            });

            tableContainer.addEventListener('scroll', function () {
                floatScroll.scrollLeft = tableContainer.scrollLeft;
            });

            // Sync on window events
            window.addEventListener('scroll', syncFloatingScrollbar);
            window.addEventListener('resize', syncFloatingScrollbar);

            // Inisiasi awal
            setTimeout(syncFloatingScrollbar, 500);

            // Prevent Enter key on table-inputs from submitting form, trigger blur instead
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && e.target.classList.contains('table-input')) {
                    e.preventDefault();
                    e.target.blur();
                }
            });
        });

        function toggleParticipant2(id) {
            const wrap1 = document.getElementById('wrapper-nama-2-' + id);
            const wrap2 = document.getElementById('wrapper-nama-asli-2-' + id);
            if (wrap1.classList.contains('participant-2-hidden')) {
                wrap1.classList.remove('participant-2-hidden');
                wrap2.classList.remove('participant-2-hidden');
            } else {
                wrap1.classList.add('participant-2-hidden');
                wrap2.classList.add('participant-2-hidden');
            }
        }

        function toggleInputRow() {
            var row = document.getElementById('inputRow');
            if (row.style.display === 'none') {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        }

        function clickSppMonth(monthNum) {
            const monthSelect = document.getElementById('smi_filter_spp_month');
            if (!monthSelect) return;
            monthSelect.value = monthNum.toString();
            updateSmiFilters();
        }

        function navigateSppMonth(direction) {
            const monthSelect = document.getElementById('smi_filter_spp_month');
            const yearSelect = document.getElementById('smi_filter_year');
            if (!monthSelect || !yearSelect) return;

            let currentMonth = monthSelect.value;
            let currentYear = yearSelect.value;

            if (currentMonth === 'all') {
                currentMonth = new Date().getMonth() + 1;
            } else {
                currentMonth = parseInt(currentMonth);
            }

            if (currentYear === 'all') {
                currentYear = new Date().getFullYear();
            } else {
                currentYear = parseInt(currentYear);
            }

            let newMonth = currentMonth + direction;
            let newYear = currentYear;

            if (newMonth < 1) {
                newMonth = 12;
                newYear -= 1;
            } else if (newMonth > 12) {
                newMonth = 1;
                newYear += 1;
            }

            monthSelect.value = newMonth.toString();

            // Check if the target year exists in the dropdown options
            let yearExists = false;
            for (let i = 0; i < yearSelect.options.length; i++) {
                if (yearSelect.options[i].value === newYear.toString()) {
                    yearExists = true;
                    break;
                }
            }

            if (yearExists) {
                yearSelect.value = newYear.toString();
            }

            // Trigger the filters to update the view
            updateSmiFilters();
        }

        function toggleSpp() {
            const header = document.getElementById('sppMainHeader');
            const extras = document.querySelectorAll('.spp-extra');
            const filterControls = document.getElementById('sppFilterControls');
            const btn = document.getElementById('btn-toggle-spp');
            const firstLabel = document.querySelector('.col-spp-label-1');

            const isCollapsed = header.getAttribute('colspan') == 1;

            if (isCollapsed) {
                // Expand
                extras.forEach(el => el.classList.remove('spp-hidden'));
                header.setAttribute('colspan', 12);
                if(filterControls) filterControls.classList.remove('spp-hidden');
                btn.innerHTML = '<i class="fas fa-compress-alt"></i>';
                firstLabel.innerHTML = 'Jan'; // Adjust if needed
                localStorage.setItem('spp_collapsed', 'false');
            } else {
                // Collapse
                extras.forEach(el => el.classList.add('spp-hidden'));
                header.setAttribute('colspan', 1);
                if(filterControls) filterControls.classList.add('spp-hidden');
                btn.innerHTML = '<i class="fas fa-expand-alt"></i>';
                firstLabel.innerHTML = 'SPP';
                localStorage.setItem('spp_collapsed', 'true');
            }
        }
        function quickUpdateField(element, id, fieldName) {
            const value = element.value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            // Visual feedback
            element.style.opacity = '0.5';

            // For status dropdown, update class
            if (fieldName === 'status') {
                element.classList.remove('status-aktif', 'status-cuti', 'status-lulus', 'status-off');
                element.classList.add('status-' + value.toLowerCase());
                const sink = document.getElementById('sink-status-' + id);
                if (sink) sink.value = value;
            }

            if (fieldName === 'approval_status') {
                element.classList.remove('status-pending', 'status-approved', 'status-rejected');
                element.classList.add('status-' + value.toLowerCase());
            }

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'PUT');
            formData.append('ajax_field', fieldName);
            formData.append(fieldName, value);
            formData.append('is_ajax', '1');
            const filterYear = document.getElementById('smi_filter_year')?.value || new Date().getFullYear();
            formData.append('filter_year', filterYear);

            fetch(`{{ url('peserta-smi') }}/${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(async response => {
                    element.style.opacity = '1';
                    if (!response.ok) {
                        const text = await response.text();
                        try {
                            const json = JSON.parse(text);
                            throw new Error(json.message || 'Server error');
                        } catch (e) {
                            throw new Error('Server returned error: ' + response.status);
                        }
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success updating ' + fieldName + ':', data);

                    // Update Date Display Real-time
                    if (data.date_field) {
                        const dateEl = document.getElementById('date_' + data.date_field + '_' + id);
                        if (dateEl) {
                            dateEl.innerText = data.date_value || '';
                        }
                    }

                    // Highlight success briefly
                    const originalColor = element.style.borderColor;
                    element.style.borderColor = '#1cc88a';
                    setTimeout(() => { element.style.borderColor = originalColor; }, 1000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    element.style.opacity = '1';
                    element.style.borderColor = '#e74a3b';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Simpan',
                        text: 'Terjadi kesalahan: ' + error.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
        }

        // Keep quickUpdateStatus legacy call if needed, but point to quickUpdateField
        function quickUpdateStatus(select, id) {
            quickUpdateField(select, id, 'status');
        }

        // DIRECT TOGGLE FOR SPP (Replaces Modal)
        function toggleSppLunasDirectly(checkbox) {
            const id = checkbox.dataset.id;
            const month = checkbox.dataset.month;
            const plannedNominal = checkbox.dataset.plannedNominal;
            // [USER_REQUEST] Use level-based nominal: Grow = 1.500.000, Start = 1.000.000
            const levelNominal = checkbox.dataset.levelNominal || '1000000';
            const input = document.getElementById(`spp_${month}_${id}`);

            if (input) {
                let newVal = '0';
                if (checkbox.checked) {
                    if (plannedNominal) {
                        newVal = plannedNominal;
                        if (newVal.indexOf('.') === -1) {
                            newVal = new Intl.NumberFormat('id-ID').format(newVal);
                        }
                    } else {
                        // Use level-based nominal
                        newVal = new Intl.NumberFormat('id-ID').format(parseInt(levelNominal));
                    }
                }

                input.value = newVal;

                if (checkbox.checked) {
                    checkbox.style.accentColor = '#1cc88a'; // Green for manual
                }

                quickUpdateField(input, id, `spp_${month}`);
                setTimeout(() => updateSmiFilters(), 500);
            }
        }

        function syncSppCheckbox(input, month, id) {
            const val = input.value.replace(/[^0-9]/g, '');
            const checkbox = document.querySelector(`.spp-checkbox[data-id="${id}"][data-month="${month}"]`);
            if (checkbox) {
                const levelNominal = parseInt(checkbox.dataset.levelNominal || '1000000');
                checkbox.checked = (parseInt(val || 0) >= levelNominal);
            }
        }

        function quickUpdateSpp(input, id, month) {
            quickUpdateField(input, id, `spp_${month}`);
        }

        function focusTanggalMasuk(id) {
            const el = document.getElementById('tgl_masuk_' + id);
            if (el) {
                el.style.backgroundColor = '#fff3cd';
                el.focus();
                // Reset color after focus
                setTimeout(() => { el.style.backgroundColor = ''; }, 2000);
            }
        }

        function toggleLunasManual(id, btn) {
            const isLunas = btn.classList.contains('btn-success');
            const newVal = isLunas ? 0 : 1;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            Swal.fire({
                title: 'Konfirmasi Lunas',
                text: `Tanda sebagai ${newVal ? 'LUNAS' : 'Belum Lunas'} manual?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1cc88a',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.disabled = true;
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('_method', 'PUT');
                    formData.append('ajax_field', 'is_lunas');
                    formData.append('is_lunas', newVal);
                    formData.append('is_ajax', '1');

                    fetch(`{{ url('admin/peserta-smi') }}/${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Status lunas berhasil diperbarui',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => updateSmiFilters());
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                            }
                        })
                        .catch(e => {
                            console.error(e);
                            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                        })
                        .finally(() => btn.disabled = false);
                }
            });
        }

        function focusTanggalSelesai(id) {
            const el = document.getElementById('tgl_selesai_' + id);
            if (el) {
                el.style.backgroundColor = '#d1e7ff';
                el.focus();
                // Reset color after focus
                setTimeout(() => { el.style.backgroundColor = ''; }, 2000);
            }
        }


        function deletePeserta(id, btn) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus data peserta ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const row = btn.closest('tr');

                    row.style.opacity = '0.5';
                    row.style.pointerEvents = 'none';

                    fetch(`{{ url('peserta-smi') }}/${id}`, {
                        method: 'POST',
                        body: new URLSearchParams({
                            '_token': csrfToken,
                            '_method': 'DELETE'
                        }),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: 'Data peserta berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                row.style.transition = 'all 0.5s ease';
                                row.style.transform = 'translateX(20px)';
                                row.style.opacity = '0';
                                setTimeout(() => {
                                    row.remove();
                                    document.querySelectorAll('#dataTable tbody tr').forEach((r, i) => {
                                        r.querySelector('td:first-child').innerText = i + 1;
                                    });
                                }, 500);
                            } else {
                                Swal.fire('Gagal', (data.message || 'Unknown error'), 'error');
                                row.style.opacity = '1';
                                row.style.pointerEvents = 'auto';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data.', 'error');
                            row.style.opacity = '1';
                            row.style.pointerEvents = 'auto';
                        });
                }
            });
        }

        function updateStatusColor(select, id) {
            // ... kept for compatibility with other fields if needed ...
            select.classList.remove('status-aktif', 'status-cuti', 'status-lulus', 'status-off');
            select.classList.add('status-' + select.value.toLowerCase());
            const sink = document.getElementById('sink-status-' + id);
            if (sink) sink.value = select.value;
        }

        // Auto-collapse on load if preference saved
        document.addEventListener('DOMContentLoaded', function () {
            if (localStorage.getItem('spp_collapsed') === 'true') {
                toggleSpp();
            }



            // Initial currency formatting
            initCurrencyInputs();

            // Enter key trigger and value syncing for search inputs (no live search)
            const searchInputs = document.querySelectorAll('.smi-search-input');
            searchInputs.forEach(input => {
                input.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        updateSmiFilters();
                    }
                });

                input.addEventListener('input', function (e) {
                    // Sync values between both inputs if both exist
                    searchInputs.forEach(otherInput => {
                        if (otherInput !== e.target) {
                            otherInput.value = e.target.value;
                        }
                    });
                });
            });
            
            // Initial active card highlighting
            highlightActiveCard();
        });

        function updateSmiFilters() {
            const container = document.getElementById('smiTableBody');
            const statTotal = document.getElementById('stat-total');
            const statAktif = document.getElementById('stat-aktif');
            const statCuti = document.getElementById('stat-cuti');

            if (!container) return;

            // Collect all filter values with safe checks
            const getVal = (id) => {
                const el = document.getElementById(id);
                return el ? el.value : 'all';
            };

            const params = new URLSearchParams({
                ajax: 1,
                filter_chapter: getVal('smi_filter_chapter'),
                filter_cs_pusat: getVal('smi_filter_cs_pusat'),
                filter_status: getVal('smi_filter_status'),
                filter_approval: getVal('smi_filter_approval'),
                filter_spp_month: getVal('smi_filter_spp_month'),
                filter_spp_status: getVal('smi_filter_spp_status'),
                filter_year: getVal('smi_filter_year'),
                filter_sort: getVal('smi_filter_sort'),
                filter_level: getVal('smi_filter_level'),
                search: getVal('smi_search')
            });

            // Visual feedback
            container.style.opacity = '0.5';

            const baseUrl = `{{ route('peserta-smi.index') }}`;
            const finalUrl = baseUrl.includes('?') ? `${baseUrl}&${params.toString()}` : `${baseUrl}?${params.toString()}`;

            fetch(finalUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Data refresh failed');
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        container.innerHTML = data.html;
                        if (statTotal) statTotal.innerText = new Intl.NumberFormat('id-ID').format(data.stats.total);
                        if (statAktif) statAktif.innerText = new Intl.NumberFormat('id-ID').format(data.stats.aktif);
                                                 if (statCuti) statCuti.innerText = new Intl.NumberFormat('id-ID').format(data.stats.cuti);
                         const statLunas = document.getElementById('stat-lunas');
                         if (statLunas) statLunas.innerText = new Intl.NumberFormat('id-ID').format(data.stats.lunas);
                         const statPending = document.getElementById('stat-pending');
                         if (statPending) statPending.innerText = new Intl.NumberFormat('id-ID').format(data.stats.pending);

                        // Update New Monthly Stats
                        const statCountClosing = document.getElementById('stat-count-closing');
                        const statNomClosing = document.getElementById('stat-nom-closing');
                        const statCountSpp = document.getElementById('stat-count-spp');
                        const statNomSpp = document.getElementById('stat-nom-spp');
                        const statCountBelum = document.getElementById('stat-count-belum');
                        const statNomBelum = document.getElementById('stat-nom-belum');
                        const statCountMenunggak = document.getElementById('stat-count-menunggak');
                        const statNomMenunggak = document.getElementById('stat-nom-menunggak');
                        
                        const monthLabelEls = document.querySelectorAll('.stat-month-label');
                        const monthCardEls = document.querySelectorAll('.stat-month-only');

                        if (data.stats.is_month_filter) {
                            const fmt = (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val);
                            const num = (val) => new Intl.NumberFormat('id-ID').format(val);

                            if (statCountClosing) statCountClosing.innerText = num(data.stats.count_closing);
                            if (statNomClosing) statNomClosing.innerText = fmt(data.stats.nominal_closing);
                            if (statCountSpp) statCountSpp.innerText = num(data.stats.count_spp);
                            if (statNomSpp) statNomSpp.innerText = fmt(data.stats.nominal_spp);
                            if (statCountBelum) statCountBelum.innerText = num(data.stats.count_belum);
                            if (statNomBelum) statNomBelum.innerText = fmt(data.stats.nominal_belum);
                            if (statCountMenunggak) statCountMenunggak.innerText = num(data.stats.count_menunggak || 0);
                            if (statNomMenunggak) statNomMenunggak.innerText = fmt(data.stats.nominal_menunggak || 0);

                            // Update A. Total Pembayaran Masuk
                            const statCountMasuk = document.getElementById('stat-count-masuk');
                            const statNomMasuk = document.getElementById('stat-nom-masuk');
                            if (statCountMasuk) statCountMasuk.innerText = num(data.stats.count_closing + data.stats.count_spp);
                            if (statNomMasuk) statNomMasuk.innerText = fmt(data.stats.nominal_closing + data.stats.nominal_spp);

                            // Update Rekap panel elements
                            const rekapTotalPeserta = document.getElementById('rekap-total-peserta');
                            const rekapSudahMasuk = document.getElementById('rekap-sudah-masuk');
                            const rekapBelumMasuk = document.getElementById('rekap-belum-masuk');
                            const rekapTunggakan = document.getElementById('rekap-tunggakan');
                            const rekapTotalPotensi = document.getElementById('rekap-total-potensi');

                            if (rekapTotalPeserta) rekapTotalPeserta.innerText = num(data.stats.count_closing + data.stats.count_spp + data.stats.count_belum);
                            if (rekapSudahMasuk) rekapSudahMasuk.innerText = fmt(data.stats.nominal_closing + data.stats.nominal_spp);
                            if (rekapBelumMasuk) rekapBelumMasuk.innerText = fmt(data.stats.nominal_belum);
                            if (rekapTunggakan) rekapTunggakan.innerText = fmt(data.stats.nominal_menunggak || 0);
                            if (rekapTotalPotensi) rekapTotalPotensi.innerText = fmt(data.stats.nominal_closing + data.stats.nominal_spp + data.stats.nominal_belum + (data.stats.nominal_menunggak || 0));

                            monthLabelEls.forEach(el => {
                                el.innerText = data.stats.filter_month_name;
                                if (data.stats.filter_month_name) {
                                    el.style.display = 'inline-block';
                                } else {
                                    el.style.display = 'none';
                                }
                            });
                            monthCardEls.forEach(el => el.classList.remove('d-none'));
                        } else {
                            monthLabelEls.forEach(el => {
                                el.innerText = "";
                                el.style.display = 'none';
                            });
                            monthCardEls.forEach(el => el.classList.add('d-none'));
                        }

                        // Update Monitoring Header
                        const sppHeaderTitle = document.getElementById('sppHeaderTitle');
                        if (sppHeaderTitle && data.spp_header) {
                            sppHeaderTitle.innerText = data.spp_header;
                        }

                        // Update active month header styling dynamically
                        const activeMonthVal = getVal('smi_filter_spp_month');
                        for (let i = 1; i <= 12; i++) {
                            const thEl = document.getElementById('th-month-' + i);
                            if (thEl) {
                                const aEl = thEl.querySelector('a');
                                if (activeMonthVal !== 'all' && parseInt(activeMonthVal) === i) {
                                    thEl.classList.add('active-spp-month');
                                    if (aEl) {
                                        aEl.classList.remove('text-white');
                                        aEl.classList.add('text-primary');
                                    }
                                } else {
                                    thEl.classList.remove('active-spp-month');
                                    if (aEl) {
                                        aEl.classList.remove('text-primary');
                                        aEl.classList.add('text-white');
                                    }
                                }
                            }
                        }
                    } else {
                        console.error('Server returned success:false', data);
                    }
                })
                .catch(err => {
                    console.error('Filter error:', err);
                })
                .finally(() => {
                    container.style.opacity = '1';
                    if (typeof initCurrencyInputs === 'function') initCurrencyInputs();
                    highlightActiveCard();
                });
        }

        function exportSmiPdf() {
            const getVal = (id) => {
                const el = document.getElementById(id);
                return el ? el.value : 'all';
            };
            const params = new URLSearchParams({
                filter_chapter: getVal('smi_filter_chapter'),
                filter_cs_pusat: getVal('smi_filter_cs_pusat'),
                filter_status: getVal('smi_filter_status'),
                filter_approval: getVal('smi_filter_approval'),
                filter_spp_month: getVal('smi_filter_spp_month'),
                filter_spp_status: getVal('smi_filter_spp_status'),
                filter_year: getVal('smi_filter_year'),
                filter_sort: getVal('smi_filter_sort'),
                filter_level: getVal('smi_filter_level'),
                search: getVal('smi_search')
            });
            window.open(`{{ route('peserta-smi.export-pdf') }}?${params.toString()}`, '_blank');
        }

        function filterByStat(type) {
            const statusSelect = document.getElementById('smi_filter_status');
            const sppStatusSelect = document.getElementById('smi_filter_spp_status');
            const sppMonthSelect = document.getElementById('smi_filter_spp_month');
            const yearSelect = document.getElementById('smi_filter_year');
            const approvalSelect = document.getElementById('smi_filter_approval');
            const defaultActiveMonth = "{{ request('filter_spp_month') && request('filter_spp_month') !== 'all' ? request('filter_spp_month') : date('n') }}";

            // Table 1 (membership stats) filters: reset month and year to 'all' to show global data
            if (['all', 'aktif', 'cuti', 'lunas', 'pending'].includes(type)) {
                if (sppMonthSelect) sppMonthSelect.value = 'all';
                if (yearSelect) yearSelect.value = 'all';
                if (sppStatusSelect) sppStatusSelect.value = 'all';

                if (type === 'all') {
                    if (statusSelect) statusSelect.value = 'all';
                    if (approvalSelect) approvalSelect.value = 'all';
                } else if (type === 'aktif') {
                    if (statusSelect) statusSelect.value = 'Aktif';
                    if (approvalSelect) approvalSelect.value = 'all';
                } else if (type === 'cuti') {
                    if (statusSelect) statusSelect.value = 'Cuti';
                    if (approvalSelect) approvalSelect.value = 'all';
                } else if (type === 'lunas') {
                    if (statusSelect) statusSelect.value = 'Lunas';
                    if (approvalSelect) approvalSelect.value = 'all';
                } else if (type === 'pending') {
                    if (statusSelect) statusSelect.value = 'all';
                    if (approvalSelect) approvalSelect.value = 'Pending';
                }
            } else {
                // Table 2 (financial stats) filters: set month from ALL to default active month
                if (sppMonthSelect && sppMonthSelect.value === 'all') {
                    sppMonthSelect.value = defaultActiveMonth;
                }
                // Also default year to current year if it's set to 'all'
                if (yearSelect && yearSelect.value === 'all') {
                    yearSelect.value = "{{ date('Y') }}";
                }

                if (type === 'closing') {
                    if (sppStatusSelect) sppStatusSelect.value = 'blue';
                    if (statusSelect) statusSelect.value = 'all';
                } else if (type === 'sudah_bayar') {
                    if (sppStatusSelect) sppStatusSelect.value = '1';
                    if (statusSelect) statusSelect.value = 'all';
                } else if (type === 'belum_bayar') {
                    if (sppStatusSelect) sppStatusSelect.value = '0';
                    if (statusSelect) statusSelect.value = 'all';
                } else if (type === 'menunggak') {
                    if (sppStatusSelect) sppStatusSelect.value = 'menunggak';
                    if (statusSelect) statusSelect.value = 'all';
                } else if (type === 'total_month') {
                    if (sppStatusSelect) sppStatusSelect.value = 'total_month';
                    if (statusSelect) statusSelect.value = 'all';
                }
            }
            updateSmiFilters();
        }

        function highlightActiveCard() {
            // Remove active highlight from all table rows
            document.querySelectorAll('[id^="row-stat-"]').forEach(row => {
                row.classList.remove('active-stat-row');
            });

            const status = document.getElementById('smi_filter_status')?.value;
            const sppStatus = document.getElementById('smi_filter_spp_status')?.value;
            const approval = document.getElementById('smi_filter_approval')?.value;

            if (approval === 'Pending') {
                document.getElementById('row-stat-pending')?.classList.add('active-stat-row');
            } else if (status === 'Aktif' && sppStatus === 'all') {
                document.getElementById('row-stat-aktif')?.classList.add('active-stat-row');
            } else if ((status === 'Cuti' || status === 'OFF' || status === 'off') && sppStatus === 'all') {
                document.getElementById('row-stat-cuti')?.classList.add('active-stat-row');
            } else if (status === 'Lunas' && sppStatus === 'all') {
                document.getElementById('row-stat-lunas')?.classList.add('active-stat-row');
            } else if (sppStatus === 'blue') {
                document.getElementById('row-stat-closing')?.classList.add('active-stat-row');
            } else if (sppStatus === '1' && status === 'all') {
                document.getElementById('row-stat-sudah_bayar')?.classList.add('active-stat-row');
            } else if (sppStatus === '0' && status === 'all') {
                document.getElementById('row-stat-belum_bayar')?.classList.add('active-stat-row');
            } else if (sppStatus === 'menunggak' && status === 'all') {
                document.getElementById('row-stat-menunggak')?.classList.add('active-stat-row');
            } else if (sppStatus === 'total_month' && status === 'all') {
                document.getElementById('row-stat-total_month')?.classList.add('active-stat-row');
            } else if (status === 'all' && sppStatus === 'all') {
                const monthVal = document.getElementById('smi_filter_spp_month')?.value;
                const isMonthActive = (monthVal && monthVal !== 'all');
                const rowId = isMonthActive ? 'row-stat-total_month' : 'row-stat-all';
                document.getElementById(rowId)?.classList.add('active-stat-row');
            }
        }



        function initCurrencyInputs() {
            document.querySelectorAll('.input-currency').forEach(input => {
                input.removeEventListener('input', currencyInputHandler);
                input.addEventListener('input', currencyInputHandler);
            });
        }

        function currencyInputHandler(e) {
            let value = this.value.replace(/[^0-9]/g, '');
            if (value) {
                this.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                this.value = '';
            }
        }

        function resetSmiFilters() {
            const ids = [
                'smi_filter_chapter',
                'smi_filter_cs_pusat',
                'smi_filter_status',
                'smi_filter_spp_month',
                'smi_filter_spp_status',
                'smi_filter_year',
                'smi_filter_sort',
                'smi_filter_approval',
                'smi_filter_level',
                'smi_search'
            ];
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    if (el.tagName === 'SELECT') el.value = 'all';
                    else el.value = '';
                }
            });
            updateSmiFilters();
        }

    </script>

    {{-- SPP NOMINAL MODAL REMOVED AS REQUESTED --}}

    {{-- ====== MODAL UPLOAD BUKTI TRANSFER ====== --}}
    <div class="modal fade" id="modalUploadBukti" tabindex="-1" role="dialog" aria-labelledby="modalUploadBuktiLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #36b9cc, #258391); border: none;">
                    <h5 class="modal-title text-white font-weight-bold" id="modalUploadBuktiLabel">
                        <i class="fas fa-upload mr-2"></i>Upload Bukti Transfer
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-1" style="font-size: 0.85rem;">Peserta: <strong id="uploadBuktiNama"></strong></p>
                    <p class="text-muted mb-3" style="font-size: 0.8rem;">Upload foto bukti transfer untuk diverifikasi oleh admin pusat.</p>

                    {{-- Preview existing bukti --}}
                    <div id="buktiPreviewWrapper" class="mb-3 text-center d-none">
                        <p class="text-muted" style="font-size: 0.78rem; margin-bottom: 4px;">Bukti saat ini:</p>
                        <img id="buktiPreviewImg" src="" alt="Bukti Transfer" class="img-fluid rounded shadow-sm" style="max-height: 160px; border: 2px solid #e3e6f0;">
                    </div>

                    <form id="formUploadBukti" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark" style="font-size: 0.85rem;">Pilih Bukti Transfer (Gambar / PDF) <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputBuktiTransfer" name="bukti_transfer"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,application/pdf" required onchange="previewSelectedFile(this)">
                                <label class="custom-file-label" for="inputBuktiTransfer">Pilih file...</label>
                            </div>
                            <small class="text-muted">Format: JPG, PNG, GIF, PDF. Maks. 5MB.</small>
                        </div>
                        {{-- New file preview --}}
                        <div id="newFilePreview" class="text-center mb-2 d-none">
                            <img id="newFilePreviewImg" src="" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height: 150px; border: 2px dashed #f6c23e;">
                            <p class="text-muted mt-1" style="font-size: 0.75rem;">Preview foto baru</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-info btn-sm font-weight-bold shadow-sm" id="btnSubmitUploadBukti" onclick="submitUploadBukti()">
                        <i class="fas fa-cloud-upload-alt mr-1"></i>Upload Bukti
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== MODAL VIEW BUKTI TRANSFER (Admin) ====== --}}
    <div class="modal fade" id="modalViewBukti" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #4e73df, #224abe); border: none;">
                    <h5 class="modal-title text-white font-weight-bold">
                        <i class="fas fa-image mr-2"></i>Bukti Transfer — <span id="viewBuktiNama"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-4">
                    <img id="viewBuktiImg" src="" alt="Bukti Transfer" class="img-fluid rounded shadow" style="max-height: 500px; border: 2px solid #e3e6f0;">
                    <div class="mt-3">
                        <a id="viewBuktiDownloadLink" href="" target="_blank" download class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download mr-1"></i>Unduh Gambar
                        </a>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let _currentUploadId = null;

        function openUploadBukti(id, nama, currentBuktiUrl) {
            _currentUploadId = id;
            document.getElementById('uploadBuktiNama').textContent = nama;
            document.getElementById('inputBuktiTransfer').value = '';
            document.querySelector('#formUploadBukti .custom-file-label').textContent = 'Pilih file...';
            document.getElementById('newFilePreview').classList.add('d-none');
            const newPdfIcon = document.getElementById('newFilePreviewPdfIcon');
            if (newPdfIcon) newPdfIcon.classList.add('d-none');
            const newPreviewImg = document.getElementById('newFilePreviewImg');
            newPreviewImg.src = '';
            newPreviewImg.classList.remove('d-none');

            const previewWrapper = document.getElementById('buktiPreviewWrapper');
            const previewImg = document.getElementById('buktiPreviewImg');
            if (currentBuktiUrl) {
                const isPdf = currentBuktiUrl.toLowerCase().endsWith('.pdf');
                if (isPdf) {
                    previewImg.src = '';
                    previewImg.classList.add('d-none');
                    let pdfLink = document.getElementById('buktiPreviewPdfLink');
                    if (!pdfLink) {
                        pdfLink = document.createElement('a');
                        pdfLink.id = 'buktiPreviewPdfLink';
                        pdfLink.target = '_blank';
                        pdfLink.className = 'btn btn-outline-danger btn-sm mb-2';
                        pdfLink.innerHTML = '<i class="far fa-file-pdf mr-1"></i>Lihat PDF';
                        previewWrapper.appendChild(pdfLink);
                    }
                    pdfLink.href = currentBuktiUrl;
                    pdfLink.classList.remove('d-none');
                } else {
                    previewImg.src = currentBuktiUrl;
                    previewImg.classList.remove('d-none');
                    const pdfLink = document.getElementById('buktiPreviewPdfLink');
                    if (pdfLink) pdfLink.classList.add('d-none');
                }
                previewWrapper.classList.remove('d-none');
            } else {
                previewWrapper.classList.add('d-none');
                const pdfLink = document.getElementById('buktiPreviewPdfLink');
                if (pdfLink) pdfLink.classList.add('d-none');
            }

            $('#modalUploadBukti').modal('show');
        }

        function previewSelectedFile(input) {
            const label = input.nextElementSibling;
            if (input.files && input.files[0]) {
                label.textContent = input.files[0].name;
                const file = input.files[0];
                const isPdf = file.name.toLowerCase().endsWith('.pdf');
                
                const previewWrapper = document.getElementById('newFilePreview');
                const previewImg = document.getElementById('newFilePreviewImg');
                let pdfIcon = document.getElementById('newFilePreviewPdfIcon');
                
                if (isPdf) {
                    previewImg.src = '';
                    previewImg.classList.add('d-none');
                    if (!pdfIcon) {
                        pdfIcon = document.createElement('div');
                        pdfIcon.id = 'newFilePreviewPdfIcon';
                        pdfIcon.className = 'mb-2';
                        pdfIcon.innerHTML = '<i class="far fa-file-pdf fa-3x text-danger"></i><p class="small text-muted mt-1">File PDF Terpilih</p>';
                        previewWrapper.insertBefore(pdfIcon, previewWrapper.firstChild);
                    }
                    pdfIcon.classList.remove('d-none');
                    previewWrapper.classList.remove('d-none');
                } else {
                    if (pdfIcon) pdfIcon.classList.add('d-none');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewImg.classList.remove('d-none');
                        previewWrapper.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            }
        }

        function submitUploadBukti() {
            if (!_currentUploadId) return;

            const fileInput = document.getElementById('inputBuktiTransfer');
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.fire({ icon: 'warning', title: 'Pilih File', text: 'Silakan pilih file bukti transfer terlebih dahulu.', timer: 2000, showConfirmButton: false });
                return;
            }

            const btn = document.getElementById('btnSubmitUploadBukti');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah...';

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('bukti_transfer', fileInput.files[0]);

            fetch(`{{ url('peserta-smi') }}/${_currentUploadId}/upload-bukti`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text().then(text => ({ ok: res.ok, text })))
            .then(({ ok, text }) => {
                $('#modalUploadBukti').modal('hide');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-cloud-upload-alt mr-1"></i>Upload Bukti';
                if (ok) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Bukti transfer berhasil diunggah. Menunggu persetujuan admin.', timer: 2500, showConfirmButton: false })
                        .then(() => updateSmiFilters());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal Upload', text: 'Terjadi kesalahan. Pastikan ukuran file tidak lebih dari 5MB.', timer: 3000, showConfirmButton: false });
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-cloud-upload-alt mr-1"></i>Upload Bukti';
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan.', timer: 2000, showConfirmButton: false });
            });
        }

        function viewBuktiTransfer(url, nama) {
            const isPdf = url.toLowerCase().endsWith('.pdf');
            if (isPdf) {
                window.open(url, '_blank');
            } else {
                document.getElementById('viewBuktiNama').textContent = nama;
                document.getElementById('viewBuktiImg').src = url;
                document.getElementById('viewBuktiDownloadLink').href = url;
                $('#modalViewBukti').modal('show');
            }
        }
    </script>

    {{-- ====== MODAL DETAIL TRANSAKSI (Linda) ====== --}}
    @if(auth()->check() && auth()->user()->name === 'Linda')
    <div class="modal fade" id="modalDetailTransaksi" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-gradient-info text-white border-0">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-money-bill-wave mr-2"></i>Detail Transaksi Pembayaran
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-3" style="font-size: 0.85rem;">Peserta: <strong id="dtNama"></strong></p>
                    <form id="formDetailTransaksi">
                        <input type="hidden" id="dtId">
                        
                        <div class="form-group mb-3 border p-3 rounded" style="background-color: #f8f9fc;">
                            <label class="font-weight-bold text-dark" style="font-size: 0.8rem;">SPP Awal</label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label style="font-size: 0.75rem;">Nominal</label>
                                    <input type="text" class="form-control input-currency" id="dtSppAwal" onkeyup="calculateTotalDetail()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label style="font-size: 0.75rem;">Tanggal Transaksi</label>
                                    <input type="date" class="form-control" id="dtTanggalSppAwal">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3 border p-3 rounded" style="background-color: #f8f9fc;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="font-weight-bold text-dark mb-0" style="font-size: 0.8rem;">Pembayaran SPP (Cicilan)</label>
                                <button type="button" class="btn btn-sm btn-primary py-0 px-2" onclick="addSppCicilanRow()" style="font-size: 0.7rem;"><i class="fas fa-plus"></i> Tambah Cicilan</button>
                            </div>
                            <div id="sppCicilanContainer">
                                <!-- Dynamic rows -->
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-primary" style="font-size: 0.85rem;">Total Pembayaran Keseluruhan</label>
                            <input type="text" class="form-control input-currency border-primary text-primary font-weight-bold" id="dtTotalPembayaran" readonly style="background-color: #eaecf4;">
                            <small class="text-muted mt-1 d-block">Total biaya closing yang akan ditampilkan di tabel utama. (Otomatis Dihitung)</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success btn-sm font-weight-bold" onclick="saveDetailTransaksi()">
                        <i class="fas fa-save mr-1"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let sppCicilanCount = 0;
        let originalSppAwalBulan = [];
        let originalSppCicilanData = [];

        function addSppCicilanRow(data = null) {
            sppCicilanCount++;
            const id = sppCicilanCount;
            const container = document.getElementById('sppCicilanContainer');
            
            const nominal = data ? data.nominal : '';
            const tanggal = data ? data.tanggal : '';

            const html = `
            <div class="spp-cicilan-row border p-2 mb-2 bg-white rounded position-relative" id="sppCicilanRow_${id}">
                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 5px; right: 5px; padding: 0.1rem 0.3rem; font-size: 0.7rem;" onclick="removeSppCicilanRow(${id})"><i class="fas fa-times"></i></button>
                <div class="row mt-2">
                    <div class="col-md-6 mb-2">
                        <label style="font-size: 0.75rem;">Nominal Cicilan</label>
                        <input type="text" class="form-control input-currency nominal-cicilan" id="cicilanNominal_${id}" value="${nominal}" onkeyup="calculateTotalDetail()">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label style="font-size: 0.75rem;">Tanggal Transaksi</label>
                        <input type="date" class="form-control" id="cicilanTanggal_${id}" value="${tanggal}">
                    </div>
                </div>
            </div>`;
            
            container.insertAdjacentHTML('beforeend', html);
            
            // Re-bind currency inputs
            initCurrencyInputs();
        }

        function removeSppCicilanRow(id) {
            document.getElementById(`sppCicilanRow_${id}`).remove();
            calculateTotalDetail();
        }

        function calculateTotalDetail() {
            const parseVal = (str) => parseInt((str || '0').replace(/[^0-9]/g, '')) || 0;
            const fmt = (val) => new Intl.NumberFormat('id-ID').format(val);
            
            let total = 0;
            total += parseVal(document.getElementById('dtSppAwal').value);
            
            document.querySelectorAll('.nominal-cicilan').forEach(el => {
                total += parseVal(el.value);
            });
            
            document.getElementById('dtTotalPembayaran').value = total > 0 ? fmt(total) : '';
        }

        function openDetailTransaksi(id, nama, biayaPendaftaran, sppAwal, pembayaranSpp, totalPembayaran) {
            document.getElementById('dtId').value = id;
            document.getElementById('dtNama').textContent = nama;
            
            const fmt = (val) => {
                if(!val || isNaN(val) || val == '0') return '';
                return new Intl.NumberFormat('id-ID').format(val);
            };
            
            document.getElementById('dtSppAwal').value = fmt(sppAwal);
            
            // Clear previous data
            document.getElementById('dtTanggalSppAwal').value = '';
            document.getElementById('sppCicilanContainer').innerHTML = '';
            sppCicilanCount = 0;
            
            // Fetch the details from server to populate correctly
            fetch(`{{ url('admin/peserta-smi') }}/${id}/detail-transaksi`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success && data.data) {
                    const dt = data.data;
                    document.getElementById('dtTanggalSppAwal').value = dt.tanggal_spp_awal || '';
                    if(dt.detail_pembayaran_spp && Array.isArray(dt.detail_pembayaran_spp)) {
                        dt.detail_pembayaran_spp.forEach(c => addSppCicilanRow(c));
                    }
                }
                // Fallback: If no cicilan details but there's a legacy total pembayaran_spp, just add one row
                if(sppCicilanCount === 0 && pembayaranSpp > 0) {
                    addSppCicilanRow({ nominal: fmt(pembayaranSpp) });
                }
                
                calculateTotalDetail();
            })
            .catch(e => {
                console.error(e);
                // Fallback: If no cicilan details but there's a legacy total pembayaran_spp, just add one row
                if(sppCicilanCount === 0 && pembayaranSpp > 0) {
                    addSppCicilanRow({ nominal: fmt(pembayaranSpp) });
                }
                calculateTotalDetail();
            });

            $('#modalDetailTransaksi').modal('show');
        }

        function saveDetailTransaksi() {
            const id = document.getElementById('dtId').value;
            const parseVal = (str) => parseInt((str || '0').replace(/[^0-9]/g, '')) || 0;
            
            const sppAwal = parseVal(document.getElementById('dtSppAwal').value);
            const tanggalSppAwal = document.getElementById('dtTanggalSppAwal').value;
            
            const detailCicilan = [];
            let totalCicilan = 0;
            
            document.querySelectorAll('.spp-cicilan-row').forEach(row => {
                const rowId = row.id.split('_')[1];
                const nom = parseVal(document.getElementById(`cicilanNominal_${rowId}`).value);
                const tgl = document.getElementById(`cicilanTanggal_${rowId}`).value;
                
                if (nom > 0 || tgl) {
                    detailCicilan.push({
                        nominal: nom,
                        tanggal: tgl
                    });
                    totalCicilan += nom;
                }
            });
            
            const totalPembayaran = sppAwal + totalCicilan;

            const btn = document.querySelector('#modalDetailTransaksi .btn-success');
            const originalBtnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'PUT');
            formData.append('is_ajax', '1');
            formData.append('ajax_field', 'detail_transaksi');
            
            formData.append('spp_awal', sppAwal);
            formData.append('tanggal_spp_awal', tanggalSppAwal);
            formData.append('pembayaran_spp', totalCicilan);
            formData.append('detail_pembayaran_spp', JSON.stringify(detailCicilan));
            formData.append('total_pembayaran', totalPembayaran);

            fetch(`{{ url('peserta-smi') }}/${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update table UI immediately
                    const trTotal = document.getElementById('biaya_closing_display_' + id);
                    if (trTotal) {
                        trTotal.innerText = new Intl.NumberFormat('id-ID').format(totalPembayaran);
                    }
                    
                    // You might also need to update the onclick function arguments to keep them in sync
                    const btnDetail = document.querySelector(`button[onclick^="openDetailTransaksi(${id}"]`);
                    if(btnDetail) {
                         const nama = document.getElementById('dtNama').textContent;
                         btnDetail.setAttribute('onclick', `openDetailTransaksi(${id}, '${nama.replace(/'/g, "\\'")}', '0', '${sppAwal}', '${totalCicilan}', '${totalPembayaran}')`);
                    }

                    $('#modalDetailTransaksi').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Detail transaksi berhasil disimpan', timer: 1500, showConfirmButton: false });
                    
                    // Trigger filter update to refresh the table if needed
                    setTimeout(() => updateSmiFilters(), 500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan' });
                }
            })
            .catch(e => {
                console.error(e);
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan sistem' });
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
            });
        }
    </script>
    @endif
@endsection