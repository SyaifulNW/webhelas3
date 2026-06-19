<form id="daily-activity-form" action="{{ route('admin.daily-activity.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-bold">Tanggal:</label>
        <div style="max-width: 250px;">
            <input type="date" name="tanggal" class="form-control" 
                   value="{{ $tanggal }}"
                   onchange="window.location='?tanggal=' + this.value">
        </div>
        <!-- Export PDF -->
        <div class="mb-3 text-end">
            <a href="{{ route('admin.daily-activity.exportPdf', ['bulan' => \Carbon\Carbon::parse($tanggal)->format('Y-m')]) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    @foreach($activities as $kategoriId => $list)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>{{ $list->first()->kategori->nama ?? 'Tanpa Kategori' }}</span>
                @if(($list->first()->kategori->nama ?? '') === 'Aktivitas Merawat Customer')
                    <small class="fst-italic"></small>
                @endif
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0 table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:35%">Aktivitas</th>
                            <th style="width:12%">Target Harian</th>
                            <th style="width:12%">Target Bulan</th>
                            <th style="width:13%">Bobot</th>
                            <th style="width:15%">Realisasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $i => $act)
                            <tr>
                                <td class="text-center">{{ $i+1 }}</td>
                                <td>{{ $act->nama }}</td>
                                @if(strpos($act->nama, 'Viewer') !== false)
                                    <td class="text-center">100%</td>
                                    <td class="text-center">100%</td>
                                @else
                                    <td class="text-center">{{ number_format($act->target_bulanan / 25, 0) }}</td>
                                    <td class="text-center">{{ number_format($act->target_bulanan, 0) }}</td>
                                @endif
                                <td class="text-center">{{ $act->bobot }}</td>
                                <td>
                                    <input type="number" 
                                           name="realisasi[{{ $act->id }}]" 
                                           class="form-control form-control-sm {{ in_array($act->id, $automatedIds ?? []) ? 'bg-light' : '' }}"
                                           min="0"
                                           value="{{ $daily[$act->id] ?? 0 }}"
                                           {{ in_array($act->id, $automatedIds ?? []) ? 'readonly' : '' }}>
                                    @if(in_array($act->id, $automatedIds ?? []))
                                        <small class="text-primary" style="font-size: 0.7rem;">
                                            <i class="fas fa-magic"></i> Otomatis dari Database
                                        </small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <button type="submit" class="btn btn-success">
        💾 Simpan Aktivitas
    </button>
</form>
