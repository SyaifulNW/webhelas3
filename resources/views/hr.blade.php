@extends('layouts.masteradmin')

@section('content')
    <!-- Include Bootstrap JS and clean premium Styling -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fc;
        }

        /* Premium Badges */
        .premium-badge {
            font-weight: 700;
            border-radius: 30px;
            padding: 6px 14px;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .bg-aktif {
            background-color: rgba(28, 200, 138, 0.15) !important;
            color: #1cc88a !important;
            border: 1px solid rgba(28, 200, 138, 0.3);
        }

        .bg-cuti {
            background-color: rgba(246, 194, 62, 0.15) !important;
            color: #f6c23e !important;
            border: 1px solid rgba(246, 194, 62, 0.3);
        }

        .bg-percobaan {
            background-color: rgba(78, 115, 223, 0.15) !important;
            color: #4e73df !important;
            border: 1px solid rgba(78, 115, 223, 0.3);
        }

        /* Table & Actions premium styles */
        .widget-card {
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            background: white;
            transition: all 0.3s ease;
        }

        .widget-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        /* Form control improvements */
        .premium-input,
        .premium-select {
            border-radius: 10px;
            border: 1.5px solid #eaecf4;
            padding: 10px 15px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            width: 100%;
            background-color: #fff;
        }

        .premium-input:focus,
        .premium-select:focus {
            border-color: #1cc88a;
            box-shadow: 0 0 0 0.2rem rgba(28, 200, 138, 0.15);
            outline: none;
        }

        .premium-btn {
            border-radius: 10px;
            font-weight: 700;
            padding: 10px 20px;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .premium-btn:hover {
            transform: translateY(-2px);
        }

        /* Premium Modal Styling */
        .premium-modal .modal-content {
            border-radius: 20px !important;
            border: none !important;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.18) !important;
            overflow: hidden;
        }

        .premium-modal .modal-header {
            background: linear-gradient(135deg, #13855c 0%, #1cc88a 100%);
            color: white;
            border: none;
            padding: 20px 24px;
        }

        .premium-modal .modal-footer {
            border-top: 1px solid #eaecf4;
            padding: 18px 24px;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50% !important;
            transition: all 0.2s ease;
            border: none;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        /* ---- General inline control for Live Edit ---- */
        .form-control-inline {
            background: transparent !important;
            border: 1px solid transparent !important;
            width: 100% !important;
            padding: 5px 8px !important;
            font-size: 0.9rem !important;
            color: #333 !important;
            border-radius: 6px !important;
            transition: border-color 0.15s, background 0.15s !important;
            font-family: inherit !important;
            outline: none !important;
        }

        .form-control-inline:hover {
            background: rgba(28, 200, 138, 0.04) !important;
            border-color: rgba(28, 200, 138, 0.2) !important;
        }

        .form-control-inline:focus {
            background: #fff !important;
            border-color: #1cc88a !important;
            box-shadow: 0 0 0 0.2rem rgba(28, 200, 138, 0.1) !important;
        }

        /* select dropdowns styling to match premium badges */
        .live-status-select {
            border-radius: 30px !important;
            padding: 6px 14px !important;
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            text-align: center !important;
            text-align-last: center !important;
            width: 110px !important;
            cursor: pointer !important;
            border: 1px solid transparent !important;
            -webkit-appearance: none !important;
            appearance: none !important;
            outline: none !important;
            transition: all 0.15s ease !important;
        }

        .live-status-select:hover,
        .live-status-select:focus {
            border-color: rgba(28, 200, 138, 0.3) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }

        /* Save Toast Notification */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .save-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            background: linear-gradient(135deg, #13855c 0%, #1cc88a 100%);
            color: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 230px;
            box-shadow: 0 6px 24px rgba(28, 200, 138, 0.35);
            animation: slideInRight 0.3s ease;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .save-toast.hiding {
            opacity: 0;
            transform: translateX(40px);
        }
    </style>

    <div class="container-fluid px-4 py-3">

        @if (request('section') == 'karyawan')
            <!-- SECTION: DATA KARYAWAN -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="font-weight-bold text-dark mb-1"><i class="fas fa-users text-success mr-2"></i> Data Karyawan
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Kelola seluruh profil, jabatan & divisi, tipe
                        kontrak, dan status aktif staf Helas.</p>
                </div>
            </div>

            <!-- Employee List Table -->
            <div class="card shadow border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                            <thead class="bg-light text-dark font-weight-bold" style="border-bottom: 2px solid #eaecf4;">
                                <tr>
                                    <th class="py-3 px-4" style="width: 30%;">Nama Karyawan</th>
                                    <th style="width: 25%;">Jabatan &amp; Divisi</th>
                                    <th style="width: 20%;">Tipe Kontrak</th>
                                    <th style="width: 15%;">Status</th>
                                    <th class="text-center" style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $emp)
                                    @php
                                        $badgeClass = $emp->status_sdm === 'Non Aktif' ? 'bg-cuti' : 'bg-aktif';
                                    @endphp
                                    <tr id="emp-row-{{ $emp->id }}">
                                        <td class="py-2 px-4 font-weight-bold text-dark">{{ $emp->name }}</td>
                                        <td class="py-2">
                                            <select class="form-control-inline"
                                                onchange="updateEmpField({{ $emp->id }}, 'divisi', this.value)">
                                                @foreach (['CS & HRD', 'CS & Keuangan', 'Operasional', 'Produksi', 'Produksi Konten', 'Advertiser', 'CS & Sales', 'Web Developer'] as $opt)
                                                    <option value="{{ $opt }}"
                                                        {{ $emp->divisi === $opt ? 'selected' : '' }}>{{ $opt }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-2">
                                            <select class="form-control-inline"
                                                onchange="updateEmpField({{ $emp->id }}, 'tipe_kontrak', this.value)">
                                                @foreach (['Permanen', 'Kontrak', 'Probation', 'Internship'] as $opt)
                                                    <option value="{{ $opt }}"
                                                        {{ $emp->tipe_kontrak === $opt ? 'selected' : '' }}>
                                                        {{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-2">
                                            <select class="live-status-select {{ $badgeClass }}"
                                                onchange="updateEmpField({{ $emp->id }}, 'status_sdm', this.value, this)">
                                                <option value="Aktif"
                                                    {{ $emp->status_sdm === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                <option value="Non Aktif"
                                                    {{ $emp->status_sdm === 'Non Aktif' ? 'selected' : '' }}>Non Aktif
                                                </option>
                                            </select>
                                        </td>
                                        <td class="text-center py-2">
                                            <button class="btn action-btn text-danger bg-light"
                                                onclick="deleteEmployee({{ $emp->id }})" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
                                            <h6 class="font-weight-bold">Belum ada data karyawan aktif.</h6>
                                            <small>Tambahkan user dengan status aktif melalui manajemen user.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @elseif(request('section') == 'absensi')
            <!-- SECTION: ABSENSI & IZIN -->
            @php
                $activeTab = request('month') ? 'monthly' : 'daily';
            @endphp

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="font-weight-bold text-dark mb-1"><i class="fas fa-calendar-check text-info mr-2"></i> Absensi
                        & Izin Karyawan</h4>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Pantau kehadiran real-time, validasi selfie, GPS
                        radius, serta rekap bulanan staf Helas.</p>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card widget-card shadow-sm p-4 text-center">
                        <h6 class="text-muted font-weight-bold">Hadir Hari Ini</h6>
                        <h2 class="text-info font-weight-bold mb-0">{{ $persenHadir }}%</h2>
                        <small class="text-success mt-1"><i class="fas fa-check-circle mr-1"></i> {{ $totalHadir }}
                            Karyawan Hadir</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card widget-card shadow-sm p-4 text-center">
                        <h6 class="text-muted font-weight-bold">Terlambat</h6>
                        <h2 class="text-warning font-weight-bold mb-0">{{ $persenTerlambat }}%</h2>
                        <small class="text-danger mt-1"><i class="fas fa-clock mr-1"></i> {{ $totalTerlambat }} Karyawan
                            Terlambat</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card widget-card shadow-sm p-4 text-center">
                        <h6 class="text-muted font-weight-bold">Izin / Sakit / Dinas</h6>
                        <h2 class="text-danger font-weight-bold mb-0">{{ $persenIzin }}%</h2>
                        <small class="text-muted mt-1"><i class="fas fa-info-circle mr-1"></i> {{ $totalIzin }} Karyawan
                            Izin/Sakit/Dinas</small>
                    </div>
                </div>
            </div>

            <!-- TABS FOR DAILY VS MONTHLY RECAP -->
            <ul class="nav nav-pills mb-3 gap-2" id="pills-tab" role="tablist"
                style="background: rgba(0,0,0,0.03); padding: 6px; border-radius: 12px; display: inline-flex;">
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if ($activeTab == 'daily') active @endif font-weight-bold" id="daily-tab-btn"
                        data-toggle="pill" href="#daily-panel" role="tab"
                        style="border-radius: 10px; font-size: 0.85rem;">
                        <i class="fas fa-desktop mr-1"></i> Monitoring Harian
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if ($activeTab == 'monthly') active @endif font-weight-bold"
                        id="monthly-tab-btn" data-toggle="pill" href="#monthly-panel" role="tab"
                        style="border-radius: 10px; font-size: 0.85rem;">
                        <i class="fas fa-file-alt mr-1"></i> Rekap Bulanan
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <!-- PANEL: DAILY MONITORING -->
                <div class="tab-pane fade @if ($activeTab == 'daily') show active @endif" id="daily-panel"
                    role="tabpanel">
                    <!-- Daily Filter -->
                    <div class="card shadow border-0 mb-3" style="border-radius: 16px;">
                        <div class="card-body p-3">
                            <form method="GET" action="{{ route('hr') }}" class="row align-items-center g-3">
                                <input type="hidden" name="section" value="absensi">
                                @if (request('embed'))
                                    <input type="hidden" name="embed" value="true">
                                @endif
                                <div class="col-auto">
                                    <label class="font-weight-bold text-dark mb-0 mr-2" style="font-size: 0.9rem;">Pilih
                                        Tanggal Monitoring:</label>
                                </div>
                                <div class="col-auto">
                                    <input type="date" name="date" class="form-control premium-input py-1 px-3"
                                        value="{{ $selectedDate }}" onchange="this.form.submit()"
                                        style="max-width: 200px;">
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow border-0" style="border-radius: 16px; overflow: hidden;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="bg-light text-dark font-weight-bold">
                                        <tr>
                                            <th class="py-3 px-4">Karyawan</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Status Kehadiran</th>
                                            <th>Keterlambatan</th>
                                            <th>Lokasi GPS / Radius</th>
                                            <th>Selfie Validasi</th>
                                            <th>Total Jam</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendances as $row)
                                            <tr>
                                                <td class="py-3 px-4 font-weight-bold text-dark">
                                                    {{ $row->employee_name }}<br>
                                                    <small class="text-muted" style="font-size: 0.75rem;">ID:
                                                        {{ $row->employee_id }}</small>
                                                </td>
                                                <td class="font-weight-bold text-success">
                                                    {{ $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') . ' WIB' : '--:--' }}
                                                </td>
                                                <td class="font-weight-bold text-danger">
                                                    {{ $row->jam_pulang ? \Carbon\Carbon::parse($row->jam_pulang)->format('H:i') . ' WIB' : '--:--' }}
                                                </td>
                                                <td>
                                                    @if ($row->status_kehadiran == 'Hadir')
                                                        <span class="badge bg-aktif premium-badge">Hadir</span>
                                                    @elseif($row->status_kehadiran == 'Sakit')
                                                        <span class="badge bg-cuti premium-badge">Sakit</span>
                                                    @elseif($row->status_kehadiran == 'Izin')
                                                        <span class="badge bg-percobaan premium-badge">Izin</span>
                                                    @else
                                                        <span class="badge bg-percobaan premium-badge">Dinas Luar</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($row->is_late == 'Terlambat')
                                                        <span class="badge bg-cuti premium-badge">Terlambat</span>
                                                    @else
                                                        <span class="badge bg-aktif premium-badge">Tepat Waktu</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($row->gps_latitude)
                                                        <span style="font-size: 0.8rem;">Lat:
                                                            {{ $row->gps_latitude }}<br>Lng:
                                                            {{ $row->gps_longitude }}</span><br>
                                                        @if ($row->radius_status == 'Dalam Radius')
                                                            <span class="badge bg-aktif premium-badge py-1 px-2 mt-1"
                                                                style="font-size: 0.65rem;">Dalam Radius</span>
                                                        @else
                                                            <span class="badge bg-cuti premium-badge py-1 px-2 mt-1"
                                                                style="font-size: 0.65rem;">Luar Radius</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Tidak ada GPS</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @if ($row->selfie_masuk)
                                                            <a href="#" class="view-selfie-btn"
                                                                data-img="{{ $row->selfie_masuk }}"
                                                                data-title="Selfie Masuk - {{ $row->employee_name }}"
                                                                style="text-decoration: none;">
                                                                <div class="position-relative"
                                                                    style="width: 40px; height: 40px; border-radius: 6px; overflow:hidden; border: 1.5px solid #eaecf4; cursor: pointer;">
                                                                    <img src="{{ $row->selfie_masuk }}"
                                                                        style="width:100%; height:100%; object-fit:cover;">
                                                                    <span
                                                                        class="position-absolute bg-success text-white px-1 font-weight-bold"
                                                                        style="font-size: 0.55rem; bottom:0; right:0; border-top-left-radius: 4px;">IN</span>
                                                                </div>
                                                            </a>
                                                        @endif
                                                        @if ($row->selfie_pulang)
                                                            <a href="#" class="view-selfie-btn"
                                                                data-img="{{ $row->selfie_pulang }}"
                                                                data-title="Selfie Pulang - {{ $row->employee_name }}"
                                                                style="text-decoration: none;">
                                                                <div class="position-relative"
                                                                    style="width: 40px; height: 40px; border-radius: 6px; overflow:hidden; border: 1.5px solid #eaecf4; cursor: pointer;">
                                                                    <img src="{{ $row->selfie_pulang }}"
                                                                        style="width:100%; height:100%; object-fit:cover;">
                                                                    <span
                                                                        class="position-absolute bg-danger text-white px-1 font-weight-bold"
                                                                        style="font-size: 0.55rem; bottom:0; right:0; border-top-left-radius: 4px;">OUT</span>
                                                                </div>
                                                            </a>
                                                        @endif
                                                        @if (!$row->selfie_masuk && !$row->selfie_pulang)
                                                            <span class="text-muted">Tidak ada foto</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="font-weight-bold text-dark">{{ $row->total_jam_kerja ?? '-' }}
                                                </td>
                                                <td><small class="text-muted">{{ $row->keterangan ?? '-' }}</small></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-5 text-muted">
                                                    <i class="fas fa-calendar-times fa-3x mb-3 text-light"></i>
                                                    <h6 class="font-weight-bold">Tidak ada data absensi untuk tanggal ini.
                                                    </h6>
                                                    <small>Karyawan belum melakukan absen pada tanggal
                                                        {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}</small>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL: MONTHLY RECAP -->
                <div class="tab-pane fade @if ($activeTab == 'monthly') show active @endif" id="monthly-panel"
                    role="tabpanel">
                    <!-- Monthly Filter -->
                    <div class="card shadow border-0 mb-3" style="border-radius: 16px;">
                        <div class="card-body p-3">
                            <form method="GET" action="{{ route('hr') }}" class="row align-items-center g-3">
                                <input type="hidden" name="section" value="absensi">
                                @if (request('embed'))
                                    <input type="hidden" name="embed" value="true">
                                @endif
                                <div class="col-auto">
                                    <label class="font-weight-bold text-dark mb-0 mr-2" style="font-size: 0.9rem;">Pilih
                                        Bulan Rekap:</label>
                                </div>
                                <div class="col-auto">
                                    <input type="month" name="month" class="form-control premium-input py-1 px-3"
                                        value="{{ $selectedMonth }}" onchange="this.form.submit()"
                                        style="max-width: 200px;">
                                </div>
                                <div class="col text-right">
                                    <button type="button"
                                        class="btn btn-success font-weight-bold premium-btn py-2 px-3 shadow-sm border-0"
                                        onclick="exportTableToExcel('monthlyTable', 'Rekap_Absensi_{{ $selectedMonth }}')"
                                        style="background-color: #1cc88a;">
                                        <i class="fas fa-file-excel mr-1"></i> Ekspor ke Excel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow border-0" style="border-radius: 16px; overflow: hidden;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="monthlyTable"
                                    style="font-size: 0.9rem;">
                                    <thead class="bg-light text-dark font-weight-bold">
                                        <tr>
                                            <th class="py-3 px-4">Tanggal</th>
                                            <th>ID Karyawan</th>
                                            <th>Nama Karyawan</th>
                                            <th>Status Kehadiran</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Terlambat?</th>
                                            <th>Total Jam Kerja</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($monthlyAttendances as $row)
                                            <tr>
                                                <td class="py-3 px-4 font-weight-bold text-dark">
                                                    {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                                                <td>{{ $row->employee_id }}</td>
                                                <td class="font-weight-bold text-dark">{{ $row->employee_name }}</td>
                                                <td>
                                                    @if ($row->status_kehadiran == 'Hadir')
                                                        <span class="badge bg-aktif premium-badge">Hadir</span>
                                                    @elseif($row->status_kehadiran == 'Sakit')
                                                        <span class="badge bg-cuti premium-badge">Sakit</span>
                                                    @elseif($row->status_kehadiran == 'Izin')
                                                        <span class="badge bg-percobaan premium-badge">Izin</span>
                                                    @else
                                                        <span class="badge bg-percobaan premium-badge">Dinas Luar</span>
                                                    @endif
                                                </td>
                                                <td>{{ $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') : '-' }}
                                                </td>
                                                <td>{{ $row->jam_pulang ? \Carbon\Carbon::parse($row->jam_pulang)->format('H:i') : '-' }}
                                                </td>
                                                <td>
                                                    @if ($row->is_late == 'Terlambat')
                                                        <span class="badge bg-cuti premium-badge">Ya (Terlambat)</span>
                                                    @else
                                                        <span class="badge bg-aktif premium-badge">Tidak</span>
                                                    @endif
                                                </td>
                                                <td>{{ $row->total_jam_kerja ?? '-' }}</td>
                                                <td><small class="text-muted">{{ $row->keterangan ?? '-' }}</small></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-5 text-muted">
                                                    <i class="fas fa-calendar-times fa-3x mb-3 text-light"></i>
                                                    <h6 class="font-weight-bold">Tidak ada data absensi untuk bulan ini.
                                                    </h6>
                                                    <small>Pilih bulan lain atau kirim absensi baru dari aplikasi.</small>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selfie Modal Viewer -->
            <div class="modal fade" id="selfieViewerModal" tabindex="-1" role="dialog" aria-hidden="true"
                style="z-index: 9999;">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                        <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                            <h6 class="modal-title font-weight-bold" id="selfieViewerTitle">Foto Selfie Validasi</h6>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                                style="opacity: 0.8; outline: none;">
                                <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-0 text-center bg-black">
                            <img id="selfieViewerImage" src=""
                                style="width: 100%; max-height: 480px; object-fit: contain; background: black;">
                        </div>
                    </div>
                </div>
            </div>
        @elseif(request('section') == 'settings' && in_array(Auth::user()->role, ['administrator', 'admin']))
            <!-- SECTION: PENGATURAN ABSENSI -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="font-weight-bold text-dark mb-1"><i class="fas fa-cogs text-secondary mr-2"></i> Pengaturan Absensi
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Konfigurasi parameter absensi seperti radius lokasi, koordinat kantor, dan jam masuk.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 4px solid #1cc88a;">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card shadow border-0" style="border-radius: 16px; overflow: hidden; max-width: 800px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Konfigurasi Parameter Absensi Mobile</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('hr.settings.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small">Latitude Kantor</label>
                                <input type="text" name="absensi_latitude" class="form-control premium-input" value="{{ $settings['absensi_latitude'] ?? '-6.201200' }}" required>
                                <small class="text-muted">Contoh: -6.201200</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small">Longitude Kantor</label>
                                <input type="text" name="absensi_longitude" class="form-control premium-input" value="{{ $settings['absensi_longitude'] ?? '106.816000' }}" required>
                                <small class="text-muted">Contoh: 106.816000</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small">Batas Jarak Radius (Meter)</label>
                                <input type="number" name="absensi_radius" class="form-control premium-input" value="{{ $settings['absensi_radius'] ?? '50' }}" required>
                                <small class="text-muted">Jarak maksimal karyawan dari koordinat kantor.</small>
                            </div>
                            <div class="col-md-12 mt-3 mb-2">
                                <h6 class="font-weight-bold text-dark border-bottom pb-2">Pengaturan Waktu (Senin - Jum'at)</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small">Batas Jam Masuk (Terlambat)</label>
                                <input type="time" name="absensi_jam_masuk_weekday" class="form-control premium-input" value="{{ \Carbon\Carbon::parse($settings['absensi_jam_masuk_weekday'] ?? '08:00')->format('H:i') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small">Jam Pulang Minimum</label>
                                <input type="time" name="absensi_jam_pulang_weekday" class="form-control premium-input" value="{{ \Carbon\Carbon::parse($settings['absensi_jam_pulang_weekday'] ?? '16:00')->format('H:i') }}" required>
                            </div>

                            <div class="col-md-12 mt-3 mb-2">
                                <h6 class="font-weight-bold text-dark border-bottom pb-2">Pengaturan Waktu (Sabtu)</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small">Batas Jam Masuk (Terlambat)</label>
                                <input type="time" name="absensi_jam_masuk_sabtu" class="form-control premium-input" value="{{ \Carbon\Carbon::parse($settings['absensi_jam_masuk_sabtu'] ?? '08:00')->format('H:i') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small">Jam Pulang Minimum</label>
                                <input type="time" name="absensi_jam_pulang_sabtu" class="form-control premium-input" value="{{ \Carbon\Carbon::parse($settings['absensi_jam_pulang_sabtu'] ?? '14:00')->format('H:i') }}" required>
                            </div>
                            <div class="col-md-12 text-muted small mt-2">
                                <i class="fas fa-info-circle"></i> Untuk hari Minggu, sistem otomatis meliburkan absensi (tidak ada status terlambat).
                            </div>
                        </div>
                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-primary font-weight-bold premium-btn py-2 px-4 shadow-sm border-0">
                                <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>

    <!-- JavaScript -->
    <script>
        const CSRF = '{{ csrf_token() }}';
        const UPDATE_URL = '{{ route('hr.employee.update') }}';
        const DESTROY_URL = '{{ url('hr/employee') }}';

        // ---- Toast Notification ----
        function showSaveToast(message) {
            message = message || 'Data berhasil disimpan!';
            document.querySelectorAll('.save-toast').forEach(t => t.remove());
            const toast = document.createElement('div');
            toast.className = 'save-toast';
            toast.innerHTML = '<i class="fas fa-check-circle"></i>' + message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 320);
            }, 2200);
        }

        // ---- Update field via AJAX ----
        function updateEmpField(id, field, value, el) {
            $.post(UPDATE_URL, {
                    _token: CSRF,
                    id,
                    field,
                    value
                })
                .done(() => {
                    showSaveToast();
                    if (field === 'status_sdm' && el) {
                        $(el).removeClass('bg-aktif bg-cuti');
                        $(el).addClass(value === 'Non Aktif' ? 'bg-cuti' : 'bg-aktif');
                    }
                })
                .fail(() => showSaveToast('Gagal menyimpan, coba lagi.'));
        }

        // ---- Delete employee (permanent) ----
        function deleteEmployee(id) {
            Swal.fire({
                title: 'Hapus karyawan ini?',
                text: 'Data karyawan akan dihapus permanen dari sistem.',
                icon: 'warning',
                position: 'top',
                width: '360px',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: DESTROY_URL + '/' + id,
                    type: 'DELETE',
                    data: {
                        _token: CSRF
                    },
                    success: () => {
                        const row = document.getElementById('emp-row-' + id);
                        if (row) {
                            row.style.transition = 'all 0.4s ease';
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 400);
                        }
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Data karyawan berhasil dihapus.',
                            icon: 'success',
                            position: 'top',
                            width: '340px',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: () => showSaveToast('Gagal menghapus, coba lagi.')
                });
            });
        }

        // Selfie Viewer modal trigger
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click', '.view-selfie-btn', function(e) {
                e.preventDefault();
                $('#selfieViewerTitle').text($(this).data('title'));
                $('#selfieViewerImage').attr('src', $(this).data('img'));
                $('#selfieViewerModal').modal('show');
            });
        });

        // Excel export helper
        function exportTableToExcel(tableID, filename = '') {
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
            filename = filename ? filename + '.xls' : 'excel_data.xls';
            var downloadLink = document.createElement('a');
            document.body.appendChild(downloadLink);
            if (navigator.msSaveOrOpenBlob) {
                navigator.msSaveOrOpenBlob(new Blob(['\ufeff' + tableSelect.outerHTML], {
                    type: dataType
                }), filename);
            } else {
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                downloadLink.download = filename;
                downloadLink.click();
            }
        }
    </script>
@endsection
