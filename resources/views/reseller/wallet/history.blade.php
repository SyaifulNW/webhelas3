@extends('layouts.masteradmin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Riwayat Transaksi</h1>
        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm shadow-sm font-weight-bold" style="border-radius: 8px;">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <!-- Total Pemasukan Card -->
        <div class="col-xl-6 col-md-6 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100 py-2" style="border-radius: 12px; border-left: 5px solid #28a745 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Pemasukan @if(request('month') || request('year')) (Filter Aktif) @endif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalIncome, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto mr-2">
                            <i class="fas fa-wallet fa-2x text-success" style="opacity: 0.35;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran Card -->
        <div class="col-xl-6 col-md-6">
            <div class="card border-0 shadow h-100 py-2" style="border-radius: 12px; border-left: 5px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Pengeluaran / Penarikan @if(request('month') || request('year')) (Filter Aktif) @endif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalWithdrawal, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto mr-2">
                            <i class="fas fa-hand-holding-usd fa-2x text-danger" style="opacity: 0.35;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4" style="border-radius: 15px; border: none;">
        <div class="card-header py-3 bg-white border-0" style="border-radius: 15px 15px 0 0;">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-1"></i> Filter Transaksi</h6>
        </div>
        <div class="card-body pt-0">
            <form action="{{ route('wallet.history') }}" method="GET" class="row align-items-end">
                <div class="col-md-3 mb-3 mb-md-0">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Jenis Transaksi</label>
                    <select name="type" class="form-control form-control-sm border-light shadow-sm" style="border-radius: 8px;">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua Jenis</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Penarikan</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Status</label>
                    <select name="status" class="form-control form-control-sm border-light shadow-sm" style="border-radius: 8px;">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Berhasil</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Bulan</label>
                    <select name="month" class="form-control form-control-sm border-light shadow-sm" style="border-radius: 8px;">
                        <option value="">Semua Bulan</option>
                        <option value="01" {{ request('month') == '01' ? 'selected' : '' }}>Januari</option>
                        <option value="02" {{ request('month') == '02' ? 'selected' : '' }}>Februari</option>
                        <option value="03" {{ request('month') == '03' ? 'selected' : '' }}>Maret</option>
                        <option value="04" {{ request('month') == '04' ? 'selected' : '' }}>April</option>
                        <option value="05" {{ request('month') == '05' ? 'selected' : '' }}>Mei</option>
                        <option value="06" {{ request('month') == '06' ? 'selected' : '' }}>Juni</option>
                        <option value="07" {{ request('month') == '07' ? 'selected' : '' }}>Juli</option>
                        <option value="08" {{ request('month') == '08' ? 'selected' : '' }}>Agustus</option>
                        <option value="09" {{ request('month') == '09' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Tahun</label>
                    <select name="year" class="form-control form-control-sm border-light shadow-sm" style="border-radius: 8px;">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm btn-block shadow-sm font-weight-bold d-flex align-items-center justify-content-center" style="border-radius: 8px; height: 31px;">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow mb-4" style="border-radius: 15px; border: none; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr style="background-color: #f8faff;">
                            <th class="border-0 py-3 text-gray-800" style="font-size: 0.85rem; font-weight: 700; padding-left: 20px;">Tanggal</th>
                            <th class="border-0 py-3 text-gray-800" style="font-size: 0.85rem; font-weight: 700;">Reference</th>
                            <th class="border-0 py-3 text-gray-800" style="font-size: 0.85rem; font-weight: 700;">Deskripsi</th>
                            <th class="border-0 py-3 text-gray-800 text-center" style="font-size: 0.85rem; font-weight: 700;">Bukti</th>
                            <th class="border-0 py-3 text-gray-800 text-center" style="font-size: 0.85rem; font-weight: 700;">Keterangan</th>
                            <th class="border-0 py-3 text-gray-800 text-right" style="font-size: 0.85rem; font-weight: 700;">Nominal</th>
                            <th class="border-0 py-3 text-gray-800 text-center" style="font-size: 0.85rem; font-weight: 700; padding-right: 20px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td class="align-middle text-gray-700" style="font-size: 0.88rem; padding-left: 20px;">
                                <div class="font-weight-bold text-gray-900">{{ $tx->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-muted">{{ $tx->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="align-middle text-xs font-weight-bold text-gray-700">{{ $tx->reference_no }}</td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-gray-900" style="font-size: 0.88rem;">{{ $tx->source ?: ($tx->type == 'income' ? 'Pemasukan' : 'Penarikan Dana') }}</div>
                                @if(isset($tx->description) && $tx->description)
                                    <div class="text-xs text-muted">{{ $tx->description }}</div>
                                @endif
                                @if(isset($tx->type) && $tx->type == 'withdrawal')
                                    <div class="text-xs text-primary font-weight-bold mt-1">{{ $tx->bank_name ?? '' }} - {{ $tx->account_number ?? '' }}</div>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if(isset($tx->proof_of_transfer) && $tx->proof_of_transfer)
                                    <img src="{{ asset($tx->proof_of_transfer) }}" 
                                         alt="Bukti Transfer" 
                                         class="img-thumbnail shadow-sm" 
                                         style="width: 45px; height: 45px; object-fit: cover; cursor: pointer; border-radius: 6px; transition: transform 0.2s;"
                                         onclick="showProof('{{ asset($tx->proof_of_transfer) }}')"
                                         onmouseover="this.style.transform='scale(1.1)'"
                                         onmouseout="this.style.transform='scale(1)'">
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if(isset($tx->admin_note) && $tx->admin_note)
                                    <div class="font-weight-bold {{ (isset($tx->status) && $tx->status == 'rejected') ? 'text-danger' : ((isset($tx->status) && $tx->status == 'success') ? 'text-success' : 'text-primary') }}" style="font-size: 0.8rem; line-height: 1.3;">
                                        {{ $tx->admin_note }}
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-right font-weight-bold {{ $tx->type == 'income' ? 'text-success' : 'text-danger' }}" style="font-size: 0.88rem;">
                                {{ $tx->type == 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="align-middle text-center" style="padding-right: 20px;">
                                <div class="d-flex align-items-center justify-content-center">
                                    @if(isset($tx->status) && $tx->status == 'success')
                                        <span class="badge badge-success px-3 py-1 shadow-sm">Berhasil</span>
                                    @elseif(isset($tx->status) && $tx->status == 'pending')
                                        <span class="badge badge-warning px-3 py-1 shadow-sm">Pending</span>
                                    @else
                                        <span class="badge badge-danger px-3 py-1 shadow-sm">Ditolak</span>
                                    @endif

                                    @if(isset($tx->status) && $tx->status == 'pending' && isset($tx->id) && $tx->id)
                                    <form action="{{ route('wallet.transaction.destroy', $tx->id) }}" method="POST" class="ml-2 mb-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger py-1 px-3 shadow-sm font-weight-bold btn-cancel-withdrawal" style="font-size: 0.65rem; border-radius: 20px;" title="Batalkan Pengajuan">
                                            Batalkan
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-2x mb-3 d-block opacity-50"></i>
                                Data transaksi tidak ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="p-3 border-top bg-light d-flex justify-content-end">
                    {{ $transactions->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Proof Modal -->
<div class="modal fade" id="proofModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow animate-up" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold" style="font-size: 1rem;"><i class="fas fa-image mr-1"></i> Bukti Transfer</h5>
                <button class="close text-white" type="button" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0 text-center bg-light">
                <img id="proofImage" src="" class="img-fluid" style="max-height: 80vh;">
            </div>
            <div class="modal-footer border-0 bg-white">
                <a id="downloadProof" href="" download class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold" style="border-radius: 8px;">
                    <i class="fas fa-download mr-1"></i> Simpan Gambar
                </a>
                <button class="btn btn-secondary btn-sm px-3 font-weight-bold" type="button" data-dismiss="modal" style="border-radius: 8px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showProof(url) {
        document.getElementById('proofImage').src = url;
        document.getElementById('downloadProof').href = url;
        $('#proofModal').modal('show');
    }

    // SweetAlert untuk pembatalan penarikan
    $(document).on('click', '.btn-cancel-withdrawal', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Batalkan Pengajuan?',
            text: "Apakah Anda yakin ingin membatalkan pengajuan penarikan ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection
