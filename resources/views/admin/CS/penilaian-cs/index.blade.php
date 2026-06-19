@extends('layouts.masteradmin')

@section('content')
<style>
    /* Sticky filter column for better UX on long evaluations */
    .sticky-filter {
        position: sticky;
        top: 85px; /* Offset for topbar + info marquee */
        z-index: 1000;
        height: fit-content;
    }
</style>
<div class="row">
    <!-- Kolom Kiri: Statistik & Input Atasan -->
    <div class="col-lg-6 mb-4 sticky-filter">
        <!-- Card Filter -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Filter Karyawan & Periode</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route($routeAction ?? 'admin.penilaian-cs.index') }}">
                    <div class="form-group">
                        <label>Pilih Nama Tim:</label>
                        <select name="user_id" class="form-control">
                            @foreach($daftarCs as $cs)
                                @if($cs->id != 1 && $cs->is_active)
                                <option value="{{ $cs->id }}" {{ $userId == $cs->id ? 'selected' : '' }}>
                                    {{ $cs->name }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label>Bulan</label>
                            <select name="bulan" class="form-control">
                                @foreach(range(1,12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label>Tahun</label>
                            <select name="tahun" class="form-control">
                                <option value="2024" {{ $tahun == '2024' ? 'selected' : '' }}>2024</option>
                                <option value="2025" {{ $tahun == '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2026" {{ $tahun == '2026' ? 'selected' : '' }}>2026</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-3 font-weight-bold">
                        <i class="fas fa-search mr-2"></i> TAMPILKAN DATA
                    </button>
                </form>
                 <br>
                  @if(trim($namaUser) === 'Eko Sulis')
                  <a href="{{ route('admin.ads-activity.export-pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'user_id' => $userId]) }}" class="btn btn-danger btn-block" target="_blank">
                     <i class="fas fa-file-pdf mr-1"></i> Export PDF Daily Activity
                  </a>
                  @else
                  <a href="{{ route('admin.activity-cs.viewPdfBulanan', ['cs_id' => $userId, 'bulan' => $tahun . '-' . $bulan]) }}" class="btn btn-danger btn-block" target="_blank">
                     <i class="fas fa-file-pdf mr-1"></i> Export PDF Daily Activity
                  </a>
                  @endif

                  
                  @if(in_array(trim($namaUser), ['Rofi', 'Linda', 'Yasmin', 'Felmi']))
                  <div class="row mt-2">
                      <div class="col">
                          {{-- Hanya Gantt Chart saja sesuai request --}}
                          <a href="{{ route('gantt.index', ['user_id' => $userId]) }}" class="btn btn-info btn-block">
                              <i class="fas fa-project-diagram mr-1"></i> Gantt Chart
                          </a>
                      </div>
                  </div>
                  @endif
            </div>
        </div>

        <!-- Card Input Penilaian Atasan -->
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Input Penilaian Atasan</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.penilaian-cs.store') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $userId }}">
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Kerajinan (0-100)</label>
                        <div class="col-sm-8">
                            <input type="number" name="kerajinan" class="form-control" required min="0" max="100" value="{{ $manual->kerajinan ?? 0 }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Kerjasama (0-100)</label>
                        <div class="col-sm-8">
                            <input type="number" name="kerjasama" class="form-control" required min="0" max="100" value="{{ $manual->kerjasama ?? 0 }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tanggung Jawab (0-100)</label>
                        <div class="col-sm-8">
                            <input type="number" name="tanggung_jawab" class="form-control" required min="0" max="100" value="{{ $manual->tanggung_jawab ?? 0 }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Inisiatif (0-100)</label>
                        <div class="col-sm-8">
                            <input type="number" name="inisiatif" class="form-control" required min="0" max="100" value="{{ $manual->inisiatif ?? 0 }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Komunikasi (0-100)</label>
                        <div class="col-sm-8">
                            <input type="number" name="komunikasi" class="form-control" required min="0" max="100" value="{{ $manual->komunikasi ?? 0 }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan Tambahan</label>
                        <textarea name="catatan" class="form-control" rows="3">{{ $manual->catatan ?? '' }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-danger btn-block">Simpan Penilaian</button>
                    @if(isset($manual))
                        <div class="mt-2 text-center text-xs text-muted">
                            Terakhir dinilai oleh: User ID {{ $manual->created_by }} pada {{ $manual->updated_at }}
                        </div>
                    @endif
                </form>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Statistik System -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Statistik Sistem (Otomatis)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="card bg-light border-left-info py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Database</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDatabase }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-database fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                      <h5 class="small font-weight-bold">Total Closing  ({{ $totalClosing }} / 30) <span class="float-right">{{ $closingTarget }}%</span></h5>
                <div class="progress mb-4">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $closingTarget }}%" aria-valuenow="{{ $closingTarget }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                
                <h5 class="small font-weight-bold">Closing Paket 0 <span class="float-right">0%</span></h5>
                <div class="progress mb-4">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <h5 class="small font-weight-bold">Pencapaian Omset <span class="float-right">{{ $nilaiOmset }}%</span></h5>
                <div class="progress mb-2">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $nilaiOmset }}%" aria-valuenow="{{ $nilaiOmset }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-right font-weight-bold text-gray-800 mb-4">
                    Omset Bulan Ini: Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </div>
                <div class="text-right font-weight-bold text-gray-800 mb-4">
                    Target Omset: Rp {{ number_format($targetOmset, 0, ',', '.') }}
                </div>
<!-- 
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Tertarik: {{ $countTertarik }}
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Transfer: {{ $countSudahTransfer }}
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Cold: {{ $countCold }}
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-danger"></i> No: {{ $countNo }}
                    </span>
                </div> -->

            </div>
        </div>

        <!-- Card Tabel Penilaian Hasil -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success">
                 <h6 class="m-0 font-weight-bold text-white text-center">PENILAIAN HASIL (CS {{ strtoupper($namaUser ?? '') }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 table-striped">
                        <thead class="bg-warning text-dark">
                            <tr>
                                <th>No</th>
                                <th>Aspek Kinerja</th>
                                <th>Indikator</th>
                                <th>Bobot</th>
                                <th>Pencapaian</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Penjualan & Omset</td>
                                <td>Target Rp {{ number_format($targetOmset ?? 0,0,',','.') }}/bulan</td>
                                <td>40%</td>
                                <td>Rp {{ number_format($totalOmset ?? 0,0,',','.') }}</td>
                                <td>{{ $scoreOmset ?? 0 }}</td>
                            </tr>
                            <tr>
                                 <td>2</td>
                                 <td>Closing Paket</td>
                                 <td>Target {{ $targetClosingPaket ?? 1 }} closing paket per bulan</td>
                                 <td>10%</td>
                                 <td>{{ $closingPaketCount ?? 0 }} peserta</td>
                                 <td>{{ $scoreClosingPaket ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Database Baru</td>
                                <td>Target {{ $targetDatabase ?? 50 }} database baru</td>
                                <td>10%</td>
                                <td>{{ $totalDatabase ?? 0 }}</td>
                                <td>{{ $scoreDatabase ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Penilaian Atasan</td>
                                <td>Total Skor Kualitatif (Max 500)</td>
                                <td>20%</td>
                                 <td>{{ $manualTotalSum ?? 0 }}</td>
                                 <td>{{ $scoreManual ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Intake</td>
                                <td>Rekap Keseluruhan Daily Activity</td>
                                <td>20%</td>
                                 <td>{{ number_format($dailyTotalKpi ?? 0, 2) }}%</td>
                                 <td>{{ $scoreIntake ?? 0 }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                             <tr style="background-color: #d1f7d6;">
                                 <td colspan="5" class="text-right">TOTAL NILAI</td>
                                 <td>{{ $grandTotal ?? 0 }}</td>
                             </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer Alert -->
        <div class="card shadow mb-4">
             <div class="card-body {{ ($grandTotal ?? 0) < 70 ? 'bg-danger' : 'bg-success' }} text-white text-center">
                <h3 class="font-weight-bold m-0">{{ ($grandTotal ?? 0) < 70 ? 'Underperformance' : 'Good Performance' }} ({{ $grandTotal ?? 0 }})</h3>
            </div>
        </div>

    </div>
</div>

<!-- G. HISTORY KINERJA PER BULAN -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-2 bg-white border-bottom d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.95rem;">
                    <i class="fas fa-chart-bar mr-2"></i>G. HISTORY KINERJA PER BULAN (TAHUN {{ $tahun }})
                </h6>
                <span class="badge badge-primary px-3 py-1 font-weight-bold" style="font-size: 0.8rem;">
                    {{ strtoupper($namaUser ?? '') }}
                </span>
            </div>
            <div class="card-body p-3">
                <div style="display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 6px;">
                    @foreach(range(1,12) as $m)
                        @php
                            $nilai = $historyNilai[$m] ?? 0;

                            // Kategori warna sesuai Keterangan Skala Nilai
                            if($nilai > 100){
                                $warna = "#009300"; // Sangat Baik
                            } elseif($nilai >= 80){
                                $warna = "#22b122"; // Baik
                            } elseif($nilai >= 60){
                                $warna = "#ffe75c"; // Cukup
                            } elseif($nilai >= 40){
                                $warna = "#ff9933"; // Pembinaan
                            } elseif($nilai > 0){
                                $warna = "#e53935"; // Underperformance
                            } else {
                                $warna = "#e5e7eb"; // Belum dinilai (0)
                            }
                        @endphp

                        <div class="history-item text-center p-2 border rounded" style="
                            background: #ffffff;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
                            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                            border-color: #e3e6f0;
                        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.05)'; this.style.borderColor='#4e73df';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)'; this.style.borderColor='#e3e6f0';">
                            <div style="font-weight: 700; font-size: 11px; color: #858796; margin-bottom: 5px;">
                                {{ DateTime::createFromFormat('!m', $m)->format('M') }}
                            </div>

                            <div style="width: 100%; height: 6px; background: #eaecf4; border-radius: 3px; overflow: hidden; margin-bottom: 6px;">
                                <div style="
                                    width: 100%;
                                    height: 100%;
                                    background: {{ $warna }};
                                    border-radius: 3px;
                                "></div>
                            </div>

                            <div style="font-size: 12px; font-weight: 800; color: #5a5c69; white-space: nowrap;">
                                {{ number_format($nilai, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Glassmorphic Loader Overlay -->
<div id="filter-loader" class="d-none position-fixed w-100 h-100 top-0 left-0 d-flex flex-column align-items-center justify-content-center" style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(8px); z-index: 999999;">
    <div class="d-flex flex-column align-items-center text-center">
        <!-- Premium Custom Spinning Loader -->
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem; border-width: 0.3em;">
            <span class="sr-only">Loading...</span>
        </div>
        <h5 class="mt-4 font-weight-bold text-dark" style="letter-spacing: 1px; font-family: 'Outfit', sans-serif;">Memuat Data Kinerja...</h5>
        <p class="text-muted small px-3">Sedang merangkum data closing, omset, dan laporan harian CS</p>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector('form[action*="penilaian-cs"]');
        if (form) {
            form.addEventListener('submit', function() {
                // Show glassmorphic loading overlay
                const loader = document.getElementById('filter-loader');
                if (loader) {
                    loader.classList.remove('d-none');
                }
                
                // Show spinning state on the button
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> MEMUAT DATA...';
                }
                
                // Notify parent iframe container if available
                if (window.parent && typeof window.parent.showLoading === 'function') {
                    window.parent.showLoading();
                }
            });
        }
    });
</script>
@endsection
