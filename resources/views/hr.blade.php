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
        border: 1px solid rgba(0,0,0,0.05);
        background: white;
        transition: all 0.3s ease;
    }

    .widget-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }

    /* Form control improvements */
    .premium-input, .premium-select {
        border-radius: 10px;
        border: 1.5px solid #eaecf4;
        padding: 10px 15px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        width: 100%;
        background-color: #fff;
    }

    .premium-input:focus, .premium-select:focus {
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
        box-shadow: 0 15px 30px rgba(0,0,0,0.18) !important;
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
</style>

<div class="container-fluid px-4 py-3">

    @if(request('section') == 'karyawan')
        <!-- SECTION: DATA KARYAWAN -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="font-weight-bold text-dark mb-1"><i class="fas fa-users text-success mr-2"></i> Data Karyawan</h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Kelola seluruh profil, hak akses jabatan, tipe kontrak, dan status aktif staf Helas.</p>
            </div>
            <button class="btn btn-success premium-btn shadow-sm" onclick="openAddModal()"><i class="fas fa-user-plus mr-2"></i> Tambah Karyawan</button>
        </div>

        <!-- Employee List Table -->
        <div class="card shadow border-0" style="border-radius: 16px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                        <thead class="bg-light text-dark font-weight-bold" style="border-bottom: 2px solid #eaecf4;">
                            <tr>
                                <th class="py-3 px-4" style="width: 25%;">Nama Karyawan</th>
                                <th style="width: 25%;">Jabatan & Divisi</th>
                                <th style="width: 20%;">Tipe Kontrak</th>
                                <th style="width: 15%;">Status</th>
                                <th class="text-center" style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="employeeTableBody">
                            <!-- Populated dynamically via JS/localStorage -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ADD & EDIT EMPLOYEE MODAL -->
        <div class="modal fade premium-modal" id="employeeModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="modalTitle"><i class="fas fa-user-edit mr-2"></i> Edit Data Karyawan</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; outline: none;">
                            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                        </button>
                    </div>
                    <form id="employeeForm" onsubmit="saveEmployee(event)">
                        <input type="hidden" id="employeeId">
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark mb-1">Nama Lengkap</label>
                                <input type="text" id="employeeName" class="premium-input" placeholder="Masukkan nama karyawan..." required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark mb-1">Jabatan & Divisi</label>
                                <select id="employeeRole" class="premium-select" required>
                                    <option value="CEO / Direksi">CEO / Direksi</option>
                                    <option value="CS Team Leader / Marketing">CS Team Leader / Marketing</option>
                                    <option value="CS Expert / Marketing">CS Expert / Marketing</option>
                                    <option value="IT Specialist / Operasional">IT Specialist / Operasional</option>
                                    <option value="Finance Head / Keuangan">Finance Head / Keuangan</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark mb-1">Tipe Kontrak</label>
                                <select id="employeeContract" class="premium-select" required>
                                    <option value="Permanent">Permanent</option>
                                    <option value="Contract (1 Year)">Contract (1 Year)</option>
                                    <option value="Contract (6 Months)">Contract (6 Months)</option>
                                    <option value="Probation">Probation (Percobaan)</option>
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label class="font-weight-bold text-dark mb-1">Status Keaktifan</label>
                                <select id="employeeStatus" class="premium-select" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Cuti">Cuti</option>
                                    <option value="Percobaan">Percobaan</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary premium-btn" data-dismiss="modal" style="background-color: #eaecf4; color: #5a5c69; border: none;">Batal</button>
                            <button type="submit" class="btn btn-success premium-btn shadow-sm" style="background-color: #1cc88a; border: none;">Simpan Data</button>
                        </div>
                    </form>
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
                <h4 class="font-weight-bold text-dark mb-1"><i class="fas fa-calendar-check text-info mr-2"></i> Absensi & Izin Karyawan</h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Pantau kehadiran real-time, validasi selfie, GPS radius, serta rekap bulanan staf Helas.</p>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card widget-card shadow-sm p-4 text-center">
                    <h6 class="text-muted font-weight-bold">Hadir Hari Ini</h6>
                    <h2 class="text-info font-weight-bold mb-0">{{ $persenHadir }}%</h2>
                    <small class="text-success mt-1"><i class="fas fa-check-circle mr-1"></i> {{ $totalHadir }} Karyawan Hadir</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card widget-card shadow-sm p-4 text-center">
                    <h6 class="text-muted font-weight-bold">Terlambat</h6>
                    <h2 class="text-warning font-weight-bold mb-0">{{ $persenTerlambat }}%</h2>
                    <small class="text-danger mt-1"><i class="fas fa-clock mr-1"></i> {{ $totalTerlambat }} Karyawan Terlambat</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card widget-card shadow-sm p-4 text-center">
                    <h6 class="text-muted font-weight-bold">Izin / Sakit / Dinas</h6>
                    <h2 class="text-danger font-weight-bold mb-0">{{ $persenIzin }}%</h2>
                    <small class="text-muted mt-1"><i class="fas fa-info-circle mr-1"></i> {{ $totalIzin }} Karyawan Izin/Sakit/Dinas</small>
                </div>
            </div>
        </div>

        <!-- TABS FOR DAILY VS MONTHLY RECAP -->
        <ul class="nav nav-pills mb-3 gap-2" id="pills-tab" role="tablist" style="background: rgba(0,0,0,0.03); padding: 6px; border-radius: 12px; display: inline-flex;">
            <li class="nav-item" role="presentation">
                <a class="nav-link @if($activeTab == 'daily') active @endif font-weight-bold" id="daily-tab-btn" data-toggle="pill" href="#daily-panel" role="tab" style="border-radius: 10px; font-size: 0.85rem;">
                    <i class="fas fa-desktop mr-1"></i> Monitoring Harian
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link @if($activeTab == 'monthly') active @endif font-weight-bold" id="monthly-tab-btn" data-toggle="pill" href="#monthly-panel" role="tab" style="border-radius: 10px; font-size: 0.85rem;">
                    <i class="fas fa-file-alt mr-1"></i> Rekap Bulanan
                </a>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <!-- PANEL: DAILY MONITORING -->
            <div class="tab-pane fade @if($activeTab == 'daily') show active @endif" id="daily-panel" role="tabpanel">
                <!-- Daily Filter -->
                <div class="card shadow border-0 mb-3" style="border-radius: 16px;">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('hr') }}" class="row align-items-center g-3">
                            <input type="hidden" name="section" value="absensi">
                            @if(request('embed'))
                                <input type="hidden" name="embed" value="true">
                            @endif
                            <div class="col-auto">
                                <label class="font-weight-bold text-dark mb-0 mr-2" style="font-size: 0.9rem;">Pilih Tanggal Monitoring:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="date" class="form-control premium-input py-1 px-3" value="{{ $selectedDate }}" onchange="this.form.submit()" style="max-width: 200px;">
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
                                                <small class="text-muted" style="font-size: 0.75rem;">ID: {{ $row->employee_id }}</small>
                                            </td>
                                            <td class="font-weight-bold text-success">{{ $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') . ' WIB' : '--:--' }}</td>
                                            <td class="font-weight-bold text-danger">{{ $row->jam_pulang ? \Carbon\Carbon::parse($row->jam_pulang)->format('H:i') . ' WIB' : '--:--' }}</td>
                                            <td>
                                                @if($row->status_kehadiran == 'Hadir')
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
                                                @if($row->is_late == 'Terlambat')
                                                    <span class="badge bg-cuti premium-badge">Terlambat</span>
                                                @else
                                                    <span class="badge bg-aktif premium-badge">Tepat Waktu</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($row->gps_latitude)
                                                    <span style="font-size: 0.8rem;">Lat: {{ $row->gps_latitude }}<br>Lng: {{ $row->gps_longitude }}</span><br>
                                                    @if($row->radius_status == 'Dalam Radius')
                                                        <span class="badge bg-aktif premium-badge py-1 px-2 mt-1" style="font-size: 0.65rem;">Dalam Radius</span>
                                                    @else
                                                        <span class="badge bg-cuti premium-badge py-1 px-2 mt-1" style="font-size: 0.65rem;">Luar Radius</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Tidak ada GPS</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    @if($row->selfie_masuk)
                                                        <a href="#" class="view-selfie-btn" data-img="{{ $row->selfie_masuk }}" data-title="Selfie Masuk - {{ $row->employee_name }}" style="text-decoration: none;">
                                                            <div class="position-relative" style="width: 40px; height: 40px; border-radius: 6px; overflow:hidden; border: 1.5px solid #eaecf4; cursor: pointer;">
                                                                <img src="{{ $row->selfie_masuk }}" style="width:100%; height:100%; object-fit:cover;">
                                                                <span class="position-absolute bg-success text-white px-1 font-weight-bold" style="font-size: 0.55rem; bottom:0; right:0; border-top-left-radius: 4px;">IN</span>
                                                            </div>
                                                        </a>
                                                    @endif
                                                    @if($row->selfie_pulang)
                                                        <a href="#" class="view-selfie-btn" data-img="{{ $row->selfie_pulang }}" data-title="Selfie Pulang - {{ $row->employee_name }}" style="text-decoration: none;">
                                                            <div class="position-relative" style="width: 40px; height: 40px; border-radius: 6px; overflow:hidden; border: 1.5px solid #eaecf4; cursor: pointer;">
                                                                <img src="{{ $row->selfie_pulang }}" style="width:100%; height:100%; object-fit:cover;">
                                                                <span class="position-absolute bg-danger text-white px-1 font-weight-bold" style="font-size: 0.55rem; bottom:0; right:0; border-top-left-radius: 4px;">OUT</span>
                                                            </div>
                                                        </a>
                                                    @endif
                                                    @if(!$row->selfie_masuk && !$row->selfie_pulang)
                                                        <span class="text-muted">Tidak ada foto</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="font-weight-bold text-dark">{{ $row->total_jam_kerja ?? '-' }}</td>
                                            <td><small class="text-muted">{{ $row->keterangan ?? '-' }}</small></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="fas fa-calendar-times fa-3x mb-3 text-light"></i>
                                                <h6 class="font-weight-bold">Tidak ada data absensi untuk tanggal ini.</h6>
                                                <small>Karyawan belum melakukan absen pada tanggal {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}</small>
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
            <div class="tab-pane fade @if($activeTab == 'monthly') show active @endif" id="monthly-panel" role="tabpanel">
                <!-- Monthly Filter -->
                <div class="card shadow border-0 mb-3" style="border-radius: 16px;">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('hr') }}" class="row align-items-center g-3">
                            <input type="hidden" name="section" value="absensi">
                            @if(request('embed'))
                                <input type="hidden" name="embed" value="true">
                            @endif
                            <div class="col-auto">
                                <label class="font-weight-bold text-dark mb-0 mr-2" style="font-size: 0.9rem;">Pilih Bulan Rekap:</label>
                            </div>
                            <div class="col-auto">
                                <input type="month" name="month" class="form-control premium-input py-1 px-3" value="{{ $selectedMonth }}" onchange="this.form.submit()" style="max-width: 200px;">
                            </div>
                            <div class="col text-right">
                                <button type="button" class="btn btn-success font-weight-bold premium-btn py-2 px-3 shadow-sm border-0" onclick="exportTableToExcel('monthlyTable', 'Rekap_Absensi_{{ $selectedMonth }}')" style="background-color: #1cc88a;">
                                    <i class="fas fa-file-excel mr-1"></i> Ekspor ke Excel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow border-0" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="monthlyTable" style="font-size: 0.9rem;">
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
                                            <td class="py-3 px-4 font-weight-bold text-dark">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                                            <td>{{ $row->employee_id }}</td>
                                            <td class="font-weight-bold text-dark">{{ $row->employee_name }}</td>
                                            <td>
                                                @if($row->status_kehadiran == 'Hadir')
                                                    <span class="badge bg-aktif premium-badge">Hadir</span>
                                                @elseif($row->status_kehadiran == 'Sakit')
                                                    <span class="badge bg-cuti premium-badge">Sakit</span>
                                                @elseif($row->status_kehadiran == 'Izin')
                                                    <span class="badge bg-percobaan premium-badge">Izin</span>
                                                @else
                                                    <span class="badge bg-percobaan premium-badge">Dinas Luar</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') : '-' }}</td>
                                            <td>{{ $row->jam_pulang ? \Carbon\Carbon::parse($row->jam_pulang)->format('H:i') : '-' }}</td>
                                            <td>
                                                @if($row->is_late == 'Terlambat')
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
                                                <h6 class="font-weight-bold">Tidak ada data absensi untuk bulan ini.</h6>
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
        <div class="modal fade" id="selfieViewerModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                        <h6 class="modal-title font-weight-bold" id="selfieViewerTitle">Foto Selfie Validasi</h6>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; outline: none;">
                            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0 text-center bg-black">
                        <img id="selfieViewerImage" src="" style="width: 100%; max-height: 480px; object-fit: contain; background: black;">
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

<!-- Realtime CRUD JavaScript Logic via localStorage -->
<script>
    // Initial employee dataset
    const defaultEmployees = [
        { id: 1, name: "Fitra Jaya Saleh", role: "CEO / Direksi", contract: "Permanent", status: "Aktif" },
        { id: 2, name: "Linda", role: "CS Team Leader / Marketing", contract: "Permanent", status: "Aktif" },
        { id: 3, name: "Yasmin", role: "CS Expert / Marketing", contract: "Contract (1 Year)", status: "Aktif" },
        { id: 4, name: "Ahmad", role: "IT Specialist / Operasional", contract: "Permanent", status: "Cuti" },
        { id: 5, name: "Maria", role: "Finance Head / Keuangan", contract: "Permanent", status: "Aktif" }
    ];

    // Load or initialize localStorage data
    function getEmployees() {
        let employees = localStorage.getItem('helas_employees');
        if (!employees) {
            localStorage.setItem('helas_employees', JSON.stringify(defaultEmployees));
            return defaultEmployees;
        }
        return JSON.parse(employees);
    }

    function saveEmployeesToStorage(employees) {
        localStorage.setItem('helas_employees', JSON.stringify(employees));
    }

    // Render employee table list
    function renderEmployees() {
        const tableBody = document.getElementById('employeeTableBody');
        if (!tableBody) return;

        const employees = getEmployees();
        tableBody.innerHTML = '';

        if (employees.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
                        <h6 class="font-weight-bold">Belum ada data karyawan.</h6>
                        <small>Klik tombol "Tambah Karyawan" di atas untuk menambah data baru.</small>
                    </td>
                </tr>
            `;
            return;
        }

        employees.forEach(emp => {
            // Match badge status style
            let badgeClass = 'bg-aktif';
            if (emp.status === 'Cuti') badgeClass = 'bg-cuti';
            if (emp.status === 'Percobaan') badgeClass = 'bg-percobaan';

            tableBody.innerHTML += `
                <tr id="emp-row-${emp.id}">
                    <td class="py-3 px-4 font-weight-bold text-dark">${emp.name}</td>
                    <td>${emp.role}</td>
                    <td>${emp.contract}</td>
                    <td><span class="badge ${badgeClass} premium-badge">${emp.status}</span></td>
                    <td class="text-center">
                        <button class="btn action-btn text-primary bg-light mr-1" onclick="openEditModal(${emp.id})" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn action-btn text-danger bg-light" onclick="deleteEmployee(${emp.id})" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
            `;
        });

        // Request parent window to resize iframe automatically
        if (window.parent && typeof window.parent.resizeIframe === 'function') {
            window.parent.resizeIframe(window.frameElement);
        }
    }

    // Open Modal for Add
    function openAddModal() {
        document.getElementById('employeeForm').reset();
        document.getElementById('employeeId').value = '';
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus mr-2"></i> Tambah Karyawan Baru';
        $('#employeeModal').modal('show');
    }

    // Open Modal for Edit
    function openEditModal(id) {
        const employees = getEmployees();
        const emp = employees.find(e => e.id == id);
        if (!emp) return;

        document.getElementById('employeeId').value = emp.id;
        document.getElementById('employeeName').value = emp.name;
        document.getElementById('employeeRole').value = emp.role;
        document.getElementById('employeeContract').value = emp.contract;
        document.getElementById('employeeStatus').value = emp.status;

        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-edit mr-2"></i> Edit Data Karyawan';
        $('#employeeModal').modal('show');
    }

    // Save (Create or Update) Employee
    function saveEmployee(e) {
        e.preventDefault();
        const id = document.getElementById('employeeId').value;
        const name = document.getElementById('employeeName').value;
        const role = document.getElementById('employeeRole').value;
        const contract = document.getElementById('employeeContract').value;
        const status = document.getElementById('employeeStatus').value;

        let employees = getEmployees();

        if (id) {
            // Update
            employees = employees.map(emp => {
                if (emp.id == id) {
                    return { id: parseInt(id), name, role, contract, status };
                }
                return emp;
            });
        } else {
            // Create
            const newId = employees.length > 0 ? Math.max(...employees.map(emp => emp.id)) + 1 : 1;
            employees.push({ id: newId, name, role, contract, status });
        }

        saveEmployeesToStorage(employees);
        $('#employeeModal').modal('hide');
        
        // Show smooth feedback and reload
        renderEmployees();
    }

    // Delete Employee
    function deleteEmployee(id) {
        if (confirm("Apakah Anda yakin ingin menghapus data karyawan ini dari Helas Corporation?")) {
            const row = document.getElementById(`emp-row-${id}`);
            if (row) {
                row.style.transition = 'all 0.4s ease';
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    let employees = getEmployees();
                    employees = employees.filter(emp => emp.id != id);
                    saveEmployeesToStorage(employees);
                    renderEmployees();
                }, 400);
            }
        }
    }

    // Initialize rendering on load
    document.addEventListener("DOMContentLoaded", function() {
        renderEmployees();

        // Selfie Viewer modal trigger
        $(document).on('click', '.view-selfie-btn', function(e) {
            e.preventDefault();
            const imgSrc = $(this).data('img');
            const title = $(this).data('title');
            
            $('#selfieViewerTitle').text(title);
            $('#selfieViewerImage').attr('src', imgSrc);
            $('#selfieViewerModal').modal('show');
        });
    });

    // Excel export helper
    function exportTableToExcel(tableID, filename = '') {
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
        
        filename = filename ? filename + '.xls' : 'excel_data.xls';
        downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        
        if(navigator.msSaveOrOpenBlob){
            var blob = new Blob(['\ufeff' + tableSelect.outerHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            downloadLink.download = filename;
            downloadLink.click();
        }
    }
</script>
@endsection
