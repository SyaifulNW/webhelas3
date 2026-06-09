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

        /* Header */
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

        /* Stat Cards */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin: 18px 0;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .stat-card .stat-label {
            font-size: 0.72rem;
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

        /* Progress bar */
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
            font-size: 0.75rem;
            color: #4f46e5;
            font-weight: 700;
            margin-top: 4px;
        }

        /* Filter tabs + search bar */
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
            font-size: 0.82rem;
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

        .search-box {
            flex: 1;
            min-width: 160px;
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            padding: 7px 14px 7px 34px;
            color: #333;
            font-size: 0.85rem;
            outline: none;
        }

        .search-box:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .08);
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
            font-size: 0.8rem;
        }

        .btn-tambah {
            background: #4f46e5;
            border: none;
            border-radius: 10px;
            padding: 8px 18px;
            color: #fff;
            font-weight: 700;
            font-size: 0.83rem;
            cursor: pointer;
            transition: background .2s;
            white-space: nowrap;
        }

        .btn-tambah:hover {
            background: #4338ca;
        }

        /* Periode Card */
        .periode-card {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 16px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
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
            font-size: 0.72rem;
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
            font-size: 0.9rem;
            font-weight: 700;
            color: #1a1a2e;
        }

        .reset-info {
            font-size: 0.76rem;
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
            font-size: 0.75rem;
            color: #bbb;
            border-bottom: 1px solid #f0f2f8;
            background: #fafbff;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Table */
        .agenda-table {
            width: 100%;
            border-collapse: collapse;
        }

        .agenda-table thead tr {
            border-bottom: 1px solid #f0f2f8;
        }

        .agenda-table thead th {
            padding: 10px 16px;
            font-size: 0.75rem;
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
            font-size: 0.88rem;
            color: #555;
            vertical-align: middle;
        }

        .agenda-table td .judul {
            font-weight: 700;
            color: #1a1a2e;
        }

        .agenda-table td .deskripsi {
            font-size: 0.78rem;
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
            font-size: 0.82rem;
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

        /* Checklist button */
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
            font-size: 0.8rem;
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

        /* Delete button */
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
            font-size: 0.78rem;
            transition: all .2s;
            outline: none;
        }

        .del-btn:hover {
            background: rgba(239, 68, 68, .08);
            border-color: #fca5a5;
            color: #ef4444;
        }

        /* Form inputs in modal */
        .dark-input {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            color: #333;
            padding: 8px 12px;
            font-size: 0.84rem;
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
            font-size: 0.84rem;
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
            font-size: 0.84rem;
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
            font-size: 0.84rem;
            cursor: pointer;
            transition: all .2s;
        }

        .cancel-btn:hover {
            border-color: #ccc;
            color: #555;
        }

        /* Empty state */
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

        /* Modal */
        .modal-dark .modal-content {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 16px;
            color: #333;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
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

        .modal-label {
            font-size: 0.78rem;
            color: #aaa;
            font-weight: 700;
            margin-bottom: 6px;
            display: block;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        /* Admin section header */
        .admin-user-header {
            background: #f0f2ff;
            border-left: 4px solid #4f46e5;
            border-radius: 0 8px 8px 0;
            padding: 10px 16px;
            margin-bottom: 12px;
            font-weight: 700;
            color: #1a1a2e;
            font-size: 0.9rem;
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
                        <span id="headerPersen">{{ $persen }}% selesai</span>
                    </div>
                @endif
            </div>
        </div>

        @if ($isAdmin)
            {{-- ============ ADMINISTRATOR VIEW ============ --}}
            @if (empty($allAgendas))
                <div class="empty-state">
                    <i class="fas fa-clipboard-list "></i>
                    Belum ada agenda dari tim.
                </div>
            @else
                @foreach ($allAgendas as $entry)
                    <div class="mb-4">
                        <div class="admin-user-header">
                            <i class="fas fa-user mr-2 text-primary"></i>
                            {{ $entry['user']->name }}
                            @if ($entry['user']->divisi)
                                <span style="color:#666; font-weight:400; font-size:0.8rem;"> —
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
        @else
            {{-- ============ USER BIASA VIEW ============ --}}

            {{-- Stat Cards --}}
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-label">Tampil hari ini</div>
                    <div class="stat-value" id="statTotal">{{ $total }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Selesai</div>
                    <div class="stat-value green" id="statSelesai">{{ $selesai }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Tersisa</div>
                    <div class="stat-value orange" id="statTersisa">{{ $tersisa }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Progress</div>
                    <div class="stat-value purple" id="statPersen">{{ $persen }}%</div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="progress-wrap">
                <div class="progress-track">
                    <div class="progress-fill" id="progressFill" style="width: {{ $persen }}%"></div>
                </div>
                <div class="progress-pct" id="progressPct">{{ $persen }}%</div>
            </div>

            {{-- Filter Bar --}}
            <div class="filter-bar">
                <div class="tab-group">
                    <button class="tab-btn active" data-filter="semua" onclick="setFilter('semua', this)">Semua</button>
                    <button class="tab-btn" data-filter="harian" onclick="setFilter('harian', this)">Harian</button>
                    <button class="tab-btn" data-filter="mingguan" onclick="setFilter('mingguan', this)">Mingguan</button>
                    <button class="tab-btn" data-filter="bulanan" onclick="setFilter('bulanan', this)">Bulanan</button>
                </div>
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-box" id="searchBox" placeholder="Cari agenda..."
                        oninput="doSearch(this.value)">
                </div>
                <button class="btn-tambah" onclick="showAddModal()">
                    <i class="fas fa-plus mr-1"></i> Tambah agenda
                </button>
            </div>

            {{-- Periode Cards --}}
            @foreach (['harian', 'mingguan', 'bulanan'] as $tipe)
                @php
                    $tplGroup = $grouped->get($tipe, collect());
                    $pi = $periodeInfo[$tipe];
                @endphp
                <div class="periode-card" data-tipe="{{ $tipe }}" id="card-{{ $tipe }}">
                    <div class="periode-card-header">
                        <div class="left">
                            <span class="tipe-chip chip-{{ $tipe }}">{{ ucfirst($tipe) }}</span>
                            <span class="periode-range">{{ $pi['range'] }}</span>
                        </div>
                        <div class="reset-info">
                            <i class="fas fa-sync-alt"></i> Reset: {{ $pi['reset_label'] }}
                        </div>
                    </div>
                    <div class="reset-note">
                        <i class="fas fa-info-circle"></i> {{ $pi['reset_info'] }}
                    </div>
                    <table class="agenda-table">
                        <thead>
                            <tr>
                                <th class="col-no">No</th>
                                <th>Deskripsi Pekerjaan</th>
                                <th class="col-check">Checklist</th>
                                <th class="col-action">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-{{ $tipe }}">
                            @forelse($tplGroup->values() as $i => $tpl)
                                <tr class="todo-row {{ $tpl->log && $tpl->log->is_done ? 'done' : '' }}"
                                    id="row-{{ $tpl->id }}" data-tipe="{{ $tipe }}"
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
                                                onclick="toggleCheck({{ $tpl->log->id }}, this)"
                                                title="{{ $tpl->log->is_done ? 'Tandai belum selesai' : 'Tandai selesai' }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td class="col-action">
                                        <button class="del-btn" onclick="deleteAgenda({{ $tpl->id }})"
                                            title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row-{{ $tipe }}">
                                    <td colspan="4" class="empty-state" style="padding: 24px;">
                                        <i class="fas fa-inbox"
                                            style="font-size:1.5rem; margin-bottom:6px; display:block;"></i>
                                        Belum ada agenda {{ $tipe }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach

        @endif {{-- end isAdmin --}}

    </div>

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
                        <div class="mb-3">
                            <label
                                style="font-size:0.8rem; color:#aaa; font-weight:700; margin-bottom:6px; display:block;">JENIS
                                AGENDA</label>
                            <select id="modalTipe" class="dark-select" style="width:100%;">
                                <option value="harian">Harian</option>
                                <option value="mingguan">Mingguan</option>
                                <option value="bulanan">Bulanan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label
                                style="font-size:0.8rem; color:#aaa; font-weight:700; margin-bottom:6px; display:block;">DESKRIPSI
                                PEKERJAAN</label>
                            <input type="text" id="modalJudul" class="dark-input" placeholder="Tulis nama agenda...">
                        </div>
                        <div>
                            <label
                                style="font-size:0.8rem; color:#aaa; font-weight:700; margin-bottom:6px; display:block;">CATATAN
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

        let currentFilter = 'semua';
        let currentSearch = '';

        // ---- Filter Tab ----
        function setFilter(filter, btn) {
            currentFilter = filter;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyFilter();
        }

        function doSearch(val) {
            currentSearch = val.toLowerCase();
            applyFilter();
        }

        function applyFilter() {
            document.querySelectorAll('.periode-card').forEach(card => {
                const tipe = card.dataset.tipe;
                const showCard = currentFilter === 'semua' || currentFilter === tipe;
                let hasVisible = false;

                card.querySelectorAll('.todo-row').forEach(row => {
                    const matchTipe = currentFilter === 'semua' || row.dataset.tipe === currentFilter;
                    const matchSearch = !currentSearch || (row.dataset.judul && row.dataset.judul.includes(
                        currentSearch));
                    const show = matchTipe && matchSearch;
                    row.style.display = show ? '' : 'none';
                    if (show) hasVisible = true;
                });

                card.style.display = showCard ? '' : 'none';
            });
        }

        // ---- Modal Tambah ----
        function showAddModal() {
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
                    tipe
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
                    updateStats(1, 0);
                    showToast('Agenda berhasil ditambahkan!');
                })
                .fail(() => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan';
                    showToast('Gagal menyimpan, coba lagi.');
                });
        }

        // Enter key in modal
        document.addEventListener('DOMContentLoaded', function() {
            const jd = document.getElementById('modalJudul');
            if (jd) jd.addEventListener('keypress', e => {
                if (e.key === 'Enter') saveAgenda();
            });
        });

        // ---- Append new row ----
        function appendRow(res) {
            const tbody = document.getElementById('tbody-' + res.tipe);
            if (!tbody) return;

            // Remove empty state row
            const emptyRow = tbody.querySelector('.empty-row-' + res.tipe);
            if (emptyRow) emptyRow.remove();

            const no = tbody.querySelectorAll('.todo-row').length + 1;
            const tr = document.createElement('tr');
            tr.className = 'todo-row';
            tr.id = 'row-' + res.template_id;
            tr.dataset.tipe = res.tipe;
            tr.dataset.judul = res.judul.toLowerCase();
            tr.innerHTML = `
        <td class="col-no">${no}</td>
        <td>
            <div class="judul">${escHtml(res.judul)}</div>
            ${res.deskripsi ? `<div class="deskripsi">${escHtml(res.deskripsi)}</div>` : ''}
        </td>
        <td class="col-check">
            <button class="check-circle" onclick="toggleCheck(${res.log_id}, this)" title="Tandai selesai">
                <i class="fas fa-check"></i>
            </button>
        </td>
        <td class="col-action">
            <button class="del-btn" onclick="deleteAgenda(${res.template_id})" title="Hapus">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>`;
            tbody.appendChild(tr);
        }

        // ---- Toggle Check ----
        function toggleCheck(logId, btn) {
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
                        updateStats(0, 1);
                    } else {
                        btn.classList.remove('checked');
                        row.classList.remove('done');
                        btn.title = 'Tandai selesai';
                        updateStats(0, -1);
                    }
                });
        }

        // ---- Delete ----
        function deleteAgenda(templateId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus agenda ini?',
                    text: 'Agenda dan seluruh riwayat checklist akan dihapus.',
                    icon: 'warning',
                    background: '#252540',
                    color: '#e0e0e0',
                    position: 'top',
                    width: '380px',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) doDelete(templateId);
                });
            } else {
                if (confirm('Hapus agenda ini?')) doDelete(templateId);
            }
        }

        function doDelete(templateId) {
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
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            updateStats(-1, wasDone ? -1 : 0);
                            renumberRows();
                        }, 300);
                    }
                    showToast('Agenda berhasil dihapus.');
                }
            });
        }

        function renumberRows() {
            document.querySelectorAll('.periode-card').forEach(card => {
                card.querySelectorAll('.todo-row').forEach((row, i) => {
                    row.querySelector('.col-no').textContent = i + 1;
                });
            });
        }

        // ---- Stats ----
        function updateStats(deltaTotal, deltaSelesai) {
            let total = parseInt(document.getElementById('statTotal').textContent) + deltaTotal;
            let selesai = parseInt(document.getElementById('statSelesai').textContent) + deltaSelesai;
            let tersisa = total - selesai;
            let persen = total > 0 ? Math.round((selesai / total) * 100) : 0;

            document.getElementById('statTotal').textContent = total;
            document.getElementById('statSelesai').textContent = selesai;
            document.getElementById('statTersisa').textContent = tersisa;
            document.getElementById('statPersen').textContent = persen + '%';
            document.getElementById('progressFill').style.width = persen + '%';
            document.getElementById('progressPct').textContent = persen + '%';
            document.getElementById('headerPersen').textContent = persen + '% selesai';
        }

        // ---- Toast ----
        function showToast(msg) {
            document.querySelectorAll('.agenda-toast').forEach(t => t.remove());
            const t = document.createElement('div');
            t.className = 'agenda-toast';
            t.style.cssText =
                'position:fixed;top:22px;right:22px;z-index:99999;background:linear-gradient(135deg,#4f46e5,#818cf8);color:#fff;padding:12px 20px;border-radius:12px;font-weight:700;font-size:0.84rem;display:flex;align-items:center;gap:8px;box-shadow:0 6px 24px rgba(79,70,229,0.4);animation:none;';
            t.innerHTML = '<i class="fas fa-check-circle"></i>' + msg;
            document.body.appendChild(t);
            setTimeout(() => {
                t.style.transition = 'opacity .3s';
                t.style.opacity = '0';
                setTimeout(() => t.remove(), 300);
            }, 2500);
        }

        function escHtml(s) {
            return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
@endsection
