@extends('layouts.masteradmin')

@section('content')
    <div class="container-fluid px-4">

        <!-- ===== HEADER ===== -->
        <div class="row mb-3 align-items-start">
            <!-- Kiri: Judul + Filter + Refresh -->
            <div class="col-md-7">
                <h1 class="h3 font-weight-bold text-gray-800 mb-1">
                    <i class="fas fa-clipboard-list text-primary mr-2"></i> Minutes of Meeting (MoM)
                </h1>
                <p class="text-muted small mb-2">Media pencatatan, monitoring hasil rapat, dan tindak lanjut pekerjaan tim CS
                    Yasmin.</p>

                <!-- Filter + Refresh sejajar di bawah subtitle -->
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <label for="filter-status" class="mb-0 text-muted font-weight-bold text-uppercase"
                        style="font-size: 0.68rem; letter-spacing: 0.5px; white-space: nowrap;">Filter Status:</label>
                    <select id="filter-status" class="border rounded px-2 py-1 text-dark font-weight-bold"
                        style="font-size: 0.8rem; outline: none; cursor: pointer; background: #fff; height: 32px; min-width: 130px;">
                        <option value="all">Semua Status</option>
                        <option value="Progress">Progress</option>
                        <option value="Done">Done</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                    <!-- Refresh di sini -->
                    <button id="btn-refresh"
                        class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center"
                        style="height: 32px; width: 32px; padding: 0;" title="Refresh / Reset Filter">
                        <i class="fas fa-sync-alt text-secondary" style="font-size: 0.8rem;"></i>
                    </button>
                </div>
            </div>

            <!-- Kanan: Tombol Tambah saja -->
            <div class="col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0">
                <button id="btn-add-mom" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="height: 38px;">
                    <i class="fas fa-plus mr-1"></i> Tambah MoM
                </button>
            </div>
        </div>

        <!-- ===== TABEL ===== -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" id="mom-table">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th class="py-3 text-uppercase small align-middle" style="width:50px;">No</th>
                                <th class="py-3 text-uppercase small align-middle" style="width:120px;">Tanggal</th>
                                <th class="py-3 text-uppercase small align-middle">Keterangan / Poin</th>
                                <th class="py-3 text-uppercase small align-middle" style="width:120px;">Deadline</th>
                                <th class="py-3 text-uppercase small align-middle" style="width:130px;">PIC</th>
                                <th class="py-3 text-uppercase small align-middle" style="width:170px;">Target</th>
                                <th class="py-3 text-uppercase small align-middle" style="width:170px;">Hasil</th>
                                <th class="py-3 text-uppercase small align-middle" style="width:130px;">Status</th>
                                <th class="py-3 text-uppercase small align-middle" style="width:55px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="mom-table-body" class="bg-white">
                            @forelse($moms as $index => $item)
                                <tr data-id="{{ $item->id }}">

                                    {{-- No --}}
                                    <td class="text-center font-weight-bold text-muted no-col align-middle">
                                        {{ $index + 1 }}</td>

                                    {{-- Tanggal --}}
                                    <td class="p-1 align-middle">
                                        <input type="date" class="form-control-inline text-center live-field"
                                            data-field="tanggal" value="{{ $item->tanggal }}">
                                    </td>

                                    {{-- Keterangan (truncate + edit) --}}
                                    <td class="p-0 align-middle tc-cell" data-label="Keterangan / Poin">
                                        <div class="tc-view">
                                            <div class="tc-text">{{ $item->keterangan ?: '' }}</div>
                                            @if ($item->keterangan)
                                                <button class="btn-lihat"
                                                    onclick="showPopup('Keterangan / Poin', this.closest('td').querySelector('textarea').value)">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>
                                            @endif
                                        </div>
                                        <textarea class="tc-textarea live-field" data-field="keterangan" placeholder="Tulis keterangan/poin rapat...">{{ $item->keterangan }}</textarea>
                                    </td>

                                    {{-- Deadline --}}
                                    <td class="p-1 align-middle">
                                        <input type="date" class="form-control-inline text-center live-field"
                                            data-field="deadline" value="{{ $item->deadline }}">
                                    </td>

                                    {{-- PIC --}}
                                    <td class="p-1 align-middle">
                                        <textarea class="form-control-inline live-field auto-resize" data-field="pic" rows="1" placeholder="Nama PIC ...">{{ $item->pic }}</textarea>
                                    </td>

                                    {{-- Target (truncate + edit) --}}
                                    <td class="p-0 align-middle tc-cell" data-label="Target">
                                        <div class="tc-view">
                                            <div class="tc-text" data-placeholder="Target pekerjaan...">
                                                {{ $item->target ?: '' }}</div>
                                            @if ($item->target)
                                                <button class="btn-lihat"
                                                    onclick="showPopup('Target', this.closest('td').querySelector('textarea').value)">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>
                                            @endif
                                        </div>
                                        <textarea class="tc-textarea live-field" data-field="target" placeholder="Target pekerjaan...">{{ $item->target }}</textarea>
                                    </td>

                                    {{-- Hasil (truncate + edit) --}}
                                    <td class="p-0 align-middle tc-cell" data-label="Hasil">
                                        <div class="tc-view">
                                            <div class="tc-text" data-placeholder="Hasil tindak lanjut...">
                                                {{ $item->hasil ?: '' }}</div>
                                            @if ($item->hasil)
                                                <button class="btn-lihat"
                                                    onclick="showPopup('Hasil', this.closest('td').querySelector('textarea').value)">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>
                                            @endif
                                        </div>
                                        <textarea class="tc-textarea live-field" data-field="hasil" placeholder="Hasil tindak lanjut...">{{ $item->hasil }}</textarea>
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center align-middle p-1">
                                        @php
                                            $sc = 'bg-status-progress';
                                            if ($item->status === 'Done') {
                                                $sc = 'bg-status-done';
                                            } elseif ($item->status === 'Overdue') {
                                                $sc = 'bg-status-overdue';
                                            }
                                        @endphp
                                        <select class="form-control-inline live-field status-select {{ $sc }}"
                                            data-field="status">
                                            <option value="" disabled {{ !$item->status ? 'selected' : '' }}>Pilih
                                                status</option>
                                            <option value="Progress" {{ $item->status === 'Progress' ? 'selected' : '' }}>
                                                Progress</option>
                                            <option value="Done" {{ $item->status === 'Done' ? 'selected' : '' }}>Done
                                            </option>
                                            <option value="Overdue" {{ $item->status === 'Overdue' ? 'selected' : '' }}>
                                                Overdue
                                            </option>
                                        </select>
                                    </td>

                                    {{-- Action --}}
                                    <td class="text-center align-middle p-1">
                                        <button class="btn btn-link text-danger p-0 btn-delete"
                                            data-id="{{ $item->id }}" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>

                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block opacity-30"></i>
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

    </div><!-- /container -->

    <!-- ===== DETAIL POPUP ===== -->
    <div id="mom-popup-overlay" onclick="closePopup(event)">
        <div id="mom-popup-box">
            <div class="mpop-header">
                <span id="mom-popup-title">Detail</span>
                <button class="mpop-close" onclick="closePopup(null)"><i class="fas fa-times"></i></button>
            </div>
            <div class="mpop-body" id="mom-popup-body"></div>
        </div>
    </div>

    <!-- ===== CSS ===== -->
    <style>
        /* ---- General inline control ---- */
        .form-control-inline {
            background: transparent !important;
            border: 1px solid transparent !important;
            width: 100% !important;
            padding: 5px 7px !important;
            font-size: 0.82rem !important;
            color: #333 !important;
            resize: none !important;
            border-radius: 4px !important;
            transition: border-color 0.15s, background 0.15s !important;
            font-family: inherit !important;
        }

        .form-control-inline:hover,
        .form-control-inline:focus {
            background: #f0f4ff !important;
            border-color: rgba(78, 115, 223, 0.45) !important;
            outline: none !important;
            box-shadow: none !important;
        }

        input[type="date"].form-control-inline {
            cursor: pointer;
            text-align: center !important;
        }

        /* ---- Status dropdown ---- */
        .status-select {
            border-radius: 20px !important;
            padding: 3px 10px !important;
            font-size: 0.72rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            text-align: center !important;
            text-align-last: center !important;
            width: 108px !important;
            cursor: pointer !important;
            border: none !important;
            -webkit-appearance: none !important;
            appearance: none !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12) !important;
        }

        .bg-status-progress {
            background: #fff176 !important;
            color: #6d5000 !important;
        }

        .bg-status-done {
            background: #c8f5d8 !important;
            color: #155724 !important;
        }

        .bg-status-overdue {
            background: #ffd6d6 !important;
            color: #721c24 !important;
        }

        .bg-status-none {
            background: #f0f0f0 !important;
            color: #888 !important;
        }

        /* ---- Truncate-cell (keterangan / target / hasil) ---- */
        .tc-cell {
            min-width: 130px;
            max-width: 200px;
            cursor: pointer;
            vertical-align: middle !important;
        }

        /* VIEW MODE */
        .tc-view {
            padding: 5px 8px;
        }

        .tc-text {
            font-size: 0.82rem;
            color: #333;
            line-height: 1.45;
            /* show max 2 lines */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
            min-height: 1.2em;
            /* always has height even when empty */
        }

        /* placeholder text when empty */
        .tc-text:empty::before {
            content: attr(data-placeholder);
            color: #aaa;
            font-style: italic;
            font-size: 0.78rem;
        }

        /* btn lihat */
        .btn-lihat {
            display: inline-block;
            margin-top: 3px;
            font-size: 0.67rem;
            padding: 1px 7px;
            border-radius: 10px;
            font-weight: 700;
            border: 1px solid #4e73df;
            color: #4e73df;
            background: transparent;
            cursor: pointer;
            transition: all 0.18s;
            line-height: 1.6;
        }

        .btn-lihat:hover {
            background: #4e73df;
            color: #fff;
        }

        /* EDIT MODE textarea */
        .tc-textarea {
            display: none;
            /* hidden in view mode */
            width: 100%;
            padding: 5px 8px;
            font-size: 0.82rem;
            font-family: inherit;
            color: #333;
            border: 1px solid #4e73df;
            border-radius: 4px;
            resize: none;
            overflow: hidden;
            outline: none;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.12);
            background: #f0f4ff;
            min-height: 60px;
            transition: all 0.15s;
        }

        /* When cell is in editing mode */
        .tc-cell.editing .tc-view {
            display: none;
        }

        .tc-cell.editing .tc-textarea {
            display: block;
        }

        /* hover highlight on tc-cell */
        .tc-cell:not(.editing):hover .tc-view {
            background: #f5f7ff;
            border-radius: 4px;
        }

        /* delete button */
        .btn-delete {
            transition: transform 0.15s;
        }

        .btn-delete:hover {
            transform: scale(1.2);
            color: #bd2130 !important;
        }

        /* ---- PIC textarea: no scrollbar, auto-height ---- */
        .form-control-inline.auto-resize {
            overflow: hidden !important;
            resize: none !important;
            min-height: unset !important;
        }

        #mom-popup-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
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
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.22);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            animation: mpopIn 0.18s ease;
        }

        @keyframes mpopIn {
            from {
                transform: scale(0.9);
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
            font-size: 0.92rem;
        }

        .mpop-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: #fff;
            border-radius: 50%;
            width: 27px;
            height: 27px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.15s;
        }

        .mpop-close:hover {
            background: rgba(255, 255, 255, 0.38);
        }

        .mpop-body {
            padding: 18px 22px;
            font-size: 0.88rem;
            color: #333;
            line-height: 1.75;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 62vh;
            overflow-y: auto;
        }
    </style>

    <!-- ===== SCRIPT ===== -->
    <script>
        (function($) {
            'use strict';

            const CSRF = "{{ csrf_token() }}";
            const ROUTE_STORE = "{{ route('admin.mom.store') }}";
            const ROUTE_INDEX = "{{ route('admin.mom.index') }}";

            // ─── Helpers ────────────────────────────────────────────────────────────

            function toast(msg, icon = 'success', timer = 900) {
                Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer,
                    timerProgressBar: true
                }).fire({
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
                const real = $('#mom-table-body tr').not('.empty-row,.filtered-empty');
                if (real.length === 0) {
                    $('#mom-table-body').html(`
                <tr class="empty-row">
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-2x d-block mb-2 opacity-30"></i>
                        <strong>Belum Ada Data MoM</strong>
                        <div class="small">Klik "Tambah MoM" untuk menambah data.</div>
                    </td>
                </tr>`);
                }
            }

            function applyFilter() {
                const f = $('#filter-status').val();
                $('.filtered-empty').remove();
                let vis = 0;
                $('#mom-table-body tr').not('.empty-row').each(function() {
                    const tr = $(this);
                    const s = tr.find('[data-field="status"]').val();
                    if (f === 'all' || s === f) {
                        tr.show();
                        vis++;
                    } else tr.hide();
                });
                reindex();
                const total = $('#mom-table-body tr').not('.empty-row,.filtered-empty').length;
                if (vis === 0 && total > 0) {
                    $('#mom-table-body').append(`
                <tr class="filtered-empty">
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="fas fa-filter fa-lg mx-auto d-block mb-2 opacity-30"></i>
                        Tidak ada data dengan status "<strong>${f}</strong>".
                    </td>
                </tr>`);
                }
            }

            // Auto-resize textarea height
            function autoResize(el) {
                if (!el) return;
                el.style.height = 'auto';
                el.style.height = (el.scrollHeight) + 'px';
            }

            // ─── Save Field via AJAX ─────────────────────────────────────────────────

            function saveField(id, field, value, $el) {
                if ($el) $el.css('opacity', '0.6');
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
                            $el.removeClass(
                                'bg-status-progress bg-status-done bg-status-overdue bg-status-none');
                            const map = {
                                Progress: 'bg-status-progress',
                                Done: 'bg-status-done',
                                Overdue: 'bg-status-overdue'
                            };
                            $el.addClass(map[value] || 'bg-status-progress');
                            applyFilter();
                        }
                        toast('Tersimpan', 'success', 700);
                    },
                    error() {
                        if ($el) $el.css('opacity', '');
                        toast('Gagal menyimpan', 'error', 1500);
                    }
                });
            }

            // ─── Build HTML row for new MoM ──────────────────────────────────────────

            function buildRow(item) {
                return `
        <tr data-id="${item.id}">
            <td class="text-center font-weight-bold text-muted no-col align-middle">1</td>
            <td class="p-1 align-middle">
                <input type="date" class="form-control-inline text-center live-field" data-field="tanggal" value="${item.tanggal || ''}">
            </td>
            <td class="p-0 align-middle tc-cell editing" data-label="Keterangan / Poin">
                <div class="tc-view">
                    <div class="tc-text"></div>
                </div>
                <textarea class="tc-textarea live-field" data-field="keterangan" placeholder="Tulis keterangan/poin rapat..."></textarea>
            </td>
            <td class="p-1 align-middle">
                <input type="date" class="form-control-inline text-center live-field" data-field="deadline" value="">
            </td>
            <td class="p-1 align-middle">
                <textarea class="form-control-inline live-field auto-resize" data-field="pic" rows="1" placeholder="PIC..."></textarea>
            </td>
            <td class="p-0 align-middle tc-cell" data-label="Target">
                <div class="tc-view"><div class="tc-text" data-placeholder="Target pekerjaan..."></div></div>
                <textarea class="tc-textarea live-field" data-field="target" placeholder="Target pekerjaan..."></textarea>
            </td>
            <td class="p-0 align-middle tc-cell" data-label="Hasil">
                <div class="tc-view"><div class="tc-text" data-placeholder="Hasil tindak lanjut..."></div></div>
                <textarea class="tc-textarea live-field" data-field="hasil" placeholder="Hasil tindak lanjut..."></textarea>
            </td>
            <td class="text-center align-middle p-1">
                <select class="form-control-inline live-field status-select bg-status-none" data-field="status">
                    <option value="" disabled selected>Pilih status</option>
                    <option value="Progress">Progress</option>
                    <option value="Done">Done</option>
                    <option value="Overdue">Overdue</option>
                </select>
            </td>
            <td class="text-center align-middle p-1">
                <button class="btn btn-link text-danger p-0 btn-delete" data-id="${item.id}" title="Hapus">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;
            }

            // ─── Event: Click on tc-cell → enter edit mode ───────────────────────────

            // Click anywhere on the cell view (not the "Lihat" button) → enter editing
            $(document).on('click', '.tc-cell:not(.editing)', function(e) {
                if ($(e.target).closest('.btn-lihat').length) return; // let Lihat button do its thing
                const $td = $(this);
                $td.addClass('editing');
                const $ta = $td.find('.tc-textarea');
                autoResize($ta[0]);
                $ta.focus();
            });

            // Blur from tc-textarea → exit edit mode, save if changed
            $(document).on('blur', '.tc-textarea.live-field', function() {
                const $ta = $(this);
                const $td = $ta.closest('.tc-cell');
                const $tr = $td.closest('tr');
                const id = $tr.data('id');
                const field = $ta.data('field');
                const val = $ta.val().trim();
                const orig = $ta.data('orig') ?? '';

                // Update the preview text
                const label = $td.data('label') || field;
                const $view = $td.find('.tc-view');
                const $text = $view.find('.tc-text');
                $text.text(val);

                // Manage "Lihat" button
                let $btn = $view.find('.btn-lihat');
                if (val.length > 0) {
                    if ($btn.length === 0) {
                        $btn = $(`<button class="btn-lihat"><i class="fas fa-eye"></i> Lihat</button>`);
                        $view.append($btn);
                    }
                    $btn.attr('onclick',
                        `showPopup('${label}', this.closest('td').querySelector('textarea').value)`);
                } else {
                    $btn.remove();
                }

                // Exit edit mode
                $td.removeClass('editing');

                // Save if changed
                if (val !== orig) {
                    $ta.data('orig', val);
                    saveField(id, field, val, $ta);
                }
            });

            // Store original value on focus
            $(document).on('focus', '.tc-textarea.live-field', function() {
                if ($(this).data('orig') === undefined) {
                    $(this).data('orig', $(this).val().trim());
                }
            });

            // Auto resize while typing inside tc-textarea
            $(document).on('input', '.tc-textarea', function() {
                autoResize(this);
            });

            // ─── Event: Regular live-field (date, pic, status) ────────────────────────

            $(document).on('change', 'input.live-field, select.live-field, textarea.form-control-inline.live-field',
                function() {
                    const $el = $(this);
                    const $tr = $el.closest('tr');
                    const id = $tr.data('id');
                    const field = $el.data('field');
                    if (!id || !field) return;
                    saveField(id, field, $el.val(), $el);
                });

            // ─── Event: Auto-resize for PIC textarea ─────────────────────────────────

            $(document).on('input', '.auto-resize', function() {
                autoResize(this);
            });

            // ─── Button: Tambah MoM ──────────────────────────────────────────────────

            $('#btn-add-mom').on('click', function() {
                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menambah...');
                $.ajax({
                    url: ROUTE_STORE,
                    type: 'POST',
                    data: {
                        _token: CSRF
                    },
                    success(res) {
                        $btn.prop('disabled', false).html(
                            '<i class="fas fa-plus mr-1"></i> Tambah MoM');
                        $('.empty-row').remove();
                        const $row = $(buildRow(res.data));
                        $('#mom-table-body').prepend($row);
                        reindex();
                        applyFilter();
                        // focus keterangan textarea
                        const $ta = $row.find('textarea[data-field="keterangan"]');
                        autoResize($ta[0]);
                        $ta.focus();
                        toast('Data MoM baru berhasil dibuat.', 'success');
                    },
                    error() {
                        $btn.prop('disabled', false).html(
                            '<i class="fas fa-plus mr-1"></i> Tambah MoM');
                        Swal.fire('Gagal', 'Terjadi kesalahan saat membuat MoM baru.', 'error');
                    }
                });
            });

            // ─── Button: Hapus ───────────────────────────────────────────────────────

            $(document).on('click', '.btn-delete', function() {
                const $btn = $(this);
                const $tr = $btn.closest('tr');
                const id = $btn.data('id');
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
                        error() {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus data.',
                                'error');
                        }
                    });
                });
            });

            // ─── Filter & Refresh ────────────────────────────────────────────────────

            $('#filter-status').on('change', applyFilter);

            $('#btn-refresh').on('click', function() {
                const $icon = $(this).find('i');
                $icon.addClass('fa-spin');

                // Reset filter ke "Semua Status"
                $('#filter-status').val('all');

                // Reload data dari server via AJAX (controller return JSON saat AJAX)
                $.ajax({
                    url: ROUTE_INDEX,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $icon.removeClass('fa-spin');
                        if (!res.success) {
                            toast('Gagal memuat ulang data.', 'error', 1500);
                            return;
                        }

                        // Rebuild semua baris dari data JSON
                        const moms = res.data;
                        let html = '';
                        if (!moms || moms.length === 0) {
                            html = `<tr class="empty-row">
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-2x d-block mb-2 opacity-30"></i>
                            <strong>Belum Ada Data MoM</strong>
                            <div class="small">Klik "Tambah MoM" untuk menambah data.</div>
                        </td>
                    </tr>`;
                        } else {
                            const statusClass = {
                                Progress: 'bg-status-progress',
                                Done: 'bg-status-done',
                                Overdue: 'bg-status-overdue'
                            };
                            moms.forEach(function(item, i) {
                                const sc = statusClass[item.status] || 'bg-status-progress';
                                html += `
                        <tr data-id="${item.id}">
                            <td class="text-center font-weight-bold text-muted no-col align-middle">${i + 1}</td>
                            <td class="p-1 align-middle">
                                <input type="date" class="form-control-inline text-center live-field" data-field="tanggal" value="${item.tanggal || ''}">
                            </td>
                            <td class="p-0 align-middle tc-cell" data-label="Keterangan / Poin">
                                <div class="tc-view">
                                    <div class="tc-text">${item.keterangan || ''}</div>
                                    ${item.keterangan ? `<button class="btn-lihat" onclick="showPopup('Keterangan / Poin', this.closest('td').querySelector('textarea').value)"><i class="fas fa-eye"></i> Lihat</button>` : ''}
                                </div>
                                <textarea class="tc-textarea live-field" data-field="keterangan" placeholder="Tulis keterangan/poin rapat...">${item.keterangan || ''}</textarea>
                            </td>
                            <td class="p-1 align-middle">
                                <input type="date" class="form-control-inline text-center live-field" data-field="deadline" value="${item.deadline || ''}">
                            </td>
                            <td class="p-1 align-middle">
                                <textarea class="form-control-inline live-field auto-resize" data-field="pic" rows="1" placeholder="PIC...">${item.pic || ''}</textarea>
                            </td>
                            <td class="p-0 align-middle tc-cell" data-label="Target">
                                <div class="tc-view">
                                    <div class="tc-text" data-placeholder="Target pekerjaan...">${item.target || ''}</div>
                                    ${item.target ? `<button class="btn-lihat" onclick="showPopup('Target', this.closest('td').querySelector('textarea').value)"><i class="fas fa-eye"></i> Lihat</button>` : ''}
                                </div>
                                <textarea class="tc-textarea live-field" data-field="target" placeholder="Target pekerjaan...">${item.target || ''}</textarea>
                            </td>
                            <td class="p-0 align-middle tc-cell" data-label="Hasil">
                                <div class="tc-view">
                                    <div class="tc-text" data-placeholder="Hasil tindak lanjut...">${item.hasil || ''}</div>
                                    ${item.hasil ? `<button class="btn-lihat" onclick="showPopup('Hasil', this.closest('td').querySelector('textarea').value)"><i class="fas fa-eye"></i> Lihat</button>` : ''}
                                </div>
                                <textarea class="tc-textarea live-field" data-field="hasil" placeholder="Hasil tindak lanjut...">${item.hasil || ''}</textarea>
                            </td>
                            <td class="text-center align-middle p-1">
                                <select class="form-control-inline live-field status-select ${sc}" data-field="status">
                                    <option value="" disabled>Pilih status</option>
                                    <option value="Progress" ${item.status === 'Progress' ? 'selected' : ''}>Progress</option>
                                    <option value="Done" ${item.status === 'Done' ? 'selected' : ''}>Done</option>
                                    <option value="Overdue" ${item.status === 'Overdue' ? 'selected' : ''}>Overdue</option>
                                </select>
                            </td>
                            <td class="text-center align-middle p-1">
                                <button class="btn btn-link text-danger p-0 btn-delete" data-id="${item.id}" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>`;
                            });
                        }

                        $('#mom-table-body').html(html);
                        $('.auto-resize').each(function() {
                            autoResize(this);
                        });
                        $('.tc-textarea').each(function() {
                            $(this).data('orig', $(this).val().trim());
                        });
                        applyFilter();
                        toast('Data diperbarui.', 'success', 700);
                    },
                    error: function() {
                        $icon.removeClass('fa-spin');
                        toast('Gagal memuat ulang data.', 'error', 1500);
                    }
                });
            });

            // ─── Init: auto-resize existing textareas on page load ───────────────────

            $(function() {
                $('.auto-resize').each(function() {
                    autoResize(this);
                });
                // init orig data for tc-textarea
                $('.tc-textarea').each(function() {
                    $(this).data('orig', $(this).val().trim());
                });
            });

        })(jQuery);

        // ─── Popup functions (global) ─────────────────────────────────────────────────

        window.showPopup = function(title, text) {
            document.getElementById('mom-popup-title').textContent = title;
            document.getElementById('mom-popup-body').textContent = (text && text.trim()) ? text : '(Tidak ada data)';
            document.getElementById('mom-popup-overlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        window.closePopup = function(e) {
            if (e === null || e.target === document.getElementById('mom-popup-overlay')) {
                document.getElementById('mom-popup-overlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('mom-popup-overlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>
@endsection
