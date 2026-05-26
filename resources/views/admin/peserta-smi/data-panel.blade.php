@php
    $m = (int)date('n');
    $y = (int)date('Y');
@endphp

<div class="peserta-m1t-container p-4" style="background: #f8f9fc;">
    <!-- Main Data Table Card -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-0 font-weight-bold text-dark"><i class="fas fa-users-cog text-primary mr-2"></i>HALAMAN DATA PESERTA M1T</h5>
                <p class="text-xs text-muted mb-0 font-weight-bold">Kelola informasi detail dan status pembayaran lifetime peserta</p>
            </div>
            <div class="d-flex align-items-center" style="gap: 15px;">
                <!-- Filter Status tetap di sini agar tidak terlalu sesak di tabel -->
                <div class="d-flex align-items-center">
                    <div class="text-xxs font-weight-bold text-muted mr-1">STATUS:</div>
                    <select class="form-control form-control-sm border bg-white font-weight-bold text-xxs rounded-lg shadow-sm" id="filter_status" style="width: 110px; cursor: pointer;">
                        <option value="all">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Pending">Pending</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Drop Out">Drop Out</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-bordered table-hover align-items-center mb-0" style="font-size: 0.75rem; border-collapse: separate; border-spacing: 0;">
                <thead class="text-white font-weight-bold" style="position: sticky; top: 0; z-index: 100;">
                    <tr class="bg-primary">
                        <th class="border py-3 text-center text-white" width="40">No</th>
                        <th class="border py-3 text-white">Nama Peserta</th>
                        <th class="text-center align-middle" style="width: 150px;">
                            <div class="mb-1 text-white font-weight-bold">CHAPTER</div>
                            <select class="form-control form-control-sm border-0 font-weight-bold text-dark mx-auto" id="filter_chapter" style="width: 120px; height: 25px; font-size: 9px; border-radius: 4px; cursor: pointer; padding: 2px 5px; color: #000 !important;">
                                <option value="all">Semua</option>
                                <option value="MBC PUSAT">MBC PUSAT</option>
                                <option value="DEPOK">CHAPTER DEPOK</option>
                                <option value="JAKARTA">CHAPTER JAKARTA</option>
                                <option value="BEKASI">CHAPTER BEKASI</option>
                                <option value="BOGOR">CHAPTER BOGOR</option>
                                <option value="TANGERANG">CHAPTER TANGERANG</option>
                                <option value="BANDUNG">CHAPTER BANDUNG</option>
                                <option value="SEMARANG">CHAPTER SEMARANG</option>
                                <option value="SURABAYA">CHAPTER SURABAYA</option>
                            </select>
                        </th>
                        <th class="text-center align-middle" style="width: 180px;">
                            <div class="mb-1 text-white font-weight-bold">CS PENANGGUNG JAWAB</div>
                            <select class="form-control form-control-sm border-0 font-weight-bold text-dark mx-auto" id="filter_cs" style="width: 140px; height: 25px; font-size: 9px; border-radius: 4px; cursor: pointer; padding: 2px 5px; color: #000 !important;">
                                <option value="all">Semua Tim</option>
                                <option value="cs-mbc">CS-MBC</option>
                                <option value="DEPOK">CHAPTER DEPOK</option>
                                <option value="JAKARTA">CHAPTER JAKARTA</option>
                                <option value="BEKASI">CHAPTER BEKASI</option>
                                <option value="BOGOR">CHAPTER BOGOR</option>
                                <option value="TANGERANG">CHAPTER TANGERANG</option>
                                <option value="BANDUNG">CHAPTER BANDUNG</option>
                                <option value="SEMARANG">CHAPTER SEMARANG</option>
                                <option value="SURABAYA">CHAPTER SURABAYA</option>
                            </select>
                        </th>
                        <th class="border py-3 text-center text-white">Tanggal Join</th>
                        <th class="border py-3 text-right text-white">Nominal SPP</th>
                        <th class="border py-3 text-center text-white">Status Bln Ini</th>
                        <th class="border py-3 text-right text-white">Total Tunggakan</th>
                        <th class="border py-3 text-center text-white">Terakhir Bayar</th>
                        <th class="border py-3 text-center text-white">Jatuh Tempo</th>
                        <th class="border py-3 text-right text-white">Total Sudah Bayar</th>
                        <th class="border py-3 text-center text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @include('admin.peserta-smi.table-rows')
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center" id="full-pagination-container">
                <div class="text-xs text-dark font-weight-bold">
                    Menampilkan {{ $data->firstItem() }}-{{ $data->lastItem() }} dari {{ $data->total() }} peserta
                </div>
                <div class="pagination-sm">
                    {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Requirement 5.3: Detail View Modal -->
<div class="modal fade" id="modalDetailPeserta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow rounded-xl">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-circle mr-2"></i>Detail Pembayaran Peserta</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="detail_peserta_content">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 font-weight-bold">Memuat riwayat pembayaran...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-xl { border-radius: 15px !important; }
    .rounded-lg { border-radius: 10px !important; }
    .rounded-left-lg { border-top-left-radius: 10px !important; border-bottom-left-radius: 10px !important; }
    .rounded-right-lg { border-top-right-radius: 10px !important; border-bottom-right-radius: 10px !important; }
    .bg-soft-primary { background: rgba(78, 115, 223, 0.1); }
    .text-xxs { font-size: 0.65rem; }
    .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.7rem; }
    
    /* Contrast Fixes */
    .text-dark { color: #000 !important; }
    .text-muted { color: #000 !important; opacity: 1 !important; }
</style>

<script>
    function showDetailPeserta(id) {
        $('#modalDetailPeserta').modal('show');
        // Simulated detail view
        setTimeout(() => {
            $('#detail_peserta_content').html(`
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="text-xxs font-weight-bold text-dark text-uppercase">Informasi Peserta</label>
                        <h4 class="font-weight-bold text-dark mb-0">Linda M1T</h4>
                        <p class="text-sm text-dark font-weight-bold">M1T-2024-001 | Chapter Depok</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <label class="text-xxs font-weight-bold text-dark text-uppercase">Total Lifetime</label>
                        <h4 class="font-weight-bold text-success">Rp 12.000.000</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-dark font-weight-bold" style="font-size: 0.75rem;">
                        <thead class="bg-light">
                            <tr>
                                <th>Bulan</th>
                                <th>Status</th>
                                <th>Nominal</th>
                                <th>Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${['Jan', 'Feb', 'Mar', 'Apr', 'Mei'].map(m => `
                                <tr>
                                    <td>${m} 2024</td>
                                    <td><span class="badge badge-success px-2">Lunas</span></td>
                                    <td>Rp 1.000.000</td>
                                    <td>05 ${m} 2024</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `);
        }, 800);
    }
</script>
