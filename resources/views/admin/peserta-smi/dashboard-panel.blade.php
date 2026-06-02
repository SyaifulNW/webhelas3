@php
    $m = (int)date('n');
    $y = (int)date('Y');
@endphp

<div class="m1t-dashboard p-4" style="background: #f8f9fc;">
    <style>
        .text-dark { color: #000 !important; }
        .text-muted { color: #000 !important; opacity: 1 !important; }
        .font-weight-black { font-weight: 800; }
        .rounded-xl { border-radius: 12px !important; }
        .icon-vibrant { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #fff; }
        .stat-card { border: 0; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
    </style>

    <!-- Top Stats Row -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card rounded-xl overflow-hidden h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">TOTAL PESERTA AKTIF</div>
                            <div class="h4 mb-0 font-weight-black text-dark">{{ number_format($dashboardData['status_pie']['lunas'] + $dashboardData['status_pie']['menunggak']) }} <span class="text-xs font-weight-bold">Orang</span></div>
                            <div class="text-xs text-primary mt-2 font-weight-bold">+2 <span class="text-dark">dari bulan lalu</span></div>
                        </div>
                        <div class="icon-vibrant bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card rounded-xl overflow-hidden h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">POTENSI SPP BULAN INI</div>
                            <div class="h4 mb-0 font-weight-black text-dark">Rp {{ number_format($badgeStats['target'] * 1000000, 0, ',', '.') }}</div>
                            <div class="text-xs text-success mt-2 font-weight-bold">+10.8% <span class="text-dark">dari bulan lalu</span></div>
                        </div>
                        <div class="icon-vibrant bg-success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card rounded-xl overflow-hidden h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">SUDAH DIBAYAR</div>
                            <div class="h4 mb-0 font-weight-black text-dark">Rp {{ number_format($badgeStats['total_sudah'], 0, ',', '.') }}</div>
                            <div class="text-xs text-success mt-2 font-weight-bold"><b>{{ $badgeStats['target'] > 0 ? round(($badgeStats['count_lunas'] / $badgeStats['target']) * 100, 2) : 0 }}%</b> <span class="text-dark">dari potensi</span></div>
                        </div>
                        <div class="icon-vibrant bg-info">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card rounded-xl overflow-hidden h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">BELUM DIBAYAR</div>
                            <div class="h4 mb-0 font-weight-black text-dark">Rp {{ number_format($badgeStats['total_belum'], 0, ',', '.') }}</div>
                            <div class="text-xs text-danger mt-2 font-weight-bold"><b>{{ $badgeStats['target'] > 0 ? round(($badgeStats['count_belum'] / $badgeStats['target']) * 100, 2) : 0 }}%</b> <span class="text-dark">dari potensi</span></div>
                        </div>
                        <div class="icon-vibrant bg-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Stats Row -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 rounded-xl">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="mr-3 icon-vibrant bg-primary shadow-sm" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase">Persentase Pembayaran</div>
                        <div class="h5 mb-0 font-weight-black text-dark">{{ $badgeStats['target'] > 0 ? round(($badgeStats['count_lunas'] / $badgeStats['target']) * 100, 2) : 0 }}%</div>
                        <div class="text-xs text-success font-weight-bold">+8.2% <span class="text-dark font-weight-normal">dari bulan lalu</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 rounded-xl">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="mr-3 icon-vibrant bg-purple shadow-sm" style="background: linear-gradient(135deg, #6f42c1 0%, #4e2b8c 100%) !important;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-purple text-uppercase">Prediksi Bulan Depan</div>
                        <div class="h5 mb-0 font-weight-black text-dark">Rp {{ number_format($badgeStats['target'] * 1050000, 0, ',', '.') }}</div>
                        <div class="text-xs text-dark font-weight-bold">Jika semua peserta bayar</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 rounded-xl">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="mr-3 icon-vibrant bg-warning shadow-sm" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%) !important;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase">Total Tunggakan</div>
                        <div class="h5 mb-0 font-weight-black text-dark">Rp {{ number_format($dashboardData['delinquents']->sum('total_tunggakan'), 0, ',', '.') }}</div>
                        <div class="text-xs text-danger font-weight-bold">{{ $dashboardData['delinquents']->count() }} <span class="text-dark font-weight-normal">Peserta menunggak</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 rounded-xl">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="mr-3 icon-vibrant bg-success shadow-sm" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%) !important;">
                        <i class="fas fa-university"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase">Cash-In Tahun Ini</div>
                        <div class="h5 mb-0 font-weight-black text-dark">Rp {{ number_format($totalSmi, 0, ',', '.') }}</div>
                        <div class="text-xs text-dark font-weight-bold">Total uang masuk {{ date('Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Summary Row -->
    <div class="row mb-4 g-4">
        <div class="col-xl-5 col-lg-6">
            <div class="card stat-card h-100 rounded-xl border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary">Trend Pembayaran 6 Bulan Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 250px;">
                        <canvas id="trendSppChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6">
            <div class="card stat-card h-100 rounded-xl border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary">Status Pembayaran Peserta</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height: 180px;">
                        <canvas id="statusSppPieChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="row g-2">
                            <div class="col-6 mb-1 text-xs font-weight-bold">
                                <i class="fas fa-circle text-success mr-1"></i> Lunas: <span class="text-dark">{{ $dashboardData['status_pie']['lunas'] }}</span>
                            </div>
                            <div class="col-6 mb-1 text-xs font-weight-bold">
                                <i class="fas fa-circle text-warning mr-1"></i> Pending: <span class="text-dark">{{ $dashboardData['status_pie']['pending'] }}</span>
                            </div>
                            <div class="col-6 mb-1 text-xs font-weight-bold">
                                <i class="fas fa-circle text-danger mr-1"></i> Menunggak: <span class="text-dark">{{ $dashboardData['status_pie']['menunggak'] }}</span>
                            </div>
                            <div class="col-6 mb-1 text-xs font-weight-bold">
                                <i class="fas fa-circle text-info mr-1"></i> OFF: <span class="text-dark">{{ $dashboardData['status_pie']['cuti'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-12">
            <div class="card stat-card h-100 bg-primary text-white overflow-hidden rounded-xl" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important; color: #fff !important;">
                <style>
                    .bg-primary .font-weight-bold, .bg-primary span, .bg-primary h6 { color: #fff !important; opacity: 1 !important; }
                </style>
                <div class="card-body p-4 position-relative">
                    <h6 class="font-weight-bold mb-4 text-white">RINGKASAN BULAN INI</h6>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-white font-weight-bold opacity-75">Potensi</span>
                        <span class="text-white font-weight-bold">Rp {{ number_format($badgeStats['target'] * 1000000, 0, ',', '.') }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-white font-weight-bold opacity-75">Sudah Dibayar</span>
                        <span class="text-white font-weight-bold">Rp {{ number_format($badgeStats['total_sudah'], 0, ',', '.') }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-white font-weight-bold opacity-75">Belum Dibayar</span>
                        <span class="text-white font-weight-bold">Rp {{ number_format($badgeStats['total_belum'], 0, ',', '.') }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-white font-weight-bold opacity-75">Persentase</span>
                        <span class="text-white font-weight-bold">{{ $badgeStats['target'] > 0 ? round(($badgeStats['count_lunas'] / $badgeStats['target']) * 100, 2) : 0 }}%</span>
                    </div>
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <span class="text-white font-weight-bold opacity-75">Tunggakan</span>
                        <span class="text-white font-weight-bold">Rp {{ number_format($dashboardData['delinquents']->sum('total_tunggakan'), 0, ',', '.') }}</span>
                    </div>
                    
                    <i class="fas fa-chart-bar position-absolute" style="bottom: -30px; right: -20px; font-size: 8rem; opacity: 0.1;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Tables Row -->
    <div class="row g-4">
        <div class="col-xl-5">
            <div class="card stat-card h-100 rounded-xl border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary">TOP 5 CS BERDASARKAN PEMBAYARAN</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush mb-0 text-dark font-weight-bold" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA CS</th>
                                    <th class="text-center">TOTAL PESERTA</th>
                                    <th>SUDAH DIBAYAR</th>
                                    <th class="text-right">PERSENTASE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dashboardData['top_cs'] as $index => $cs)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="font-weight-bold text-dark">{{ $cs->cs_name }}</td>
                                    <td class="text-center">{{ $cs->total_peserta }}</td>
                                    <td>Rp {{ number_format($cs->sudah_dibayar, 0, ',', '.') }}</td>
                                    <td class="text-right text-success font-weight-black">{{ round($cs->persentase, 1) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card stat-card h-100 rounded-xl border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary">PESERTA MENUNGGAK (TOP 5)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush mb-0 text-dark font-weight-bold" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th>NAMA PESERTA</th>
                                    <th>BULAN MENUNGGAK</th>
                                    <th class="text-right">TOTAL TUNGGAKAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dashboardData['delinquents']->take(5) as $p)
                                <tr>
                                    <td class="font-weight-bold">{{ $p->nama }}</td>
                                    <td><span class="badge badge-danger rounded-pill px-3">{{ $p->bulan_menunggak }} Bulan</span></td>
                                    <td class="text-right text-danger font-weight-black">Rp {{ number_format($p->total_tunggakan, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card stat-card h-100 rounded-xl border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary">PENGINGAT JATUH TEMPO</h6>
                </div>
                <div class="card-body p-2">
                    @foreach($dashboardData['reminders']->take(4) as $r)
                    @php
                        $day = \Carbon\Carbon::parse($r->tanggal_masuk)->day;
                        $today = (int)date('d');
                        $diff = $day - $today;
                        $badgeClass = 'bg-warning text-dark';
                        if ($diff < 0) $badgeClass = 'bg-danger text-white';
                        elseif ($diff <= 3) $badgeClass = 'bg-info text-white';
                    @endphp
                    <div class="d-flex align-items-center mb-3 p-2 rounded-lg border shadow-xs" style="background: #fff;">
                        <div class="mr-2">
                            <div class="badge {{ $badgeClass }} px-2 py-1" style="font-size: 0.65rem;">H{{ $diff >= 0 ? '-'.$diff : '+'.abs($diff) }}</div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-dark">{{ $r->nama }}</div>
                            <div class="text-xxs text-dark opacity-75">Jatuh tempo: {{ $day }} {{ $monthsRaw[(int)date('n')] }}</div>
                        </div>
                        <button class="btn btn-sm btn-outline-warning rounded-pill px-2 text-xxs font-weight-bold">Remind</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Trend SPP Chart
        const trendCtx = document.getElementById('trendSppChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($dashboardData['trends']['labels']),
                datasets: [{
                    label: "Sudah Dibayar",
                    lineTension: 0.3,
                    backgroundColor: "rgba(28, 200, 138, 0.05)",
                    borderColor: "rgba(28, 200, 138, 1)",
                    pointRadius: 4,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "rgba(28, 200, 138, 1)",
                    pointBorderWidth: 2,
                    data: @json($dashboardData['trends']['paid']),
                }, {
                    label: "Belum Dibayar",
                    lineTension: 0.3,
                    backgroundColor: "rgba(231, 74, 59, 0.05)",
                    borderColor: "rgba(231, 74, 59, 1)",
                    pointRadius: 4,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "rgba(231, 74, 59, 1)",
                    pointBorderWidth: 2,
                    data: @json($dashboardData['trends']['unpaid']),
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{ gridLines: { display: false } }],
                    yAxes: [{ ticks: { callback: function(value) { return (value/1000000) + 'M'; } } }],
                },
                legend: { position: 'top', labels: { boxWidth: 12, fontStyle: 'bold', fontColor: '#000' } }
            }
        });

        // Status SPP Pie Chart
        const pieCtx = document.getElementById('statusSppPieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ["Lunas", "Pending", "Menunggak", "OFF", "Drop Out"],
                datasets: [{
                    data: [{{ $dashboardData['status_pie']['lunas'] }}, {{ $dashboardData['status_pie']['pending'] }}, {{ $dashboardData['status_pie']['menunggak'] }}, {{ $dashboardData['status_pie']['cuti'] }}, 0],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc', '#858796'],
                    borderWidth: 0,
                }],
            },
            options: {
                maintainAspectRatio: false,
                cutoutPercentage: 75,
                legend: { display: false }
            },
        });
    });
</script>
