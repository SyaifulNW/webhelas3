@extends('layouts.masteradmin')

@section('content')
    @php
        $canEdit = $permissions['canEdit'] ?? false;
        $canDelete = $permissions['canDelete'] ?? false;
        $isReadOnly = $permissions['isReadOnly'] ?? true;
        $seeAllData = $permissions['seeAllData'] ?? false;
        $groupView = $groupView ?? false;
        $availableUnits = $permissions['canAccessUnits'] ?? ['Helas Corp'];
        // Tampil kolom Aksi jika bisa edit ATAU bisa hapus (misal Yasmin)
        $colCount = ($canEdit || $canDelete) ? 9 : 8;
    @endphp

    <div class="container-fluid px-4">

        {{-- ===== HEADER ===== --}}
        <div class="row mb-3 align-items-start">
            <div class="col-12 d-flex align-items-center flex-wrap" style="gap:8px;">
                <div>
                    <h1 class="h3 font-weight-bold text-gray-800 mb-1">
                        <i class="fas fa-clipboard-list text-primary mr-2"></i> Minutes of Meeting (MoM)
                    </h1>
                    <p class="text-muted small mb-0">Media pencatatan, monitoring hasil rapat, dan tindak lanjut pekerjaan tim.</p>
                </div>
                <div class="ml-auto d-flex align-items-center" style="gap:6px;">
                    @if ($isReadOnly)
                        <span class="badge badge-warning px-3 py-2" style="font-size:.7rem;">
                            <i class="fas fa-eye mr-1"></i> Read Only — Rekap semua divisi
                        </span>
                    @endif
                    @if ($seeAllData)
                        <span class="badge badge-info px-3 py-2" style="font-size:.7rem;">
                            <i class="fas fa-globe mr-1"></i> Semua Data
                        </span>
                    @else
                        <span class="badge badge-secondary px-3 py-2" style="font-size:.7rem;">
                            <i class="fas fa-user mr-1"></i> Data Saya
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== TABS ===== --}}
        <ul class="nav nav-tabs mom-tabs mb-0" id="momTabs" role="tablist">
            @foreach ($availableUnits as $tabUnit)
                <li class="nav-item">
                    <a class="nav-link {{ $unit === $tabUnit ? 'active' : '' }} d-flex align-items-center" href="#"
                        data-unit="{{ $tabUnit }}" role="tab">
                        <i class="fas {{ $tabUnit === 'Helas Corp' ? 'fa-building' : 'fa-clinic-medical' }} mr-2"></i>
                        <span>MoM {{ $tabUnit }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- ===== TAB CONTENT PANEL ===== --}}
        <div class="tab-panel-wrapper">

            {{-- Toolbar --}}
            <div class="row mb-3 align-items-center px-1 pt-3">
                <div class="col-lg-8 col-md-7 d-flex align-items-center flex-wrap" style="gap:10px;">
                    <div class="d-none">
                        <label for="filter-deadline" class="mb-0 text-muted font-weight-bold text-uppercase"
                            style="font-size:.68rem;letter-spacing:.5px;white-space:nowrap;">Filter Deadline:</label>
                        <div class="d-flex align-items-center" style="gap:4px;">
                            <input type="date" id="filter-deadline" class="border rounded px-2 py-1 text-dark font-weight-bold"
                                style="font-size:.8rem;outline:none;cursor:pointer;background:#fff;height:32px;min-width:180px;">
                            <button type="button" id="btn-clear-deadline" class="btn btn-light border d-flex align-items-center justify-content-center"
                                style="height:32px;width:32px;padding:0;display:none;" title="Bersihkan filter">
                                <i class="fas fa-times text-secondary" style="font-size:.8rem;"></i>
                            </button>
                        </div>
                    </div>
                    <span id="active-unit-badge" class="badge badge-pill px-3 py-2 font-weight-bold shadow-sm"
                        style="font-size:.72rem;">
                        <i class="fas fa-building mr-1"></i>
                        <span id="active-unit-label">{{ $unit }}</span>
                    </span>
                    {{-- Filter PIC --}}
                    <div class="d-flex align-items-center filter-pic-container" style="gap:8px;">
                        <label for="filter-pic" class="mb-0 text-muted font-weight-bold text-uppercase"
                            style="font-size:.68rem;letter-spacing:.5px;white-space:nowrap;">Filter PIC:</label>
                        <select id="filter-pic" class="border rounded px-2 py-1 text-dark font-weight-bold shadow-sm"
                            style="font-size:.8rem;outline:none;cursor:pointer;background:#fff;height:32px;min-width:150px;">
                            <option value="all">Semua PIC</option>
                            @foreach ($pics as $p)
                                <option value="{{ $p->name }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 d-flex justify-content-md-end justify-content-start align-items-center mt-3 mt-md-0 flex-wrap" style="gap:8px;">
                    @if ($canEdit && $unit === 'Helas Corp')
                        <a href="{{ route('admin.mom.create', ['username' => \Illuminate\Support\Str::slug(Auth::user()->name)]) }}"
                            class="btn btn-primary px-3 shadow-sm font-weight-bold d-inline-flex align-items-center justify-content-center btn-responsive"
                            style="height:38px;white-space:nowrap;">
                            <i class="fas fa-link mr-1"></i> Tambah via Link
                        </a>
                    @endif
                    @if ($canEdit)
                        <button id="btn-add-mom" class="btn btn-primary px-4 shadow-sm font-weight-bold d-inline-flex align-items-center justify-content-center btn-responsive"
                            style="height:38px;">
                            <i class="fas fa-plus mr-1"></i> Tambah MoM
                        </button>
                    @endif
                </div>
            </div>

            {{-- ===== KONTEN TABEL ===== --}}
            <div id="mom-content-area">

                @if ($groupView)
                    {{-- ── GROUPED VIEW (Yasmin): grouped per user ── --}}
                    @if ($groupedMoms && $groupedMoms->isNotEmpty())
                        @foreach ($groupedMoms as $creatorId => $rows)
                            @php
                                $creator = $rows->first()->creator;
                                $creatorName = $creator->name ?? '(User dihapus)';
                                $creatorRole = $creator->role ?? '';
                                $creatorDivisi = $creator->divisi ?? '';
                                $divisiLabel = $creatorDivisi ?: $creatorRole;
                                $isOwnerGroup = (int)$creatorId === (int)Auth::id();
                                $groupColCount = ($isOwnerGroup && ($canEdit || $canDelete)) ? 8 : 7;
                            @endphp

                            <div class="mom-user-group mb-4" data-creator="{{ $creatorId }}">
                                {{-- Header divisi --}}
                                <div class="mom-user-header">
                                    <i class="fas fa-user mr-2"></i>
                                    <span class="mom-user-name">{{ $creatorName }}</span>
                                    @if ($divisiLabel)
                                        <span class="mom-user-divisi"> — {{ $divisiLabel }}</span>
                                    @endif
                                    <span class="mom-user-count ml-auto">{{ $rows->count() }} data</span>
                                </div>

                                {{-- Tabel per user --}}
                                <div class="card border-0 shadow-sm" style="border-radius:0 0 10px 10px;overflow:hidden;">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover mb-0 mom-group-table">
                                                <thead class="text-white text-center mom-group-thead" id="mom-thead">
                                                    <tr>
                                                        <th class="py-2 text-uppercase small align-middle"
                                                            style="width:40px;">No</th>
                                                        <th class="py-2 text-uppercase small align-middle">ToDoList / Task</th>
                                                        <th class="py-2 text-uppercase small align-middle"
                                                            style="width:105px;">Deadline</th>
                                                        <th class="py-2 text-uppercase small align-middle"
                                                            style="width:120px;">PIC</th>
                                                        <th class="py-2 text-uppercase small align-middle"
                                                            style="width:120px;">Requester</th>
                                                        <th class="py-2 text-uppercase small align-middle"
                                                            style="width:155px;">Target</th>
                                                        <th class="py-2 text-uppercase small align-middle"
                                                            style="min-width:150px;">Realisasi</th>
                                                        <th class="py-2 text-uppercase small align-middle"
                                                            style="width:130px;">
                                                            <div class="d-flex flex-column align-items-center" style="gap:4px;">
                                                                <span>Status</span>
                                                                <select class="filter-status-table border rounded px-1 py-0 text-white font-weight-bold" 
                                                                    style="font-size:.7rem; outline:none; cursor:pointer; background:rgba(255,255,255,0.25); border:1px solid rgba(255,255,255,0.4); height:22px; width:110px;">
                                                                    <option value="all" style="color:#333;">Semua Status</option>
                                                                    <option value="Progress" style="color:#333;">Progress</option>
                                                                    <option value="Done" style="color:#333;">Done</option>
                                                                    <option value="Overdue" style="color:#333;">Overdue</option>
                                                                </select>
                                                            </div>
                                                        </th>
                                                        @if ($isOwnerGroup && ($canEdit || $canDelete))
                                                            <th class="py-2 text-uppercase small align-middle" style="width:55px;">Aksi</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white">
                                                    @foreach ($rows as $idx => $item)
                                                        @php
                                                            $sc =
                                                                $item->status === 'Done'
                                                                    ? 'bg-status-done'
                                                                    : ($item->status === 'Overdue'
                                                                        ? 'bg-status-overdue'
                                                                        : 'bg-status-progress');
                                                        @endphp
                                                        <tr data-id="{{ $item->id }}" data-status="{{ $item->status }}">
                                                            <td class="text-center font-weight-bold text-muted align-middle small">
                                                                {{ $idx + 1 }}
                                                            </td>

                                                            @if ($isOwnerGroup && $canEdit)
                                                                <td class="p-0 align-middle tc-cell" data-label="ToDoList / Task">
                                                                    <div class="tc-view">
                                                                        <div class="tc-text">{{ $item->keterangan ?: '' }}</div>
                                                                        @if ($item->keterangan)
                                                                            <button class="btn-lihat"
                                                                                onclick="showPopup('ToDoList / Task', this.closest('td').querySelector('textarea, .tc-text').textContent)">
                                                                                <i class="fas fa-eye"></i> Lihat
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                    <textarea class="tc-textarea live-field" data-field="keterangan" placeholder="Tulis keterangan/poin rapat...">{{ $item->keterangan }}</textarea>
                                                                </td>
                                                            @else
                                                                <td class="p-0 align-middle tc-cell readonly-cell"
                                                                    data-label="ToDoList / Task">
                                                                    <div class="tc-view">
                                                                        <div class="tc-text">{{ $item->keterangan ?: '' }}</div>
                                                                        @if ($item->keterangan)
                                                                            <button class="btn-lihat"
                                                                                onclick="showPopup('ToDoList / Task', this.closest('td').querySelector('.tc-text').textContent)">
                                                                                <i class="fas fa-eye"></i> Lihat
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            @endif

                                                            @if ($isOwnerGroup && $canEdit)
                                                                <td class="p-1 align-middle">
                                                                    <input type="date"
                                                                        class="form-control-inline text-center live-field"
                                                                        data-field="deadline" value="{{ $item->deadline }}">
                                                                </td>
                                                            @else
                                                                <td class="text-center align-middle small">
                                                                    {{ $item->deadline ? \Carbon\Carbon::parse($item->deadline)->format('d/m/Y') : '-' }}
                                                                </td>
                                                            @endif

                                                            @if ($isOwnerGroup && $canEdit)
                                                                <td class="p-1 align-middle pic-cell">
                                                                    <select class="form-control-inline live-field" data-field="pic">
                                                                        <option value="">Pilih PIC</option>
                                                                        @foreach ($pics as $pic)
                                                                            <option value="{{ $pic->name }}" {{ $item->pic === $pic->name ? 'selected' : '' }}>
                                                                                {{ $pic->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                            @else
                                                                <td class="align-middle small pic-cell" style="padding:5px 7px;">
                                                                    {{ $item->pic ?: '-' }}
                                                                </td>
                                                            @endif

                                                            @if ($isOwnerGroup && $canEdit)
                                                                <td class="p-1 align-middle">
                                                                    <select class="form-control-inline live-field" data-field="requester">
                                                                        <option value="">Pilih Requester</option>
                                                                        @foreach ($pics as $pic)
                                                                            <option value="{{ $pic->name }}" {{ $item->requester === $pic->name ? 'selected' : '' }}>
                                                                                {{ $pic->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                            @else
                                                                <td class="align-middle small" style="padding:5px 7px;">
                                                                    {{ $item->requester ?: '-' }}
                                                                </td>
                                                            @endif

                                                            @if ($isOwnerGroup && $canEdit)
                                                                <td class="p-0 align-middle tc-cell" data-label="Target">
                                                                    <div class="tc-view">
                                                                        <div class="tc-text" data-placeholder="Target pekerjaan...">
                                                                            {{ $item->target ?: '' }}</div>
                                                                        @if ($item->target)
                                                                            <button class="btn-lihat"
                                                                                onclick="showPopup('Target', this.closest('td').querySelector('textarea, .tc-text').textContent)">
                                                                                <i class="fas fa-eye"></i> Lihat
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                    <textarea class="tc-textarea live-field" data-field="target" placeholder="Target pekerjaan...">{{ $item->target }}</textarea>
                                                                </td>
                                                            @else
                                                                <td class="p-0 align-middle tc-cell readonly-cell"
                                                                    data-label="Target">
                                                                    <div class="tc-view">
                                                                        <div class="tc-text"
                                                                            data-placeholder="Target pekerjaan...">
                                                                            {{ $item->target ?: '' }}</div>
                                                                        @if ($item->target)
                                                                            <button class="btn-lihat"
                                                                                onclick="showPopup('Target', this.closest('td').querySelector('.tc-text').textContent)">
                                                                                <i class="fas fa-eye"></i> Lihat
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            @endif

                                                            @if ($isOwnerGroup && $canEdit)
                                                                <td class="p-0 align-middle tc-cell" data-label="Realisasi">
                                                                    <div class="tc-view">
                                                                        <div class="tc-text" data-placeholder="Hasil tindak lanjut...">{{ $item->hasil ?: '' }}</div>
                                                                        @if ($item->hasil)
                                                                            <button class="btn-lihat"
                                                                                onclick="showPopup('Realisasi', this.closest('td').querySelector('textarea, .tc-text').textContent)">
                                                                                <i class="fas fa-eye"></i> Lihat
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                    <textarea class="tc-textarea live-field" data-field="hasil" placeholder="Hasil tindak lanjut...">{{ $item->hasil }}</textarea>
                                                                </td>
                                                                <td class="p-1 align-middle text-center" style="width:130px;">
                                                                    <select class="form-control-inline live-field status-select {{ $sc }}" data-field="status" style="width:100% !important;">
                                                                        <option value="Progress" {{ $item->status === 'Progress' ? 'selected' : '' }}>Progress</option>
                                                                        <option value="Done" {{ $item->status === 'Done' ? 'selected' : '' }}>Done</option>
                                                                        <option value="Overdue" {{ $item->status === 'Overdue' ? 'selected' : '' }}>Overdue</option>
                                                                    </select>
                                                                </td>
                                                            @else
                                                                <td class="p-0 align-middle tc-cell readonly-cell" data-label="Realisasi">
                                                                    <div class="tc-view">
                                                                        <div class="tc-text" data-placeholder="Hasil tindak lanjut...">{{ $item->hasil ?: '' }}</div>
                                                                        @if ($item->hasil)
                                                                            <button class="btn-lihat"
                                                                                onclick="showPopup('Realisasi', this.closest('td').querySelector('.tc-text').textContent)">
                                                                                <i class="fas fa-eye"></i> Lihat
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    <span class="badge {{ $sc }} px-2 py-1 font-weight-bold" style="font-size:0.65rem; border-radius:20px; text-transform:uppercase; box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                                                                        {{ $item->status ?: '-' }}
                                                                    </span>
                                                                </td>
                                                            @endif

                                                            @if ($isOwnerGroup && ($canEdit || $canDelete))
                                                                <td class="text-center align-middle p-1">
                                                                    <button class="btn btn-link text-danger p-0 btn-delete"
                                                                        data-id="{{ $item->id }}" title="Hapus">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            <strong>Belum Ada Data MoM</strong>
                        </div>
                    @endif
                @else
                    {{-- ── NON-ADMIN VIEW: tabel biasa ── --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius:0 0 12px 12px;overflow:hidden;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0" id="mom-table">
                                    <thead class="text-white text-center" id="mom-thead">
                                        <tr>
                                            <th class="py-3 text-uppercase small align-middle" style="width:50px;">No</th>
                                            <th class="py-3 text-uppercase small align-middle">ToDoList / Task</th>
                                            <th class="py-3 text-uppercase small align-middle" style="width:120px;">Deadline</th>
                                            <th class="py-3 text-uppercase small align-middle" style="width:130px;">PIC</th>
                                            <th class="py-3 text-uppercase small align-middle" style="width:130px;">Requester</th>
                                            <th class="py-3 text-uppercase small align-middle" style="width:170px;">Target</th>
                                            <th class="py-3 text-uppercase small align-middle" style="min-width:150px;">Realisasi</th>
                                            <th class="py-3 text-uppercase small align-middle" style="width:140px;">
                                                <div class="d-flex flex-column align-items-center" style="gap:5px;">
                                                    <span>Status</span>
                                                    <select id="filter-status-table" class="border rounded px-2 py-1 text-white font-weight-bold" 
                                                        style="font-size:.75rem; outline:none; cursor:pointer; background:rgba(255,255,255,0.25); border:1px solid rgba(255,255,255,0.4); height:26px; width:120px;">
                                                        <option value="all" style="color:#333;">Semua Status</option>
                                                        <option value="Progress" style="color:#333;">Progress</option>
                                                        <option value="Done" style="color:#333;">Done</option>
                                                        <option value="Overdue" style="color:#333;">Overdue</option>
                                                    </select>
                                                </div>
                                            </th>
                                            @if ($canEdit || $canDelete)
                                                <th class="py-3 text-uppercase small align-middle" style="width:55px;">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="mom-table-body" class="bg-white">
                                        @forelse($moms as $index => $item)
                                            @php
                                                $sc =
                                                    $item->status === 'Done'
                                                        ? 'bg-status-done'
                                                        : ($item->status === 'Overdue'
                                                            ? 'bg-status-overdue'
                                                            : 'bg-status-progress');
                                                $isOwner = (int)$item->created_by === (int)Auth::id();
                                            @endphp
                                            <tr data-id="{{ $item->id }}" data-status="{{ $item->status }}">
                                                <td class="text-center font-weight-bold text-muted no-col align-middle">
                                                    {{ $index + 1 }}
                                                </td>
 
                                                @if ($isOwner)
                                                    <td class="p-0 align-middle tc-cell" data-label="ToDoList / Task">
                                                        <div class="tc-view">
                                                            <div class="tc-text">{{ $item->keterangan ?: '' }}</div>
                                                            @if ($item->keterangan)
                                                                <button class="btn-lihat"
                                                                    onclick="showPopup('ToDoList / Task', this.closest('td').querySelector('textarea, .tc-text').textContent)">
                                                                    <i class="fas fa-eye"></i> Lihat
                                                                </button>
                                                            @endif
                                                        </div>
                                                        <textarea class="tc-textarea live-field" data-field="keterangan" placeholder="Tulis keterangan/poin rapat...">{{ $item->keterangan }}</textarea>
                                                    </td>
                                                @else
                                                    <td class="p-0 align-middle tc-cell readonly-cell" data-label="ToDoList / Task">
                                                        <div class="tc-view">
                                                            <div class="tc-text">{{ $item->keterangan ?: '' }}</div>
                                                            @if ($item->keterangan)
                                                                <button class="btn-lihat"
                                                                    onclick="showPopup('ToDoList / Task', this.closest('td').querySelector('.tc-text').textContent)">
                                                                    <i class="fas fa-eye"></i> Lihat
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endif
 
                                                @if ($isOwner)
                                                    <td class="p-1 align-middle">
                                                        <input type="date"
                                                            class="form-control-inline text-center live-field"
                                                            data-field="deadline" value="{{ $item->deadline }}">
                                                    </td>
                                                @else
                                                    <td class="text-center align-middle small">
                                                        {{ $item->deadline ? \Carbon\Carbon::parse($item->deadline)->format('d/m/Y') : '-' }}
                                                    </td>
                                                @endif
 
                                                @if ($isOwner)
                                                    <td class="p-1 align-middle pic-cell">
                                                        <select class="form-control-inline live-field" data-field="pic">
                                                            <option value="">Pilih PIC</option>
                                                            @foreach ($pics as $pic)
                                                                <option value="{{ $pic->name }}" {{ $item->pic === $pic->name ? 'selected' : '' }}>
                                                                    {{ $pic->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                @else
                                                    <td class="align-middle small pic-cell" style="padding:5px 7px;">
                                                        {{ $item->pic ?: '-' }}
                                                    </td>
                                                @endif
 
                                                @if ($isOwner)
                                                    <td class="p-1 align-middle">
                                                        <select class="form-control-inline live-field" data-field="requester">
                                                            <option value="">Pilih Requester</option>
                                                            @foreach ($pics as $pic)
                                                                <option value="{{ $pic->name }}" {{ $item->requester === $pic->name ? 'selected' : '' }}>
                                                                    {{ $pic->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                @else
                                                    <td class="align-middle small" style="padding:5px 7px;">
                                                        {{ $item->requester ?: '-' }}
                                                    </td>
                                                @endif
 
                                                @if ($isOwner)
                                                    <td class="p-0 align-middle tc-cell" data-label="Target">
                                                        <div class="tc-view">
                                                            <div class="tc-text" data-placeholder="Target pekerjaan...">
                                                                {{ $item->target ?: '' }}</div>
                                                            @if ($item->target)
                                                                <button class="btn-lihat"
                                                                    onclick="showPopup('Target', this.closest('td').querySelector('textarea, .tc-text').textContent)">
                                                                    <i class="fas fa-eye"></i> Lihat
                                                                </button>
                                                            @endif
                                                        </div>
                                                        <textarea class="tc-textarea live-field" data-field="target" placeholder="Target pekerjaan...">{{ $item->target }}</textarea>
                                                    </td>
                                                @else
                                                    <td class="p-0 align-middle tc-cell readonly-cell" data-label="Target">
                                                        <div class="tc-view">
                                                            <div class="tc-text" data-placeholder="Target pekerjaan...">
                                                                {{ $item->target ?: '' }}</div>
                                                            @if ($item->target)
                                                                <button class="btn-lihat"
                                                                    onclick="showPopup('Target', this.closest('td').querySelector('.tc-text').textContent)">
                                                                    <i class="fas fa-eye"></i> Lihat
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endif
 
                                                @if ($isOwner)
                                                    <td class="p-0 align-middle tc-cell" data-label="Realisasi">
                                                        <div class="tc-view">
                                                            <div class="tc-text" data-placeholder="Hasil tindak lanjut...">{{ $item->hasil ?: '' }}</div>
                                                            @if ($item->hasil)
                                                                <button class="btn-lihat"
                                                                    onclick="showPopup('Realisasi', this.closest('td').querySelector('textarea, .tc-text').textContent)">
                                                                    <i class="fas fa-eye"></i> Lihat
                                                                </button>
                                                            @endif
                                                        </div>
                                                        <textarea class="tc-textarea live-field" data-field="hasil" placeholder="Hasil tindak lanjut...">{{ $item->hasil }}</textarea>
                                                    </td>
                                                    <td class="p-1 align-middle text-center" style="width:130px;">
                                                        <select class="form-control-inline live-field status-select {{ $sc }}" data-field="status" style="width:100% !important;">
                                                            <option value="Progress" {{ $item->status === 'Progress' ? 'selected' : '' }}>Progress</option>
                                                            <option value="Done" {{ $item->status === 'Done' ? 'selected' : '' }}>Done</option>
                                                            <option value="Overdue" {{ $item->status === 'Overdue' ? 'selected' : '' }}>Overdue</option>
                                                        </select>
                                                    </td>
                                                @else
                                                    <td class="p-0 align-middle tc-cell readonly-cell" data-label="Realisasi">
                                                        <div class="tc-view">
                                                            <div class="tc-text" data-placeholder="Hasil tindak lanjut...">{{ $item->hasil ?: '' }}</div>
                                                            @if ($item->hasil)
                                                                <button class="btn-lihat"
                                                                    onclick="showPopup('Realisasi', this.closest('td').querySelector('.tc-text').textContent)">
                                                                    <i class="fas fa-eye"></i> Lihat
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge {{ $sc }} px-2 py-1 font-weight-bold" style="font-size:0.65rem; border-radius:20px; text-transform:uppercase; box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                                                            {{ $item->status }}
                                                        </span>
                                                    </td>
                                                @endif

                                                @if ($canEdit || $canDelete)
                                                    <td class="text-center align-middle p-1">
                                                        @if ($isOwner)
                                                            <button class="btn btn-link text-danger p-0 btn-delete"
                                                                data-id="{{ $item->id }}" title="Hapus">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr class="empty-row">
                                                <td colspan="{{ $colCount }}" class="text-center py-5 text-muted">
                                                    <i class="fas fa-folder-open fa-2x mb-2 d-block"
                                                        style="opacity:.3;"></i>
                                                    <strong>Belum Ada Data MoM</strong>
                                                    <div class="small">Klik tombol "Tambah MoM" untuk menambah data.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>{{-- /mom-content-area --}}

        </div>{{-- /tab-panel-wrapper --}}
    </div>{{-- /container --}}

    {{-- ===== POPUP DETAIL ===== --}}
    <div id="mom-popup-overlay" onclick="closePopup(event)">
        <div id="mom-popup-box">
            <div class="mpop-header">
                <span id="mom-popup-title">Detail</span>
                <button class="mpop-close" onclick="closePopup(null)"><i class="fas fa-times"></i></button>
            </div>
            <div class="mpop-body" id="mom-popup-body"></div>
        </div>
    </div>

    {{-- ===== CSS ===== --}}
    <style>
        /* ── Tabs ──────────────────────────────────────────── */
        .mom-tabs {
            border-bottom: none;
        }

        .mom-tabs .nav-link {
            border: 1px solid #dee2e6;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            color: #6c757d;
            font-size: .82rem;
            font-weight: 600;
            padding: 10px 20px;
            background: #f8f9fc;
            transition: all .18s;
            margin-right: 4px;
        }

        .mom-tabs .nav-link:hover {
            color: #4e73df;
            background: #eef0fb;
        }

        .mom-tabs .nav-link.active {
            background: #fff;
            color: #4e73df;
            border-color: #dee2e6;
            border-bottom-color: #fff;
            z-index: 2;
        }

        .mom-tabs .nav-link[data-unit="Helas Corp"].active {
            border-top: 3px solid #4e73df;
            color: #4e73df;
        }

        .mom-tabs .nav-link[data-unit="Helas Aesthetic Clinic"].active {
            border-top: 3px solid #e83e8c;
            color: #e83e8c;
        }

        .tab-panel-wrapper {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0 8px 8px 8px;
            padding: 0 16px 16px;
            position: relative;
        }

        /* ── Thead / btn color ──────────────────────────────── */
        #mom-thead.thead-corp,
        .mom-group-thead.thead-corp {
            background-color: #4e73df;
        }

        #mom-thead.thead-clinic,
        .mom-group-thead.thead-clinic {
            background-color: #e83e8c;
        }

        #btn-add-mom.btn-corp {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        #btn-add-mom.btn-clinic {
            background-color: #e83e8c;
            border-color: #e83e8c;
        }

        #active-unit-badge.badge-corp {
            background-color: #dde8ff;
            color: #2850b0;
        }

        #active-unit-badge.badge-clinic {
            background-color: #fce4f0;
            color: #a01060;
        }

        /* ── Admin user-group header (pola Agenda) ──────────── */
        .mom-user-header {
            display: flex;
            align-items: center;
            background: #f0f2ff;
            border-left: 4px solid #4f46e5;
            border-radius: 0 8px 0 0;
            padding: 10px 16px;
            font-weight: 700;
            color: #1a1a2e;
            font-size: .88rem;
            margin-bottom: 0;
        }

        .mom-user-name {
            color: #1a1a2e;
            font-weight: 700;
        }

        .mom-user-divisi {
            color: #555;
            font-weight: 400;
            font-size: .8rem;
        }

        .mom-user-count {
            margin-left: auto;
            background: #4f46e5;
            color: #fff;
            font-size: .68rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
        }

        /* ── Read-only cell ─────────────────────────────────── */
        .readonly-cell {
            cursor: default !important;
        }

        .readonly-cell:hover .tc-view {
            background: transparent !important;
        }

        /* ── Inline controls ────────────────────────────────── */
        .form-control-inline {
            background: transparent !important;
            border: 1px solid transparent !important;
            width: 100% !important;
            padding: 5px 7px !important;
            font-size: .82rem !important;
            color: #333 !important;
            resize: none !important;
            border-radius: 4px !important;
            transition: border-color .15s, background .15s !important;
            font-family: inherit !important;
        }

        .form-control-inline:hover,
        .form-control-inline:focus {
            background: #f0f4ff !important;
            border-color: rgba(78, 115, 223, .45) !important;
            outline: none !important;
            box-shadow: none !important;
        }

        input[type="date"].form-control-inline {
            cursor: pointer;
            text-align: center !important;
        }

        /* ── Status ─────────────────────────────────────────── */
        .status-select {
            border-radius: 20px !important;
            padding: 3px 10px !important;
            font-size: .72rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: .5px !important;
            text-align: center !important;
            text-align-last: center !important;
            width: 100% !important;
            cursor: pointer !important;
            border: none !important;
            -webkit-appearance: none !important;
            appearance: none !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .12) !important;
        }

        .bg-status-progress,
        .badge.bg-status-progress {
            background: #fff176 !important;
            color: #6d5000 !important;
        }

        .bg-status-done,
        .badge.bg-status-done {
            background: #c8f5d8 !important;
            color: #155724 !important;
        }

        .bg-status-overdue,
        .badge.bg-status-overdue {
            background: #ffd6d6 !important;
            color: #721c24 !important;
        }

        .bg-status-none,
        .badge.bg-status-none {
            background: #f0f0f0 !important;
            color: #888 !important;
        }

        /* ── tc-cell ────────────────────────────────────────── */
        .tc-cell {
            min-width: 130px;
            max-width: 200px;
            cursor: pointer;
            vertical-align: middle !important;
        }

        .tc-view {
            padding: 5px 8px;
        }

        .tc-text {
            font-size: .82rem;
            color: #333;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
            min-height: 1.2em;
        }

        .tc-text:empty::before {
            content: attr(data-placeholder);
            color: #aaa;
            font-style: italic;
            font-size: .78rem;
        }

        .btn-lihat {
            display: inline-block;
            margin-top: 3px;
            font-size: .67rem;
            padding: 1px 7px;
            border-radius: 10px;
            font-weight: 700;
            border: 1px solid #4e73df;
            color: #4e73df;
            background: transparent;
            cursor: pointer;
            transition: all .18s;
            line-height: 1.6;
        }

        .btn-lihat:hover {
            background: #4e73df;
            color: #fff;
        }

        .tc-textarea {
            display: none;
            width: 100%;
            padding: 5px 8px;
            font-size: .82rem;
            font-family: inherit;
            color: #333;
            border: 1px solid #4e73df;
            border-radius: 4px;
            resize: none;
            overflow: hidden;
            outline: none;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, .12);
            background: #f0f4ff;
            min-height: 60px;
            transition: all .15s;
        }

        .tc-cell.editing .tc-view {
            display: none;
        }

        .tc-cell.editing .tc-textarea {
            display: block;
        }

        .tc-cell.editing .tc-edit-wrapper {
            display: block !important;
        }

        .tc-cell:not(.editing):not(.readonly-cell):hover .tc-view {
            background: #f5f7ff;
            border-radius: 4px;
        }

        .btn-delete {
            transition: transform .15s;
        }

        .btn-delete:hover {
            transform: scale(1.2);
            color: #bd2130 !important;
        }

        .form-control-inline.auto-resize {
            overflow: hidden !important;
            resize: none !important;
            min-height: unset !important;
        }

        /* ── Popup ──────────────────────────────────────────── */
        #mom-popup-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        #mom-popup-overlay.active {
            display: flex;
        }

        #mom-popup-box {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .22);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            animation: mpopIn .18s ease;
        }

        @keyframes mpopIn {
            from {
                transform: scale(.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .mpop-header {
            background: #4e73df;
            color: #fff;
            padding: 13px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            font-size: .92rem;
        }

        .mpop-close {
            background: rgba(255, 255, 255, .2);
            border: none;
            color: #fff;
            border-radius: 50%;
            width: 27px;
            height: 27px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: .9rem;
            transition: background .15s;
        }

        .mpop-close:hover {
            background: rgba(255, 255, 255, .38);
        }

        .mpop-body {
            padding: 18px 22px;
            font-size: .88rem;
            color: #333;
            line-height: 1.75;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 62vh;
            overflow-y: auto;
        }

        /* ── Responsive Overrides ───────────────────────────── */
        @media (max-width: 767.98px) {
            .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            .tab-panel-wrapper {
                padding: 0 10px 10px !important;
            }
            .mom-tabs .nav-link {
                padding: 8px 12px !important;
                font-size: .78rem !important;
            }
            /* Make buttons take full width and stack side-by-side cleanly on mobile */
            .col-lg-4.col-md-5.d-flex {
                width: 100% !important;
                flex-direction: row !important;
                justify-content: stretch !important;
                gap: 8px !important;
                margin-top: 15px !important;
            }
            .btn-responsive {
                flex: 1 1 0% !important;
                width: auto !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
                font-size: .8rem !important;
                text-align: center !important;
            }
            .filter-pic-container {
                margin-top: 5px !important;
            }
        }
    </style>

    {{-- ===== JAVASCRIPT ===== --}}
    <script>
        (function($) {
            'use strict';

            const CSRF = "{{ csrf_token() }}";
            const ROUTE_STORE = "{{ route('admin.mom.store') }}";
            const ROUTE_INDEX = "{{ route('admin.mom.index') }}";
            const CAN_EDIT = {{ $canEdit ? 'true' : 'false' }};
            const CAN_DELETE = {{ $canDelete ? 'true' : 'false' }};
            const USER_ID = {{ Auth::id() }};
            // GROUP_VIEW: gunakan grouped view
            const GROUP_VIEW = {{ $groupView ? 'true' : 'false' }};
            const COL_COUNT = {{ $colCount }};
            const PICS = @json($pics);

            let activeUnit = "{{ $unit }}";

            // ── Theme ────────────────────────────────────────────────────────────────

            function statusClass(s) {
                return s === 'Done' ? 'bg-status-done' : s === 'Overdue' ? 'bg-status-overdue' : 'bg-status-progress';
            }

            window.statusClass = statusClass; // expose to window for html access if needed

            function applyUnitTheme(unit) {
                const clinic = unit === 'Helas Aesthetic Clinic';
                $('#mom-thead, .mom-group-thead')
                    .removeClass('thead-corp thead-clinic')
                    .addClass(clinic ? 'thead-clinic' : 'thead-corp');
                if (CAN_EDIT) {
                    $('#btn-add-mom').removeClass('btn-corp btn-clinic').addClass(clinic ? 'btn-clinic' : 'btn-corp');
                }
                const $b = $('#active-unit-badge');
                $b.removeClass('badge-corp badge-clinic').addClass(clinic ? 'badge-clinic' : 'badge-corp');
                $b.find('i').removeClass('fa-building fa-clinic-medical').addClass(clinic ? 'fa-clinic-medical' :
                    'fa-building');
                $('#active-unit-label').text(unit);
            }
            applyUnitTheme(activeUnit);

            // ── Helpers ──────────────────────────────────────────────────────────────

            function toast(msg, icon = 'success', timer = 900) {
                Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer,
                        timerProgressBar: true
                    })
                    .fire({
                        icon,
                        title: msg
                    });
            }

            function reindex() {
                let n = 1;
                $('#mom-table-body tr:visible').not('.empty-row,.filtered-empty').each(function() {
                    $(this).find('.no-col').text(n++);
                });
            }

            function checkEmpty() {
                if ($('#mom-table-body tr').not('.empty-row,.filtered-empty').length === 0) {
                    $('#mom-table-body').html(emptyRow());
                }
            }

            function emptyRow() {
                const dl = $('#filter-deadline').val();
                if (dl) {
                    return `<tr class="empty-row">
                    <td colspan="${COL_COUNT}" class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-times fa-2x d-block mb-2" style="opacity:.3;"></i>
                        <strong>Tidak ada data MoM pada tanggal deadline tersebut (${fmtDate(dl)})</strong>
                    </td>
                </tr>`;
                }
                return `<tr class="empty-row">
                <td colspan="${COL_COUNT}" class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-2x d-block mb-2" style="opacity:.3;"></i>
                    <strong>Belum Ada Data MoM</strong>
                    ${CAN_EDIT ? '<div class="small">Klik "Tambah MoM" untuk menambah data.</div>' : ''}
                </td>
            </tr>`;
            }

            // ── Filter (non-admin only — admin reloads from server) ───────────────────

            function applyFilter() {
                if (GROUP_VIEW) {
                    loadUnit(activeUnit, false);
                    return;
                }
                const f = $('#filter-status-table').val();
                const picVal = $('#filter-pic').val();
                $('.filtered-empty').remove();
                let vis = 0;
                $('#mom-table-body tr').not('.empty-row').each(function() {
                    const s = $(this).attr('data-status') || '';
                    
                    const $picCell = $(this).find('.pic-cell');
                    let picText = '';
                    if ($picCell.length) {
                        const $picSelect = $picCell.find('select');
                        picText = $picSelect.length ? ($picSelect.val() || '') : $picCell.text().trim();
                    }

                    const matchStatus = (f === 'all' || s === f);
                    const matchPic = (picVal === 'all' || picText === picVal);

                    if (matchStatus && matchPic) {
                        $(this).show();
                        vis++;
                    } else {
                        $(this).hide();
                    }
                });
                reindex();
                const total = $('#mom-table-body tr').not('.empty-row,.filtered-empty').length;
                if (vis === 0 && total > 0) {
                    $('#mom-table-body').append(`<tr class="filtered-empty">
                    <td colspan="${COL_COUNT}" class="text-center py-5 text-muted">
                        <i class="fas fa-filter fa-lg d-block mb-2" style="opacity:.3;"></i>
                        Tidak ada data yang cocok dengan kriteria filter.
                    </td>
                </tr>`);
                }
            }

            function autoResize(el) {
                if (!el) return;
                el.style.height = 'auto';
                el.style.height = el.scrollHeight + 'px';
            }

            function escHtml(str) {
                return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(
                    /"/g, '&quot;');
            }

            function fmtDate(val) {
                if (!val) return '-';
                const p = val.split('-');
                return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : val;
            }

            function buildPicSelect(selectedPic) {
                let options = `<option value="">Pilih PIC</option>`;
                PICS.forEach(pic => {
                    const sel = pic.name === selectedPic ? 'selected' : '';
                    options += `<option value="${escHtml(pic.name)}" ${sel}>${escHtml(pic.name)}</option>`;
                });
                return `<select class="form-control-inline live-field" data-field="pic">${options}</select>`;
            }

            function buildRequesterSelect(selectedRequester) {
                let options = `<option value="">Pilih Requester</option>`;
                PICS.forEach(pic => {
                    const sel = pic.name === selectedRequester ? 'selected' : '';
                    options += `<option value="${escHtml(pic.name)}" ${sel}>${escHtml(pic.name)}</option>`;
                });
                return `<select class="form-control-inline live-field" data-field="requester">${options}</select>`;
            }

            // ── Row builders (non-admin) ──────────────────────────────────────────────

            function buildTcCell(item, field, label, placeholder) {
                const val = item[field] || '';
                const lihat = val ?
                    `<button class="btn-lihat" onclick="showPopup('${label}', this.closest('td').querySelector('textarea,.tc-text').textContent)"><i class="fas fa-eye"></i> Lihat</button>` :
                    '';
                return `<td class="p-0 align-middle tc-cell" data-label="${label}">
                <div class="tc-view">
                    <div class="tc-text" data-placeholder="${placeholder}">${escHtml(val)}</div>${lihat}
                </div>
                <textarea class="tc-textarea live-field" data-field="${field}" placeholder="${placeholder}">${escHtml(val)}</textarea>
            </td>`;
            }

            function buildStatusCell(item) {
                const statusVal = item.status || 'Progress';
                const sc = statusClass(statusVal);
                return `<td class="p-1 align-middle text-center" style="width:130px;">
                    <select class="form-control-inline live-field status-select ${sc}" data-field="status" style="width:100% !important;">
                        <option value="Progress" ${statusVal==='Progress'?'selected':''}>Progress</option>
                        <option value="Done"     ${statusVal==='Done'    ?'selected':''}>Done</option>
                        <option value="Overdue"  ${statusVal==='Overdue' ?'selected':''}>Overdue</option>
                    </select>
                </td>`;
            }

            function buildRowsHtml(moms) {
                if (!moms || moms.length === 0) return emptyRow();
                return moms.map((item, i) => {
                    const isOwner = Number(item.created_by) === Number(USER_ID);
                    
                    const keteranganCell = isOwner 
                        ? buildTcCell(item,'keterangan','ToDoList / Task','Tulis keterangan/poin rapat...')
                        : `<td class="p-0 align-middle tc-cell readonly-cell" data-label="ToDoList / Task">
                            <div class="tc-view">
                                <div class="tc-text">${escHtml(item.keterangan || '')}</div>
                                ${item.keterangan ? `<button class="btn-lihat" onclick="showPopup('ToDoList / Task', this.closest('td').querySelector('.tc-text').textContent)"><i class="fas fa-eye"></i> Lihat</button>` : ''}
                            </div>
                           </td>`;

                    const deadlineCell = isOwner
                        ? `<td class="p-1 align-middle"><input type="date" class="form-control-inline text-center live-field" data-field="deadline" value="${escHtml(item.deadline||'')}"></td>`
                        : `<td class="text-center align-middle small">${fmtDate(item.deadline)}</td>`;

                    const picCell = isOwner
                        ? `<td class="p-1 align-middle pic-cell">${buildPicSelect(item.pic)}</td>`
                        : `<td class="align-middle small pic-cell" style="padding:5px 7px;">${escHtml(item.pic||'-')}</td>`;

                    const requesterCell = isOwner
                        ? `<td class="p-1 align-middle">${buildRequesterSelect(item.requester)}</td>`
                        : `<td class="align-middle small" style="padding:5px 7px;">${escHtml(item.requester||'-')}</td>`;

                    const targetCell = isOwner
                        ? buildTcCell(item,'target','Target','Target pekerjaan...')
                        : `<td class="p-0 align-middle tc-cell readonly-cell" data-label="Target">
                            <div class="tc-view">
                                <div class="tc-text" data-placeholder="Target pekerjaan...">${escHtml(item.target||'')}</div>
                                ${item.target ? `<button class="btn-lihat" onclick="showPopup('Target', this.closest('td').querySelector('.tc-text').textContent)"><i class="fas fa-eye"></i> Lihat</button>` : ''}
                            </div>
                           </td>`;

                    const hasilCell = isOwner
                        ? buildTcCell(item, 'hasil', 'Realisasi', 'Hasil tindak lanjut...')
                        : `<td class="p-0 align-middle tc-cell readonly-cell" data-label="Realisasi">
                            <div class="tc-view">
                                <div class="tc-text" data-placeholder="Hasil tindak lanjut...">${escHtml(item.hasil || '')}</div>
                                ${item.hasil ? `<button class="btn-lihat" onclick="showPopup('Realisasi', this.closest('td').querySelector('.tc-text').textContent)"><i class="fas fa-eye"></i> Lihat</button>` : ''}
                            </div>
                           </td>`;

                    const statusCell = isOwner
                        ? buildStatusCell(item)
                        : `<td class="text-center align-middle">
                            <span class="badge ${statusClass(item.status)} px-2 py-1 font-weight-bold" style="font-size:0.65rem; border-radius:20px; text-transform:uppercase; box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                                ${escHtml(item.status || '-')}
                            </span>
                           </td>`;

                    const actionCell = (CAN_EDIT || CAN_DELETE)
                        ? `<td class="text-center align-middle p-1">
                            ${isOwner ? `<button class="btn btn-link text-danger p-0 btn-delete" data-id="${item.id}" title="Hapus"><i class="fas fa-trash-alt"></i></button>` : ''}
                           </td>`
                        : '';

                    return `<tr data-id="${item.id}" data-status="${item.status}">
                    <td class="text-center font-weight-bold text-muted no-col align-middle">${i + 1}</td>
                    ${keteranganCell}
                    ${deadlineCell}
                    ${picCell}
                    ${requesterCell}
                    ${targetCell}
                    ${hasilCell}
                    ${statusCell}
                    ${actionCell}
                </tr>`;
                }).join('');
            }

            function buildNewRow(item) {
                return `<tr data-id="${item.id}" data-status="${item.status}">
                <td class="text-center font-weight-bold text-muted no-col align-middle">1</td>
                <td class="p-0 align-middle tc-cell editing" data-label="ToDoList / Task">
                    <div class="tc-view"><div class="tc-text"></div></div>
                    <textarea class="tc-textarea live-field" data-field="keterangan" placeholder="Tulis keterangan/poin rapat..."></textarea>
                </td>
                <td class="p-1 align-middle"><input type="date" class="form-control-inline text-center live-field" data-field="deadline" value=""></td>
                <td class="p-1 align-middle pic-cell">${buildPicSelect(item.pic)}</td>
                <td class="p-1 align-middle">${buildRequesterSelect(item.requester)}</td>
                <td class="p-0 align-middle tc-cell" data-label="Target">
                    <div class="tc-view"><div class="tc-text" data-placeholder="Target pekerjaan..."></div></div>
                    <textarea class="tc-textarea live-field" data-field="target" placeholder="Target pekerjaan..."></textarea>
                </td>
                ${buildTcCell(item, 'hasil', 'Realisasi', 'Hasil tindak lanjut...')}
                ${buildStatusCell(item)}
                <td class="text-center align-middle p-1">
                    <button class="btn btn-link text-danger p-0 btn-delete" data-id="${item.id}" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>`;
            }

            // ── Admin grouped HTML builder (for AJAX reload) ─────────────────────────

            function buildGroupedHtml(moms) {
                if (!moms || moms.length === 0) {
                    const dl = $('#filter-deadline').val();
                    const msg = dl ? `Tidak ada data MoM pada tanggal deadline tersebut (${fmtDate(dl)})` : 'Belum Ada Data MoM';
                    return `<div class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-2x mb-2 d-block" style="opacity:.3;"></i>
                    <strong>${msg}</strong>
                </div>`;
                }

                // Group by created_by
                const groups = {};
                moms.forEach(item => {
                    const key = item.created_by ?? 'unknown';
                    if (!groups[key]) groups[key] = {
                        creator: item.creator,
                        rows: []
                    };
                    groups[key].rows.push(item);
                });

                const clinic = activeUnit === 'Helas Aesthetic Clinic';
                const theadClass = clinic ? 'thead-clinic' : 'thead-corp';

                return Object.entries(groups)
                    .sort(([keyA], [keyB]) => {
                        const idA = Number(keyA);
                        const idB = Number(keyB);
                        const userId = Number(USER_ID);
                        if (idA === userId) return -1;
                        if (idB === userId) return 1;
                        return 0;
                    })
                    .map(([key, group]) => {
                        const isOwnerGroup = Number(key) === Number(USER_ID);
                    const name = group.creator?.name || '(User dihapus)';
                    const divisi = group.creator?.divisi || group.creator?.role || '';
                    const divisiHtml = divisi ? ` <span class="mom-user-divisi"> — ${escHtml(divisi)}</span>` :
                        '';
                    const count = group.rows.length;

                    const rows = group.rows.map((item, i) => {
                        if (isOwnerGroup && CAN_EDIT) {
                            const keteranganCell = buildTcCell(item,'keterangan','ToDoList / Task','Tulis keterangan/poin rapat...');
                            const deadlineCell = `<td class="p-1 align-middle"><input type="date" class="form-control-inline text-center live-field" data-field="deadline" value="${escHtml(item.deadline||'')}"></td>`;
                            const picCell = `<td class="p-1 align-middle pic-cell">${buildPicSelect(item.pic)}</td>`;
                            const requesterCell = `<td class="p-1 align-middle">${buildRequesterSelect(item.requester)}</td>`;
                            const targetCell = buildTcCell(item,'target','Target','Target pekerjaan...');
                            const hasilCell = buildTcCell(item, 'hasil', 'Realisasi', 'Hasil tindak lanjut...');
                            const statusCell = buildStatusCell(item);
                            const actionCell = (CAN_EDIT || CAN_DELETE)
                                ? `<td class="text-center align-middle p-1">
                                    <button class="btn btn-link text-danger p-0 btn-delete" data-id="${item.id}" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                   </td>`
                                : '';

                            return `<tr data-id="${item.id}" data-status="${item.status}">
                                <td class="text-center font-weight-bold text-muted align-middle small">${i + 1}</td>
                                ${keteranganCell}
                                ${deadlineCell}
                                ${picCell}
                                ${requesterCell}
                                ${targetCell}
                                ${hasilCell}
                                ${statusCell}
                                ${actionCell}
                            </tr>`;
                        } else {
                            // Read-only view (for others, or if user cannot edit)
                            const keterangan = item.keterangan || '';
                            const target = item.target || '';
                            const hasil = item.hasil || '';

                            const tcReadonly = (val, label, placeholder) => {
                                const lihat = val ?
                                    `<button class="btn-lihat" onclick="showPopup('${label}', this.closest('td').querySelector('.tc-text').textContent)"><i class="fas fa-eye"></i> Lihat</button>` :
                                    '';
                                return `<td class="p-0 align-middle tc-cell readonly-cell" data-label="${label}">
                                <div class="tc-view">
                                    <div class="tc-text" data-placeholder="${placeholder}">${escHtml(val)}</div>${lihat}
                                </div>
                            </td>`;
                            };

                            const tcReadonlyStatus = (status) => {
                                const sc = statusClass(status);
                                return `<td class="text-center align-middle">
                                    <span class="badge ${sc} px-2 py-1 font-weight-bold" style="font-size:0.65rem; border-radius:20px; text-transform:uppercase; box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                                        ${escHtml(status||'-')}
                                    </span>
                                </td>`;
                            };

                            return `<tr data-status="${item.status}">
                                <td class="text-center font-weight-bold text-muted align-middle small">${i + 1}</td>
                                ${tcReadonly(keterangan, 'ToDoList / Task', 'Tulis keterangan/poin rapat...')}
                                <td class="text-center align-middle small">${fmtDate(item.deadline)}</td>
                                <td class="align-middle small pic-cell" style="padding:5px 7px;">${escHtml(item.pic||'-')}</td>
                                <td class="align-middle small" style="padding:5px 7px;">${escHtml(item.requester||'-')}</td>
                                ${tcReadonly(target, 'Target', 'Target pekerjaan...')}
                                ${tcReadonly(hasil, 'Realisasi', 'Hasil tindak lanjut...')}
                                ${tcReadonlyStatus(item.status)}
                            </tr>`;
                        }
                    }).join('');

                    const actionHeader = (isOwnerGroup && (CAN_EDIT || CAN_DELETE))
                        ? `<th class="py-2 text-uppercase small align-middle" style="width:55px;">Aksi</th>`
                        : '';

                    return `<div class="mom-user-group mb-4" data-creator="${escHtml(key)}">
                    <div class="mom-user-header">
                        <i class="fas fa-user mr-2"></i>
                        <span class="mom-user-name">${escHtml(name)}</span>${divisiHtml}
                        <span class="mom-user-count ml-auto">${count} data</span>
                    </div>
                    <div class="card border-0 shadow-sm" style="border-radius:0 0 10px 10px;overflow:hidden;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0 mom-group-table">
                                    <thead class="text-white text-center mom-group-thead ${theadClass}">
                                        <tr>
                                            <th class="py-2 text-uppercase small align-middle" style="width:40px;">No</th>
                                            <th class="py-2 text-uppercase small align-middle">ToDoList / Task</th>
                                            <th class="py-2 text-uppercase small align-middle" style="width:105px;">Deadline</th>
                                            <th class="py-2 text-uppercase small align-middle" style="width:120px;">PIC</th>
                                            <th class="py-2 text-uppercase small align-middle" style="width:120px;">Requester</th>
                                            <th class="py-2 text-uppercase small align-middle" style="width:155px;">Target</th>
                                            <th class="py-2 text-uppercase small align-middle" style="min-width:150px;">Realisasi</th>
                                            <th class="py-2 text-uppercase small align-middle" style="width:130px;">
                                                <div class="d-flex flex-column align-items-center" style="gap:4px;">
                                                    <span>Status</span>
                                                    <select class="filter-status-table border rounded px-1 py-0 text-white font-weight-bold" 
                                                        style="font-size:.7rem; outline:none; cursor:pointer; background:rgba(255,255,255,0.25); border:1px solid rgba(255,255,255,0.4); height:22px; width:110px;">
                                                        <option value="all">Semua Status</option>
                                                        <option value="Progress">Progress</option>
                                                        <option value="Done">Done</option>
                                                        <option value="Overdue">Overdue</option>
                                                    </select>
                                                </div>
                                            </th>
                                            ${actionHeader}
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">${rows}</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>`;
                }).join('');
            }

            // ── AJAX: Save field ─────────────────────────────────────────────────────

            function saveField(id, field, value, $el) {
                if (!CAN_EDIT) return;
                if ($el) $el.css('opacity', '.6');
                $.ajax({
                    url: '/admin/mom/' + id,
                    type: 'POST',
                    data: {
                        _token: CSRF,
                        _method: 'PUT',
                        [field]: value
                    },
                    success(res) {
                        if ($el) $el.css('opacity', '');
                        if (field === 'status') {
                            loadUnit(activeUnit, false);
                        }
                        toast('Tersimpan', 'success', 700);
                    },
                    error(xhr) {
                        if ($el) $el.css('opacity', '');
                        toast(xhr.responseJSON?.message || 'Gagal menyimpan', 'error', 1800);
                    }
                });
            }

            // ── Load unit (AJAX) ─────────────────────────────────────────────────────

            function loadUnit(unit, resetFilter) {
                activeUnit = unit;
                applyUnitTheme(unit);
                if (resetFilter) {
                    $('#filter-status-table, .filter-status-table').val('all');
                    $('#filter-pic').val('all');
                    $('#filter-deadline').val('');
                    $('#btn-clear-deadline').hide();
                }

                const params = {
                    unit
                };
                const f = $('#filter-status-table').val() || $('.filter-status-table').val();
                if (f && f !== 'all') params.status = f;

                const picVal = $('#filter-pic').val();
                if (picVal && picVal !== 'all') params.pic_filter = picVal;

                const dl = $('#filter-deadline').val();
                if (dl) {
                    params.deadline_filter = dl;
                    $('#btn-clear-deadline').show();
                } else {
                    $('#btn-clear-deadline').hide();
                }

                const $icon = $('#btn-refresh').find('i').addClass('fa-spin');

                $.ajax({
                    url: ROUTE_INDEX,
                    type: 'GET',
                    dataType: 'json',
                    data: params,
                    success(res) {
                        $icon.removeClass('fa-spin');
                        if (!res.success) {
                            toast('Gagal memuat data.', 'error', 1500);
                            return;
                        }

                        if (res.grouped) {
                            // Rebuild grouped view
                            $('#mom-content-area').html(buildGroupedHtml(res.data));
                        } else {
                            // Non-admin: rebuild flat table
                            $('#mom-table-body').html(buildRowsHtml(res.data));
                            $('.auto-resize').each(function() {
                                autoResize(this);
                            });
                            $('.tc-textarea').each(function() {
                                $(this).data('orig', $(this).val().trim());
                            });
                            applyFilter();
                        }
                    },
                    error() {
                        $icon.removeClass('fa-spin');
                        toast('Gagal memuat data.', 'error', 1500);
                    }
                });
            }

            // ── Tab switch ───────────────────────────────────────────────────────────

            $('#momTabs .nav-link').on('click', function(e) {
                e.preventDefault();
                const unit = $(this).data('unit');
                if (unit === activeUnit) return;
                $('#momTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                loadUnit(unit, true);
            });

            // ── Non-admin edit events (Click outside handler) ─────────────────────────

            if (CAN_EDIT) {
                $(document).on('click', function(e) {
                    const $clickedCell = $(e.target).closest('.tc-cell:not(.editing):not(.readonly-cell)');
                    if ($clickedCell.length) {
                        if ($(e.target).closest('.btn-lihat, select, input, textarea').length) return;
                        
                        closeAllEditingCells();
                        
                        $clickedCell.addClass('editing');
                        const $ta = $clickedCell.find('.tc-textarea');
                        if ($ta.length) {
                            autoResize($ta[0]);
                            $ta.focus();
                        }
                        return;
                    }

                    // Click outside to close and save
                    $('.tc-cell.editing').each(function() {
                        const $td = $(this);
                        if (!$td.is(e.target) && $td.has(e.target).length === 0) {
                            saveCellData($td);
                        }
                    });
                });

                function saveCellData($td) {
                    const id = $td.closest('tr').data('id');
                    const $ta = $td.find('.tc-textarea');
                    if (!$ta.length) {
                        $td.removeClass('editing');
                        return;
                    }
                    const field = $ta.data('field');
                    const val = $ta.val().trim();
                    const orig = $ta.data('orig') ?? '';
                    const $text = $td.find('.tc-text');
                    
                    $text.text(val);
                    
                    let $btn = $td.find('.tc-view .btn-lihat');
                    if (val) {
                        if (!$btn.length) {
                            $btn = $(`<button class="btn-lihat"><i class="fas fa-eye"></i> Lihat</button>`);
                            if (field === 'hasil') {
                                $td.find('.tc-view .d-flex').prepend($btn);
                            } else {
                                $td.find('.tc-view').append($btn);
                            }
                        }
                        $btn.attr('onclick',
                            `showPopup('${$td.data('label')}', this.closest('td').querySelector('textarea,.tc-text').textContent)`
                        );
                    } else {
                        $btn.remove();
                    }

                    $td.removeClass('editing');
                    if (val !== orig) {
                        $ta.data('orig', val);
                        saveField(id, field, val, $ta);
                    }
                }

                function closeAllEditingCells() {
                    $('.tc-cell.editing').each(function() {
                        saveCellData($(this));
                    });
                }

                $(document).on('focus', '.tc-textarea.live-field', function() {
                    if ($(this).data('orig') === undefined) $(this).data('orig', $(this).val().trim());
                });

                $(document).on('input', '.tc-textarea', function() {
                    autoResize(this);
                });

                $(document).on('change', 'input.live-field, select.live-field:not([data-field="status"]), textarea.form-control-inline.live-field', function() {
                    const $el = $(this);
                    const id = $el.closest('tr').data('id');
                    const field = $el.data('field');
                    if (!id || !field) return;
                    saveField(id, field, $el.val(), $el);
                });

                $(document).on('change', 'select[data-field="status"]', function() {
                    const $select = $(this);
                    const id = $select.closest('tr').data('id');
                    const val = $select.val();
                    if (!id) return;
                    
                    $select.closest('tr').attr('data-status', val);
                    saveField(id, 'status', val, $select);
                    
                    $select.removeClass('bg-status-progress bg-status-done bg-status-overdue bg-status-none')
                           .addClass(statusClass(val));
                });

                $(document).on('input', '.auto-resize', function() {
                    autoResize(this);
                });

                // Tambah MoM
                $('#btn-add-mom').on('click', function() {
                    const $btn = $(this);
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menambah...');
                    $.ajax({
                        url: ROUTE_STORE,
                        type: 'POST',
                        data: {
                            _token: CSRF,
                            unit: activeUnit
                        },
                        success(res) {
                            $btn.prop('disabled', false).html(
                                '<i class="fas fa-plus mr-1"></i> Tambah MoM');
                            if (GROUP_VIEW) {
                                loadUnit(activeUnit, false);
                                toast('Data MoM baru berhasil dibuat.', 'success');
                                return;
                            }
                            $('.empty-row').remove();
                            const $row = $(buildNewRow(res.data));
                            $('#mom-table-body').prepend($row);
                            reindex();
                            applyFilter();
                            const $ta = $row.find('textarea[data-field="keterangan"]');
                            autoResize($ta[0]);
                            $ta.focus();
                            toast('Data MoM baru berhasil dibuat.', 'success');
                        },
                        error(xhr) {
                            $btn.prop('disabled', false).html(
                                '<i class="fas fa-plus mr-1"></i> Tambah MoM');
                            Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan.',
                                'error');
                        }
                    });
                });

            }  // end if (CAN_EDIT)

            // ── Hapus — tersedia untuk CAN_EDIT maupun CAN_DELETE (misal Yasmin) ──────
            if (CAN_EDIT || CAN_DELETE) {
                $(document).on('click', '.btn-delete', function() {
                    const $tr = $(this).closest('tr');
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Hapus data MoM ini?',
                        text: 'Data yang dihapus tidak bisa dipulihkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e74a3b',
                        cancelButtonColor: '#858796',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then(r => {
                        if (!r.isConfirmed) return;
                        $.ajax({
                            url: '/admin/mom/' + id,
                            type: 'POST',
                            data: {
                                _token: CSRF,
                                _method: 'DELETE'
                              },
                            success() {
                                $tr.fadeOut(350, function() {
                                    $tr.remove();
                                    reindex();
                                    checkEmpty();
                                });
                                toast('Data MoM berhasil dihapus.', 'success');
                            },
                            error(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON?.message ||
                                    'Gagal menghapus.', 'error');
                            }
                        });
                    });
                });
            }

            // ── Filter & Sort Events ──────────────────────────────────────────────────

            $(document).on('change', '#filter-status-table, .filter-status-table', function() {
                const val = $(this).val();
                $('#filter-status-table, .filter-status-table').val(val);
                applyFilter();
            });

            $(document).on('change', '#filter-pic', function() {
                applyFilter();
            });

            $('#filter-deadline').on('change', function() {
                loadUnit(activeUnit, false);
            });

            $('#btn-clear-deadline').on('click', function() {
                $('#filter-deadline').val('');
                $(this).hide();
                loadUnit(activeUnit, false);
            });

            $('#btn-refresh').on('click', function() {
                loadUnit(activeUnit, false);
            });

            // ── Init ─────────────────────────────────────────────────────────────────

            $(function() {
                applyUnitTheme(activeUnit);
                $('.auto-resize').each(function() {
                    autoResize(this);
                });
                $('.tc-textarea').each(function() {
                    $(this).data('orig', $(this).val().trim());
                });
            });

        })(jQuery);

        // ── Popup ─────────────────────────────────────────────────────────────────────
        window.showPopup = function(title, text) {
            document.getElementById('mom-popup-title').textContent = title;
            document.getElementById('mom-popup-body').textContent = (text && String(text).trim()) ? text.trim() :
                '(Tidak ada data)';
            document.getElementById('mom-popup-overlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        };
        window.closePopup = function(e) {
            if (e === null || e.target === document.getElementById('mom-popup-overlay')) {
                document.getElementById('mom-popup-overlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        };
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.getElementById('mom-popup-overlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>
@endsection
