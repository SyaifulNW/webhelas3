@php
    $latestSp = $item->salesplan->first();
    $statusKey = $latestSp ? strtolower($latestSp->status) : 'cold';
    $statusConfig = [
        'cold'           => ['label' => 'Cold',           'bg' => '#ffffff', 'text' => '#6c757d', 'rowBg' => '#ffffff'],
        'tertarik'       => ['label' => 'Tertarik',       'bg' => '#F2F527', 'text' => '#000000', 'rowBg' => '#F2F527'],
        'mau_transfer'   => ['label' => 'Mau Transfer',   'bg' => '#3CDE1D', 'text' => '#000000', 'rowBg' => '#3CDE1D'],
        'sudah_transfer' => ['label' => 'Sudah Transfer', 'bg' => '#1786E6', 'text' => '#ffffff', 'rowBg' => '#1786E6'],
        'no'             => ['label' => 'No',             'bg' => '#E61717', 'text' => '#ffffff', 'rowBg' => '#E61717'],
    ];
    $currentConfig = $statusConfig[$statusKey] ?? $statusConfig['cold'];

    $userRole = strtolower(auth()->user()->role);
    $canEdit  = false; // Administrators have read-only access in CS Database view
    $nominalVal = $latestSp ? ($latestSp->nominal ?? 0) : 0;
@endphp

<tr data-id="{{ $item->id }}" style="background-color: #ffffff; color: #212529; transition: background-color 0.3s ease;">
    {{-- 1. No --}}
    <td class="text-center" style="vertical-align: middle;">
        {{ isset($data) && method_exists($data, 'firstItem') ? $data->firstItem() + $loop->index : $loop->iteration }}
    </td>

    {{-- 2. Nama & WA --}}
    <td class="px-2 py-2">
        <div class="d-flex flex-column gap-2" style="min-width: 160px;">
            <div contenteditable="false" class="fw-bold text-dark text-nowrap p-1 px-2 shadow-sm" style="font-size:0.95rem; border:1px solid #dee2e6; border-radius:10px; background:#fff; min-height:32px; display:flex; align-items:center;">
                {{ $item->nama }}
            </div>
            <div class="d-flex align-items-center p-1 px-2 shadow-sm" style="border:1px solid #dee2e6; border-radius:10px; background:#fff; min-height:32px;">
                <i class="bi bi-phone me-1 text-primary" style="font-size:0.8rem;"></i>
                <span contenteditable="false" class="small text-secondary flex-grow-1" style="font-size:0.85rem; outline:none; border:none;">
                    {{ $item->no_wa }}
                </span>
            </div>
            <!-- Buttons -->
            <div class="d-flex align-items-center gap-1 mt-1">
                @if($item->no_wa)
                    @php $waNumber = preg_replace('/^0/', '62', $item->no_wa); @endphp
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="btn btn-success btn-sm rounded-circle d-flex align-items-center justify-content-center border-0 shadow-sm" style="width:28px; height:28px;">
                        <i class="bi bi-whatsapp" style="font-size:0.9rem;"></i>
                    </a>
                @endif
            </div>
        </div>
    </td>

    {{-- 3. Sumber Leads --}}
    <td>
        <select class="form-control form-control-sm select-sumber" data-id="{{ $item->id }}" style="font-size:0.85rem;" disabled>
            <option value="">- Pilih -</option>
            <option value="Ads"        {{ $item->leads == 'Ads'        ? 'selected' : '' }}>Ads</option>
            <option value="Sosmed"     {{ $item->leads == 'Sosmed'     ? 'selected' : '' }}>Sosmed</option>
            <option value="Zoom"       {{ $item->leads == 'Zoom'       ? 'selected' : '' }}>Zoom</option>
            <option value="Open House" {{ $item->leads == 'Open House' ? 'selected' : '' }}>Open House</option>
            <option value="Mandiri"    {{ $item->leads == 'Mandiri'    ? 'selected' : '' }}>Mandiri</option>
        </select>
    </td>

    {{-- 4. Prov/Kota --}}
    <td>
        <div class="d-flex flex-column gap-1">
            <select class="form-control form-control-sm select-provinsi mb-1" data-id="{{ $item->id }}" style="font-size:0.8rem;" disabled>
                <option value="">{{ $item->provinsi_nama ?: '-- Prov --' }}</option>
            </select>
            <select class="form-control form-control-sm select-kota" data-id="{{ $item->id }}" style="font-size:0.8rem;" disabled>
                <option value="">{{ $item->kota_nama ?: '-- Kota --' }}</option>
            </select>
        </div>
    </td>

    {{-- 5. Nama Bisnis --}}
    <td>
        <div contenteditable="false" class="fw-bold text-dark p-1 px-2 shadow-sm" style="font-size:0.9rem; border:1px solid #dee2e6; border-radius:8px; background:#fff; min-height:28px; min-width:140px;">
            {{ $item->nama_bisnis }}
        </div>
    </td>

    {{-- 6. Situasi Bisnis --}}
    <td class="text-wrap-normal">
        <div class="read-more-container" data-type="situasi">
            <div contenteditable="false" class="fw-bold" style="outline:none; min-height: 45px; background: rgba(255,255,255,0.9); color: #000; padding: 8px; border-radius: 8px; border: 1px solid #dee2e6;">{{ $item->situasi_bisnis }}</div>
        </div>
        @if(strlen($item->situasi_bisnis ?? '') > 100)
            <button class="btn-read-more">Baca Selengkapnya</button>
        @endif
    </td>
    
    {{-- 7. Potensi Ikut Kelas (Status Badges) --}}
    <td class="text-center" style="vertical-align: middle;">
        @php
            $validSalesplans = $item->salesplan->filter(function($sp) {
                return $sp->kelas_id != null;
            });
        @endphp
        <div class="d-flex flex-column align-items-center justify-content-center p-2" style="gap: 4px;">
            @if($validSalesplans->isNotEmpty())
                @foreach($validSalesplans as $sp)
                    @php 
                        $namaKls = $sp->kelas?->nama_kelas ?? '';
                        $shortKls = str_contains($namaKls,'Muslim Indonesia') ? 'M1T' : (str_contains($namaKls,'Muda Indonesia') ? 'Start-Up Muda' : $namaKls);
                        $statusKeySP = strtolower($sp->status);
                        $cfg = $statusConfig[$statusKeySP] ?? $statusConfig['cold'];
                        $schedule = \App\Models\ZoomSchedule::where('salesplan_id', $sp->id)->first();
                        
                        // Zoom status color configuration
                        $zoomStatus = $schedule ? strtolower($schedule->status) : '';
                        if ($zoomStatus === 'done' || $item->ikut_zoom == 1) {
                            $zoomColorBg = '#3CDE1D'; // Hijau
                            $zoomColorText = '#ffffff';
                            $zoomBorder = 'none';
                        } elseif ($zoomStatus === 'scheduled') {
                            $zoomColorBg = '#25799E'; // Biru
                            $zoomColorText = '#ffffff';
                            $zoomBorder = 'none';
                        } elseif ($zoomStatus === 'cancelled') {
                            $zoomColorBg = '#E61717'; // Merah
                            $zoomColorText = '#ffffff';
                            $zoomBorder = 'none';
                        } else {
                            $zoomColorBg = '#ffffff'; // Putih
                            $zoomColorText = '#475569';
                            $zoomBorder = '1px solid #cbd5e1';
                        }
                    @endphp
                    <div class="d-flex align-items-center justify-content-center mb-1" style="gap: 8px;">
                        <!-- Dedicated Zoom button for this prospect -->
                        <button type="button" class="btn btn-zoom-bant p-0 d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 24px; height: 24px; border-radius: 6px; background: {{ $zoomColorBg }}; color: {{ $zoomColorText }}; border: {{ $zoomBorder }}; transition: all 0.2s;"
                                data-id="{{ $item->id }}" 
                                data-nama="{{ $item->nama }}"
                                data-kelas-nama="{{ $namaKls }}"
                                data-salesplan-id="{{ $sp->id }}"
                                data-schedule-date="{{ $schedule ? $schedule->scheduled_at->format('Y-m-d\TH:i') : '' }}"
                                data-schedule-link="{{ $schedule ? $schedule->zoom_link : '' }}"
                                data-schedule-status="{{ $schedule ? $schedule->status : '' }}"
                                data-schedule-notes="{{ $schedule ? $schedule->notes : '' }}"
                                data-no-wa="{{ $item->no_wa }}"
                                data-can-edit="{{ $canEdit ? '1' : '0' }}"
                                data-bant-budget="{{ $item->bant_budget }}"
                                data-bant-authority="{{ $item->bant_authority }}"
                                data-bant-time="{{ $item->bant_time }}"
                                data-ikut-zoom="{{ $item->ikut_zoom }}"
                                title="Zoom & BANT ({{ $shortKls }})">
                            <i class="fas fa-video" style="font-size: 0.7rem;"></i>
                        </button>
                        
                        <!-- Follow Up button for this specific class -->
                        @php
                            $fuCount = 0;
                            for ($j = 1; $j <= 10; $j++) {
                                $atProp = "fu{$j}_at";
                                if (!empty($sp->$atProp)) {
                                    $fuCount++;
                                }
                            }
                        @endphp
                        <button type="button" class="btn btn-primary btn-sm btn-riwayat shadow-sm border-0 px-2 position-relative"
                                style="height: 24px; line-height: 1; font-size: 0.65rem; font-weight: 700; background: linear-gradient(45deg, #4e73df, #224abe); border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;"
                                data-kelas-nama="{{ $namaKls }}"
                                data-salesplan-id="{{ $sp->id }}"
                                data-id="{{ $item->id }}" data-nama="{{ $item->nama }}" 
                                data-fu1="{{ $sp->fu1_hasil }}"
                                data-fu1-wa="{{ $sp->fu1_wa ? 1 : 0 }}" data-fu1-telp="{{ $sp->fu1_telp ? 1 : 0 }}"
                                data-fu1-at="{{ $sp->fu1_at ? ($sp->fu1_at instanceof \Carbon\Carbon ? $sp->fu1_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu1_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu1-hasil="{{ $sp->fu1_hasil }}" data-fu1-tindak-lanjut="{{ $sp->fu1_tindak_lanjut }}"
                                data-fu2="{{ $sp->fu2_hasil }}"
                                data-fu2-wa="{{ $sp->fu2_wa ? 1 : 0 }}" data-fu2-telp="{{ $sp->fu2_telp ? 1 : 0 }}"
                                data-fu2-at="{{ $sp->fu2_at ? ($sp->fu2_at instanceof \Carbon\Carbon ? $sp->fu2_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu2_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu2-hasil="{{ $sp->fu2_hasil }}" data-fu2-tindak-lanjut="{{ $sp->fu2_tindak_lanjut }}"
                                data-fu3="{{ $sp->fu3_hasil }}"
                                data-fu3-wa="{{ $sp->fu3_wa ? 1 : 0 }}" data-fu3-telp="{{ $sp->fu3_telp ? 1 : 0 }}"
                                data-fu3-at="{{ $sp->fu3_at ? ($sp->fu3_at instanceof \Carbon\Carbon ? $sp->fu3_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu3_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu3-hasil="{{ $sp->fu3_hasil }}" data-fu3-tindak-lanjut="{{ $sp->fu3_tindak_lanjut }}"
                                data-fu4="{{ $sp->fu4_hasil }}"
                                data-fu4-wa="{{ $sp->fu4_wa ? 1 : 0 }}" data-fu4-telp="{{ $sp->fu4_telp ? 1 : 0 }}"
                                data-fu4-at="{{ $sp->fu4_at ? ($sp->fu4_at instanceof \Carbon\Carbon ? $sp->fu4_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu4_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu4-hasil="{{ $sp->fu4_hasil }}" data-fu4-tindak-lanjut="{{ $sp->fu4_tindak_lanjut }}"
                                data-fu5="{{ $sp->fu5_hasil }}"
                                data-fu5-wa="{{ $sp->fu5_wa ? 1 : 0 }}" data-fu5-telp="{{ $sp->fu5_telp ? 1 : 0 }}"
                                data-fu5-at="{{ $sp->fu5_at ? ($sp->fu5_at instanceof \Carbon\Carbon ? $sp->fu5_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu5_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu5-hasil="{{ $sp->fu5_hasil }}" data-fu5-tindak-lanjut="{{ $sp->fu5_tindak_lanjut }}"
                                data-fu6="{{ $sp->fu6_hasil }}"
                                data-fu6-wa="{{ $sp->fu6_wa ? 1 : 0 }}" data-fu6-telp="{{ $sp->fu6_telp ? 1 : 0 }}"
                                data-fu6-at="{{ $sp->fu6_at ? ($sp->fu6_at instanceof \Carbon\Carbon ? $sp->fu6_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu6_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu6-hasil="{{ $sp->fu6_hasil }}" data-fu6-tindak-lanjut="{{ $sp->fu6_tindak_lanjut }}"
                                data-fu7="{{ $sp->fu7_hasil }}"
                                data-fu7-wa="{{ $sp->fu7_wa ? 1 : 0 }}" data-fu7-telp="{{ $sp->fu7_telp ? 1 : 0 }}"
                                data-fu7-at="{{ $sp->fu7_at ? ($sp->fu7_at instanceof \Carbon\Carbon ? $sp->fu7_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu7_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu7-hasil="{{ $sp->fu7_hasil }}" data-fu7-tindak-lanjut="{{ $sp->fu7_tindak_lanjut }}"
                                data-fu8="{{ $sp->fu8_hasil }}"
                                data-fu8-wa="{{ $sp->fu8_wa ? 1 : 0 }}" data-fu8-telp="{{ $sp->fu8_telp ? 1 : 0 }}"
                                data-fu8-at="{{ $sp->fu8_at ? ($sp->fu8_at instanceof \Carbon\Carbon ? $sp->fu8_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu8_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu8-hasil="{{ $sp->fu8_hasil }}" data-fu8-tindak-lanjut="{{ $sp->fu8_tindak_lanjut }}"
                                data-fu9="{{ $sp->fu9_hasil }}"
                                data-fu9-wa="{{ $sp->fu9_wa ? 1 : 0 }}" data-fu9-telp="{{ $sp->fu9_telp ? 1 : 0 }}"
                                data-fu9-at="{{ $sp->fu9_at ? ($sp->fu9_at instanceof \Carbon\Carbon ? $sp->fu9_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu9_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu9-hasil="{{ $sp->fu9_hasil }}" data-fu9-tindak-lanjut="{{ $sp->fu9_tindak_lanjut }}"
                                data-fu10="{{ $sp->fu10_hasil }}" data-fu10-wa="{{ $sp->fu10_wa ? 1 : 0 }}"
                                data-fu10-telp="{{ $sp->fu10_telp ? 1 : 0 }}"
                                data-fu10-at="{{ $sp->fu10_at ? ($sp->fu10_at instanceof \Carbon\Carbon ? $sp->fu10_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu10_at)->format('d/m/Y H:i')) : '' }}"
                                data-fu10-hasil="{{ $sp->fu10_hasil }}" data-fu10-tindak-lanjut="{{ $sp->fu10_tindak_lanjut }}">
                            Follow Up
                            @if($fuCount > 0)
                                <span class="badge badge-danger position-absolute fu-badge" style="top: -5px; right: -5px; font-size: 0.6rem; border-radius: 50%; padding: 2px 5px;">{{ $fuCount }}</span>
                            @endif
                        </button>
                        <span class="badge shadow-sm" style="background:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};font-size:0.75rem;border-radius:8px;padding:6px 12px;border:1px solid #ccc; text-wrap: normal; word-break: break-word; min-width: 110px;">
                            {{ $shortKls }}
                            @if($statusKeySP === 'sudah_transfer') ✓ @endif
                        </span>
                    </div>
                @endforeach
            @endif
            
            <button type="button" class="btn btn-sm btn-success rounded-circle shadow-sm mt-1" 
                    style="width: 26px; height: 26px; padding: 0; display: inline-flex; align-items: center; justify-content: center; background-color: #28a745; border-color: #28a745; transition: all 0.2s;"
                    onclick="document.querySelector('.btn-detail-peserta[data-id=\'{{ $item->id }}\']')?.click()"
                    title="Tambah Prospek">
                <i class="fas fa-plus" style="font-size: 0.8rem;"></i>
            </button>
        </div>
    </td>


    {{-- 8. CS PIC --}}
    <td class="text-center" style="vertical-align: middle;">
        <span class="badge badge-light border text-dark fw-bold" style="font-size: 0.75rem; padding: 6px 12px;">{{ $item->createdBy?->name ?? $item->created_by ?? '-' }}</span>
        
        {{-- Hidden button for modal trigger --}}
        @php
            $today = \Carbon\Carbon::now()->startOfDay();
            $kelasJson = $kelas->filter(function($k) use ($today) {
                if (!$k->tanggal_selesai) return true;
                try {
                    return \Carbon\Carbon::parse($k->tanggal_selesai)->startOfDay()->greaterThanOrEqualTo($today);
                } catch (\Exception $e) {
                    return true;
                }
            })->map(function($k) {
                return ['id' => $k->id, 'nama' => $k->nama_kelas];
            })->values()->toJson(JSON_HEX_APOS | JSON_HEX_QUOT);

            $spJson = $item->salesplan->map(function($sp) {
                return [
                    'kelas_id' => $sp->kelas_id,
                    'kelas' => $sp->kelas->nama_kelas ?? 'N/A',
                    'status' => $sp->status,
                    'nominal' => $sp->nominal
                ];
            })->toJson(JSON_HEX_APOS | JSON_HEX_QUOT);
        @endphp
        <button type="button" class="btn btn-sm btn-detail-peserta text-white d-none" 
            data-id="{{ $item->id }}" 
            data-nama="{{ $item->nama }}" 
            data-no-wa="{{ $item->no_wa }}" 
            data-status="{{ $statusKey }}" 
            data-nominal="{{ $nominalVal }}"
            data-can-edit="0" {{-- Strict Read-Only --}}
            data-input-oleh="{{ $item->createdBy->name ?? $item->created_by ?? '-' }}"
            data-updated-at="{{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '-' }}"
            data-potensi="{{ $item->potensi }}"
            data-kelas-id="{{ $item->kelas_id }}"
            data-kelas='{!! $kelasJson !!}'
            data-salesplan='{!! $spJson !!}'
            data-bant-budget="{{ $item->bant_budget }}"
            data-bant-authority="{{ $item->bant_authority }}"
            data-bant-time="{{ $item->bant_time }}"
            data-ikut-zoom="{{ $item->ikut_zoom }}"
            data-keterangan-spin="{{ $item->keterangan_spin }}"
            @for($i=1; $i<=10; $i++)
                data-fu{{$i}}-hasil="{{ $item->{'fu'.$i.'_hasil'} }}"
                data-fu{{$i}}-at="{{ $item->{'fu'.$i.'_at'} ? $item->{'fu'.$i.'_at'}->format('d/m/Y H:i') : '' }}"
                data-fu{{$i}}-tindak-lanjut="{{ $item->{'fu'.$i.'_tindak_lanjut'} }}"
            @endfor
        ></button>
    </td>
</tr>
