@php
    // Common Logic for Chapter/Reseller/Admin Chapter View
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

    $rawSkor = null;
    $rawKategori = 'COLD';
    if (preg_match('/Total Skor(?: Form)?:? ?(\d+)/i', $item->situasi_bisnis ?? '', $matches)) { $rawSkor = (int) $matches[1]; }
    if (preg_match('/Kategori: ?(\w+)/i', $item->situasi_bisnis ?? '', $matches)) { $rawKategori = strtoupper($matches[1]); }

    $authUser = auth()->user();
    $userRole = strtolower($authUser->role);
    // Rafi (operasional) diberi hak edit di chapter view
    $isRafi = ($userRole === 'operasional' && stripos($authUser->name, 'Rafi') !== false);
    $canEdit  = !in_array($userRole, ['marketing', 'administrator', 'operasional']) || $isRafi;

    static $chapterUsers = null;
    if ($chapterUsers === null) {
        $chapterUsers = \App\Models\User::whereIn('role', ['chapter', 'reseller'])
            ->where('name', 'not like', '%umum%')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
@endphp

<tr data-id="{{ $item->id }}" style="background-color: #ffffff; color: #212529; transition: background-color 0.3s ease;">
    {{-- 1. No --}}
    <td class="text-center" style="vertical-align: middle;">
        {{ isset($data) && method_exists($data, 'firstItem') ? $data->firstItem() + $loop->index : $loop->iteration }}
    </td>

    {{-- 2. Nama & WA --}}
    <td class="px-2 py-2">
        <div class="d-flex flex-column gap-2" style="min-width: 160px;">
            <div contenteditable="{{ $canEdit ? 'true' : 'false' }}"
                class="{{ $canEdit ? 'editable' : '' }} fw-bold {{ $item->is_no_potensi ? 'text-white' : 'text-dark' }} text-nowrap p-1 px-2 shadow-sm"
                data-field="nama"
                style="font-size:0.95rem; border:1px solid #dee2e6; border-radius:10px; background:{{ $item->is_no_potensi ? '#e74a3b' : '#fff' }}; transition:all 0.3s; min-height:32px; display:flex; align-items:center;">
                {{ $item->nama }}
            </div>
            <div class="d-flex align-items-center p-1 px-2 shadow-sm" style="border:1px solid #dee2e6; border-radius:10px; background:#fff; min-height:32px;">
                <i class="bi bi-phone me-1 text-primary" style="font-size:0.8rem;"></i>
                <span contenteditable="{{ $canEdit ? 'true' : 'false' }}" class="{{ $canEdit ? 'editable' : '' }} small text-secondary flex-grow-1" data-field="no_wa" style="font-size:0.85rem; outline:none; border:none;">
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
        <select class="form-control form-control-sm select-sumber" data-id="{{ $item->id }}" style="font-size:0.85rem;" {{ $canEdit ? '' : 'disabled' }}>
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
        <div class="d-flex flex-column gap-1" style="min-width: 120px;">
            @if(in_array($userRole, ['administrator', 'operasional']) && !$isRafi)
                <div class="p-1 px-2 shadow-sm border rounded bg-white text-muted" style="font-size: 0.75rem; border-color: #dee2e6 !important;">
                    {{ $item->provinsi_nama ?: '-' }}
                </div>
                <div class="p-1 px-2 shadow-sm border rounded bg-white fw-bold text-dark" style="font-size: 0.75rem; border-color: #dee2e6 !important;">
                    {{ $item->kota_nama ?: '-' }}
                </div>
            @else
                <select class="form-control form-control-sm select-provinsi mb-1" data-id="{{ $item->id }}" style="font-size:0.8rem;">
                    <option value="">{{ $item->provinsi_nama ?: '-- Prov --' }}</option>
                </select>
                <select class="form-control form-control-sm select-kota" data-id="{{ $item->id }}" style="font-size:0.8rem;">
                    <option value="">{{ $item->kota_nama ?: '-- Kota --' }}</option>
                </select>
            @endif
        </div>
    </td>

    {{-- 5. Nama Bisnis --}}
    <td>
        <div contenteditable="{{ $canEdit ? 'true' : 'false' }}" class="{{ $canEdit ? 'editable' : '' }} fw-bold text-dark p-1 px-2 shadow-sm" data-field="nama_bisnis" style="font-size:0.9rem; border:1px solid #dee2e6; border-radius:8px; background:#fff; min-height:28px; min-width:140px;">
            {{ $item->nama_bisnis }}
        </div>
    </td>

    {{-- 6. Situasi Bisnis --}}
    <td class="text-wrap-normal">
        <div class="read-more-container" data-type="situasi">
            <div contenteditable="{{ $canEdit ? 'true' : 'false' }}" class="{{ $canEdit ? 'editable' : '' }} fw-bold" data-field="situasi_bisnis" style="outline:none; min-height: 45px; background: rgba(255,255,255,0.9); color: #000; padding: 8px; border-radius: 8px; border: 1px solid #dee2e6;">{{ $item->situasi_bisnis }}</div>
        </div>
        @if(strlen($item->situasi_bisnis ?? '') > 100)
            <button class="btn-read-more">Baca Selengkapnya</button>
        @endif
    </td>

    {{-- 7. Rekap Penilaian --}}
    <td class="text-center" style="vertical-align: middle;">
        @php
            $badgeColor = '#6c757d';
            if($rawKategori === 'HOT') $badgeColor = '#28a745';
            elseif($rawKategori === 'WARM') $badgeColor = '#f6c23e';
        @endphp
        <span class="badge mb-1" style="background:{{ $badgeColor }};color:#fff;font-size:0.8rem;font-weight:700;border-radius:8px;padding:5px 12px;">{{ $rawKategori }}</span>
        @if($rawSkor) <br><span class="small font-weight-bold" style="color:#555;">{{ $rawSkor }} / 51</span> @endif
    </td>

    {{-- 8. Prospek M1T --}}
    <td class="text-center" style="vertical-align: middle;">
        <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
            @php 
                $sp = $latestSp; 
                $schedule = $sp ? \App\Models\ZoomSchedule::where('salesplan_id', $sp->id)->first() : null;
            @endphp
            <!-- Dedicated Zoom button for this prospect -->
            <button type="button" class="btn btn-zoom-bant p-0 d-flex align-items-center justify-content-center border-0 shadow-sm text-white"
                    style="width: 24px; height: 24px; border-radius: 6px; background: linear-gradient(45deg, #2D8CFF, #1570E0); transition: all 0.2s;"
                    data-id="{{ $item->id }}" 
                    data-nama="{{ $item->nama }}"
                    data-kelas-nama="M1T"
                    data-salesplan-id="{{ $sp ? $sp->id : '' }}"
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
                    title="Zoom & BANT (M1T)">
                <i class="fas fa-video" style="font-size: 0.7rem;"></i>
            </button>
            
            <!-- Follow Up button for this specific class -->
            <button type="button" class="btn btn-primary btn-sm btn-riwayat shadow-sm border-0 px-2"
                    style="height: 24px; line-height: 1; font-size: 0.65rem; font-weight: 700; background: linear-gradient(45deg, #4e73df, #224abe); border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;"
                    data-kelas-nama="M1T"
                    data-salesplan-id="{{ $sp ? $sp->id : '' }}"
                    data-id="{{ $item->id }}" data-nama="{{ $item->nama }}" 
                    data-fu1="{{ $sp ? $sp->fu1_hasil : '' }}"
                    data-fu1-wa="{{ $sp && $sp->fu1_wa ? 1 : 0 }}" data-fu1-telp="{{ $sp && $sp->fu1_telp ? 1 : 0 }}"
                    data-fu1-at="{{ $sp && $sp->fu1_at ? ($sp->fu1_at instanceof \Carbon\Carbon ? $sp->fu1_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu1_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu1-hasil="{{ $sp ? $sp->fu1_hasil : '' }}" data-fu1-tindak-lanjut="{{ $sp ? $sp->fu1_tindak_lanjut : '' }}"
                    data-fu2="{{ $sp ? $sp->fu2_hasil : '' }}"
                    data-fu2-wa="{{ $sp && $sp->fu2_wa ? 1 : 0 }}" data-fu2-telp="{{ $sp && $sp->fu2_telp ? 1 : 0 }}"
                    data-fu2-at="{{ $sp && $sp->fu2_at ? ($sp->fu2_at instanceof \Carbon\Carbon ? $sp->fu2_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu2_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu2-hasil="{{ $sp ? $sp->fu2_hasil : '' }}" data-fu2-tindak-lanjut="{{ $sp ? $sp->fu2_tindak_lanjut : '' }}"
                    data-fu3="{{ $sp ? $sp->fu3_hasil : '' }}"
                    data-fu3-wa="{{ $sp && $sp->fu3_wa ? 1 : 0 }}" data-fu3-telp="{{ $sp && $sp->fu3_telp ? 1 : 0 }}"
                    data-fu3-at="{{ $sp && $sp->fu3_at ? ($sp->fu3_at instanceof \Carbon\Carbon ? $sp->fu3_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu3_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu3-hasil="{{ $sp ? $sp->fu3_hasil : '' }}" data-fu3-tindak-lanjut="{{ $sp ? $sp->fu3_tindak_lanjut : '' }}"
                    data-fu4="{{ $sp ? $sp->fu4_hasil : '' }}"
                    data-fu4-wa="{{ $sp && $sp->fu4_wa ? 1 : 0 }}" data-fu4-telp="{{ $sp && $sp->fu4_telp ? 1 : 0 }}"
                    data-fu4-at="{{ $sp && $sp->fu4_at ? ($sp->fu4_at instanceof \Carbon\Carbon ? $sp->fu4_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu4_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu4-hasil="{{ $sp ? $sp->fu4_hasil : '' }}" data-fu4-tindak-lanjut="{{ $sp ? $sp->fu4_tindak_lanjut : '' }}"
                    data-fu5="{{ $sp ? $sp->fu5_hasil : '' }}"
                    data-fu5-wa="{{ $sp && $sp->fu5_wa ? 1 : 0 }}" data-fu5-telp="{{ $sp && $sp->fu5_telp ? 1 : 0 }}"
                    data-fu5-at="{{ $sp && $sp->fu5_at ? ($sp->fu5_at instanceof \Carbon\Carbon ? $sp->fu5_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu5_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu5-hasil="{{ $sp ? $sp->fu5_hasil : '' }}" data-fu5-tindak-lanjut="{{ $sp ? $sp->fu5_tindak_lanjut : '' }}"
                    data-fu6="{{ $sp ? $sp->fu6_hasil : '' }}"
                    data-fu6-wa="{{ $sp && $sp->fu6_wa ? 1 : 0 }}" data-fu6-telp="{{ $sp && $sp->fu6_telp ? 1 : 0 }}"
                    data-fu6-at="{{ $sp && $sp->fu6_at ? ($sp->fu6_at instanceof \Carbon\Carbon ? $sp->fu6_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu6_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu6-hasil="{{ $sp ? $sp->fu6_hasil : '' }}" data-fu6-tindak-lanjut="{{ $sp ? $sp->fu6_tindak_lanjut : '' }}"
                    data-fu7="{{ $sp ? $sp->fu7_hasil : '' }}"
                    data-fu7-wa="{{ $sp && $sp->fu7_wa ? 1 : 0 }}" data-fu7-telp="{{ $sp && $sp->fu7_telp ? 1 : 0 }}"
                    data-fu7-at="{{ $sp && $sp->fu7_at ? ($sp->fu7_at instanceof \Carbon\Carbon ? $sp->fu7_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu7_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu7-hasil="{{ $sp ? $sp->fu7_hasil : '' }}" data-fu7-tindak-lanjut="{{ $sp ? $sp->fu7_tindak_lanjut : '' }}"
                    data-fu8="{{ $sp ? $sp->fu8_hasil : '' }}"
                    data-fu8-wa="{{ $sp && $sp->fu8_wa ? 1 : 0 }}" data-fu8-telp="{{ $sp && $sp->fu8_telp ? 1 : 0 }}"
                    data-fu8-at="{{ $sp && $sp->fu8_at ? ($sp->fu8_at instanceof \Carbon\Carbon ? $sp->fu8_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu8_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu8-hasil="{{ $sp ? $sp->fu8_hasil : '' }}" data-fu8-tindak-lanjut="{{ $sp ? $sp->fu8_tindak_lanjut : '' }}"
                    data-fu9="{{ $sp ? $sp->fu9_hasil : '' }}"
                    data-fu9-wa="{{ $sp && $sp->fu9_wa ? 1 : 0 }}" data-fu9-telp="{{ $sp && $sp->fu9_telp ? 1 : 0 }}"
                    data-fu9-at="{{ $sp && $sp->fu9_at ? ($sp->fu9_at instanceof \Carbon\Carbon ? $sp->fu9_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu9_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu9-hasil="{{ $sp ? $sp->fu9_hasil : '' }}" data-fu9-tindak-lanjut="{{ $sp ? $sp->fu9_tindak_lanjut : '' }}"
                    data-fu10="{{ $sp ? $sp->fu10_hasil : '' }}" data-fu10-wa="{{ $sp && $sp->fu10_wa ? 1 : 0 }}"
                    data-fu10-telp="{{ $sp && $sp->fu10_telp ? 1 : 0 }}"
                    data-fu10-at="{{ $sp && $sp->fu10_at ? ($sp->fu10_at instanceof \Carbon\Carbon ? $sp->fu10_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($sp->fu10_at)->format('d/m/Y H:i')) : '' }}"
                    data-fu10-hasil="{{ $sp ? $sp->fu10_hasil : '' }}" data-fu10-tindak-lanjut="{{ $sp ? $sp->fu10_tindak_lanjut : '' }}">
                Follow Up
            </button>
            <span class="badge shadow-sm" style="background:#25799E;color:#fff;font-size:0.85rem;font-weight:800;border-radius:8px;padding:7px 18px;border:2px solid #fff;">M1T</span>
        </div>
    </td>

    {{-- 9. Status Potensi --}}
    <td class="text-center" style="vertical-align: middle;">
        <select class="form-control form-control-sm font-weight-bold status-direct-select" 
                data-nama="{{ $item->nama }}"
                data-kelas-nama="{{ $latestSp->kelas?->nama_kelas ?? 'Startup Muslim Indonesia' }}"
                style="border-radius:10px;font-size:0.75rem;height:35px;background-color:{{ $currentConfig['bg'] }};color:{{ $currentConfig['text'] }};" 
                onchange="updateStatusDirectTable('{{ $item->id }}', this)" 
                {{ $userRole === 'administrator' ? 'disabled' : ($canEdit ? '' : 'disabled') }}>
            <option value="cold"           {{ $statusKey==='cold'           ?'selected':'' }}>⚪ Cold</option>
            <option value="tertarik"       {{ $statusKey==='tertarik'       ?'selected':'' }}>🟡 Tertarik</option>
            <option value="mau_transfer"   {{ $statusKey==='mau_transfer'   ?'selected':'' }}>🟢 Mau Transfer</option>
            <option value="sudah_transfer" {{ $statusKey==='sudah_transfer' ?'selected':'' }}>🔵 Sudah Transfer</option>
            <option value="no"             {{ $statusKey==='no'             ?'selected':'' }}>🔴 No</option>
        </select>
    </td>

    @if(stripos(auth()->user()->chapter ?? '', 'depok') !== false || (in_array(strtolower(auth()->user()->role), ['administrator', 'operasional']) && request('view_type') === 'chapter'))
        @php
            $buktiUrl = null;
            if (isset($item->situasi_bisnis) && preg_match('/Bukti Transfer:\s*(https?:\/\/\S+)/i', $item->situasi_bisnis, $matches)) {
                $buktiUrl = $matches[1];
                // Resolve local testing URL issue by converting path relative to assets
                if (preg_match('/uploads\/bukti_transfer\/\S+/i', $buktiUrl, $pathMatches)) {
                    $buktiUrl = asset($pathMatches[0]);
                }
            }
        @endphp
        <td class="text-center" style="vertical-align: middle;">
            @if($buktiUrl)
                @if(preg_match('/\.(jpg|jpeg|png|gif)$/i', $buktiUrl))
                    <a href="{{ $buktiUrl }}" target="_blank">
                        <img src="{{ $buktiUrl }}" style="max-width: 60px; max-height: 60px; border-radius: 6px; border: 1px solid #dee2e6; box-shadow: 0 2px 4px rgba(0,0,0,0.08); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    </a>
                @else
                    <a href="{{ $buktiUrl }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius: 8px; font-size: 0.75rem;">
                        <i class="fas fa-file-pdf text-danger me-1"></i> Lihat PDF
                    </a>
                @endif
            @else
                <span class="text-muted small fw-500">Belum Upload</span>
            @endif
        </td>
    @endif

    {{-- 10. PIC / Action --}}
    <td class="text-center" style="vertical-align: middle;">
        <div class="d-flex flex-column gap-2 align-items-center">
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
            @if($userRole !== 'administrator')
                <button type="button" class="btn btn-sm btn-detail-peserta text-white mb-1 d-none" 
                    style="background:#25799E; border-radius:8px; width:110px;" 
                    data-id="{{ $item->id }}" 
                    data-nama="{{ $item->nama }}" 
                    data-no-wa="{{ $item->no_wa }}" 
                    data-status="{{ $statusKey }}" 
                    data-nominal="{{ $latestSp ? ($latestSp->nominal ?? 0) : 0 }}"
                    data-can-edit="{{ $canEdit ? '1' : '0' }}"
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
                >
                    <i class="fas fa-eye"></i> Prospek
                </button>
            @endif

            @if($isRafi)
                @php
                    $picValue = $item->pic;
                    if (empty($picValue)) {
                        // Fallback to creator name only if creator is NOT operasional/Rafi
                        $creatorName = $item->createdBy?->name ?? $item->created_by;
                        $creatorRole = strtolower($item->created_by_role ?? '');
                        if ($creatorRole !== 'operasional') {
                            $picValue = $creatorName;
                        }
                    }
                @endphp
                <select class="form-control form-control-sm select-pic" data-id="{{ $item->id }}" style="font-size: 0.75rem; width: 110px; height: 28px; padding: 2px 5px;">
                    <option value="">- Pilih PIC -</option>
                    @foreach($chapterUsers as $cu)
                        <option value="{{ $cu->name }}" {{ $picValue === $cu->name ? 'selected' : '' }}>
                            {{ $cu->name }}
                        </option>
                    @endforeach
                </select>
            @elseif(in_array($userRole, ['administrator', 'operasional']))
                @php
                    $picValue = $item->pic;
                    if (empty($picValue)) {
                        $picValue = $item->createdBy?->name ?? $item->created_by;
                    }
                @endphp
                <span class="badge badge-light border" style="font-size: 0.75rem; padding: 6px 12px; width: 110px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $picValue }}">{{ $picValue }}</span>
            @else
                @if(!$item->is_no_potensi)
                    <button type="button" class="btn btn-sm btn-warning" onclick="markNoPotensi('{{ $item->id }}')" style="font-size: 0.7rem; width: 110px;">X Tidak Potensi</button>
                @endif
                <form action="{{ route('admin.database.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" style="font-size: 0.7rem; width: 110px;">Hapus</button>
                </form>
            @endif
        </div>
    </td>

    {{-- 11. Status Data (hanya tampil untuk administrator/operasional di chapter view) --}}
    @if(in_array($userRole, ['administrator', 'operasional']))
    @php
        // Jika data berasal dari MBC, tampilkan '-'
        $isMbcData = in_array($item->created_by_role ?? '', ['cs-mbc', 'mbc']);
        $statusDataValue = $item->status_data ?? 'CHAPTER';
    @endphp
    <td class="text-center" style="vertical-align: middle;">
        @if($isMbcData)
            <span style="color: #999; font-size: 0.85rem;">-</span>
        @elseif($statusDataValue === 'ADD OPS')
            <span class="badge" style="background:#28a745; color:#fff; font-size:0.75rem; font-weight:700; border-radius:8px; padding:5px 10px; white-space:nowrap;">ADD OPS</span>
        @elseif($statusDataValue === 'EDIT OPS')
            <span class="badge" style="background:#ffc107; color:#212529; font-size:0.75rem; font-weight:700; border-radius:8px; padding:5px 10px; white-space:nowrap;">EDIT OPS</span>
        @else
            <span class="badge" style="background:#6c757d; color:#fff; font-size:0.75rem; font-weight:700; border-radius:8px; padding:5px 10px; white-space:nowrap;">CHAPTER</span>
        @endif
    </td>
    @endif

    {{-- 12. Aksi (hanya tampil untuk administrator/operasional di chapter view) --}}
    @if(in_array($userRole, ['administrator', 'operasional']))
    <td class="text-center" style="vertical-align: middle;">
        @if($isRafi)
            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-direct shadow-sm d-flex align-items-center justify-content-center mx-auto"
                    style="width: 28px; height: 28px; border-radius: 6px; padding: 0; transition: all 0.2s;"
                    data-id="{{ $item->id }}"
                    data-nama="{{ $item->nama }}"
                    title="Hapus Data">
                <i class="fas fa-trash-alt" style="font-size: 0.85rem;"></i>
            </button>
        @else
            <span style="color: #999; font-size: 0.85rem;">-</span>
        @endif
    </td>
    @endif
</tr>
