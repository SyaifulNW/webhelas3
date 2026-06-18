@extends('layouts.masteradmin')

@section('content')
    <div class="container-fluid">
        <h3 class="fw-bold mb-4">{{ $isOperasional ? 'Pengaturan Operasional (Chapter & Agen)' : 'Pengaturan Administrator' }}</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <ul class="nav nav-tabs" id="settingTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users" role="tab">Users & Roles</a>
            </li>
            @if (!$isOperasional)
            <li class="nav-item">
                <a class="nav-link" id="target-tab" data-toggle="tab" href="#target" role="tab">Target Omset</a>
            </li>
            @endif
        </ul>

        <div class="tab-content" id="settingTabContent">

            {{-- TAB 1: USER MANAGEMENT --}}
            <div class="tab-pane fade show active p-3 bg-white border border-top-0" id="users" role="tabpanel">
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-plus"></i> Tambah User Baru
                </button>

                @if (!$isOperasional)
                {{-- Table Pusat Helas (Administrator only) --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold"><i class="fas fa-building mr-2"></i>Pusat Helas</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th style="width: 50px;">No.</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th class="text-center">Transfer Database</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($usersPusat as $u)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="font-weight-bold">{{ $u->name }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td>
                                                <span class="badge badge-info shadow-sm">{{ ucfirst($u->role) }}</span>
                                            </td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input user-toggle"
                                                        id="userSwitch{{ $u->id }}" data-id="{{ $u->id }}"
                                                        {{ $u->is_active ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="userSwitch{{ $u->id }}">
                                                        {{ $u->is_active ? 'Aktif' : 'Non-Aktif' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-indigo shadow-sm btn-transfer-db"
                                                    data-id="{{ $u->id }}" data-name="{{ $u->name }}">
                                                    <i class="fas fa-exchange-alt mr-1"></i> Transfer
                                                </button>
                                            </td>
                                            <td class="text-right">
                                                <button class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                                    data-target="#editUserModal{{ $u->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.settings.users.destroy', $u->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger shadow-sm delete-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        {{-- Modal Edit (Pusat) --}}
                                        @include('admin.Core.settings.partials.edit_modal', [
                                            'u' => $u,
                                            'roles' => $roles,
                                        ])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Table Cabang Helas --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold"><i class="fas fa-store mr-2"></i>{{ $isOperasional ? 'Cabang Helas (Chapter & Agen)' : 'Cabang Helas (Chapter & Reseller)' }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="width: 50px;">No.</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role & Jumlah Agen</th>
                                        <th>Status</th>
                                        @if (!$isOperasional)
                                        <th class="text-center">Transfer Database</th>
                                        @endif
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $groupedCabang = $usersCabang->groupBy('chapter');
                                    @endphp
                                    @foreach ($groupedCabang as $chapterName => $members)
                                        @php
                                            $leader = $members->where('role', 'chapter')->first();
                                            // Include both reseller and agen as subordinates
                                            $staff = $members->whereIn('role', ['reseller', 'agen']);
                                            $chapterId = Str::slug($chapterName ?: 'no-chapter');
                                        @endphp

                                        {{-- Header Chapter / Leader Row --}}
                                        <tr class="bg-light @if ($staff->count() > 0) clickable-row @endif"
                                            @if ($staff->count() > 0) data-toggle="collapse" data-target="#members-{{ $chapterId }}" @endif
                                            style="cursor: pointer; transition: all 0.2s;">
                                            <td class="text-center">
                                                <span class="text-muted font-weight-bold">{{ $loop->iteration }}</span>
                                            </td>
                                            <td class="font-weight-bold">
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="d-flex align-items-center">
                                                            <span>{{ $leader ? $leader->name : 'N/A' }}</span>
                                                            @if ($staff->count() > 0)
                                                                <span
                                                                    class="btn btn-xs btn-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm ml-2"
                                                                    style="width: 22px; height: 22px; padding: 0; font-size: 10px; flex-shrink: 0;">
                                                                    <i class="fas fa-plus toggle-icon"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <small
                                                            class="text-muted d-block">{{ $chapterName ?: 'Tanpa Lokasi' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $leader ? $leader->email : '-' }}</td>
                                            <td>
                                                <span class="badge badge-primary shadow-sm">Chapter</span>
                                                <span class="badge bg-white text-dark border shadow-sm ml-1">
                                                    <i class="fas fa-users-cog mr-1"></i>{{ $staff->count() }} Agen
                                                </span>
                                            </td>
                                            <td>
                                                @if ($leader)
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input user-toggle"
                                                            id="userSwitch{{ $leader->id }}"
                                                            data-id="{{ $leader->id }}"
                                                            {{ $leader->is_active ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="userSwitch{{ $leader->id }}">
                                                            {{ $leader->is_active ? 'Aktif' : 'Non-Aktif' }}
                                                        </label>
                                                    </div>
                                                @endif
                                            </td>
                                            @if (!$isOperasional)
                                            <td class="text-center">
                                                @if ($leader)
                                                    <button class="btn btn-sm btn-indigo shadow-sm btn-transfer-db"
                                                        data-id="{{ $leader->id }}" data-name="{{ $leader->name }}">
                                                        <i class="fas fa-exchange-alt mr-1"></i> Transfer
                                                    </button>
                                                @endif
                                            </td>
                                            @endif
                                            <td class="text-right">
                                                @if ($leader)
                                                    <button class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                                        data-target="#editUserModal{{ $leader->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form
                                                        action="{{ route('admin.settings.users.destroy', $leader->id) }}"
                                                        method="POST" class="d-inline delete-form">
                                                        @csrf @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger shadow-sm delete-btn">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Staff / Reseller Rows (Collapsible) --}}
                                        @if ($staff->count() > 0)
                                            <tr id="members-{{ $chapterId }}" class="collapse">
                                                <td colspan="7" class="p-0">
                                                    <table class="table table-sm mb-0 bg-white">
                                                        <tbody style="border-left: 5px solid #4e73df;">
                                                            @foreach ($staff as $u)
                                                                <tr>
                                                                    <td style="width: 50px; padding-left: 30px;"
                                                                        class="text-muted font-weight-bold">
                                                                        @php
                                                                            $resellerIndex = '';
                                                                            $temp = $loop->iteration;
                                                                            while ($temp > 0) {
                                                                                $mod = ($temp - 1) % 26;
                                                                                $resellerIndex =
                                                                                    chr(65 + $mod) . $resellerIndex;
                                                                                $temp = intval(($temp - $mod) / 26);
                                                                            }
                                                                        @endphp
                                                                        {{ $resellerIndex }}.
                                                                    </td>
                                                                    <td style="width: 25%;">
                                                                        <i
                                                                            class="fas fa-level-up-alt fa-rotate-90 text-muted mr-2"></i>
                                                                        {{ $u->name }}
                                                                    </td>
                                                                    <td style="width: 25%;">{{ $u->email }}</td>
                                                                    <td>
                                                                        <span
                                                                            class="badge badge-info shadow-sm">{{ ucfirst($u->role) }}</span>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-switch">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input user-toggle"
                                                                                id="userSwitch{{ $u->id }}"
                                                                                data-id="{{ $u->id }}"
                                                                                {{ $u->is_active ? 'checked' : '' }}>
                                                                            <label class="custom-control-label"
                                                                                for="userSwitch{{ $u->id }}">
                                                                                {{ $u->is_active ? 'Aktif' : 'Non-Aktif' }}
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    @if (!$isOperasional)
                                                                    <td class="text-center">
                                                                        <button
                                                                            class="btn btn-xs btn-outline-indigo btn-transfer-db"
                                                                            data-id="{{ $u->id }}"
                                                                            data-name="{{ $u->name }}">
                                                                            <i class="fas fa-exchange-alt mr-1"></i>
                                                                            Transfer
                                                                        </button>
                                                                    </td>
                                                                    @endif
                                                                    <td class="text-right">
                                                                        <button class="btn btn-sm btn-outline-warning"
                                                                            data-toggle="modal"
                                                                            data-target="#editUserModal{{ $u->id }}">
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                        <form
                                                                            action="{{ route('admin.settings.users.destroy', $u->id) }}"
                                                                            method="POST" class="d-inline delete-form">
                                                                            @csrf @method('DELETE')
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-danger delete-btn">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    </td>
                                                                </tr>

                                                                {{-- Modal Edit (Cabang - Staff) --}}
                                                                @include(
                                                                    'admin.Core.settings.partials.edit_modal',
                                                                    ['u' => $u, 'roles' => $roles]
                                                                )
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        @endif

                                        {{-- Modal Edit (Leader) --}}
                                        @if ($leader)
                                            @include('admin.Core.settings.partials.edit_modal', [
                                                'u' => $leader,
                                                'roles' => $roles,
                                            ])
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                </div>

                {{-- Table Agen Pusat --}}
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold"><i class="fas fa-user-tie mr-2"></i>Agen Pusat</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th style="width: 50px;">No.</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Asal Kota</th>
                                        <th>Status</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($usersAgenPusat as $u)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="font-weight-bold">{{ $u->name }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td>{{ $u->chapter ?: '-' }}</td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input user-toggle"
                                                        id="userSwitch{{ $u->id }}" data-id="{{ $u->id }}"
                                                        {{ $u->is_active ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="userSwitch{{ $u->id }}">
                                                        {{ $u->is_active ? 'Aktif' : 'Non-Aktif' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                <button class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                                    data-target="#editUserModal{{ $u->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.settings.users.destroy', $u->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger shadow-sm delete-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        {{-- Modal Edit (Agen Pusat) --}}
                                        @include('admin.Core.settings.partials.edit_modal', [
                                            'u' => $u,
                                            'roles' => $roles,
                                        ])
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted p-4">Tidak ada data agen pusat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if (!$isOperasional)
            {{-- TAB 2: TARGET OMSET (Administrator only) --}}
            <div class="tab-pane fade p-3 bg-white border border-top-0" id="target" role="tabpanel">
                <form action="{{ route('admin.settings.target.update') }}" method="POST" class="col-md-6">
                    @csrf
                    <div class="form-group">
                        <label class="fw-bold">Target Omset Saat Ini (Rp)</label>
                        <input type="number" name="target_omset" class="form-control" value="{{ $targetOmset }}"
                            required>
                        <small class="text-muted">Target ini akan digunakan untuk perhitungan bonus semua CS secara default
                            kecuali diatur lain.</small>
                    </div>

                    <div class="form-group mt-3">
                        <label class="fw-bold">Target Omset Start-Up Muda Indonesia (Rp)</label>
                        <input type="number" name="target_omset_smi" class="form-control"
                            value="{{ $targetOmsetSmi ?? 0 }}" required>
                        <small class="text-muted">Target khusus untuk Start-Up Muda Indonesia (SMI).</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Target</button>
                </form>
            </div>
            @endif



        </div>
    </div>

    {{-- Add User Modal --}}
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('admin.settings.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control role-select" required>
                                <option value="" disabled selected>-- Pilih Role --</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r }}">
                                        @if ($r == 'cs-mbc')
                                            CS MBC
                                        @elseif($r == 'cs-smi')
                                            CS SMI
                                        @else
                                            {{ ucfirst($r) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control" required>
                                <option value="Pusat" selected>Pusat</option>
                                <option value="Cabang">Cabang</option>
                                <option value="Agen Pusat">Agen Pusat</option>
                            </select>
                        </div>
                        <div class="form-group chapter-field-container" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="mb-0">Pilih Chapter</label>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-add-chapter-toggle"
                                    style="font-size: 0.65rem; padding: 2px 8px;">
                                    <i class="fas fa-plus mr-1"></i>Tambah Chapter
                                </button>
                            </div>

                            <div class="chapter-select-wrapper">
                                <select name="chapter" class="form-control chapter-select" data-current="">
                                    <option value="">-- Pilih / Tulis Chapter --</option>
                                    @foreach ($takenChapters as $chap)
                                        <option value="{{ $chap }}">{{ $chap }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="city-input-wrapper" style="display: none;">
                                <input type="text" class="form-control city-input" placeholder="Tulis Asal Kota...">
                            </div>

                            <div class="chapter-input-wrapper mt-1" style="display: none;">
                                <div class="input-group">
                                    <input type="text" class="form-control new-chapter-input"
                                        placeholder="Tulis Wilayah Chapter Baru...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-sm btn-secondary btn-cancel-new-chapter">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-info mt-1 d-block"><i class="fas fa-info-circle mr-1"></i>Chapter baru
                                    akan otomatis tersimpan saat user disimpan.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan User</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Transfer Database --}}
    <div class="modal fade" id="transferDatabaseModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-exchange-alt mr-2"></i> Transfer Database</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="transferDatabaseForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Peringatan!</strong> Tindakan ini akan
                            memindahkan semua data Calon Peserta dan Peserta M1T dari user sumber ke user tujuan.
                        </div>

                        <input type="hidden" name="from_id" id="transfer_from_id">
                        <div class="form-group">
                            <label class="font-weight-bold">User Sumber:</label>
                            <p id="transfer_from_name" class="form-control-plaintext text-primary font-weight-bold ml-2">
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Transfer ke User Tujuan:</label>
                            <select name="to_id" id="transfer_to_id" class="form-control" required>
                                <option value="">-- Pilih User Tujuan --</option>
                                @foreach ($usersPusat->merge($usersCabang)->sortBy('name') as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitTransfer">
                            <i class="fas fa-check-circle mr-1"></i> Mulai Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // AJAX Toggle Status User
        document.querySelectorAll('.user-toggle').forEach(item => {
            item.addEventListener('change', event => {
                const id = event.target.dataset.id;
                const active = event.target.checked ? 1 : 0;
                const label = event.target.nextElementSibling;

                label.textContent = active ? 'Aktif' : 'Non-Aktif';

                fetch('{{ route('admin.settings.users.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: id,
                            active: active
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            Swal.fire('Gagal!', 'Gagal mengubah status user', 'error');
                            // Revert toggle if failed
                            event.target.checked = !active;
                            label.textContent = !active ? 'Aktif' : 'Non-Aktif';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
                        // Revert toggle if failed
                        event.target.checked = !active;
                        label.textContent = !active ? 'Aktif' : 'Non-Aktif';
                    });
            });
        });



        // SweetAlert Konfirmasi Hapus
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Yakin hapus user ini?',
                text: "Data ini tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });

        function updateChapterFieldVisibility(modal) {
            let role = modal.find('.role-select').val();
            let kategori = modal.find('select[name="kategori"]').val();
            let container = modal.find('.chapter-field-container');
            let chapterSelect = container.find('.chapter-select');
            let currentChapter = chapterSelect.data('current');
            let takenList = @json($takenChapters);
            let label = container.find('label').first();
            let btnAddChapter = container.find('.btn-add-chapter-toggle');

            let roleSupportsChapter = (role === 'chapter' || role === 'reseller' || role === 'agen');

            if (!roleSupportsChapter) {
                container.slideUp();
                container.find('select, input').removeAttr('name').attr('required', false);
            } else if (kategori === 'Agen Pusat') {
                container.slideDown();
                label.text('Asal Kota');
                btnAddChapter.hide();

                container.find('.chapter-select-wrapper').hide();
                container.find('.chapter-input-wrapper').hide();
                container.find('.city-input-wrapper').show();

                container.find('.city-input').attr('name', 'chapter').attr('required', false);
                container.find('.chapter-select').removeAttr('name').attr('required', false);
                container.find('.new-chapter-input').removeAttr('name').attr('required', false);
            } else {
                container.slideDown();
                label.text('Pilih Chapter');
                container.find('.city-input-wrapper').hide().find('.city-input').removeAttr('name').attr('required', false);

                let isNewChapterMode = container.find('.chapter-input-wrapper').is(':visible') && !container.find('.chapter-select-wrapper').is(':visible');
                
                if (modal.is(':hidden') || (!container.find('.chapter-select-wrapper').is(':visible') && !container.find('.chapter-input-wrapper').is(':visible'))) {
                    container.find('.chapter-select-wrapper').show();
                    container.find('.chapter-input-wrapper').hide();
                    btnAddChapter.show();
                    isNewChapterMode = false;
                }

                if (isNewChapterMode) {
                    container.find('.new-chapter-input').attr('name', 'chapter').attr('required', true);
                    container.find('.chapter-select').removeAttr('name').attr('required', false);
                    btnAddChapter.hide();
                } else {
                    container.find('.chapter-select').attr('name', 'chapter').attr('required', true);
                    container.find('.new-chapter-input').removeAttr('name').attr('required', false);
                    btnAddChapter.show();
                }

                chapterSelect.find('option').each(function() {
                    let val = $(this).val();
                    if (!val) return;

                    if (role === 'chapter') {
                        // If role is chapter, show taken warning
                        if (takenList.includes(val) && val !== currentChapter) {
                            $(this).prop('disabled', true).text(val + ' (Sudah Ada Penanggung Jawab)').css(
                                'background-color', '#f8d7da').show();
                        } else {
                            $(this).prop('disabled', false).text(val).css('background-color', '').show();
                        }
                    } else {
                        // If role is reseller or agen, clean view
                        $(this).prop('disabled', false).text(val).css('background-color', '').show();
                    }
                });
            }
        }

        // Toggle Chapter Visibility & Clean/Dirty Labels on role or category change
        $(document).on('change', '.role-select, select[name="kategori"]', function() {
            let modal = $(this).closest('.modal');
            updateChapterFieldVisibility(modal);
        });

        // Initialize display for modals
        $('.modal').on('show.bs.modal', function() {
            updateChapterFieldVisibility($(this));
        });

        // Trigger initial visibility for all modal selects on page load
        $('.modal').each(function() {
            updateChapterFieldVisibility($(this));
        });

        // Toggle Expand/Collapse Icon
        $('.clickable-row').on('click', function() {
            $(this).find('.toggle-icon').toggleClass('fa-plus fa-minus');
            $(this).toggleClass('bg-white bg-light');
        });

        // Transfer Database Logic
        $('.btn-transfer-db').on('click', function(e) {
            e.stopPropagation();
            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#transfer_from_id').val(id);
            $('#transfer_from_name').text(name);

            $('#transfer_to_id option').prop('disabled', false);
            $('#transfer_to_id option[value="' + id + '"]').prop('disabled', true);

            $('#transferDatabaseModal').modal('show');
        });

        $('#transferDatabaseForm').on('submit', function(e) {
            e.preventDefault();

            const toId = $('#transfer_to_id').val();
            const toName = $('#transfer_to_id option:selected').text();

            if (!toId) {
                Swal.fire('Error', 'Silakan pilih user tujuan.', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Transfer',
                text: "Anda yakin ingin memindahkan SEMUA database ke " + toName + "?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Transfer Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memindahkan database, mohon tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('admin.settings.users.transfer') }}",
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON ? xhr.responseJSON.message :
                                'Terjadi kesalahan sistem.';
                            Swal.fire('Gagal!', msg, 'error');
                        }
                    });
                }
            });
        });
        // Toggle New Chapter Input
        $(document).on('click', '.btn-add-chapter-toggle', function() {
            let container = $(this).closest('.chapter-field-container');
            container.find('.chapter-select-wrapper').hide().find('select').removeAttr('name').attr('required',
                false);
            container.find('.chapter-input-wrapper').show().find('input').attr('name', 'chapter').attr('required',
                true).focus();
            $(this).hide();
        });

        $(document).on('click', '.btn-cancel-new-chapter', function() {
            let container = $(this).closest('.chapter-field-container');
            container.find('.chapter-input-wrapper').hide().find('input').removeAttr('name').attr('required', false)
                .val('');
            container.find('.chapter-select-wrapper').show().find('select').attr('name', 'chapter').attr('required',
                true);
            container.find('.btn-add-chapter-toggle').show();
        });
    </script>
@endsection
