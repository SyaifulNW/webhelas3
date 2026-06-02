@php
    $m = (int)date('n');
    $y = (int)date('Y');
    $bulanLengkap = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
@endphp

<div class="monitoring-spp-container p-4" style="background: #f8f9fc;">
    <style>
        .text-muted, .text-gray-500, .text-gray-600, .text-gray-700, .text-gray-800, .opacity-75 {
            color: #000 !important;
            opacity: 1 !important;
        }
        .small, .text-xs, .text-xxs {
            color: #000 !important;
        }
    </style>

    <!-- Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col">
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden h-100 p-3 bg-white border-left-premium-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xxs font-weight-bold text-primary text-uppercase mb-1">POTENSI SPP {{ strtoupper($monthsRaw[$m]) }} {{ $y }}</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">Rp {{ number_format($badgeStats['target'] * 1000000, 0, ',', '.') }}</div>
                        <div class="text-xxs text-muted mt-2">Total tagihan bulan ini</div>
                    </div>
                    <div class="icon-circle-shape bg-soft-primary">
                        <i class="fas fa-dollar-sign text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden h-100 p-3 bg-white border-left-premium-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xxs font-weight-bold text-success text-uppercase mb-1">SUDAH DIBAYAR</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">Rp {{ number_format($badgeStats['total_sudah'], 0, ',', '.') }}</div>
                        <div class="text-xxs text-success mt-2"><b>{{ $badgeStats['target'] > 0 ? round(($badgeStats['count_lunas'] / $badgeStats['target']) * 100, 2) : 0 }}%</b> dari potensi</div>
                    </div>
                    <div class="icon-circle-shape bg-soft-success">
                        <i class="fas fa-check text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden h-100 p-3 bg-white border-left-premium-danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xxs font-weight-bold text-danger text-uppercase mb-1">BELUM DIBAYAR</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">Rp {{ number_format($badgeStats['total_belum'], 0, ',', '.') }}</div>
                        <div class="text-xxs text-danger mt-2"><b>{{ $badgeStats['target'] > 0 ? round(($badgeStats['count_belum'] / $badgeStats['target']) * 100, 2) : 0 }}%</b> dari potensi</div>
                    </div>
                    <div class="icon-circle-shape bg-soft-danger">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden h-100 p-3 bg-white border-left-premium-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xxs font-weight-bold text-warning text-uppercase mb-1">PENDING VERIFIKASI</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">Rp 2.000.000</div>
                        <div class="text-xxs text-warning mt-2"><b>3 Bukti</b> menunggu verifikasi</div>
                    </div>
                    <div class="icon-circle-shape bg-soft-warning">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden h-100 p-3 bg-white border-left-premium-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xxs font-weight-bold text-dark text-uppercase mb-1">TOTAL TUNGGAKAN</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">Rp {{ number_format($dashboardData['delinquents']->sum('total_tunggakan'), 0, ',', '.') }}</div>
                        <div class="text-xxs text-danger mt-2"><b>{{ $dashboardData['delinquents']->count() }}</b> Peserta menunggak</div>
                    </div>
                    <div class="icon-circle-shape bg-soft-dark">
                        <i class="fas fa-wallet text-dark"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Multi-Filter Bar -->
    <div class="card border-0 shadow-sm rounded-xl mb-4">
        <div class="card-body p-3">
            <form action="" method="GET" class="row align-items-end g-2">
                <div class="col-md-1">
                    <label class="text-xxs font-weight-bold text-dark mb-1">Bulan</label>
                    <select class="form-control form-control-sm border bg-light rounded-lg font-weight-bold text-xs h-35">
                        @foreach($monthsRaw as $idx => $name)
                            <option value="{{ $idx }}" {{ $idx == date('n') ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="text-xxs font-weight-bold text-dark mb-1">Tahun</label>
                    <select class="form-control form-control-sm border bg-light rounded-lg font-weight-bold text-xs h-35">
                        <option value="2024">2024</option>
                        <option value="2025" selected>2025</option>
                        <option value="2026">2026</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="text-xxs font-weight-bold text-dark mb-1">Chapter</label>
                    <select class="form-control form-control-sm border bg-light rounded-lg font-weight-bold text-xs h-35">
                        <option value="all">Semua Chapter</option>
                        <option value="Depok">Depok</option>
                        <option value="Jakarta">Jakarta</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="text-xxs font-weight-bold text-dark mb-1">CS Penanggung Jawab</label>
                    <select class="form-control form-control-sm border bg-light rounded-lg font-weight-bold text-xs h-35">
                        <option value="all">Semua CS</option>
                        <option value="Linda">Linda</option>
                        <option value="Yasmin">Yasmin</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="text-xxs font-weight-bold text-dark mb-1">Status Pembayaran</label>
                    <select class="form-control form-control-sm border bg-light rounded-lg font-weight-bold text-xs h-35">
                        <option value="all">Semua Status</option>
                        <option value="Lunas">Lunas</option>
                        <option value="Menunggak">Menunggak</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="text-xxs font-weight-bold text-dark mb-1">Status Aktif</label>
                    <select class="form-control form-control-sm border bg-light rounded-lg font-weight-bold text-xs h-35">
                        <option value="Aktif">Aktif</option>
                        <option value="OFF">OFF</option>
                        <option value="Drop Out">Drop Out</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="text-xxs font-weight-bold text-dark mb-1">Cari Peserta</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control border bg-light rounded-left-lg text-xs h-35" placeholder="Nama, CS, Chapter...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-light border-left-0 rounded-right-lg"><i class="fas fa-search text-muted"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="d-flex" style="gap: 5px;">
                        <button type="button" class="btn btn-light btn-sm rounded-lg h-35 px-3"><i class="fas fa-sync-alt text-muted"></i></button>
                        <button type="button" class="btn btn-primary btn-sm rounded-lg h-35 px-4 font-weight-bold">Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Content Tabs & Main Grid -->
    <div class="row">
        <!-- Main Column -->
        <div class="col-xl-9">
            <!-- Sub Tabs -->
            <ul class="nav nav-tabs border-0 mb-3" id="monitoringSubTabs" role="tablist" style="gap: 10px;">
                <li class="nav-item">
                    <a class="nav-link active rounded-lg border-0 shadow-sm font-weight-bold text-xs px-4" data-toggle="tab" href="#tabel-bulanan"><i class="fas fa-table mr-2"></i> Tabel Bulanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-lg border-0 shadow-sm font-weight-bold text-xs px-4" data-toggle="tab" href="#timeline"><i class="fas fa-history mr-2"></i> Timeline Pembayaran</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-lg border-0 shadow-sm font-weight-bold text-xs px-4" data-toggle="tab" href="#tunggakan"><i class="fas fa-user-clock mr-2"></i> Tunggakan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-lg border-0 shadow-sm font-weight-bold text-xs px-4" data-toggle="tab" href="#pending"><i class="fas fa-shield-alt mr-2"></i> Verifikasi Pending <span class="badge badge-warning ml-1">3</span></a>
                </li>
            </ul>

            <!-- Table Container -->
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="far fa-calendar-check text-primary mr-2"></i>Status Pembayaran Bulan {{ $monthsRaw[$m] }} {{ $y }}</h6>
                    <div class="d-flex" style="gap: 15px;">
                        <span class="text-xxs font-weight-bold"><i class="fas fa-check-circle text-success mr-1"></i> Lunas</span>
                        <span class="text-xxs font-weight-bold"><i class="fas fa-clock text-warning mr-1"></i> Pending</span>
                        <span class="text-xxs font-weight-bold"><i class="fas fa-times-circle text-danger mr-1"></i> Menunggak</span>
                        <span class="text-xxs font-weight-bold"><i class="fas fa-user-slash text-info mr-1"></i> OFF</span>
                        <span class="text-xxs font-weight-bold"><i class="fas fa-ban text-secondary mr-1"></i> Drop Out</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-items-center mb-0" style="font-size: 0.75rem;">
                        <thead class="bg-light-blue text-dark font-weight-bold">
                            <tr>
                                <th class="border-0 text-center py-3" width="40">No</th>
                                <th class="border-0 py-3">Nama Peserta</th>
                                <th class="border-0 py-3">Chapter</th>
                                <th class="border-0 py-3">CS</th>
                                <th class="border-0 py-3">Nominal SPP</th>
                                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'] as $mon)
                                    <th class="border-0 text-center py-3 {{ $mon == 'Mei' ? 'bg-soft-primary' : '' }}">{{ $mon }}</th>
                                @endforeach
                                <th class="border-0 text-center py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data->take(10) as $index => $item)
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xxs mr-2 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center text-primary font-weight-bold" style="width: 28px; height: 28px; font-size: 10px;">
                                            {{ strtoupper(substr($item->nama, 0, 1)) }}
                                        </div>
                                        <span class="font-weight-bold text-dark">{{ $item->nama }}</span>
                                    </div>
                                </td>
                                <td><span class="badge badge-soft-primary px-2">{{ $item->chapter ?? 'MBC Pusat' }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xxs mr-1 rounded-circle bg-secondary" style="width: 20px; height: 20px;"></div>
                                        <span class="text-xs">{{ $item->cs_name ?? 'Linda' }}</span>
                                    </div>
                                </td>
                                <td class="font-weight-bold">Rp 1.500.000</td>
                                @for($i = 1; $i <= 12; $i++)
                                <td class="text-center {{ $i == 5 ? 'bg-soft-primary' : '' }}">
                                    @php
                                        $status = $item->{"spp_$i"} > 0;
                                        $isPending = ($i == 5 && $index == 1); // Dummy for demo
                                        $isUnpaid = ($i == 5 && $index % 3 == 2); // Dummy for demo
                                    @endphp
                                    @if($status)
                                        <i class="fas fa-check-square text-success fa-lg"></i>
                                    @elseif($isPending)
                                        <i class="fas fa-stopwatch text-warning fa-lg"></i>
                                    @elseif($isUnpaid)
                                        <i class="fas fa-window-close text-danger fa-lg"></i>
                                    @else
                                        <i class="far fa-square text-light fa-lg"></i>
                                    @endif
                                </td>
                                @endfor
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-soft-info btn-xs rounded-lg mr-1"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-soft-primary btn-xs rounded-lg mr-1"><i class="fas fa-file-invoice"></i></button>
                                        <button class="btn btn-soft-success btn-xs rounded-lg"><i class="fas fa-plus"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Verifikasi Bottom Section -->
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-shield-alt text-warning mr-2"></i>Pending Verifikasi (3)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-items-center mb-0 text-xs">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Nama Peserta</th>
                                <th class="border-0">Bulan</th>
                                <th class="border-0">Nominal</th>
                                <th class="border-0">Tanggal Upload</th>
                                <th class="border-0">Metode</th>
                                <th class="border-0">Bukti</th>
                                <th class="border-0 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dashboardData['reminders']->take(3) as $r)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xxs mr-2 bg-secondary rounded-circle" style="width: 24px; height: 24px;"></div>
                                        <span class="font-weight-bold">{{ $r->nama }}</span>
                                    </div>
                                </td>
                                <td>Mei 2026</td>
                                <td class="font-weight-bold">Rp 1.500.000</td>
                                <td class="text-muted">08 Mei 2026 10:23</td>
                                <td>Transfer Bank BCA</td>
                                <td><button class="btn btn-soft-primary btn-xs px-3"><i class="fas fa-image mr-1"></i> Lihat</button></td>
                                <td class="text-center">
                                    <button class="btn btn-success btn-xs px-3 rounded-lg mr-1 font-weight-bold">Verifikasi</button>
                                    <button class="btn btn-outline-danger btn-xs px-3 rounded-lg font-weight-bold">Tolak</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-xl-3">
            <!-- Chart Card -->
            <div class="card border-0 shadow-sm rounded-xl mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Ringkasan Status Pembayaran (Mei 2026)</h6>
                </div>
                <div class="card-body">
                    <div class="position-relative d-flex justify-content-center align-items-center mb-4" style="height: 180px;">
                        <canvas id="sppDoughnutChart"></canvas>
                        <div class="position-absolute text-center">
                            <div class="h4 font-weight-bold mb-0 text-dark">38</div>
                            <div class="text-xxs text-muted font-weight-bold">Peserta</div>
                        </div>
                    </div>
                    <div class="status-legend">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xxs font-weight-bold"><i class="fas fa-circle text-success mr-2"></i> Lunas</span>
                            <span class="text-xxs font-weight-bold">23 (60.5%)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xxs font-weight-bold"><i class="fas fa-circle text-warning mr-2"></i> Pending</span>
                            <span class="text-xxs font-weight-bold">3 (7.9%)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xxs font-weight-bold"><i class="fas fa-circle text-danger mr-2"></i> Menunggak</span>
                            <span class="text-xxs font-weight-bold">9 (23.7%)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xxs font-weight-bold"><i class="fas fa-circle text-info mr-2"></i> OFF</span>
                            <span class="text-xxs font-weight-bold">2 (5.3%)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xxs font-weight-bold"><i class="fas fa-circle text-secondary mr-2"></i> Drop Out</span>
                            <span class="text-xxs font-weight-bold">1 (2.6%)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Tunggakan -->
            <div class="card border-0 shadow-sm rounded-xl mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-dark">Top 5 Tunggakan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($dashboardData['delinquents'] as $p)
                        <div class="list-group-item border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-xxs mr-3 rounded-circle bg-secondary" style="width: 28px; height: 28px;"></div>
                                <div class="flex-grow-1">
                                    <div class="text-xs font-weight-bold text-dark">{{ $p->nama }}</div>
                                    <div class="text-xxs text-muted">{{ $p->bulan_menunggak }} Bulan</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-weight-bold text-danger">Rp {{ number_format($p->total_tunggakan, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3 text-center">
                    <a href="#" class="btn btn-soft-primary btn-sm btn-block font-weight-bold rounded-lg text-xxs">Lihat Semua Piutang <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>

            <!-- Aktivitas Terakhir -->
            <div class="card border-0 shadow-sm rounded-xl">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Aktivitas Terakhir</h6>
                </div>
                <div class="card-body p-3">
                    <div class="timeline-v2">
                        <div class="timeline-item-v2 pb-4 border-left-dashed ml-2 position-relative pl-4">
                            <div class="timeline-dot bg-success"></div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-xs font-weight-bold text-dark">Pembayaran diverifikasi</span>
                                <span class="text-xxs text-muted">08 Mei 2026 10:30</span>
                            </div>
                            <p class="text-xxs text-muted mb-0">Linda - Mei 2026 (Rp 2.000.000)</p>
                        </div>
                        <div class="timeline-item-v2 pb-4 border-left-dashed ml-2 position-relative pl-4">
                            <div class="timeline-dot bg-warning"></div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-xs font-weight-bold text-dark">Bukti diupload</span>
                                <span class="text-xxs text-muted">08 Mei 2026 10:23</span>
                            </div>
                            <p class="text-xxs text-muted mb-0">Melly - Mei 2026 (Rp 1.500.000)</p>
                        </div>
                        <div class="timeline-item-v2 ml-2 position-relative pl-4">
                            <div class="timeline-dot bg-danger"></div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-xs font-weight-bold text-dark">Tunggakan diperbarui</span>
                                <span class="text-xxs text-muted">08 Mei 2026 09:00</span>
                            </div>
                            <p class="text-xxs text-muted mb-0">Suripto Karsad - 3 Bulan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Palette & Layout */
    .rounded-xl { border-radius: 12px !important; }
    .text-xxs { font-size: 0.65rem; }
    .text-xs { font-size: 0.75rem; }
    .h-35 { height: 35px !important; }
    .bg-light-blue { background-color: #f0f4f9; }
    .bg-soft-primary { background-color: rgba(78, 115, 223, 0.1); }
    .bg-soft-success { background-color: rgba(28, 200, 138, 0.1); }
    .bg-soft-danger { background-color: rgba(231, 74, 59, 0.1); }
    .bg-soft-warning { background-color: rgba(246, 194, 62, 0.1); }
    .bg-soft-dark { background-color: rgba(90, 92, 105, 0.1); }
    
    .border-left-premium-blue { border-left: 5px solid #4e73df !important; }
    .border-left-premium-success { border-left: 5px solid #1cc88a !important; }
    .border-left-premium-danger { border-left: 5px solid #e74a3b !important; }
    .border-left-premium-warning { border-left: 5px solid #f6c23e !important; }
    .border-left-premium-dark { border-left: 5px solid #5a5c69 !important; }
    
    .icon-circle-shape { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    
    .nav-tabs .nav-link { background: #fff; color: #858796; margin-right: 10px; }
    .nav-tabs .nav-link.active { background: #4e73df !important; color: #fff !important; }
    
    .btn-white { background: #fff; border: 1px solid #e3e6f0; }
    .btn-soft-primary { background: rgba(78, 115, 223, 0.1); color: #4e73df; }
    .btn-soft-info { background: rgba(54, 185, 204, 0.1); color: #36b9cc; }
    .btn-soft-success { background: rgba(28, 200, 138, 0.1); color: #1cc88a; }
    .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.7rem; }
    
    .timeline-item-v2 { border-left: 2px dashed #e3e6f0; }
    .timeline-dot { width: 10px; height: 10px; border-radius: 50%; position: absolute; left: -6px; top: 5px; }
    
    /* Contrast Enhancements */
    .table thead th { color: #334155; letter-spacing: 0.025em; text-transform: uppercase; }
    .table tbody td { vertical-align: middle; }
    
    /* Scrollbar */
    .table-responsive::-webkit-scrollbar { height: 8px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const monitoringDoughnutCtx = document.getElementById('sppDoughnutChart').getContext('2d');
        new Chart(monitoringDoughnutCtx, {
            type: 'doughnut',
            data: {
                labels: ["Lunas", "Pending", "Menunggak", "OFF", "Drop Out"],
                datasets: [{
                    data: [23, 3, 9, 2, 1],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc', '#858796'],
                    hoverBackgroundColor: ['#17a673', '#dda20a', '#be2617', '#2c9faf', '#717384'],
                    borderWidth: 0,
                }],
            },
            options: {
                maintainAspectRatio: false,
                cutoutPercentage: 80,
                legend: { display: false },
                tooltips: { enabled: true }
            },
        });
    });
</script>
