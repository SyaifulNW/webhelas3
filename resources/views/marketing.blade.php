@extends('layouts.masteradmin')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-chart-line me-2"></i>
            Marketing Performance Dashboard - Helas Corporation
        </h4>
        <span class="badge bg-gradient-success fs-6 px-3 py-2 shadow-sm text-white">
            {{ now()->format('F Y') }}
        </span>
    </div>

    @if(stripos($userName, 'Felmi') !== false)
    <!-- Dual Panel Navigation Pills (Only for Felmi) -->
    <div class="mb-4">
        <ul class="nav nav-pills shadow-sm p-1 bg-white rounded-pill border" style="width: fit-content;" id="performanceTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active rounded-pill px-4 fw-bold" id="performance-tab" data-toggle="tab" data-bs-toggle="tab" href="#performance-panel" role="tab" aria-controls="performance-panel" aria-selected="true">
                    <i class="fas fa-chart-line mr-2"></i> Marketing Performance Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 fw-bold" id="kpi-tab" data-toggle="tab" data-bs-toggle="tab" href="#kpi-panel" role="tab" aria-controls="kpi-panel" aria-selected="false">
                    <i class="fas fa-key mr-2"></i> Key Performance Index
                </a>
            </li>
        </ul>
    </div>
    @endif

    @if(stripos($userName, 'Felmi') !== false)
    <div class="tab-content" id="performanceTabContent">
        <!-- PANEL 1: Marketing Performance Dashboard -->
        <div class="tab-pane fade show active" id="performance-panel" role="tabpanel" aria-labelledby="performance-tab">
    @endif

    {{-- Filter & Action Section --}}
    <div class="card shadow-sm mb-4 border-0 rounded-4">
        <div class="card-body p-4 bg-white">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
                
                {{-- Left Side: Actions --}}
                <div class="d-flex align-items-center gap-3">
                    @if(!$isAdministrator)
                    <button type="button" id="btnAddRow" class="btn btn-primary px-4 rounded-pill shadow-sm d-flex align-items-center gap-2 transition-all">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span class="fw-bold">TAMBAH BARIS</span>
                    </button>
                    @endif
                    
                    <a href="{{ route('marketing.export-pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'status' => $status, 'marketing_user_id' => $selectedMarketingUserId]) }}" 
                       class="btn btn-danger px-4 rounded-pill shadow-sm d-flex align-items-center gap-2 transition-all">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span class="fw-bold">EXPORT PDF</span>
                    </a>
                </div>

                {{-- Right Side: Filters --}}
                <form action="{{ route('marketing') }}" method="GET" id="filterForm" class="d-flex flex-wrap align-items-end gap-3">
                    
                    @if($isAdministrator)
                    <div class="filter-item">
                        <label class="small fw-bold text-muted mb-2 px-1 text-uppercase letter-spacing-1">Pilih Tim Marketing</label>
                        <select name="marketing_user_id" class="form-select border-0 shadow-sm rounded-3 bg-light px-3" style="min-width: 180px; height: 42px;" onchange="this.form.submit()">
                            @foreach($marketingUsers as $mUser)
                                <option value="{{ $mUser->id }}" {{ $selectedMarketingUserId == $mUser->id ? 'selected' : '' }}>
                                    {{ $mUser->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="filter-item">
                        <label class="small fw-bold text-muted mb-2 px-1 text-uppercase letter-spacing-1">Bulan</label>
                        <select name="bulan" class="form-select border-0 shadow-sm rounded-3 bg-light px-3" style="min-width: 140px; height: 42px;" onchange="this.form.submit()">
                            <option value="all" {{ $bulan == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <label class="small fw-bold text-muted mb-2 px-1 text-uppercase letter-spacing-1">Tahun</label>
                        <select name="tahun" class="form-select border-0 shadow-sm rounded-3 bg-light px-3" style="min-width: 110px; height: 42px;" onchange="this.form.submit()">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <label class="small fw-bold text-muted mb-2 px-1 text-uppercase letter-spacing-1">Status</label>
                        <select name="status" class="form-select border-0 shadow-sm rounded-3 bg-light px-3" style="min-width: 150px; height: 42px;" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Terlaksana" {{ $status == 'Terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                            <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Ditunda" {{ $status == 'Ditunda' ? 'selected' : '' }}>Ditunda</option>
                            <option value="Dibatalkan" {{ $status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Performance --}}
    <div class="card shadow-lg rounded-4 overflow-hidden mb-5 border-0">
        <div class="card-header py-4 text-center" style="background: linear-gradient(135deg, #ffff00, #ffec00); border-bottom: 2px solid #eee;">
            <h5 class="mb-0 fw-bolder text-dark letter-spacing-2 text-uppercase">
                @if(stripos($userName, 'Nisa') !== false)
                    DASHBOARD PERFORMANCE SOSMED SPESIALIS MARKETING ({{ $userName }})
                @elseif(stripos($userName, 'Felmi') !== false)
                    DASHBOARD PERFORMANCE EVENT MARKETING ({{ $userName }})
                @else
                    DASHBOARD PERFORMANCE MARKETING ({{ $userName }})
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 text-center table-hover custom-table" id="performanceTable">
                    <thead>
                        <tr class="fw-bold bg-light-blue text-dark text-uppercase small letter-spacing-1">
                            <th style="width: 50px;">NO</th>
                            <th>{{ stripos($userName, 'Nisa') !== false ? 'Event Zoom' : 'Nama Event' }}</th>
                            <th>Tema</th>
                            <th class="sortable" data-column="3" style="cursor: pointer;">
                                Pemateri <i class="fa-solid fa-sort ms-1 opacity-50"></i>
                            </th>
                            <th class="sortable" data-column="4" style="width: 150px; cursor: pointer;">
                                Tanggal <i class="fa-solid fa-sort ms-1 opacity-50"></i>
                            </th>
                            <th>Lokasi</th>
                            @if(stripos($userName, 'Felmi') === false)
                            <th>Jenis Event</th>
                            @endif
                            <th style="width: 100px;">Target Peserta</th>
                            <th style="width: 100px;">Peserta Hadir</th>
                            <th style="width: 100px;">Target Closing</th>
                            <th style="width: 100px;">Real Closing</th>
                            <th style="width: 160px;">Selisih</th>
                            <th class="sortable" data-column="last" style="width: 200px; cursor: pointer;">
                                Aksi <i class="fa-solid fa-sort ms-1 opacity-50"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($performances as $i => $perf)
                        <tr data-id="{{ $perf->id }}">
                            <td class="bg-light fw-bold text-muted row-number">{{ $i + 1 }}</td>
                            @if(stripos($userName, 'Felmi') !== false)
                            <td class="p-1">
                                <select class="form-select form-select-sm border-0 fw-bold event-name-select" data-field="event_name" {{ $isAdministrator ? 'disabled' : '' }}>
                                    <option value="E-Fest" {{ $perf->event_name == 'E-Fest' ? 'selected' : '' }}>E-Fest</option>
                                    <option value="Up Rev" {{ $perf->event_name == 'Up Rev' ? 'selected' : '' }}>Up Rev</option>
                                </select>
                            </td>
                            @else
                            <td class="text-start px-3 {{ !$isAdministrator ? 'editable' : '' }}" data-field="event_name" {{ !$isAdministrator ? 'contenteditable=true' : '' }}>{{ $perf->event_name }}</td>
                            @endif
                            <td class="text-start px-3 {{ !$isAdministrator ? 'editable' : '' }}" data-field="tema" {{ !$isAdministrator ? 'contenteditable=true' : '' }}>{{ $perf->tema }}</td>
                            <td class="text-start px-3 {{ !$isAdministrator ? 'editable' : '' }}" data-field="pemateri" {{ !$isAdministrator ? 'contenteditable=true' : '' }}>{{ $perf->pemateri }}</td>
                            <td>
                                <input type="date" class="form-control form-control-sm border-0 bg-transparent text-center date-input font-outfit" 
                                       value="{{ $perf->tanggal ? \Carbon\Carbon::parse($perf->tanggal)->format('Y-m-d') : '' }}"
                                       data-field="tanggal" {{ $isAdministrator ? 'disabled' : '' }}>
                            </td>
                            <td class="{{ !$isAdministrator ? 'editable' : '' }}" data-field="lokasi" {{ !$isAdministrator ? 'contenteditable=true' : '' }}>{{ $perf->lokasi }}</td>
                            @if(stripos($userName, 'Felmi') === false)
                            <td class="{{ !$isAdministrator ? 'editable' : '' }}" data-field="jenis_event" {{ !$isAdministrator ? 'contenteditable=true' : '' }}>{{ $perf->jenis_event }}</td>
                            @endif
                            <td class="bg-soft-yellow fw-bold">{{ $perf->target_peserta }}</td>
                            <td class="{{ !$isAdministrator ? 'editable' : '' }}" data-field="peserta_hadir" {{ !$isAdministrator ? 'contenteditable=true' : '' }}>{{ $perf->peserta_hadir ?: '' }}</td>
                            <td class="bg-soft-yellow fw-bold">{{ $perf->target_closing }}</td>
                            @if(stripos($userName, 'Felmi') !== false)
                                <td class="fw-bold text-primary">{{ $perf->real_closing ?: '0' }}</td>
                            @else
                                <td class="{{ !$isAdministrator ? 'editable' : '' }} fw-bold text-primary" data-field="real_closing" {{ !$isAdministrator ? 'contenteditable=true' : '' }}>{{ $perf->real_closing ?: '' }}</td>
                            @endif
                            <td class="selisih-val fw-bold {{ $perf->selisih < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $perf->selisih ?: '0' }}
                            </td>
                            <td>
                                <div class="action-container">
                                    @php
                                        $statusClass = '';
                                        if($perf->status == 'Terlaksana') $statusClass = 'status-terlaksana';
                                        elseif($perf->status == 'Pending') $statusClass = 'status-pending';
                                        elseif($perf->status == 'Ditunda') $statusClass = 'status-ditunda';
                                        elseif($perf->status == 'Dibatalkan') $statusClass = 'status-dibatalkan';
                                    @endphp
                                    <select class="form-select form-select-sm border-0 text-center fw-bold status-select status-select-precise font-outfit {{ $statusClass }}" 
                                            data-field="status" {{ $isAdministrator ? 'disabled' : '' }}>
                                        <option value="Terlaksana" {{ $perf->status == 'Terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                                        <option value="Pending" {{ $perf->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Ditunda" {{ $perf->status == 'Ditunda' ? 'selected' : '' }}>Ditunda</option>
                                        <option value="Dibatalkan" {{ $perf->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                    
                                    @if(!$isAdministrator)
                                    <form action="{{ route('marketing.delete', $perf->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        <button type="button" class="btn btn-delete-precise btn-delete shadow-sm" title="Hapus Data">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row text-center">
                            <td colspan="{{ stripos($userName, 'Felmi') !== false ? 12 : 13 }}" class="py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fa-solid fa-calendar-plus fa-3x mb-3 opacity-25"></i>
                                    <span class="fs-5">Belum ada data performance di periode ini.</span>
                                    @if(!$isAdministrator)
                                    <p class="small">Klik <strong>TAMBAH BARIS</strong> untuk mulai menginput data.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(stripos($userName, 'Felmi') !== false)
        </div> {{-- End of PANEL 1 --}}
        
        <!-- PANEL 2: Key Performance Index (Felmi Only) -->
        <div class="tab-pane fade" id="kpi-panel" role="tabpanel" aria-labelledby="kpi-tab">
            
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-0">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="m-0 font-weight-bold text-gray-900">
                                <i class="fas fa-key text-primary me-2"></i> Key Performance Index - Mba Felmi (Sosmed)
                            </h5>
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end align-items-center mt-2 mt-md-0" style="gap: 10px;">
                            {{-- Bulan --}}
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="text-xs font-weight-bold text-muted mb-0">Bulan:</label>
                                <select id="kpiMonth" class="form-select form-select-sm border-dark rounded font-weight-bold" style="width: 130px; height: 32px; border: 1.5px solid #000 !important; font-size: 0.85rem;" onchange="updateKpiPeriod()">
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5" selected>Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            {{-- Tahun --}}
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="text-xs font-weight-bold text-muted mb-0">Tahun:</label>
                                <select id="kpiYear" class="form-select form-select-sm border-dark rounded font-weight-bold" style="width: 95px; height: 32px; border: 1.5px solid #000 !important; font-size: 0.85rem;" onchange="updateKpiPeriod()">
                                    <option value="2025">2025</option>
                                    <option value="2026" selected>2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    {{-- Alert Box Instructions --}}
                    <div class="alert alert-info border-0 shadow-sm rounded-lg py-3 px-4 mb-4 d-flex align-items-center" style="background-color: rgba(52, 152, 219, 0.1);">
                        <i class="fas fa-info-circle text-info fa-2x mr-3"></i>
                        <div class="text-dark small" style="line-height: 1.5;">
                            Masukkan data <strong>Realisasi</strong> Anda di bawah ini. Target Angka dan Realisasi disimpan otomatis untuk setiap kombinasi <strong>Bulan</strong> dan <strong>Tahun</strong> yang dipilih!
                        </div>
                    </div>

                    {{-- Performance Status and Legend Bar --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 px-1" style="gap: 15px;">
                        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                            <span class="text-muted small font-weight-bold">Status Performa:</span>
                            <span id="kpi-status-badge" class="badge badge-secondary px-3 py-2 shadow-sm rounded-pill font-weight-bold" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                                BELUM DIISI
                            </span>
                        </div>
                        
                        {{-- Legend Tiers --}}
                        <div class="d-flex flex-wrap align-items-center" style="gap: 8px; font-size: 0.75rem;">
                            <span class="text-muted font-weight-bold mr-1">Legenda:</span>
                            <span class="badge bg-soft-success text-success border-success rounded-pill px-2 py-1 font-weight-bold border" style="border-width: 1.5px !important;">
                                96 - 100 Sangat Baik
                            </span>
                            <span class="badge bg-soft-primary text-primary border-primary rounded-pill px-2 py-1 font-weight-bold border" style="border-width: 1.5px !important;">
                                76 - 95 Baik
                            </span>
                            <span class="badge bg-soft-warning text-warning border-warning rounded-pill px-2 py-1 font-weight-bold border text-dark" style="border-width: 1.5px !important; color: #856404 !important; background-color: rgba(243, 156, 18, 0.1) !important;">
                                60 - 75 Cukup
                            </span>
                            <span class="badge bg-soft-orange text-orange border-orange rounded-pill px-2 py-1 font-weight-bold border" style="border-width: 1.5px !important;">
                                40 - 59 Pembinaan
                            </span>
                            <span class="badge bg-soft-danger text-danger border-danger rounded-pill px-2 py-1 font-weight-bold border" style="border-width: 1.5px !important;">
                                < 40 Underperformance
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-dark" style="border: 2px solid #000 !important; font-size: 0.95rem; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                            <thead>
                                <tr style="background-color: #FFFF00 !important; color: #000000 !important; border: 2px solid #000 !important;">
                                    <th class="text-center font-weight-bold py-3 text-uppercase align-middle" style="border: 2px solid #000 !important; width: 5%; font-size: 0.95rem; color: #000;">
                                        NO
                                    </th>
                                    <th class="text-center font-weight-bold py-3 text-uppercase align-middle" style="border: 2px solid #000 !important; width: 23%; font-size: 0.95rem; letter-spacing: 0.5px; color: #000;">
                                        AREA KERJA
                                    </th>
                                    <th class="text-center font-weight-bold py-3 text-uppercase align-middle" style="border: 2px solid #000 !important; width: 36%; font-size: 0.95rem; letter-spacing: 0.5px; color: #000;">
                                        Lag KPI (Hasil Akhir) Leading Indicator (Penggerak)
                                    </th>
                                    <th class="text-center font-weight-bold py-3 text-uppercase align-middle" style="border: 2px solid #000 !important; width: 18%; font-size: 0.95rem; letter-spacing: 0.5px; color: #000;">
                                        Target Akhir
                                    </th>
                                    <th class="text-center font-weight-bold py-3 text-uppercase align-middle" style="border: 2px solid #000 !important; width: 18%; font-size: 0.95rem; letter-spacing: 0.5px; color: #000;">
                                        Realisasi
                                    </th>
                                    <th class="text-center font-weight-bold py-3 text-uppercase align-middle" style="border: 2px solid #000 !important; width: 18%; font-size: 0.95rem; letter-spacing: 0.5px; color: #000;">
                                        Nilai
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="kpi-table-body">
                                {{-- ROW 1: Sosmed (Merged Area Kerja & NO column spans 3 rows) --}}
                                <tr style="border: 2px solid #000 !important;" data-row-id="sosmed_leads">
                                    <td rowspan="3" class="text-center font-weight-bold align-middle py-3 px-2" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        1
                                    </td>
                                    <td rowspan="3" class="font-weight-bold align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        Sosial Media
                                    </td>
                                    <td class="align-middle py-3 px-4" style="border: 2px solid #000 !important; font-weight: 500;">
                                        Lead masuk ke Cs baik via DM / WA Target 50/bulan untuk lead baru
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 py-0 target-num text-center mx-auto" style="width: 75px; height: 32px; border: 1.5px solid #000 !important; font-weight: bold;" value="50" oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 realisasi-num text-center mx-auto" style="width: 90px; border: 2px solid #000 !important; font-weight: bold; font-size: 1rem;" placeholder="Input..." oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #fcfcfc;">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 2px;">
                                            <div class="nilai-result h4 font-weight-bold mb-0 text-secondary">-</div>
                                            <span class="h5 font-weight-bold text-gray-600 mb-0 label-persen d-none">%</span>
                                        </div>
                                    </td>
                                </tr>
                                
                                {{-- ROW 2: Sosmed Followers (Belongs to Sosmed, spans from Row 1) --}}
                                <tr style="border: 2px solid #000 !important;" data-row-id="sosmed_followers">
                                    <td class="align-middle py-3 px-4" style="border: 2px solid #000 !important; font-weight: 500;">
                                        Growth Followers Pertumbuhan followers IG & TikTok 100 Followers baru per bulan
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 py-0 target-num text-center mx-auto" style="width: 75px; height: 32px; border: 1.5px solid #000 !important; font-weight: bold;" value="100" oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 realisasi-num text-center mx-auto" style="width: 90px; border: 2px solid #000 !important; font-weight: bold; font-size: 1rem;" placeholder="Input..." oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #fcfcfc;">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 2px;">
                                            <div class="nilai-result h4 font-weight-bold mb-0 text-secondary">-</div>
                                            <span class="h5 font-weight-bold text-gray-600 mb-0 label-persen d-none">%</span>
                                        </div>
                                    </td>
                                </tr>

                                {{-- ROW 3: Sosmed Posting (Belongs to Sosmed, spans from Row 1) --}}
                                <tr style="border: 2px solid #000 !important;" data-row-id="sosmed_posting">
                                    <td class="align-middle py-3 px-4" style="border: 2px solid #000 !important; font-weight: 500;">
                                        Jumlah posting sesuai content plan 3 konten/hari
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 py-0 target-num text-center mx-auto" style="width: 75px; height: 32px; border: 1.5px solid #000 !important; font-weight: bold;" value="90" oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 realisasi-num text-center mx-auto" style="width: 90px; border: 2px solid #000 !important; font-weight: bold; font-size: 1rem;" placeholder="Input..." oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #fcfcfc;">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 2px;">
                                            <div class="nilai-result h4 font-weight-bold mb-0 text-secondary">-</div>
                                            <span class="h5 font-weight-bold text-gray-600 mb-0 label-persen d-none">%</span>
                                        </div>
                                    </td>
                                </tr>

                                {{-- ROW 4: Konten Kelas dan Testimoni (Merged Area Kerja & NO column spans 2 rows) --}}
                                <tr style="border: 2px solid #000 !important;" data-row-id="konten_testimoni">
                                    <td rowspan="2" class="text-center font-weight-bold align-middle py-3 px-2" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        2
                                    </td>
                                    <td rowspan="2" class="font-weight-bold align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        Konten Kelas dan Testimoni
                                    </td>
                                    <td class="align-middle py-3 px-4" style="border: 2px solid #000 !important; font-weight: 500;">
                                        Testimoni minimal 3 video / kelas
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 py-0 target-num text-center mx-auto" style="width: 75px; height: 32px; border: 1.5px solid #000 !important; font-weight: bold;" value="3" oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 realisasi-num text-center mx-auto" style="width: 90px; border: 2px solid #000 !important; font-weight: bold; font-size: 1rem;" placeholder="Input..." oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #fcfcfc;">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 2px;">
                                            <div class="nilai-result h4 font-weight-bold mb-0 text-secondary">-</div>
                                            <span class="h5 font-weight-bold text-gray-600 mb-0 label-persen d-none">%</span>
                                        </div>
                                    </td>
                                </tr>

                                {{-- ROW 5: Video Event (Belongs to Konten Kelas dan Testimoni, spans from Row 4) --}}
                                <tr style="border: 2px solid #000 !important;" data-row-id="konten_video_event">
                                    <td class="align-middle py-3 px-4" style="border: 2px solid #000 !important; font-weight: 500;">
                                        Video kelas dan video event 3/hari
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 py-0 target-num text-center mx-auto" style="width: 75px; height: 32px; border: 1.5px solid #000 !important; font-weight: bold;" value="90" oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important;">
                                        <input type="number" class="form-control form-control-sm px-2 realisasi-num text-center mx-auto" style="width: 90px; border: 2px solid #000 !important; font-weight: bold; font-size: 1rem;" placeholder="Input..." oninput="onInputChanged(this)">
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #fcfcfc;">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 2px;">
                                            <div class="nilai-result h4 font-weight-bold mb-0 text-secondary">-</div>
                                            <span class="h5 font-weight-bold text-gray-600 mb-0 label-persen d-none">%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr style="border: 2px solid #000 !important; background-color: #FFFF00 !important; color: #000 !important;">
                                    <td colspan="5" class="text-end font-weight-bold py-3 px-4 align-middle text-uppercase" style="border: 2px solid #000 !important; font-size: 1rem; color: #000;">
                                        TOTAL NILAI (RATA-RATA)
                                    </td>
                                    <td class="text-center align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #fcfcfc;">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 2px;">
                                            <div id="total-nilai-result" class="h3 font-weight-bold mb-0 text-secondary">-</div>
                                            <span id="total-label-persen" class="h4 font-weight-bold text-gray-800 mb-0 d-none">%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
        </div> {{-- End of PANEL 2 --}}
    </div>
    @endif

</div>

{{-- Styles --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
    
    :root {
        --primary-blue: #4DABF7;
        --soft-blue: #f0f7ff;
        --excel-blue: #cddff3;
        --premium-yellow: #ffff00;
        --dark-text: #2d3436;
    }

    body {
        font-family: 'Outfit', sans-serif;
        background-color: #f0f2f5;
        color: var(--dark-text);
    }

    .letter-spacing-1 { letter-spacing: 1px; }
    .letter-spacing-2 { letter-spacing: 2px; }
    
    .font-outfit { font-family: 'Outfit', sans-serif; }
    
    .bg-light-blue { background-color: var(--excel-blue) !important; }
    .bg-soft-yellow { background-color: #fffde7 !important; }
    
    .transition-all { transition: all 0.2s ease-in-out; }
    .transition-all:hover { transform: translateY(-2px); filter: brightness(1.05); }

    .custom-table { border: 1px solid #e0e0e0; }
    .custom-table thead th {
        background-color: var(--excel-blue);
        border: 1px solid #b8c6d4;
        padding: 12px 8px;
    }

    .editable { outline: none; transition: background 0.2s; cursor: text; }
    .editable:hover { background-color: #fff9c4 !important; }
    .editable:focus { background-color: #fff !important; box-shadow: inset 0 0 0 2px var(--primary-blue); }

    .card { border-radius: 1.2rem !important; }
    
    .form-select, .form-control { border-radius: 8px; }
    .form-select:focus, .form-control:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.1); }

    .shadow-icon { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }
    
    /* Status Badge Colors */
    .status-select { 
        color: white !important; 
        font-weight: 700 !important;
        border-radius: 20px !important;
        padding: 4px 12px !important;
        cursor: pointer;
        outline: none;
        appearance: none; /* Hide default arrow to style better */
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='white' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 10px 10px;
        padding-right: 24px !important;
    }
    .status-select option { color: #333; background: white; }
    
    .status-terlaksana { background-color: #28a745 !important; border: none; }
    .status-pending { background-color: #17a2b8 !important; border: none; }
    .status-ditunda { background-color: #fd7e14 !important; border: none; }
    .status-dibatalkan { background-color: #dc3545 !important; border: none; }

    /* Action Column Refinement */
    .action-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 4px 8px;
    }

    .btn-delete-precise {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: #fff1f0;
        border: 1px solid #ffa39e;
        color: #f5222d;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .btn-delete-precise:hover {
        background-color: #f5222d;
        color: #ffffff;
        border-color: #f5222d;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(245, 34, 45, 0.2);
    }

    .status-select-precise {
        min-width: 125px;
        height: 32px;
        font-size: 0.75rem !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
    }

    /* Felmi KPI CSS */
    .font-weight-bold { font-weight: 700 !important; }
    .rounded-lg { border-radius: 10px !important; }
    
    /* Premium Nav Pills Style */
    .nav-pills .nav-link {
        color: #4e73df;
        transition: all 0.3s ease;
        border-radius: 50px;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
        color: #fff !important;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.25);
    }
    
    /* Soft Legend Badges & Custom Colors */
    .text-orange { color: #fd7e14 !important; }
    .bg-soft-orange { background-color: rgba(253, 126, 20, 0.1); border-color: rgba(253, 126, 20, 0.3) !important; }
    .border-orange { border-color: #fd7e14 !important; }
    
    .bg-soft-success { background-color: rgba(46, 204, 113, 0.1); }
    .bg-soft-primary { background-color: rgba(78, 115, 223, 0.1); }
    .bg-soft-warning { background-color: rgba(243, 156, 18, 0.1); }
    .bg-soft-danger  { background-color: rgba(231, 76, 60, 0.1); }
    
    .border-success { border-color: #2ecc71 !important; }
    .border-primary { border-color: #4e73df !important; }
    .border-warning { border-color: #f39c12 !important; }
    .border-danger { border-color: #e74c3c !important; }
    
    .badge-warning-orange {
        background: linear-gradient(135deg, #fd7e14 0%, #d96302 100%) !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(253, 126, 20, 0.2);
    }
</style>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const btnAddRow = document.getElementById('btnAddRow');
        const tableBody = document.querySelector('#performanceTable tbody');
        const isFelmi = {{ stripos($userName, 'Felmi') !== false ? 'true' : 'false' }};
        const isAdministrator = {{ $isAdministrator ? 'true' : 'false' }};

        if (btnAddRow) {
            btnAddRow.addEventListener('click', function() {
                btnAddRow.disabled = true;
                btnAddRow.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> MEMPROSES...';

                fetch("{{ route('marketing.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        // Remove empty row if exists
                        const emptyRow = tableBody.querySelector('.empty-row');
                        if (emptyRow) emptyRow.remove();

                        const count = tableBody.querySelectorAll('tr').length + 1;
                        const perf = res.data;
                        
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-id', perf.id);
                        newRow.innerHTML = `
                            <td class="bg-light fw-bold text-muted row-number">${count}</td>
                            ${isFelmi ? `
                            <td class="p-1">
                                <select class="form-select form-select-sm border-0 fw-bold event-name-select" data-field="event_name">
                                    <option value="E-Fest" ${perf.event_name == 'E-Fest' ? 'selected' : ''}>E-Fest</option>
                                    <option value="Up Rev" ${perf.event_name == 'Up Rev' ? 'selected' : ''}>Up Rev</option>
                                </select>
                            </td>
                            ` : `
                            <td class="text-start px-3 editable" data-field="event_name" contenteditable="true">${perf.event_name}</td>
                            `}
                            <td class="text-start px-3 editable" data-field="tema" contenteditable="true">${perf.tema || ''}</td>
                            <td class="text-start px-3 editable" data-field="pemateri" contenteditable="true">${perf.pemateri || ''}</td>
                            <td>
                                <input type="date" class="form-control form-control-sm border-0 bg-transparent text-center date-input font-outfit" 
                                       value="${res.formatted_date}" data-field="tanggal">
                            </td>
                            <td class="editable" data-field="lokasi" contenteditable="true">${perf.lokasi}</td>
                            ${!isFelmi ? `<td class="editable" data-field="jenis_event" contenteditable="true">${perf.jenis_event}</td>` : ''}
                            <td class="bg-soft-yellow fw-bold">${perf.target_peserta}</td>
                            <td class="editable" data-field="peserta_hadir" contenteditable="true"></td>
                            <td class="bg-soft-yellow fw-bold">${perf.target_closing}</td>
                            <td class="${!isFelmi ? 'editable' : ''} fw-bold text-primary" ${!isFelmi ? 'data-field="real_closing" contenteditable="true"' : ''}>${isFelmi ? '0' : ''}</td>
                            <td class="selisih-val fw-bold text-success">${perf.target_peserta}</td>
                            <td>
                                <div class="action-container">
                                    <select class="form-select form-select-sm border-0 text-center fw-bold status-select status-select-precise font-outfit status-terlaksana" data-field="status">
                                        <option value="Terlaksana" selected>Terlaksana</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Ditunda">Ditunda</option>
                                        <option value="Dibatalkan">Dibatalkan</option>
                                    </select>
                                    <form action="{{ url('marketing/delete') }}/${perf.id}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        <button type="button" class="btn btn-delete-precise btn-delete shadow-sm" title="Hapus Data">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(newRow);
                        
                        // Re-bind listeners for new elements
                        bindEvents(newRow);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Baris baru ditambahkan di paling bawah',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                })
                .finally(() => {
                    btnAddRow.disabled = false;
                    btnAddRow.innerHTML = '<i class="fa-solid fa-plus-circle"></i> <span class="fw-bold">TAMBAH BARIS</span>';
                });
            });
        }

        // --- DELEGATED EVENTS OR REBIND ---
        function bindEvents(row) {
            if (isAdministrator) return; // Don't bind events if admin

            // Editables
            row.querySelectorAll('.editable').forEach(cell => {
                cell.addEventListener('blur', () => updateData(cell));
                cell.addEventListener('keydown', (e) => { if(e.key==='Enter'){ e.preventDefault(); cell.blur(); } });
            });
            // Date
            const dateInput = row.querySelector('.date-input');
            if (dateInput) dateInput.addEventListener('change', (e) => updateData(e.target));
            // Event Name Select
            const eventNameSelect = row.querySelector('.event-name-select');
            if (eventNameSelect) eventNameSelect.addEventListener('change', (e) => updateData(e.target));
            // Status
            const statusSelect = row.querySelector('.status-select');
            if (statusSelect) {
                statusSelect.addEventListener('change', (e) => {
                    const select = e.target;
                    select.classList.remove('status-terlaksana', 'status-pending', 'status-ditunda', 'status-dibatalkan');
                    if(select.value === 'Terlaksana') select.classList.add('status-terlaksana');
                    else if(select.value === 'Pending') select.classList.add('status-pending');
                    else if(select.value === 'Ditunda') select.classList.add('status-ditunda');
                    else if(select.value === 'Dibatalkan') select.classList.add('status-dibatalkan');
                    updateData(select);
                });
            }
            // Delete
            const btnDelete = row.querySelector('.btn-delete');
            if (btnDelete) {
                btnDelete.addEventListener('click', function() {
                    if(confirm('Hapus baris ini?')) {
                        const form = this.closest('form');
                        form.submit();
                    }
                });
            }
        }

        // Bind initial rows
        document.querySelectorAll('#performanceTable tbody tr').forEach(row => {
            if(!row.classList.contains('empty-row')) bindEvents(row);
        });

        function updateData(element) {
            if (isAdministrator) return;

            const row = element.closest('tr');
            const id = row.getAttribute('data-id');
            const field = element.getAttribute('data-field');
            const value = (element.tagName === 'INPUT' || element.tagName === 'SELECT') ? element.value : element.innerText;

            fetch("{{ route('marketing.update-inline') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id, field, value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const selisihCell = row.querySelector('.selisih-val');
                    if (selisihCell) {
                        selisihCell.innerText = data.selisih;
                        selisihCell.className = 'selisih-val fw-bold ' + (data.selisih < 0 ? 'text-danger' : 'text-success');
                    }
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1000 });
                    Toast.fire({ icon: 'success', title: 'Tersimpan' });
                } else if (data.error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.error });
                }
            });
        }

        // --- SORTING LOGIC ---
        let sortOrder = {};
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                const column = this.getAttribute('data-column');
                let actualIndex = column;
                
                // If column is 'last', find the actual index
                if (column === 'last') {
                    actualIndex = this.parentElement.children.length - 1;
                } else {
                    actualIndex = parseInt(column);
                }

                const rows = Array.from(tableBody.querySelectorAll('tr:not(.empty-row)'));
                
                // Toggle order
                sortOrder[column] = (sortOrder[column] === 'asc') ? 'desc' : 'asc';
                const order = sortOrder[column];

                // Update Icons
                document.querySelectorAll('.sortable i').forEach(icon => {
                    icon.className = 'fa-solid fa-sort ms-1 opacity-50';
                });
                const icon = this.querySelector('i');
                icon.className = `fa-solid fa-sort-${order === 'asc' ? 'up' : 'down'} ms-1`;
                icon.classList.remove('opacity-50');

                rows.sort((a, b) => {
                    let valA, valB;
                    const cellA = a.children[actualIndex];
                    const cellB = b.children[actualIndex];

                    if (actualIndex === 4) { // Tanggal
                        valA = cellA.querySelector('input').value || '';
                        valB = cellB.querySelector('input').value || '';
                    } else if (actualIndex === (a.children.length - 1)) { // Aksi/Status (Last)
                        valA = cellA.querySelector('select').value || '';
                        valB = cellB.querySelector('select').value || '';
                    } else { // Pemateri / Text
                        valA = cellA.innerText.trim().toLowerCase();
                        valB = cellB.innerText.trim().toLowerCase();
                    }

                    if (valA < valB) return order === 'asc' ? -1 : 1;
                    if (valA > valB) return order === 'asc' ? 1 : -1;
                    return 0;
                });

                // Clear and re-append
                rows.forEach((row, index) => {
                    tableBody.appendChild(row);
                    row.querySelector('.row-number').innerText = index + 1;
                });
            });
        });

        // --- FELMI KPI LOGIC ---
        if (isFelmi) {
            window.getKpiKey = function() {
                const month = document.getElementById('kpiMonth').value;
                const year = document.getElementById('kpiYear').value;
                return `kpi_felmi_values_${month}_${year}`;
            };

            window.onInputChanged = function(element) {
                calculateRow(element);
                calculateTotalNilai();
                saveKpiData();
            };

            window.calculateRow = function(element) {
                const row = element.closest('tr');
                if (!row) return;

                const targetInput = row.querySelector('.target-num');
                const realisasiInput = row.querySelector('.realisasi-num');
                const nilaiResult = row.querySelector('.nilai-result');
                const labelPersen = row.querySelector('.label-persen');

                if (!targetInput || !realisasiInput || !nilaiResult) return;

                const targetVal = parseFloat(targetInput.value);
                const realisasiVal = parseFloat(realisasiInput.value);

                if (isNaN(realisasiVal) || realisasiInput.value.trim() === '') {
                    nilaiResult.innerText = '-';
                    nilaiResult.className = 'nilai-result h4 font-weight-bold mb-0 text-secondary';
                    if (labelPersen) labelPersen.classList.add('d-none');
                    return;
                }

                if (isNaN(targetVal) || targetVal === 0) {
                    nilaiResult.innerText = '0';
                    nilaiResult.className = 'nilai-result h4 font-weight-bold mb-0 text-danger';
                    if (labelPersen) labelPersen.classList.remove('d-none');
                    return;
                }

                const nilai = Math.round((realisasiVal / targetVal) * 100);
                nilaiResult.innerText = nilai;
                if (labelPersen) labelPersen.classList.remove('d-none');

                if (nilai >= 96) {
                    nilaiResult.className = 'nilai-result h4 font-weight-bold mb-0 text-success';
                } else if (nilai >= 76) {
                    nilaiResult.className = 'nilai-result h4 font-weight-bold mb-0 text-primary';
                } else if (nilai >= 60) {
                    nilaiResult.className = 'nilai-result h4 font-weight-bold mb-0 text-warning';
                } else if (nilai >= 40) {
                    nilaiResult.className = 'nilai-result h4 font-weight-bold mb-0 text-orange';
                } else {
                    nilaiResult.className = 'nilai-result h4 font-weight-bold mb-0 text-danger';
                }
            };

            window.calculateTotalNilai = function() {
                const rows = document.querySelectorAll('#kpi-table-body tr');
                let totalScore = 0;
                let count = 0;

                rows.forEach(row => {
                    const nilaiResult = row.querySelector('.nilai-result');
                    if (nilaiResult && nilaiResult.innerText !== '-') {
                        const score = parseFloat(nilaiResult.innerText);
                        if (!isNaN(score)) {
                            totalScore += score;
                            count++;
                        }
                    }
                });

                const totalNilaiContainer = document.getElementById('total-nilai-result');
                const totalLabelPersen = document.getElementById('total-label-persen');
                const kpiStatusBadge = document.getElementById('kpi-status-badge');

                if (count === 0) {
                    totalNilaiContainer.innerText = '-';
                    totalNilaiContainer.className = 'h3 font-weight-bold mb-0 text-secondary';
                    if (totalLabelPersen) totalLabelPersen.classList.add('d-none');
                    if (kpiStatusBadge) {
                        kpiStatusBadge.innerText = 'BELUM DIISI';
                        kpiStatusBadge.className = 'badge badge-secondary px-3 py-2 shadow-sm rounded-pill font-weight-bold';
                    }
                    return;
                }

                const average = Math.round(totalScore / count);
                totalNilaiContainer.innerText = average;
                if (totalLabelPersen) totalLabelPersen.classList.remove('d-none');

                if (average >= 96) {
                    totalNilaiContainer.className = 'h3 font-weight-bold mb-0 text-success';
                    if (kpiStatusBadge) {
                        kpiStatusBadge.innerText = 'SANGAT BAIK';
                        kpiStatusBadge.className = 'badge badge-success px-3 py-2 shadow-sm rounded-pill font-weight-bold';
                    }
                } else if (average >= 76) {
                    totalNilaiContainer.className = 'h3 font-weight-bold mb-0 text-primary';
                    if (kpiStatusBadge) {
                        kpiStatusBadge.innerText = 'BAIK';
                        kpiStatusBadge.className = 'badge badge-primary px-3 py-2 shadow-sm rounded-pill font-weight-bold';
                    }
                } else if (average >= 60) {
                    totalNilaiContainer.className = 'h3 font-weight-bold mb-0 text-warning';
                    if (kpiStatusBadge) {
                        kpiStatusBadge.innerText = 'CUKUP';
                        kpiStatusBadge.className = 'badge badge-warning px-3 py-2 shadow-sm rounded-pill font-weight-bold text-dark';
                    }
                } else if (average >= 40) {
                    totalNilaiContainer.className = 'h3 font-weight-bold mb-0 text-orange';
                    if (kpiStatusBadge) {
                        kpiStatusBadge.innerText = 'PEMBINAAN';
                        kpiStatusBadge.className = 'badge badge-warning-orange px-3 py-2 shadow-sm rounded-pill font-weight-bold text-white';
                    }
                } else {
                    totalNilaiContainer.className = 'h3 font-weight-bold mb-0 text-danger';
                    if (kpiStatusBadge) {
                        kpiStatusBadge.innerText = 'UNDERPERFORMANCE';
                        kpiStatusBadge.className = 'badge badge-danger px-3 py-2 shadow-sm rounded-pill font-weight-bold';
                    }
                }
            };

            window.saveKpiData = function() {
                const key = getKpiKey();
                const rows = document.querySelectorAll('#kpi-table-body tr');
                const data = {};

                rows.forEach(row => {
                    const rowId = row.getAttribute('data-row-id');
                    const targetVal = row.querySelector('.target-num').value;
                    const realisasiVal = row.querySelector('.realisasi-num').value;
                    data[rowId] = {
                        target: targetVal,
                        realisasi: realisasiVal
                    };
                });

                localStorage.setItem(key, JSON.stringify(data));
            };

            window.loadKpiData = function() {
                const key = getKpiKey();
                const savedData = localStorage.getItem(key);
                const rows = document.querySelectorAll('#kpi-table-body tr');

                // Default values if no stored data
                const defaults = {
                    sosmed_leads: { target: '50', realisasi: '' },
                    sosmed_followers: { target: '100', realisasi: '' },
                    sosmed_posting: { target: '90', realisasi: '' },
                    konten_testimoni: { target: '3', realisasi: '' },
                    konten_video_event: { target: '90', realisasi: '' }
                };

                const data = savedData ? JSON.parse(savedData) : defaults;

                rows.forEach(row => {
                    const rowId = row.getAttribute('data-row-id');
                    const rowData = data[rowId] || defaults[rowId] || { target: '1', realisasi: '' };

                    const targetInput = row.querySelector('.target-num');
                    const realisasiInput = row.querySelector('.realisasi-num');

                    if (targetInput) targetInput.value = rowData.target;
                    if (realisasiInput) realisasiInput.value = rowData.realisasi;

                    // Recalculate for this row
                    calculateRow(targetInput);
                });

                calculateTotalNilai();
            };

            window.updateKpiPeriod = function() {
                loadKpiData();
            };

            // Initial load
            loadKpiData();
        }

    });
</script>
@endsection
