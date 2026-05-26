@php
    $items = $data ?? [];
    $totalPencapaianAll = collect($items)->sum('total_nominal');
    $totalTargetAll = collect($items)->sum('target');
    $totalPersentaseAll = $totalTargetAll > 0 ? min(100, round(($totalPencapaianAll / $totalTargetAll) * 100)) : 0;
    $isTotalTercapai = $totalPersentaseAll >= 100;
@endphp
<table class="table table-bordered table-hover align-middle mb-0 custom-target-table" style="font-size: 1rem;">
    <thead class="table-secondary text-dark shadow-sm">
        <tr>
            <th class="text-center py-3" style="width: 50px;">No</th>
            <th class="py-3" style="min-width: 220px;">{{ ($isChapter ?? false) ? 'Nama Chapter' : 'Nama CS' }}</th>
            <th class="text-end py-3 px-3">Pencapaian</th>
            @if(!($isChapter ?? false))
                <th class="text-end py-3 px-3">Target</th>
            @endif
            <th class="text-center py-3" style="width: 150px;">{{ ($isChapter ?? false) ? 'Total Peserta M1T Aktif' : 'Status' }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $index => $sales)
            @php
                $isTercapai = $sales['realisasi'] >= 100;
                $showBreakdown = (request('type', 'all') === 'all') && (!($sales['is_spp_row'] ?? false)) && (($sales['mbc_nominal'] ?? 0) > 0 || ($sales['m1t_nominal'] ?? 0) > 0);
            @endphp
             <tr class="target-row transition-all position-relative {{ ($sales['is_spp_row'] ?? false) ? 'bg-light' : '' }}">
                 <td class="text-center fw-medium text-muted fs-6">{{ $index + 1 }}</td>
                 <td class="fw-bold">
                     <div class="d-flex align-items-center">
                         @if($sales['is_spp_row'] ?? false)
                             <div class="avatar bg-soft-info text-info fw-bold rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1rem; background-color: #e0f7fa;">
                                 <i class="fa-solid fa-receipt"></i>
                             </div>
                             <div class="d-flex flex-column">
                                 <span class="fs-6 fw-bold text-dark">SPP M1T</span>
                                 <small class="badge badge-light border text-muted" style="font-size: 0.65rem; width: fit-content;">AUTO</small>
                             </div>
                         @elseif($sales['is_lainnya_row'] ?? false)
                             <div class="avatar bg-soft-warning text-warning fw-bold rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1rem; background-color: #fff8e1;">
                                 <i class="fa-solid fa-sack-dollar"></i>
                             </div>
                             <div class="d-flex flex-column">
                                 <span class="fs-6 fw-bold text-dark">Pendapatan Lainnya</span>
                                 <small class="badge badge-light border text-muted" style="font-size: 0.65rem; width: fit-content;">MANUAL</small>
                             </div>
                         @elseif($sales['is_chapter_summary_row'] ?? false)
                             <div class="avatar bg-soft-success text-success fw-bold rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1rem; background-color: #e8f5e9;">
                                 <i class="fa-solid fa-map-location-dot"></i>
                             </div>
                             <div class="d-flex flex-column">
                                 <span class="fs-6 fw-bold text-dark">{{ $sales['nama'] }}</span>
                                 <small class="badge badge-light border text-muted" style="font-size: 0.65rem; width: fit-content;">CHAPTER TOTAL</small>
                             </div>
                         @else
                             <div class="avatar bg-soft-primary text-primary fw-bold rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1rem; background-color: #e6f3ff;">
                                 {{ substr($sales['nama'], 0, 1) }}
                             </div>
                                 <span class="fs-6 fw-bold text-dark">{{ $sales['nama'] }}</span>
                         @endif
                         
                         @if($isChapter ?? false)
                             <div class="d-flex gap-1 ms-2 mt-1">
                                 <button class="btn btn-xs btn-outline-primary py-0 px-2 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#personal-detail-{{ $index }}" style="font-size: 0.65rem; border-radius: 20px;">
                                     <i class="fa-solid fa-user me-1"></i> Pribadi
                                 </button>
                                 @if(!empty($sales['agents']))
                                     <button class="btn btn-xs btn-outline-info py-0 px-2 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#agents-detail-{{ $index }}" style="font-size: 0.65rem; border-radius: 20px;">
                                         <i class="fa-solid fa-users me-1"></i> Agen ({{ count($sales['agents']) }})
                                     </button>
                                 @endif
                             </div>
                         @endif
                     </div>

                     @if($isChapter ?? false)
                         {{-- 1. Collapsible Pribadi --}}
                         <div class="collapse mt-2" id="personal-detail-{{ $index }}">
                             <div class="bg-white p-2 rounded border border-primary shadow-sm" style="min-width: 250px;">
                                 <div class="fw-bold text-primary border-bottom mb-2 pb-1 d-flex justify-content-between align-items-center" style="font-size: 0.7rem; text-transform: uppercase;">
                                     <span><i class="fa-solid fa-user me-1"></i> Pendapatan Pribadi</span>
                                     <span class="badge bg-primary text-white">Rp {{ number_format($sales['personal_nominal'] ?? 0, 0, ',', '.') }}</span>
                                 </div>
                                 <div class="text-muted italic" style="font-size: 0.65rem;">
                                     Penjualan langsung oleh Chapter Head.
                                 </div>
                             </div>
                         </div>

                         {{-- 2. Collapsible Agen --}}
                         @if(!empty($sales['agents']))
                             <div class="collapse mt-2" id="agents-detail-{{ $index }}">
                                 <div class="bg-light p-2 rounded border border-info shadow-sm" style="min-width: 250px;">
                                     <div class="fw-bold text-info border-bottom mb-2 pb-1 d-flex justify-content-between align-items-center" style="font-size: 0.7rem; text-transform: uppercase;">
                                         <span><i class="fa-solid fa-user-group me-1"></i> Pendapatan Agen ({{ count($sales['agents'] ?? []) }})</span>
                                         <span class="badge bg-info text-white">Rp {{ number_format($sales['agents_total_nominal'] ?? 0, 0, ',', '.') }}</span>
                                     </div>
                                     <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                                         <tbody>
                                             @foreach($sales['agents'] as $agent)
                                                 <tr class="border-bottom border-white">
                                                     <td class="py-1 px-0 fw-bold text-dark">{{ $agent['nama'] }}</td>
                                                     <td class="py-1 px-0 text-end text-info fw-bold">Rp {{ number_format($agent['total_nominal'], 0, ',', '.') }}</td>
                                                 </tr>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                         @endif
                     @endif
                 </td>
                <td class="text-end px-3">
                    @if($showBreakdown)
                        <div class="d-flex gap-2 justify-content-end flex-wrap mb-2">
                            @if(($sales['mbc_nominal'] ?? 0) > 0)
                                <div class="d-flex flex-column align-items-end">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge px-3 py-2 fw-semibold text-white" style="font-size:0.8rem; background:#4e73df; border-radius:20px; letter-spacing:0.3px;">
                                            MBC: Rp {{ number_format($sales['mbc_nominal'], 0, ',', '.') }}
                                        </span>
                                        <button class="btn btn-link p-0 text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#mbc-detail-{{ $index }}-{{ $loop->parent->index ?? 0 }}" style="font-size: 1rem; text-decoration: none;">
                                            <i class="fa-solid fa-circle-plus"></i>
                                        </button>
                                    </div>
                                    <div class="collapse" id="mbc-detail-{{ $index }}-{{ $loop->parent->index ?? 0 }}">
                                        <div class="mt-2 text-end bg-white border rounded p-2 shadow-sm" style="min-width: 180px;">
                                            @foreach($sales['mbc_breakdown'] as $cName => $cNominal)
                                                <div class="d-flex justify-content-between gap-2 border-bottom py-1" style="font-size: 0.75rem;">
                                                    <span class="text-muted text-start">{{ $cName }}</span>
                                                    <span class="fw-bold text-dark">Rp {{ number_format($cNominal, 0, ',', '.') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(($sales['m1t_nominal'] ?? 0) > 0)
                                <span class="badge px-3 py-2 fw-semibold text-white" style="font-size:0.8rem; background:#1cc88a; border-radius:20px; letter-spacing:0.3px;">
                                    M1T: Rp {{ number_format($sales['m1t_nominal'], 0, ',', '.') }}
                                </span>
                            @endif
                            @if(($sales['spp_nominal'] ?? 0) > 0)
                                <span class="badge px-3 py-2 fw-semibold text-white" style="font-size:0.8rem; background:#36b9cc; border-radius:20px; letter-spacing:0.3px;">
                                    SPP: Rp {{ number_format($sales['spp_nominal'], 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    @if(!($isChapter ?? false))
                        <div class="fw-bold text-dark" style="font-size: 1.1rem;">
                            <span class="text-muted small fw-normal">Total:</span>
                            Rp {{ number_format($sales['total_nominal'], 0, ',', '.') }}
                        </div>
                    @else
                        <div class="fw-bold text-primary" style="font-size: 1.1rem;">
                            <span class="text-muted small fw-normal">Total:</span>
                            Rp {{ number_format($sales['total_nominal'] ?? 0, 0, ',', '.') }}
                        </div>
                        @if(!empty($sales['agents'] ?? []))
                            <small class="text-muted" style="font-size: 0.65rem;">(Termasuk Agen)</small>
                        @endif
                    @endif
                </td>
                @if(!($isChapter ?? false))
                    <td class="text-end px-3 text-muted fw-medium" style="font-size: 1rem;">
                        @if(!($sales['is_spp_row'] ?? false) && !($sales['is_lainnya_row'] ?? false))
                            Rp {{ number_format($sales['target'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                @endif
                <td class="text-center px-2">
                    @if($isChapter ?? false)
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span class="fs-4 fw-bold text-primary">{{ number_format($sales['m1t_aktif_count'] ?? 0) }}</span>
                            <small class="text-muted fw-bold" style="font-size: 0.75rem;">PESERTA AKTIF</small>
                        </div>
                    @elseif(!($sales['is_spp_row'] ?? false) && !($sales['is_lainnya_row'] ?? false))
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span class="badge rounded-pill px-3 py-2 fw-bold mb-1 {{ $isTercapai ? 'bg-success text-white' : 'bg-warning text-dark' }}" style="font-size: 0.85rem;">
                                <i class="fa-solid {{ $isTercapai ? 'fa-check-circle' : 'fa-hourglass-start' }} me-1"></i>
                                {{ $isTercapai ? 'Tercapai' : 'Belum' }}
                            </span>
                            
                            <div class="w-100 px-2 mt-2">
                                <div class="progress rounded-pill bg-light" style="height: 7px;">
                                    <div class="progress-bar rounded-pill {{ $isTercapai ? 'bg-success' : 'bg-warning' }} progress-bar-animated" 
                                         role="progressbar" 
                                         style="width: {{ min(100, $sales['realisasi']) }}%;" 
                                         aria-valuenow="{{ min(100, $sales['realisasi']) }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1 fw-bold" style="font-size:0.85rem;">{{ $sales['realisasi'] }}%</small>
                            </div>
                        </div>
                    @else
                        <span class="text-muted small italic">{{ ($sales['is_lainnya_row'] ?? false) ? 'Manual/Income' : 'Manual/Monthly' }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-muted fst-italic text-center py-4">
                    <i class="fa-regular fa-folder-open mb-2 fs-3 text-secondary opacity-50 block"></i><br>
                    Data pencapaian target belum tersedia.
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot class="table-light fw-bold border-top-2">
        <tr>
            <td colspan="2" class="text-center py-3 fs-6">TOTAL KESELURUHAN</td>
            <td class="text-end px-3 py-3 text-dark fs-6">Rp {{ number_format($totalPencapaianAll, 0, ',', '.') }}</td>
            @if(!($isChapter ?? false))
                <td class="text-end px-3 py-3 text-dark fs-6">Rp {{ number_format($totalTargetAll, 0, ',', '.') }}</td>
            @endif
            <td class="text-center px-2 py-3">
                <span class="badge rounded-pill {{ $isTotalTercapai ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 fs-6 shadow-sm">
                    {{ ($isChapter ?? false) ? '-' : $totalPersentaseAll . '%' }}
                </span>
            </td>
        </tr>
    </tfoot>
</table>
