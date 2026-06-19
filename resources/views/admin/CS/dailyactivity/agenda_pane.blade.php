@php
    $dData = $divisiData[$divisi] ?? [
        'total' => 0,
        'selesai' => 0,
        'tersisa' => 0,
        'persen' => 0,
        'templates' => collect(),
        'grouped' => collect(),
    ];
    $total = $dData['total'];
    $selesai = $dData['selesai'];
    $tersisa = $dData['tersisa'];
    $persen = $dData['persen'];
    $grouped = $dData['grouped'];
    $divisiSlug = \Illuminate\Support\Str::slug($divisi, '_');
@endphp
<div class="agenda-wrap" id="panel-{{ $divisiSlug }}" style="min-height: auto; padding: 10px 0 30px;">
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
            <div class="progress-fill" id="progressFill-{{ $divisiSlug }}" style="width:{{ $persen }}%"></div>
        </div>
        <div class="progress-pct" id="progressPct-{{ $divisiSlug }}">{{ $persen }}%</div>
    </div>

    {{-- Main Sub Tabs (Hari Ini / Riwayat / Rekap) - Aligned Left --}}
    <div class="main-sub-tabs mb-4 d-flex justify-content-start gap-2">
        <button class="divisi-tab-btn active sub-tab-btn" id="btn-hari-ini-{{ $divisiSlug }}"
            onclick="switchSubTab('{{ $divisiSlug }}', 'hari-ini', this)">
            <i class="fas fa-calendar-day mr-1"></i> Hari Ini
        </button>
        <button class="divisi-tab-btn sub-tab-btn" id="btn-riwayat-{{ $divisiSlug }}"
            onclick="switchSubTab('{{ $divisiSlug }}', 'riwayat', this); loadRiwayat('{{ $divisi }}', '{{ $divisiSlug }}')">
            <i class="fas fa-history mr-1"></i> Riwayat
        </button>
        @if (auth()->user()->hasSubrole('cs_supervisor'))
            <button class="divisi-tab-btn sub-tab-btn" id="btn-rekap-{{ $divisiSlug }}"
                onclick="switchSubTab('{{ $divisiSlug }}', 'rekap', this); loadRekap('{{ $divisiSlug }}')">
                <i class="fas fa-chart-pie mr-1"></i> Rekap Semua
            </button>
        @endif
    </div>

    {{-- Panel Hari Ini --}}
    <div id="sub-panel-hari-ini-{{ $divisiSlug }}">
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
                $pi = $periodeInfo[$tipe] ?? ['range' => '', 'reset_label' => '', 'reset_info' => ''];
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

                <table class="table table-bordered mb-0 table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%; text-align: center;">No</th>
                            <th>Deskripsi Pekerjaan</th>
                            <th style="width: 15%; text-align: center;">Target</th>
                            <th style="width: 15%; text-align: center;">Realisasi</th>
                            <th style="width: 12%; text-align: center;">Checklist</th>
                            <th style="width: 10%; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-{{ $divisiSlug }}-{{ $tipe }}">
                        @forelse($tplGroup->values() as $i => $tpl)
                            <tr class="todo-row {{ $tpl->log && $tpl->log->is_done ? 'done' : '' }}"
                                id="row-{{ $tpl->id }}" data-tipe="{{ $tipe }}"
                                data-divisi="{{ $divisiSlug }}" data-judul="{{ strtolower($tpl->judul) }}">
                                <td class="text-center font-weight-bold col-no"
                                    style="color: #666; font-size: 0.85rem; vertical-align: middle;">
                                    {{ $i + 1 }}</td>
                                <td style="vertical-align: middle;">
                                    <div class="judul font-weight-bold" style="font-size: 0.9rem; color: #333;">
                                        {{ $tpl->judul }}</div>
                                    @if ($tpl->deskripsi)
                                        <div class="deskripsi text-muted" style="font-size: 0.78rem; margin-top: 2px;">
                                            {{ $tpl->deskripsi }}</div>
                                    @endif
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <input type="text"
                                        class="live-edit-input live-todo-target font-weight-bold text-center"
                                        value="{{ $tpl->target ?? '' }}" data-template-id="{{ $tpl->id }}"
                                        placeholder="Target...">
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <input type="text"
                                        class="live-edit-input live-todo-realisasi font-weight-bold text-center"
                                        value="{{ $tpl->log ? $tpl->log->realisasi : '' }}"
                                        data-log-id="{{ $tpl->log ? $tpl->log->id : '' }}" placeholder="Realisasi..."
                                        {{ $tpl->log ? '' : 'disabled' }}>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    @if ($tpl->log)
                                        <button class="check-circle {{ $tpl->log->is_done ? 'checked' : '' }}"
                                            onclick="toggleCheck({{ $tpl->log->id }}, this, '{{ $divisiSlug }}')"
                                            title="{{ $tpl->log->is_done ? 'Tandai belum selesai' : 'Tandai selesai' }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
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

        {{-- Tugas Harian Beda --}}
        <div class="periode-card" data-tipe="harian_beda" id="card-{{ $divisiSlug }}-harian_beda">
            <div class="periode-card-header" style="background: #fafbff;">
                <div class="left">
                    <span class="tipe-chip"
                        style="background: rgba(232, 62, 140, 0.1); color: #e83e8c; border: 1px solid rgba(232, 62, 140, 0.25);">Tugas
                        Harian Beda</span>
                    <span class="periode-range">Manual Input</span>
                </div>
                <div class="reset-info" style="color:#e83e8c;">
                    <i class="fas fa-info-circle"></i> Tidak reset otomatis & arsip permanen
                </div>
            </div>
            <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between"
                style="background: #f8f9fc;">
                <span class="text-muted small"><i class="fas fa-keyboard"></i> Diinput manual per hari. Tugas yang
                    sudah selesai akan masuk ke Riwayat.</span>
                <button class="btn btn-sm btn-primary btn-add-daily-task-{{ $divisiSlug }} py-1 px-3"
                    onclick="addDailyTaskLive('{{ $divisi }}', '{{ $divisiSlug }}')"
                    style="font-size: 0.82rem; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fas fa-plus"></i> Tambah Tugas Harian
                </button>
            </div>

            <table class="table table-bordered mb-0 table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th>Deskripsi Tugas</th>
                        <th style="width: 15%; text-align: center;">Target</th>
                        <th style="width: 15%; text-align: center;">Realisasi</th>
                        <th style="width: 12%; text-align: center;">Deadline</th>
                        <th style="width: 10%; text-align: center;">Checklist</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-{{ $divisiSlug }}-harian_beda">
                    @php
                        $divisiTasks = isset($dailyTasks)
                            ? $dailyTasks->filter(fn($task) => $task->divisi === $divisi)->values()
                            : collect();
                    @endphp
                    @foreach ($divisiTasks as $i => $task)
                        <tr class="todo-row {{ $task->is_done ? 'done' : '' }}" id="daily-row-{{ $task->id }}"
                            data-divisi="{{ $divisiSlug }}">
                            <td class="text-center font-weight-bold col-no"
                                style="color: #666; font-size: 0.85rem; vertical-align: middle;">{{ $i + 1 }}
                            </td>
                            <td class="col-task-content" style="vertical-align: middle;">
                                <input type="text" class="live-edit-input live-task-judul font-weight-bold"
                                    value="{{ $task->judul }}" data-task-id="{{ $task->id }}"
                                    placeholder="Nama tugas..." style="font-size: 0.9rem; color: #333;">
                                <input type="text"
                                    class="live-edit-input live-task-deskripsi text-muted small mt-1"
                                    value="{{ $task->deskripsi ?? '' }}" data-task-id="{{ $task->id }}"
                                    placeholder="Catatan tambahan (opsional)..." style="font-size: 0.78rem;">
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <input type="text"
                                    class="live-edit-input live-task-target font-weight-bold text-center"
                                    value="{{ $task->target ?? '' }}" data-task-id="{{ $task->id }}"
                                    placeholder="Target...">
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <input type="text"
                                    class="live-edit-input live-task-realisasi font-weight-bold text-center"
                                    value="{{ $task->realisasi ?? '' }}" data-task-id="{{ $task->id }}"
                                    placeholder="Realisasi...">
                            </td>
                            <td class="col-task-deadline text-center" style="vertical-align: middle;">
                                <input type="date"
                                    class="live-edit-input live-task-deadline text-center text-danger font-weight-bold"
                                    value="{{ $task->deadline ?? '' }}" data-task-id="{{ $task->id }}">
                            </td>
                            <td class="text-center col-check" style="vertical-align: middle;">
                                <button class="check-circle {{ $task->is_done ? 'checked' : '' }}"
                                    onclick="toggleDailyTaskCheck({{ $task->id }}, this, '{{ $divisiSlug }}')"
                                    title="{{ $task->is_done ? 'Tandai belum selesai' : 'Tandai selesai' }}">
                                    <i class="fas fa-check"></i>
                                </button>
                            </td>
                            <td class="text-center col-action" style="vertical-align: middle;">
                                <button class="del-btn btn-sm btn-action-icon"
                                    onclick="deleteDailyTask({{ $task->id }}, '{{ $divisiSlug }}')"
                                    title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Panel Riwayat --}}
    <div id="sub-panel-riwayat-{{ $divisiSlug }}" style="display: none;">
        <div class="card p-3 mb-3 border border-light shadow-sm" style="background: #fafbff; border-radius: 12px;">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label font-weight-bold small text-muted mb-1" style="font-size: 0.75rem;">Cari
                        Kata Kunci</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="history-search-{{ $divisiSlug }}" class="form-control"
                            placeholder="Ketik kata kunci..."
                            oninput="filterRiwayat('{{ $divisi }}', '{{ $divisiSlug }}')">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold small text-muted mb-1" style="font-size: 0.75rem;">Dari
                        Tanggal</label>
                    <input type="date" id="history-dari-{{ $divisiSlug }}" class="form-control form-control-sm"
                        onchange="filterRiwayat('{{ $divisi }}', '{{ $divisiSlug }}')">
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold small text-muted mb-1"
                        style="font-size: 0.75rem;">Sampai Tanggal</label>
                    <input type="date" id="history-sampai-{{ $divisiSlug }}"
                        class="form-control form-control-sm"
                        onchange="filterRiwayat('{{ $divisi }}', '{{ $divisiSlug }}')">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-sm btn-outline-secondary"
                        onclick="resetRiwayatFilters('{{ $divisi }}', '{{ $divisiSlug }}')">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <div id="history-list-{{ $divisiSlug }}" class="history-list">
            {{-- Loaded via AJAX --}}
        </div>
    </div>

    {{-- Panel Rekap (hanya CS Supervisor) --}}
    @if (auth()->user()->hasSubrole('cs_supervisor'))
        <div id="sub-panel-rekap-{{ $divisiSlug }}" style="display: none;">

            {{-- Date Navigation Bar --}}
            <div class="d-flex align-items-center justify-content-between mb-2"
                style="background:#fff; border:1px solid #e3e6f0; border-radius:14px; padding:8px 14px; box-shadow:0 2px 8px rgba(0,0,0,.04);">
                <button id="rekap-btn-prev-{{ $divisiSlug }}" class="rekap-nav-btn"
                    onclick="changeRekapDate('{{ $divisiSlug }}', -1)" title="Hari sebelumnya">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <div class="text-center" style="flex:1;">
                    <div id="rekap-date-label-{{ $divisiSlug }}"
                        style="font-weight:800; color:#4f46e5; font-size:0.88rem; line-height:1.2;">
                        Hari Ini
                    </div>
                    <div id="rekap-date-sub-{{ $divisiSlug }}"
                        style="font-size:0.7rem; color:#aaa; margin-top:1px;">
                        {{ \Carbon\Carbon::today()->translatedFormat('l, j F Y') }}
                    </div>

                </div>

                <button id="rekap-btn-next-{{ $divisiSlug }}" class="rekap-nav-btn"
                    onclick="changeRekapDate('{{ $divisiSlug }}', 1)" title="Hari berikutnya" disabled>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            {{-- Name Search Filter (dropdown) --}}
            <div class="mb-2" style="position:relative;">
                <i class="fas fa-user"
                    style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#aaa; font-size:0.75rem; pointer-events:none; z-index:1;"></i>
                <select id="rekap-name-search-{{ $divisiSlug }}" class="search-box"
                    style="padding:7px 10px 7px 30px; width:100%; font-size:0.82rem; appearance:auto; cursor:pointer;"
                    onchange="filterRekapByName(this.value, '{{ $divisiSlug }}')">
                    <option value="">— Semua Karyawan —</option>
                </select>
            </div>

            {{-- Rekap Content --}}
            <div id="rekap-list-{{ $divisiSlug }}" class="rekap-list"
                style="max-height:60vh; overflow-y:auto; padding-right:2px;">
                {{-- Loaded via AJAX --}}
            </div>
        </div>
    @endif
</div>
