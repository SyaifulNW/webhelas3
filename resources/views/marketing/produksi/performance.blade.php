@extends('layouts.masteradmin')

@section('content')
<div class="container-fluid pb-4">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <div>
            <h1 class="h3 mb-1 text-gray-900 font-weight-bold">Performa Dashboard Produksi</h1>
            <p class="text-muted small mb-0">Ikhtisar penyelesaian seluruh tugas dan inisiatif.</p>
        </div>
        <div class="d-none d-sm-inline-block">
            <div class="badge bg-white shadow-sm border px-3 py-2 rounded-lg text-dark">
                <i class="fas fa-calendar-alt text-primary me-2"></i> {{ date('F Y') }}
            </div>
        </div>
    </div>

    <!-- Dual Panel Navigation Pills -->
    <div class="mb-4">
        <ul class="nav nav-pills shadow-sm p-1 bg-white rounded-pill border" style="width: fit-content;" id="performanceTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active rounded-pill px-4 fw-bold" id="performance-tab" data-toggle="pill" href="#performance-panel" role="tab" aria-controls="performance-panel" aria-selected="true">
                    <i class="fas fa-chart-line mr-2"></i> Performa Dashboard Produksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 fw-bold" id="kpi-tab" data-toggle="pill" href="#kpi-panel" role="tab" aria-controls="kpi-panel" aria-selected="false">
                    <i class="fas fa-key mr-2"></i> Key Performance Index
                </a>
            </li>
        </ul>
    </div>

    <!-- Tab Content Panels -->
    <div class="tab-content" id="performanceTabContent">
        
        <!-- PANEL 1: Performa Dashboard Produksi -->
        <div class="tab-pane fade show active" id="performance-panel" role="tabpanel" aria-labelledby="performance-tab">
            <!-- Statistik Utama -->
            <div class="row g-3">
                <!-- 1. JUMLAH TASK SELESAI -->
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden stats-card card-done">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="icon-shape bg-soft-success rounded-circle">
                                    <i class="fas fa-check-double text-success"></i>
                                </div>
                                <div class="text-success small font-weight-bold">DONE</div>
                            </div>
                            <h3 class="font-weight-bold mb-0 text-gray-800">{{ $stats['done'] }}</h3>
                            <p class="text-muted small font-weight-bold mb-0">TASK SELESAI</p>
                        </div>
                        <div class="bg-success" style="height: 3px; width: 100%"></div>
                    </div>
                </div>

                <!-- 2. JUMLAH TASK ON PROSES -->
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden stats-card card-progress">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="icon-shape bg-soft-warning rounded-circle">
                                    <i class="fas fa-spinner text-warning fa-spin-slow"></i>
                                </div>
                                <div class="text-warning small font-weight-bold">PROCESS</div>
                            </div>
                            <h3 class="font-weight-bold mb-0 text-gray-800">{{ $stats['progress'] }}</h3>
                            <p class="text-muted small font-weight-bold mb-0">ON PROCESS</p>
                        </div>
                        <div class="bg-warning" style="height: 3px; width: 100%"></div>
                    </div>
                </div>

                <!-- 3. JUMLAH TASK OVERDUE -->
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden stats-card card-overdue">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="icon-shape bg-soft-danger rounded-circle">
                                    <i class="fas fa-clock text-danger"></i>
                                </div>
                                <div class="text-danger small font-weight-bold">OVERDUE</div>
                            </div>
                            <h3 class="font-weight-bold mb-0 text-gray-800">{{ $stats['overdue'] }}</h3>
                            <p class="text-muted small font-weight-bold mb-0">TERLAMBAT</p>
                        </div>
                        <div class="bg-danger" style="height: 3px; width: 100%"></div>
                    </div>
                </div>

                <!-- 4. JUMLAH SELURUH TASK -->
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden stats-card card-total">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="icon-shape bg-soft-info rounded-circle">
                                    <i class="fas fa-layer-group text-info"></i>
                                </div>
                                <div class="text-info small font-weight-bold">TOTAL</div>
                            </div>
                            <h3 class="font-weight-bold mb-0 text-gray-800">{{ $stats['total'] }}</h3>
                            <p class="text-muted small font-weight-bold mb-0">SEMUA TASK</p>
                        </div>
                        <div class="bg-info" style="height: 3px; width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Visualization of Progress -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 border-0">
                            <h6 class="m-0 font-weight-bold text-gray-800">
                                <i class="fas fa-chart-line text-primary me-2"></i> Progres Penyelesaian
                            </h6>
                        </div>
                        <div class="card-body px-4 pb-4 pt-0">
                            @php
                                $percentage = $stats['total'] > 0 ? round(($stats['done'] / $stats['total']) * 100) : 0;
                            @endphp
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="h2 font-weight-bold text-gray-900 mb-0">{{ $percentage }}%</span>
                                <div class="text-end text-muted small">
                                     {{ $stats['done'] }} dari {{ $stats['total'] }} Task Selesai
                                </div>
                            </div>
                            <div class="progress rounded-pill" style="height: 12px;">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: {{ $percentage }}%" 
                                     aria-valuenow="{{ $percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            
                            <div class="row mt-4 g-2">
                                <div class="col-6 col-md-3">
                                    <a href="{{ route('programkerja.index') }}" class="btn btn-primary btn-sm w-100 py-2 rounded font-weight-bold">
                                        <i class="fas fa-list me-1"></i> Program Kerja
                                    </a>
                                </div>
                                <div class="col-6 col-md-3">
                                    <a href="{{ route('gantt.index') }}" class="btn btn-outline-primary btn-sm w-100 py-2 rounded font-weight-bold">
                                        <i class="fas fa-project-diagram me-1"></i> Gantt Chart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL 2: Key Performance Index -->
        <div class="tab-pane fade" id="kpi-panel" role="tabpanel" aria-labelledby="kpi-tab">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-0">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="m-0 font-weight-bold text-gray-900">
                                <i class="fas fa-key text-primary me-2"></i> Key Performance Index - Mas Roffi (Produksi)
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
                                        Target Akhir KPI
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
                                {{-- ROW 1: Video Clipper (Merged Area Kerja & NO column spans 2 rows) --}}
                                <tr style="border: 2px solid #000 !important;" data-row-id="video_clipper">
                                    <td rowspan="2" class="text-center font-weight-bold align-middle py-3 px-2" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        1
                                    </td>
                                    <td rowspan="2" class="font-weight-bold align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        <i class="fas fa-video text-danger mr-2"></i> Video Clipper
                                    </td>
                                    <td class="align-middle py-3 px-4" style="border: 2px solid #000 !important; font-weight: 500;">
                                        <div class="mb-2 text-gray-800 font-weight-bold">Target Video Clipper 15 Per Minggu/ 60 per bulan</div>
                                        <div class="d-flex align-items-center text-xs text-muted" style="gap: 5px;">
                                            <span>Target Angka:</span>
                                            <input type="number" class="form-control form-control-sm px-2 py-0 border-dark target-num text-center" style="width: 75px; height: 26px; border: 1.5px solid #000 !important; font-weight: bold;" value="60" oninput="onInputChanged(this)">
                                        </div>
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
                                
                                {{-- ROW 2: Draft Phase (Belongs to Video Clipper, columns NO & AREA spans from Row 1) --}}
                                <tr style="border: 2px solid #000 !important;" data-row-id="draft_phase">
                                    <td class="align-middle py-3 px-4" style="border: 2px solid #000 !important; font-weight: 500;">
                                        <div class="mb-2 text-gray-800 font-weight-bold">Jumlah video selesai tahap draft (siap final) 3 video/hari (±)</div>
                                        <div class="d-flex align-items-center text-xs text-muted" style="gap: 5px;">
                                            <span>Target Angka:</span>
                                            <input type="number" class="form-control form-control-sm px-2 py-0 border-dark target-num text-center" style="width: 75px; height: 26px; border: 1.5px solid #000 !important; font-weight: bold;" value="90" oninput="onInputChanged(this)">
                                        </div>
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

                                {{-- ROW 3: PPT --}}
                                <tr style="border: 2px solid #000 !important;" data-row-id="ppt">
                                    <td class="text-center font-weight-bold align-middle py-3 px-2" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        2
                                    </td>
                                    <td class="font-weight-bold align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        <i class="fas fa-file-powerpoint text-warning mr-2"></i> PPT
                                    </td>
                                    <td class="align-middle py-3 px-4" style="border: 2px solid #000 !important; font-weight: 500;">
                                        <div class="mb-2 text-gray-800 font-weight-bold">Jadi tepat waktu</div>
                                        <div class="d-flex align-items-center text-xs text-muted" style="gap: 5px;">
                                            <span>Target Angka:</span>
                                            <input type="number" class="form-control form-control-sm px-2 py-0 border-dark target-num text-center" style="width: 75px; height: 26px; border: 1.5px solid #000 !important; font-weight: bold;" value="1" oninput="onInputChanged(this)">
                                        </div>
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

                                {{-- ROW 4: Flayer --}}
                                <tr style="border: 2px solid #000 !important;" data-row-id="flayer">
                                    <td class="text-center font-weight-bold align-middle py-3 px-2" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        3
                                    </td>
                                    <td class="font-weight-bold align-middle py-3 px-4" style="border: 2px solid #000 !important; background-color: #f8f9fa;">
                                        <i class="fas fa-image text-info mr-2"></i> Flayer
                                    </td>
                                    <td class="align-middle py-3 px-4" style="border: 2px solid #000 !important; font-weight: 500;">
                                        <div class="mb-2 text-gray-800 font-weight-bold">Selesai tepat waktu (Sesuai dengan target yang berjalan)<br>Menggunakan LM</div>
                                        <div class="d-flex align-items-center text-xs text-muted" style="gap: 5px;">
                                            <span>Target Angka:</span>
                                            <input type="number" class="form-control form-control-sm px-2 py-0 border-dark target-num text-center" style="width: 75px; height: 26px; border: 1.5px solid #000 !important; font-weight: bold;" value="1" oninput="onInputChanged(this)">
                                        </div>
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
                                    <td colspan="4" class="text-end font-weight-bold py-3 px-4 align-middle text-uppercase" style="border: 2px solid #000 !important; font-size: 1rem; color: #000;">
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
        </div>

    </div>
</div>

<script>
function getKpiKey() {
    const month = document.getElementById('kpiMonth').value;
    const year = document.getElementById('kpiYear').value;
    return `kpi_rofi_values_${month}_${year}`;
}

function onInputChanged(element) {
    calculateRow(element);
    calculateTotalNilai();
    saveKpiData();
}

function calculateRow(element) {
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
}

function calculateTotalNilai() {
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
}

function saveKpiData() {
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
}

function loadKpiData() {
    const key = getKpiKey();
    const savedData = localStorage.getItem(key);
    const rows = document.querySelectorAll('#kpi-table-body tr');

    // Default values if no stored data
    const defaults = {
        video_clipper: { target: '60', realisasi: '' },
        draft_phase: { target: '90', realisasi: '' },
        ppt: { target: '1', realisasi: '' },
        flayer: { target: '1', realisasi: '' }
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
}

function updateKpiPeriod() {
    loadKpiData();
}

// Initial load immediately and on DOMContentLoaded
loadKpiData();
document.addEventListener('DOMContentLoaded', function() {
    loadKpiData();
});
</script>

<style>
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
    
    .stats-card {
        transition: transform 0.2s ease;
    }
    .stats-card:hover { 
        transform: translateY(-4px);
    }

    .icon-shape {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
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

    .fa-spin-slow {
        animation: fa-spin 3s infinite linear;
    }

    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(359deg); }
    }
</style>
@endsection
