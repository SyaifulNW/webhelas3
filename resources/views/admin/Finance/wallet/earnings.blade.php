@extends('layouts.masteradmin')

@section('content')
<div class="container-fluid py-4">
    {{-- Back Button & Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('admin.wallet.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1 class="h3 mb-0 text-gray-800 mt-2">
                <i class="fas fa-wallet text-primary mr-2"></i>Detail Pendapatan: {{ $user->name }}
            </h1>
            <p class="text-muted mb-0">
                {{ strtoupper($user->role) }} &middot; Chapter {{ $user->chapter ?? '-' }} &middot; ID: {{ $user->id }}
            </p>
        </div>
    </div>

    {{-- Saldo Cards --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-3" style="border-radius:12px">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Penghasilan (All Time)</div>
                    <div class="h4 mb-0 font-weight-bold text-success">Rp {{ number_format($totalEarningsAllTime, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-3" style="border-radius:12px">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Tersedia</div>
                    <div class="h4 mb-0 font-weight-bold text-primary">Rp {{ number_format($availableBalance, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-3" style="border-radius:12px">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sudah Ditarik</div>
                    <div class="h4 mb-0 font-weight-bold text-warning">Rp {{ number_format($totalEarningsAllTime - $availableBalance, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rincian Penghasilan --}}
    <div class="card shadow mb-4" style="border-radius:12px;border:none">
        <div class="card-header py-3 bg-gradient-primary text-white" style="border-radius:12px 12px 0 0">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-pie mr-2"></i>Rincian Penghasilan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" style="border-radius:8px;overflow:hidden">
                    <thead class="bg-light">
                        <tr>
                            <th>Komponen</th>
                            <th>Perhitungan</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-light">
                            <td colspan="3" class="font-weight-bold text-dark"><i class="fas fa-user mr-1"></i> Penjualan Pribadi</td>
                        </tr>
                        <tr>
                            <td>Omset Pribadi</td>
                            <td class="text-muted small">Total SPP peserta closing pribadi</td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($omsetPribadi, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Komisi (10%)</td>
                            <td class="text-muted small">10% × Rp {{ number_format($omsetPribadi, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold text-success">Rp {{ number_format($komisi, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Bonus Pribadi</td>
                            <td class="text-muted small">
                                @if($firstMonthOmset >= 20000000)
                                    10% × Rp {{ number_format($firstMonthOmset, 0, ',', '.') }} (omset bulan pertama ≥ 20jt)
                                @elseif($firstMonthOmset >= 10000000)
                                    5% × Rp {{ number_format($firstMonthOmset, 0, ',', '.') }} (omset bulan pertama ≥ 10jt)
                                @else
                                    Belum tercapai (min. omset 10jt di bulan pertama)
                                @endif
                            </td>
                            <td class="text-right font-weight-bold text-success">Rp {{ number_format($bonusPribadi, 0, ',', '.') }}</td>
                        </tr>

                        @if($isChapter)
                        <tr class="bg-light">
                            <td colspan="3" class="font-weight-bold text-dark"><i class="fas fa-building mr-1"></i> Direct Fee Chapter</td>
                        </tr>
                        <tr>
                            <td>Direct Fee</td>
                            <td class="text-muted small">{{ $directFeeCount }} peserta regional × Rp 500.000</td>
                            <td class="text-right font-weight-bold text-success">Rp {{ number_format($directFee, 0, ',', '.') }}</td>
                        </tr>
                        @endif

                        @if($omsetReseller > 0 || $teamMembers->count() > 0)
                        <tr class="bg-light">
                            <td colspan="3" class="font-weight-bold text-dark"><i class="fas fa-users mr-1"></i> Tim Reseller</td>
                        </tr>
                        <tr>
                            <td>Omset Reseller</td>
                            <td class="text-muted small">Total SPP dari {{ $teamMembers->count() }} reseller downline</td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($omsetReseller, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Royalti (5%)</td>
                            <td class="text-muted small">5% × Rp {{ number_format($omsetReseller, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold text-success">Rp {{ number_format($royalti, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Bonus Tim</td>
                            <td class="text-muted small">
                                @if($totalTeamSales >= 30000000)
                                    10% × Rp {{ number_format($totalTeamSales, 0, ',', '.') }} (tim ≥ 30jt)
                                @else
                                    Belum tercapai (min. sales tim 30jt, saat ini Rp {{ number_format($totalTeamSales, 0, ',', '.') }})
                                @endif
                            </td>
                            <td class="text-right font-weight-bold text-success">Rp {{ number_format($bonusTim, 0, ',', '.') }}</td>
                        </tr>
                        @endif

                        <tr class="bg-primary text-white">
                            <td colspan="2" class="font-weight-bold">TOTAL PENGHASILAN</td>
                            <td class="text-right font-weight-bold" style="font-size:1.2rem">Rp {{ number_format($totalEarningsAllTime, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Detail Peserta Closing --}}
    <div class="card shadow mb-4" style="border-radius:12px;border:none">
        <div class="card-header py-3 bg-white" style="border-radius:12px 12px 0 0">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-graduate mr-2"></i>Peserta Closing (Approved + Sudah Transfer)
                <span class="badge badge-primary ml-2">{{ $pesertaList->count() }} peserta</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Peserta</th>
                            <th>CS/Sales</th>
                            <th class="text-right">Nominal</th>
                            <th class="text-right">SPP Dihitung</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesertaList as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-weight-bold text-dark">{{ $p->peserta_nama }}</td>
                            <td>
                                <span class="badge {{ $p->cs_id == $user->id ? 'badge-success' : 'badge-info' }}">
                                    {{ $p->cs_name }}
                                    @if($p->cs_id == $user->id)
                                        (Pribadi)
                                    @endif
                                </span>
                            </td>
                            <td class="text-right">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold text-primary">Rp {{ number_format($p->spp, 0, ',', '.') }}</td>
                            <td class="text-muted small">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada peserta closing yang approved</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tim Reseller --}}
    @if($teamMembers->count() > 0)
    <div class="card shadow mb-4" style="border-radius:12px;border:none">
        <div class="card-header py-3 bg-white" style="border-radius:12px 12px 0 0">
            <h6 class="m-0 font-weight-bold text-info">
                <i class="fas fa-users-cog mr-2"></i>Tim Reseller Downline
                <span class="badge badge-info ml-2">{{ $teamMembers->count() }} orang</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Chapter</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teamMembers as $i => $m)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="font-weight-bold">{{ $m->name }}</td>
                        <td><span class="badge badge-secondary">{{ strtoupper($m->role) }}</span></td>
                        <td>{{ $m->chapter ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Riwayat Transaksi Income --}}
    <div class="card shadow mb-4" style="border-radius:12px;border:none">
        <div class="card-header py-3 bg-white" style="border-radius:12px 12px 0 0">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-coins mr-2"></i>Rincian Transaksi Pendapatan
                <span class="badge badge-success ml-2">{{ $dynamicIncomes->count() }} transaksi</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Sumber</th>
                            <th>Keterangan</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dynamicIncomes as $inc)
                        <tr>
                            <td class="text-muted small">{{ \Carbon\Carbon::parse($inc['created_at'])->format('d/m/Y') }}</td>
                            <td class="font-weight-bold text-dark">{{ $inc['source'] }}</td>
                            <td class="text-muted small">{{ $inc['description'] }}</td>
                            <td class="text-right font-weight-bold text-success">Rp {{ number_format($inc['amount'], 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi pendapatan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Riwayat Penarikan --}}
    <div class="card shadow mb-4" style="border-radius:12px;border:none">
        <div class="card-header py-3 bg-white" style="border-radius:12px 12px 0 0">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-money-bill-wave mr-2"></i>Riwayat Penarikan Dana
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Referensi</th>
                            <th>Bank</th>
                            <th class="text-right">Nominal</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawals as $wd)
                        <tr>
                            <td class="text-muted small">{{ $wd->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge badge-light border">{{ $wd->reference_no }}</span></td>
                            <td class="small">{{ $wd->bank_name }} ({{ $wd->account_number }})</td>
                            <td class="text-right font-weight-bold text-danger">- Rp {{ number_format($wd->amount, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($wd->status === 'success')
                                    <span class="badge badge-success">Success</span>
                                @elseif($wd->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat penarikan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
