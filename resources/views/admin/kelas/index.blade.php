@extends('layouts.masteradmin')

@section('content')
    {{-- Header & Filter Section --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fa-solid fa-calendar-days text-primary me-2"></i> Manajemen Kelas
            </h1>
            <p class="text-muted small mb-0">Kelola jadwal dan agenda kelas pelatihan Anda.</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                <i class="fa-solid fa-plus me-2"></i> Tambah Kelas
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.kelas.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="letter-spacing: 0.5px;">Pilih Bulan</label>
                    <select name="bulan" class="form-select border-0 bg-light fw-semibold" style="height: 45px; border-radius: 10px;">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="letter-spacing: 0.5px;">Pilih Tahun</label>
                    <select name="tahun" class="form-select border-0 bg-light fw-semibold" style="height: 45px; border-radius: 10px;">
                        <option value="">Semua Tahun</option>
                        @php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 3;
                            $endYear = $currentYear + 3;
                        @endphp
                        @for($y = $startYear; $y <= $endYear; $y++)
                            <option value="{{ $y }}" {{ (request('tahun') == $y || (!request()->has('tahun') && $y == $currentYear)) ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-dark fw-bold px-4 shadow-sm w-100" style="height: 45px; border-radius: 10px;">
                        <i class="fa-solid fa-filter me-2"></i> TERAPKAN FILTER
                    </button>
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline-secondary fw-bold px-3 shadow-sm" style="height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-rotate"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3 ps-4 text-uppercase small fw-bold text-center" style="letter-spacing: 1px; width: 70px;">No</th>
                            <th class="py-3 text-uppercase small fw-bold" style="letter-spacing: 1px;">Info Kelas</th>
                            <th class="py-3 text-uppercase small fw-bold text-center" style="letter-spacing: 1px;">Periode</th>
                            <th class="py-3 text-uppercase small fw-bold text-center" style="letter-spacing: 1px;">Status</th>
                            <th class="py-3 text-uppercase small fw-bold text-end pe-4" style="letter-spacing: 1px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($kelas as $index => $k)
                            @php
                                $today = \Carbon\Carbon::today();
                                $mulai = \Carbon\Carbon::parse($k->tanggal_mulai);
                                $selesai = \Carbon\Carbon::parse($k->tanggal_selesai);
                                
                                if($today->lt($mulai)) {
                                    $statusLabel = 'Upcoming';
                                    $statusClass = 'bg-info text-dark';
                                } elseif($today->gt($selesai)) {
                                    $statusLabel = 'Completed';
                                    $statusClass = 'bg-secondary text-white';
                                } else {
                                    $statusLabel = 'Ongoing';
                                    $statusClass = 'bg-success text-white';
                                }
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">{{ $k->nama_kelas }}</div>
                                    <div class="text-muted small" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $k->deskripsi ?? 'Tidak ada deskripsi' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-light text-dark border fw-normal mb-1">
                                            <i class="fa-regular fa-calendar-check text-primary me-1"></i>
                                            {{ $mulai->format('d M Y') }}
                                        </span>
                                        <i class="fa-solid fa-arrow-down-long text-muted small opacity-50 my-1"></i>
                                        <span class="badge bg-light text-dark border fw-normal">
                                            <i class="fa-regular fa-calendar-xmark text-danger me-1"></i>
                                            {{ $selesai->format('d M Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill" style="font-size: 0.75rem; min-width: 90px;">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button 
                                        class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold btn-edit"
                                        data-id="{{ $k->id }}"
                                        data-nama="{{ $k->nama_kelas }}"
                                        data-mulai="{{ $k->tanggal_mulai }}"
                                        data-selesai="{{ $k->tanggal_selesai }}"
                                        data-deskripsi="{{ $k->deskripsi }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditKelas"
                                    >
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center">
                                    <div class="text-muted mb-2"><i class="fa-solid fa-folder-open fa-3x opacity-20"></i></div>
                                    <div class="fw-bold">Belum ada data kelas</div>
                                    <div class="small text-muted">Silakan tambah kelas baru atau sesuaikan filter Anda.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Modal Tambah Kelas --}}
<div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-labelledby="modalTambahKelasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('admin.kelas.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fa-solid fa-plus-circle me-2"></i> Tambah Kelas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Kelas</label>
            <input type="text" name="nama_kelas" class="form-control" required>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai</label>
              <input type="date" name="tanggal_mulai" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Selesai</label>
              <input type="date" name="tanggal_selesai" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success"><i class="fa-solid fa-save me-1"></i> Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- ✅ Modal Edit Kelas --}}
<div class="modal fade" id="modalEditKelas" tabindex="-1" aria-labelledby="modalEditKelasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" id="formEditKelas">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Kelas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Kelas</label>
            <input type="text" name="nama_kelas" id="edit_nama_kelas" class="form-control" required>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai</label>
              <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Selesai</label>
              <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning"><i class="fa-solid fa-save me-1"></i> Update</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- ✅ Script handle edit modal --}}
<script>


document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-edit');
    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            document.getElementById('edit_nama_kelas').value = this.dataset.nama;
            document.getElementById('edit_tanggal_mulai').value = this.dataset.mulai;
            document.getElementById('edit_tanggal_selesai').value = this.dataset.selesai;
            document.getElementById('edit_deskripsi').value = this.dataset.deskripsi;
      document.getElementById('formEditKelas').action = `/admin/kelas/${id}`;
        });
    });
});
</script>
@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

