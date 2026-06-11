import os

file_path = r'c:\xampp\htdocs\webhelas\resources\views\admin\keuangan\kas_kecil.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

start_marker = '        <!-- Summary Cards -->'
end_marker = '    <script>'

start_idx = content.find(start_marker)
end_idx = content.rfind('    </div>\n\n    <script>')

if start_idx != -1 and end_idx != -1:
    before = content[:start_idx]
    after = content[end_idx:]
    
    new_content = """        <div class="tab-content" id="kasTabsContent">
            @foreach($categories as $catName => $data)
                @php
                    $catKas = $data['kas'];
                    $catSaldoAwal = $data['saldoAwal'];
                    $totalMasuk = $catKas->sum('masuk');
                    $totalKeluar = $catKas->sum('keluar');
                    $saldoAkhir = $catSaldoAwal + $totalMasuk - $totalKeluar;
                    $tabId = strtolower($catName);
                @endphp
                <div class="tab-pane fade {{ $kategori == $catName ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
                    
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-2">
                            <div class="card border-left-info shadow h-100 py-1">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Saldo Bulan Sebelumnya
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">Rp
                                                {{ number_format($catSaldoAwal, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-2">
                            <div class="card border-left-success shadow h-100 py-1">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan</div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">Rp
                                                {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-2">
                            <div class="card border-left-danger shadow h-100 py-1">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">Rp
                                                {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-2">
                            <div class="card border-left-primary shadow h-100 py-1">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Akhir</div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">Rp
                                                {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">Data Kas Kecil Helas {{ $catName }}</h6>
                            <button type="button" class="btn btn-primary btn-sm shadow-sm btnTambahBaris" data-kategori="{{ $catName }}">
                                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah data
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 tableKasKecil">
                                    <thead>
                                        <tr class="text-center font-weight-bold text-dark header-yellow">
                                            <th width="5%">NO</th>
                                            <th width="12%">TGL</th>
                                            <th>KET</th>
                                            <th width="15%">SALDO MASUK</th>
                                            <th width="15%">SALDO KELUAR</th>
                                            <th width="15%">SISA</th>
                                            <th width="10%">BUKTI</th>
                                            <th width="5%">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tbodyKasKecil" data-kategori="{{ $catName }}">
                                        @php $runningBalance = $catSaldoAwal; @endphp
                                        @if($catSaldoAwal != 0)
                                        <tr class="bg-light">
                                            <td class="text-center align-middle">-</td>
                                            <td class="text-center align-middle">-</td>
                                            <td class="align-middle px-3 font-italic text-muted">Saldo Bulan Sebelumnya</td>
                                            <td class="text-right align-middle px-3">-</td>
                                            <td class="text-right align-middle px-3">-</td>
                                            <td class="text-right align-middle px-3 font-weight-bold">
                                                {{ number_format($catSaldoAwal, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center align-middle">-</td>
                                            <td class="text-center align-middle">-</td>
                                        </tr>
                                        @endif
                                        @forelse($catKas as $index => $item)
                                            @php $runningBalance += ($item->masuk - $item->keluar); @endphp
                                            <tr data-id="{{ $item->id }}">
                                                <td class="text-center align-middle">{{ $index + 1 }}</td>
                                                <td class="text-center align-middle">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                                <td class="align-middle px-3">{{ $item->keterangan }}</td>
                                                <td class="text-right align-middle px-3">
                                                    {{ $item->masuk > 0 ? number_format($item->masuk, 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right align-middle px-3">
                                                    {{ $item->keluar > 0 ? number_format($item->keluar, 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right align-middle px-3 font-weight-bold">
                                                    {{ number_format($runningBalance, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex flex-column align-items-center">
                                                        @if($item->bukti_transfer)
                                                            @php
                                                                $isPdf = strtolower(pathinfo($item->bukti_transfer, PATHINFO_EXTENSION)) === 'pdf';
                                                            @endphp
                                                            @if($isPdf)
                                                                <div class="position-relative mb-1">
                                                                    <a href="{{ asset($item->bukti_transfer) }}" target="_blank" class="text-danger" title="Lihat PDF" style="display:inline-block; border: 1px solid #ddd; border-radius: 4px; padding: 2px 4px; background: #fff;">
                                                                        <i class="far fa-file-pdf fa-2x"></i>
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div class="position-relative mb-1">
                                                                    <img src="{{ asset($item->bukti_transfer) }}" alt="Bukti"
                                                                        class="img-thumbnail shadow-sm preview-image"
                                                                        style="width: 45px; height: 45px; object-fit: cover; cursor: pointer;"
                                                                        onclick="previewImage('{{ asset($item->bukti_transfer) }}', 'Bukti Kas - {{ $item->keterangan }}')">
                                                                </div>
                                                            @endif
                                                        @endif
            
                                                        {{-- Tombol Upload untuk Lama/Baru --}}
                                                        <label class="btn btn-sm btn-outline-primary p-0 px-2 m-0"
                                                            style="font-size: 10px; cursor: pointer;" title="Upload/Ganti Bukti">
                                                            <i class="fas fa-upload mr-1"></i>
                                                            {{ $item->bukti_transfer ? 'Ganti' : 'Upload' }}
                                                            <input type="file" class="d-none upload-bukti-existing"
                                                                data-id="{{ $item->id }}" accept="image/*,.pdf">
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <form action="{{ route('admin.keuangan.kas-kecil.destroy', $item->id) }}" method="POST"
                                                        class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="emptyRow">
                                                <td colspan="8" class="text-center py-4 text-muted small italic">Belum ada data.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
"""
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(before + new_content + after)
    print("Patch applied.")
else:
    print(f"Markers not found. start_idx={start_idx}, end_idx={end_idx}")
