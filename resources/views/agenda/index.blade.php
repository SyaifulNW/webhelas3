@extends('layouts.masteradmin')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* ======= LIGHT THEME AGENDA ======= */
        .agenda-wrap {
            background: #f8f9fc;
            min-height: 100vh;
            padding: 28px 20px 60px;
            color: #333;
            font-family: 'Segoe UI', sans-serif;
        }

        .agenda-header-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #1a1a2e;
        }

        .agenda-header-sub {
            font-size: 0.82rem;
            color: #888;
            margin-top: 2px;
        }

        /* ── Divisi Tabs ────────────────────────────────────────────── */
        .divisi-tabs {
            display: flex;
            gap: 8px;
            margin: 16px 0 20px;
            flex-wrap: wrap;
        }

        .divisi-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #fff;
            border: 1.5px solid #e3e6f0;
            color: #555;
            border-radius: 12px;
            padding: 9px 22px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .04);
        }

        .divisi-tab-btn:hover {
            border-color: #4f46e5;
            color: #4f46e5;
        }

        .divisi-tab-btn.active {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #fff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, .3);
        }

        .divisi-tab-btn.active-sm {
            background: #e83e8c;
            border-color: #e83e8c;
            color: #fff;
            box-shadow: 0 4px 14px rgba(232, 62, 140, .3);
        }

        .divisi-tab-btn:hover.tab-sm {
            border-color: #e83e8c;
            color: #e83e8c;
        }

        /* ── Divisi Panels ──────────────────────────────────────────── */
        .divisi-panel {
            display: none;
        }

        .divisi-panel.active {
            display: block;
        }

        /* ── Stat Cards ─────────────────────────────────────────────── */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin: 0 0 18px;
        }

        @media(max-width:640px) {
            .stat-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        }

        .stat-card .stat-label {
            font-size: .72rem;
            color: #aaa;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .stat-card .stat-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1a1a2e;
        }

        .stat-card .stat-value.green {
            color: #16a34a;
        }

        .stat-card .stat-value.orange {
            color: #ea580c;
        }

        .stat-card .stat-value.purple {
            color: #4f46e5;
        }

        /* ── Progress ───────────────────────────────────────────────── */
        .progress-wrap {
            margin: 6px 0 20px;
        }

        .progress-track {
            background: #e9ecef;
            border-radius: 30px;
            height: 8px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            border-radius: 30px;
            transition: width .5s ease;
        }

        .progress-pct {
            text-align: right;
            font-size: .75rem;
            color: #4f46e5;
            font-weight: 700;
            margin-top: 4px;
        }

        /* ── Filter Bar ─────────────────────────────────────────────── */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .tab-group {
            display: flex;
            gap: 6px;
        }

        .tab-btn {
            background: #fff;
            border: 1px solid #e3e6f0;
            color: #777;
            border-radius: 10px;
            padding: 7px 18px;
            font-size: .82rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
        }

        .tab-btn.active,
        .tab-btn:hover {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #fff;
        }

        .search-wrap {
            position: relative;
            flex: 1;
        }

        .search-wrap .fa-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: .8rem;
        }

        .search-box {
            flex: 1;
            min-width: 160px;
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            padding: 7px 14px 7px 34px;
            color: #333;
            font-size: .85rem;
            outline: none;
            width: 100%;
        }

        .search-box:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .08);
        }

        .btn-tambah {
            background: #4f46e5;
            border: none;
            border-radius: 10px;
            padding: 8px 18px;
            color: #fff;
            font-weight: 700;
            font-size: .83rem;
            cursor: pointer;
            transition: background .2s;
            white-space: nowrap;
        }

        .btn-tambah:hover {
            background: #4338ca;
        }

        /* ── Periode Card ───────────────────────────────────────────── */
        .periode-card {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 16px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        }

        .periode-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-bottom: 1px solid #f0f2f8;
            background: #fafbff;
        }

        .periode-card-header .left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tipe-chip {
            font-size: .72rem;
            font-weight: 800;
            border-radius: 20px;
            padding: 4px 12px;
            text-transform: capitalize;
            letter-spacing: .3px;
        }

        .chip-harian {
            background: rgba(22, 163, 74, .1);
            color: #16a34a;
            border: 1px solid rgba(22, 163, 74, .25);
        }

        .chip-mingguan {
            background: rgba(79, 70, 229, .1);
            color: #4f46e5;
            border: 1px solid rgba(79, 70, 229, .25);
        }

        .chip-bulanan {
            background: rgba(234, 88, 12, .1);
            color: #ea580c;
            border: 1px solid rgba(234, 88, 12, .25);
        }

        .periode-range {
            font-size: .9rem;
            font-weight: 700;
            color: #1a1a2e;
        }

        .reset-info {
            font-size: .76rem;
            color: #aaa;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .reset-info i {
            color: #4f46e5;
        }

        .reset-note {
            padding: 7px 20px;
            font-size: .75rem;
            color: #bbb;
            border-bottom: 1px solid #f0f2f8;
            background: #fafbff;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── Table ──────────────────────────────────────────────────── */
        .agenda-table {
            width: 100%;
            border-collapse: collapse;
        }

        .agenda-table thead tr {
            border-bottom: 1px solid #f0f2f8;
        }

        .agenda-table thead th {
            padding: 10px 16px;
            font-size: .75rem;
            color: #bbb;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            background: transparent;
        }

        .agenda-table tbody tr {
            border-bottom: 1px solid #f5f6fa;
            transition: background .15s;
        }

        .agenda-table tbody tr:last-child {
            border-bottom: none;
        }

        .agenda-table tbody tr:hover {
            background: rgba(79, 70, 229, .03);
        }

        .agenda-table td {
            padding: 13px 16px;
            font-size: .88rem;
            color: #555;
            vertical-align: middle;
        }

        .agenda-table td .judul {
            font-weight: 700;
            color: #1a1a2e;
        }

        .agenda-table td .deskripsi {
            font-size: .78rem;
            color: #aaa;
            margin-top: 2px;
        }

        .agenda-table tr.done td .judul {
            text-decoration: line-through;
            color: #ccc;
        }

        .agenda-table td.col-no {
            width: 50px;
            color: #ccc;
            font-size: .82rem;
            text-align: center;
        }

        .agenda-table td.col-check {
            width: 80px;
            text-align: center;
        }

        .agenda-table td.col-action {
            width: 60px;
            text-align: center;
        }

        /* ── Buttons ────────────────────────────────────────────────── */
        .check-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            background: transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            font-size: .8rem;
            transition: all .2s;
            outline: none;
        }

        .check-circle:hover {
            border-color: #4f46e5;
        }

        .check-circle.checked {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #fff;
        }

        .del-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e3e6f0;
            background: transparent;
            color: #ccc;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .78rem;
            transition: all .2s;
            outline: none;
        }

        .del-btn:hover {
            background: rgba(239, 68, 68, .08);
            border-color: #fca5a5;
            color: #ef4444;
        }

        /* ── Modal inputs ───────────────────────────────────────────── */
        .dark-input {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            color: #333;
            padding: 8px 12px;
            font-size: .84rem;
            outline: none;
            width: 100%;
            transition: border .2s;
        }

        .dark-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .08);
        }

        .dark-select {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            color: #333;
            padding: 8px 10px;
            font-size: .84rem;
            outline: none;
            cursor: pointer;
            width: 100%;
        }

        .dark-select:focus {
            border-color: #4f46e5;
        }

        .save-btn {
            background: #4f46e5;
            border: none;
            border-radius: 8px;
            color: #fff;
            padding: 8px 18px;
            font-size: .84rem;
            cursor: pointer;
            font-weight: 700;
            transition: background .2s;
        }

        .save-btn:hover {
            background: #4338ca;
        }

        .cancel-btn {
            background: transparent;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            color: #888;
            padding: 8px 14px;
            font-size: .84rem;
            cursor: pointer;
            transition: all .2s;
        }

        .cancel-btn:hover {
            border-color: #ccc;
            color: #555;
        }

        /* ── Misc ───────────────────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 36px;
            color: #ccc;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: block;
        }

        .modal-dark .modal-content {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 16px;
            color: #333;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .12);
        }

        .modal-dark .modal-header {
            background: #fafbff;
            border-bottom: 1px solid #f0f2f8;
        }

        .modal-dark .modal-footer {
            border-top: 1px solid #f0f2f8;
            background: #fafbff;
        }

        .modal-dark .modal-title {
            color: #1a1a2e;
            font-weight: 800;
        }

        .modal-dark .close {
            color: #aaa !important;
        }

        .admin-user-header {
            background: #f0f2ff;
            border-left: 4px solid #4f46e5;
            border-radius: 0 8px 8px 0;
            padding: 10px 16px;
            margin-bottom: 12px;
            font-weight: 700;
            color: #1a1a2e;
            font-size: .9rem;
        }
    </style>

    <div class="agenda-wrap">

        {{-- ===== HEADER ===== --}}
        <div class="d-flex align-items-start justify-content-between mb-1">
            <div>
                <div class="agenda-header-title">
                    <i class="fas fa-calendar-check mr-2" style="color:#4f46e5;"></i> Agenda Kerja
                </div>
                @if (!$isAdmin)
                    <div class="agenda-header-sub">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}
                        &nbsp;·&nbsp;
                        <span id="headerPersen">
                            {{-- overall persen across all divisi --}}
                            @php
                                $overallTotal = collect($divisiData)->sum('total');
                                $overallSelesai = collect($divisiData)->sum('selesai');
                                $overallPersen = $overallTotal > 0 ? round(($overallSelesai / $overallTotal) * 100) : 0;
                            @endphp
                            {{ $overallPersen }}% selesai
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============ ADMINISTRATOR VIEW ============ --}}
        @if ($isAdmin)
            @if (empty($allAgendas))
                <div class="empty-state"><i class="fas fa-clipboard-list"></i>Belum ada agenda dari tim.</div>
            @else
                @foreach ($allAgendas as $entry)
                    <div class="mb-4">
                        <div class="admin-user-header">
                            <i class="fas fa-user mr-2 text-primary"></i>
                            {{ $entry['user']->name }}
                            @if ($entry['user']->divisi)
                                <span style="color:#666; font-weight:400; font-size:.8rem;"> —
                                    {{ $entry['user']->divisi }}</span>
                            @endif
                        </div>
                        @foreach (['harian', 'mingguan', 'bulanan'] as $tipe)
                            @php $tplGroup = $entry['templates']->where('tipe', $tipe); @endphp
                            @if ($tplGroup->isNotEmpty())
                                @php $pi = \App\Http\Controllers\AgendaController::getPeriodeInfo($tipe); @endphp
                                <div class="periode-card mb-3">
                                    <div class="periode-card-header">
                                        <div class="left">
                                            <span class="tipe-chip chip-{{ $tipe }}">{{ ucfirst($tipe) }}</span>
                                            <span class="periode-range">{{ $pi['range'] }}</span>
                                        </div>
                                        <div class="reset-info"><i class="fas fa-sync-alt"></i> Reset:
                                            {{ $pi['reset_label'] }}</div>
                                    </div>
                                    <div class="reset-note"><i class="fas fa-info-circle"></i> {{ $pi['reset_info'] }}</div>
                                    <table class="agenda-table">
                                        <thead>
                                            <tr>
                                                <th class="col-no">No</th>
                                                <th>Deskripsi Pekerjaan</th>
                                                <th class="col-check" style="text-align:center;">Checklist</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tplGroup->values() as $i => $tpl)
                                                <tr class="{{ $tpl->log && $tpl->log->is_done ? 'done' : '' }}">
                                                    <td class="col-no">{{ $i + 1 }}</td>
                                                    <td>
                                                        <div class="judul">{{ $tpl->judul }}</div>
                                                        @if ($tpl->deskripsi)
                                                            <div class="deskripsi">{{ $tpl->deskripsi }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="col-check">
                                                        <button
                                                            class="check-circle {{ $tpl->log && $tpl->log->is_done ? 'checked' : '' }}"
                                                            disabled>
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            @endif

            {{-- ============ USER (LINDA) VIEW ============ --}}
        @else
            {{-- ── DIVISI TABS ── --}}
            @php
                $divisiIcons = [
                    'Divisi Keuangan' => 'fa-wallet',
                    'Sales & Marketing' => 'fa-bullhorn',
                ];
                $firstDivisi = $divisiList[0];
            @endphp
            <div class="divisi-tabs" id="divisiTabs">
                @foreach ($divisiList as $idx => $divisi)
                    <button class="divisi-tab-btn {{ $idx === 0 ? 'active' : '' }}" data-divisi="{{ $divisi }}"
                        data-idx="{{ $idx }}" onclick="switchDivisi('{{ $divisi }}', this)">
                        <i class="fas {{ $divisiIcons[$divisi] ?? 'fa-layer-group' }}"></i>
                        {{ $divisi }}
                    </button>
                @endforeach
            </div>

            {{-- ── DIVISI PANELS ── --}}
            @foreach ($divisiList as $idx => $divisi)
                @php
                    $dData = $divisiData[$divisi];
                    $total = $dData['total'];
                    $selesai = $dData['selesai'];
                    $tersisa = $dData['tersisa'];
                    $persen = $dData['persen'];
                    $grouped = $dData['grouped'];
                    $divisiSlug = \Illuminate\Support\Str::slug($divisi, '_');
                @endphp
                <div class="divisi-panel {{ $idx === 0 ? 'active' : '' }}" id="panel-{{ $divisiSlug }}">

                    {{-- Stat Cards --}}
                    <div class="stat-cards">
                        <div class="stat-card">
                            <div class="stat-label">Total hari ini</div>
                            <div class="stat-value" id="statTotal-{{ $divisiSlug }}">{{ $total }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Selesai</div>
                            <div class="stat-value green" id="statSelesai-{{ $divisiSlug }}">{{ $selesai }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Tersisa</div>
                            <div class="stat-value orange" id="statTersisa-{{ $divisiSlug }}">{{ $tersisa }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Progress</div>
                            <div class="stat-value purple" id="statPersen-{{ $divisiSlug }}">{{ $persen }}%</div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="progress-wrap">
                        <div class="progress-track">
                            <div class="progress-fill" id="progressFill-{{ $divisiSlug }}"
                                style="width:{{ $persen }}%"></div>
                        </div>
                        <div class="progress-pct" id="progressPct-{{ $divisiSlug }}">{{ $persen }}%</div>
                    </div>

                    {{-- Filter Bar --}}
                    <div class="filter-bar">
                        <div class="tab-group" id="tabGroup-{{ $divisiSlug }}">
                            <button class="tab-btn active" data-filter="semua"
                                onclick="setFilter('semua',   this,'{{ $divisiSlug }}')">Semua</button>
                            <button class="tab-btn" data-filter="harian"
                                onclick="setFilter('harian',  this,'{{ $divisiSlug }}')">Harian</button>
                            <button class="tab-btn"
                                data-filter="mingguan"onclick="setFilter('mingguan',this,'{{ $divisiSlug }}')">Mingguan</button>
                            <button class="tab-btn" data-filter="bulanan"
                                onclick="setFilter('bulanan', this,'{{ $divisiSlug }}')">Bulanan</button>
                        </div>
                        <div class="search-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-box" id="searchBox-{{ $divisiSlug }}"
                                placeholder="Cari agenda..." oninput="doSearch(this.value, '{{ $divisiSlug }}')">
                        </div>
                        <button class="btn-tambah" onclick="showAddModal('{{ $divisi }}')">
                            <i class="fas fa-plus mr-1"></i> Tambah agenda
                        </button>
                    </div>

                    {{-- Periode Cards --}}
                    @foreach (['harian', 'mingguan', 'bulanan'] as $tipe)
                        @php
                            $tplGroup = $grouped->get($tipe, collect());
                            $pi = $periodeInfo[$tipe];
                        @endphp
                        <div class="periode-card" data-tipe="{{ $tipe }}"
                            id="card-{{ $divisiSlug }}-{{ $tipe }}">
                            <div class="periode-card-header">
                                <div class="left">
                                    <span class="tipe-chip chip-{{ $tipe }}">{{ ucfirst($tipe) }}</span>
                                    <span class="periode-range">{{ $pi['range'] }}</span>
                                </div>
                                <div class="reset-info"><i class="fas fa-sync-alt"></i> Reset: {{ $pi['reset_label'] }}
                                </div>
                            </div>
                            <div class="reset-note"><i class="fas fa-info-circle"></i> {{ $pi['reset_info'] }}</div>
                            <table class="agenda-table">
                                <thead>
                                    <tr>
                                        <th class="col-no">No</th>
                                        <th>Deskripsi Pekerjaan</th>
                                        <th class="col-check">Checklist</th>
                                        <th class="col-action">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-{{ $divisiSlug }}-{{ $tipe }}">
                                    @forelse($tplGroup->values() as $i => $tpl)
                                        <tr class="todo-row {{ $tpl->log && $tpl->log->is_done ? 'done' : '' }}"
                                            id="row-{{ $tpl->id }}" data-tipe="{{ $tipe }}"
                                            data-divisi="{{ $divisiSlug }}"
                                            data-judul="{{ strtolower($tpl->judul) }}">
                                            <td class="col-no">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="judul">{{ $tpl->judul }}</div>
                                                @if ($tpl->deskripsi)
                                                    <div class="deskripsi">{{ $tpl->deskripsi }}</div>
                                                @endif
                                            </td>
                                            <td class="col-check">
                                                @if ($tpl->log)
                                                    <button class="check-circle {{ $tpl->log->is_done ? 'checked' : '' }}"
                                                        onclick="toggleCheck({{ $tpl->log->id }}, this, '{{ $divisiSlug }}')"
                                                        title="{{ $tpl->log->is_done ? 'Tandai belum selesai' : 'Tandai selesai' }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="col-action">
                                                <button class="del-btn"
                                                    onclick="deleteAgenda({{ $tpl->id }}, '{{ $divisiSlug }}')"
                                                    title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row-{{ $divisiSlug }}-{{ $tipe }}">
                                            <td colspan="4" class="empty-state" style="padding:24px;">
                                                <i class="fas fa-inbox"
                                                    style="font-size:1.5rem;margin-bottom:6px;display:block;"></i>
                                                Belum ada agenda {{ $tipe }}.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endforeach

                </div>{{-- /divisi-panel --}}
            @endforeach

        @endif

    </div>{{-- /agenda-wrap --}}

    {{-- ===== MODAL TAMBAH AGENDA ===== --}}
    @if (!$isAdmin)
        <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-dark">
                    <div class="modal-header">
                        <h6 class="modal-title"><i class="fas fa-plus mr-2" style="color:#4f46e5;"></i>Tambah Agenda Baru
                        </h6>
                        <button type="button" class="close" data-dismiss="modal" style="color:#aaa;">&times;</button>
                    </div>
                    <div class="modal-body p-4">
                        {{-- Hidden: divisi aktif --}}
                        <input type="hidden" id="modalDivisi">

                        <div class="mb-3">
                            <label
                                style="font-size:.8rem;color:#aaa;font-weight:700;margin-bottom:6px;display:block;">DIVISI</label>
                            <div id="modalDivisiLabel"
                                style="font-weight:700;color:#4f46e5;font-size:.9rem;padding:6px 0;"></div>
                        </div>
                        <div class="mb-3">
                            <label
                                style="font-size:.8rem;color:#aaa;font-weight:700;margin-bottom:6px;display:block;">JENIS
                                AGENDA</label>
                            <select id="modalTipe" class="dark-select">
                                <option value="harian">Harian</option>
                                <option value="mingguan">Mingguan</option>
                                <option value="bulanan">Bulanan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label
                                style="font-size:.8rem;color:#aaa;font-weight:700;margin-bottom:6px;display:block;">DESKRIPSI
                                PEKERJAAN</label>
                            <input type="text" id="modalJudul" class="dark-input" placeholder="Tulis nama agenda...">
                        </div>
                        <div>
                            <label
                                style="font-size:.8rem;color:#aaa;font-weight:700;margin-bottom:6px;display:block;">CATATAN
                                (opsional)</label>
                            <input type="text" id="modalDeskripsi" class="dark-input"
                                placeholder="Catatan tambahan...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="cancel-btn" data-dismiss="modal">Batal</button>
                        <button type="button" class="save-btn" onclick="saveAgenda()">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        const CSRF = '{{ csrf_token() }}';
        const STORE_URL = '{{ route('agenda.store') }}';
        const TOGGLE_BASE = '{{ url('agenda/toggle') }}';
        const DELETE_BASE = '{{ url('agenda') }}';

        // Track active filter per divisi
        const filters = {};
        const searches = {};

        // ── Divisi Tab Switch ──────────────────────────────────────────────────────
        function switchDivisi(divisi, btn) {
            // Deactivate all tabs & panels
            document.querySelectorAll('.divisi-tab-btn').forEach(b => {
                b.classList.remove('active', 'active-sm');
            });
            document.querySelectorAll('.divisi-panel').forEach(p => p.classList.remove('active'));

            // Activate clicked
            const idx = parseInt(btn.dataset.idx);
            btn.classList.add(idx === 1 ? 'active-sm' : 'active');

            const slug = slugify(divisi);
            const panel = document.getElementById('panel-' + slug);
            if (panel) panel.classList.add('active');

            // Update header subtitle
            const slug0 = slugify(document.querySelector('.divisi-panel').id.replace('panel-', ''));
            updateHeaderPersen();
        }

        function slugify(str) {
            return str.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
        }

        // ── Filter (per divisi) ────────────────────────────────────────────────────
        function setFilter(filter, btn, divisiSlug) {
            filters[divisiSlug] = filter;
            document.querySelectorAll('#tabGroup-' + divisiSlug + ' .tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyFilter(divisiSlug);
        }

        function doSearch(val, divisiSlug) {
            searches[divisiSlug] = val.toLowerCase();
            applyFilter(divisiSlug);
        }

        function applyFilter(divisiSlug) {
            const filter = filters[divisiSlug] || 'semua';
            const search = searches[divisiSlug] || '';

            document.querySelectorAll('#panel-' + divisiSlug + ' .periode-card').forEach(card => {
                const tipe = card.dataset.tipe;
                const showCard = filter === 'semua' || filter === tipe;
                let hasVisible = false;

                card.querySelectorAll('.todo-row').forEach(row => {
                    const matchTipe = filter === 'semua' || row.dataset.tipe === filter;
                    const matchSearch = !search || (row.dataset.judul && row.dataset.judul.includes(
                    search));
                    const show = matchTipe && matchSearch;
                    row.style.display = show ? '' : 'none';
                    if (show) hasVisible = true;
                });
                card.style.display = showCard ? '' : 'none';
            });
        }

        // ── Modal ──────────────────────────────────────────────────────────────────
        let _currentDivisi = '';

        function showAddModal(divisi) {
            _currentDivisi = divisi;
            document.getElementById('modalDivisi').value = divisi;
            document.getElementById('modalDivisiLabel').textContent = divisi;
            document.getElementById('modalJudul').value = '';
            document.getElementById('modalDeskripsi').value = '';
            document.getElementById('modalTipe').value = 'harian';
            $('#modalTambah').modal('show');
            setTimeout(() => document.getElementById('modalJudul').focus(), 400);
        }

        function saveAgenda() {
            const judul = document.getElementById('modalJudul').value.trim();
            const deskripsi = document.getElementById('modalDeskripsi').value.trim();
            const tipe = document.getElementById('modalTipe').value;
            const divisi = document.getElementById('modalDivisi').value;

            if (!judul) {
                document.getElementById('modalJudul').focus();
                return;
            }

            const saveBtn = document.querySelector('.save-btn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

            $.post(STORE_URL, {
                    _token: CSRF,
                    judul,
                    deskripsi,
                    tipe,
                    divisi
                })
                .done(res => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan';
                    if (!res.success) {
                        alert(res.message);
                        return;
                    }
                    $('#modalTambah').modal('hide');
                    appendRow(res);
                    updateStats(1, 0, slugify(res.divisi));
                    showToast('Agenda berhasil ditambahkan!');
                })
                .fail(() => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan';
                    showToast('Gagal menyimpan, coba lagi.');
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const jd = document.getElementById('modalJudul');
            if (jd) jd.addEventListener('keypress', e => {
                if (e.key === 'Enter') saveAgenda();
            });
        });

        // ── Append new row ─────────────────────────────────────────────────────────
        function appendRow(res) {
            const divisiSlug = slugify(res.divisi);
            const tbody = document.getElementById('tbody-' + divisiSlug + '-' + res.tipe);
            if (!tbody) return;

            const emptyRow = tbody.querySelector('.empty-row-' + divisiSlug + '-' + res.tipe);
            if (emptyRow) emptyRow.remove();

            const no = tbody.querySelectorAll('.todo-row').length + 1;
            const tr = document.createElement('tr');
            tr.className = 'todo-row';
            tr.id = 'row-' + res.template_id;
            tr.dataset.tipe = res.tipe;
            tr.dataset.divisi = divisiSlug;
            tr.dataset.judul = res.judul.toLowerCase();
            tr.innerHTML = `
            <td class="col-no">${no}</td>
            <td>
                <div class="judul">${escHtml(res.judul)}</div>
                ${res.deskripsi ? `<div class="deskripsi">${escHtml(res.deskripsi)}</div>` : ''}
            </td>
            <td class="col-check">
                <button class="check-circle" onclick="toggleCheck(${res.log_id}, this, '${divisiSlug}')" title="Tandai selesai">
                    <i class="fas fa-check"></i>
                </button>
            </td>
            <td class="col-action">
                <button class="del-btn" onclick="deleteAgenda(${res.template_id}, '${divisiSlug}')" title="Hapus">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>`;
            tbody.appendChild(tr);
        }

        // ── Toggle Check ───────────────────────────────────────────────────────────
        function toggleCheck(logId, btn, divisiSlug) {
            $.post(TOGGLE_BASE + '/' + logId, {
                    _token: CSRF
                })
                .done(res => {
                    if (!res.success) return;
                    const row = btn.closest('tr');
                    if (res.is_done) {
                        btn.classList.add('checked');
                        row.classList.add('done');
                        btn.title = 'Tandai belum selesai';
                        updateStats(0, 1, divisiSlug);
                    } else {
                        btn.classList.remove('checked');
                        row.classList.remove('done');
                        btn.title = 'Tandai selesai';
                        updateStats(0, -1, divisiSlug);
                    }
                });
        }

        // ── Delete ─────────────────────────────────────────────────────────────────
        function deleteAgenda(templateId, divisiSlug) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus agenda ini?',
                    text: 'Agenda dan seluruh riwayat checklist akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) doDelete(templateId, divisiSlug);
                });
            } else {
                if (confirm('Hapus agenda ini?')) doDelete(templateId, divisiSlug);
            }
        }

        function doDelete(templateId, divisiSlug) {
            $.ajax({
                url: DELETE_BASE + '/' + templateId,
                type: 'DELETE',
                data: {
                    _token: CSRF
                },
                success: res => {
                    if (!res.success) return;
                    const row = document.getElementById('row-' + templateId);
                    if (row) {
                        const wasDone = row.classList.contains('done');
                        row.style.transition = 'opacity .3s';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            updateStats(-1, wasDone ? -1 : 0, divisiSlug);
                            renumberRows(divisiSlug);
                        }, 300);
                    }
                    showToast('Agenda berhasil dihapus.');
                }
            });
        }

        function renumberRows(divisiSlug) {
            document.querySelectorAll('#panel-' + divisiSlug + ' .periode-card').forEach(card => {
                card.querySelectorAll('.todo-row').forEach((row, i) => {
                    const el = row.querySelector('.col-no');
                    if (el) el.textContent = i + 1;
                });
            });
        }

        // ── Stats ──────────────────────────────────────────────────────────────────
        function updateStats(deltaTotal, deltaSelesai, divisiSlug) {
            const get = id => parseInt(document.getElementById(id + '-' + divisiSlug)?.textContent ?? '0');
            const set = (id, val) => {
                const el = document.getElementById(id + '-' + divisiSlug);
                if (el) el.textContent = val;
            };

            let total = get('statTotal') + deltaTotal;
            let selesai = get('statSelesai') + deltaSelesai;
            let tersisa = total - selesai;
            let persen = total > 0 ? Math.round((selesai / total) * 100) : 0;

            set('statTotal', total);
            set('statSelesai', selesai);
            set('statTersisa', tersisa);
            set('statPersen', persen + '%');

            const fill = document.getElementById('progressFill-' + divisiSlug);
            const pct = document.getElementById('progressPct-' + divisiSlug);
            if (fill) fill.style.width = persen + '%';
            if (pct) pct.textContent = persen + '%';

            updateHeaderPersen();
        }

        function updateHeaderPersen() {
            // Sum all divisi panels for overall header %
            let total = 0,
                selesai = 0;
            document.querySelectorAll('[id^="statTotal-"]').forEach(el => {
                total += parseInt(el.textContent || '0');
            });
            document.querySelectorAll('[id^="statSelesai-"]').forEach(el => {
                selesai += parseInt(el.textContent || '0');
            });
            const persen = total > 0 ? Math.round((selesai / total) * 100) : 0;
            const hdr = document.getElementById('headerPersen');
            if (hdr) hdr.textContent = persen + '% selesai';
        }

        // ── Toast ──────────────────────────────────────────────────────────────────
        function showToast(msg) {
            document.querySelectorAll('.agenda-toast').forEach(t => t.remove());
            const t = document.createElement('div');
            t.className = 'agenda-toast';
            t.style.cssText =
                'position:fixed;top:22px;right:22px;z-index:99999;background:linear-gradient(135deg,#4f46e5,#818cf8);color:#fff;padding:12px 20px;border-radius:12px;font-weight:700;font-size:.84rem;display:flex;align-items:center;gap:8px;box-shadow:0 6px 24px rgba(79,70,229,.4);';
            t.innerHTML = '<i class="fas fa-check-circle"></i>' + msg;
            document.body.appendChild(t);
            setTimeout(() => {
                t.style.transition = 'opacity .3s';
                t.style.opacity = '0';
                setTimeout(() => t.remove(), 300);
            }, 2500);
        }

        function escHtml(s) {
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
@endsection
