@extends('layouts.masteradmin')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* ======= TABS & AGENDA STYLES ======= */
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
            outline: none !important;
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

        .main-pane {
            display: none;
        }

        /* ======= LIGHT THEME AGENDA ======= */
        .agenda-wrap {
            background: #f8f9fc;
            padding: 10px 0 30px;
            color: #333;
            font-family: 'Segoe UI', sans-serif;
        }

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
            outline: none !important;
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

        /* Done / Checked row decoration */
        tr.done td .judul {
            text-decoration: line-through;
            color: #aaa;
        }

        /* Square Checkbox Styling */
        .check-circle {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            /* Square checklist */
            border: 2px solid #cbd5e1;
            /* slate-300 */
            background: #fff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            font-size: .75rem;
            transition: all .2s;
            outline: none !important;
        }

        .check-circle:hover {
            border-color: #198754;
        }

        .check-circle.checked {
            background: #198754;
            /* Green checkbox, matching Daily Activity design */
            border-color: #198754;
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
            outline: none !important;
        }

        .del-btn:hover {
            background: rgba(239, 68, 68, .08);
            border-color: #fca5a5;
            color: #ef4444;
        }

        .btn-action-icon {
            width: 28px !important;
            height: 28px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.75rem !important;
            border-radius: 6px !important;
        }

        .live-edit-input {
            background: transparent;
            border: 1px solid transparent;
            box-shadow: none;
            width: 100%;
            padding: 4px 6px;
            transition: all 0.15s ease-in-out;
            border-radius: 4px;
            outline: none;
        }

        .live-edit-input:hover {
            background: rgba(0, 0, 0, 0.02);
            border-color: #e3e6f0;
        }

        .live-edit-input:focus {
            background: #fff;
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
        }

        .todo-row.done .live-edit-input {
            text-decoration: line-through;
            color: #adadad;
            opacity: 0.7;
        }

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

        /* ======= REKAP STYLES (CS Supervisor only) ======= */
        .rekap-divisi-card {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 16px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        }

        .rekap-divisi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border-bottom: 1px solid #d1fae5;
        }

        .rekap-divisi-name {
            font-size: 0.95rem;
            font-weight: 800;
            color: #065f46;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rekap-stat-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .rekap-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .rekap-badge-total {
            background: #e0e7ff;
            color: #3730a3;
        }

        .rekap-badge-done {
            background: #d1fae5;
            color: #065f46;
        }

        .rekap-badge-sisa {
            background: #fef3c7;
            color: #92400e;
        }

        .rekap-progress-wrap {
            padding: 8px 20px 14px;
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border-bottom: 1px solid #d1fae5;
        }

        .rekap-progress-track {
            background: #d1fae5;
            border-radius: 30px;
            height: 6px;
            overflow: hidden;
        }

        .rekap-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #059669, #10b981);
            border-radius: 30px;
            transition: width .5s ease;
        }

        .rekap-progress-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #059669;
            text-align: right;
            margin-top: 3px;
        }

        .rekap-task-row {
            display: flex;
            align-items: center;
            padding: 9px 16px;
            border-bottom: 1px solid #f8f9fc;
            gap: 10px;
            font-size: 0.85rem;
            transition: background 0.12s;
        }

        .rekap-task-row:last-child {
            border-bottom: none;
        }

        .rekap-task-row:hover {
            background: #fafbff;
        }

        .rekap-task-row.is-done .rekap-task-judul {
            text-decoration: line-through;
            color: #aaa;
        }

        .rekap-task-check {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 2px solid #cbd5e1;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            color: transparent;
            flex-shrink: 0;
        }

        .rekap-task-check.done {
            background: #059669;
            border-color: #059669;
            color: #fff;
        }

        .rekap-task-judul {
            font-weight: 700;
            color: #1a1a2e;
            flex: 1;
            line-height: 1.3;
        }

        .rekap-task-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .rekap-task-user {
            font-size: 0.72rem;
            color: #888;
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .rekap-tipe-chip {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .rekap-chip-harian {
            background: #ecfdf5;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .rekap-chip-mingguan {
            background: #eff6ff;
            color: #3b82f6;
            border: 1px solid #bfdbfe;
        }

        .rekap-chip-bulanan {
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid #fed7aa;
        }

        .rekap-chip-harian_beda {
            background: #fdf2f8;
            color: #db2777;
            border: 1px solid #fbcfe8;
        }

        .rekap-header-date {
            font-size: 0.8rem;
            font-weight: 700;
            color: #6b7280;
            padding: 10px 0 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rekap-overall-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .rekap-overall-card {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            padding: 14px 16px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .04);
        }

        .rekap-overall-card .val {
            font-size: 1.5rem;
            font-weight: 800;
        }

        .rekap-overall-card .lbl {
            font-size: 0.7rem;
            color: #aaa;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* ======= REKAP NAV BUTTON ======= */
        .rekap-nav-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1.5px solid #e3e6f0;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            color: #4f46e5;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s, transform 0.1s;
            flex-shrink: 0;
        }

        .rekap-nav-btn:hover:not(:disabled) {
            background: #ede9fe;
            border-color: #7c3aed;
            transform: scale(1.08);
        }

        .rekap-nav-btn:disabled,
        .rekap-nav-btn[disabled] {
            opacity: 0.32;
            cursor: not-allowed;
            pointer-events: none;
        }

        .rekap-nav-btn:not(:disabled) {
            opacity: 1;
            cursor: pointer;
            pointer-events: auto;
        }

        /* ======= REKAP USER SECTION (grouped by person) ======= */
        .rekap-user-section {
            margin: 0;
        }

        .rekap-user-header {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 7px 16px;
            background: #f8faff;
            border-bottom: 1px solid #eef1f8;
            font-size: 0.78rem;
            font-weight: 800;
            color: #374151;
            letter-spacing: 0.2px;
        }

        .rekap-user-avatar {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.62rem;
            font-weight: 900;
            color: #fff;
            flex-shrink: 0;
        }

        .rekap-user-task-count {
            margin-left: auto;
            font-size: 0.68rem;
            font-weight: 700;
            color: #9ca3af;
            background: #e5e7eb;
            border-radius: 20px;
            padding: 1px 8px;
        }

        /* Scrollbar styling */
        .rekap-list::-webkit-scrollbar {
            width: 4px;
        }

        .rekap-list::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }

        .rekap-list::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .rekap-list::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>

    <div class="container my-4">
        @if (isset($isCs) && $isCs)
            {{-- ===== 1. UNIFIED MAIN TITLE (TOP) ===== --}}
            <h4 id="mainPageTitle" class="mb-3 text-center text-primary fw-bold"
                style="font-size: 1.5rem; letter-spacing: 0.5px;">📅 DAILY ACTIVITY</h4>

            {{-- ===== 2. MAIN TAB SWITCHER (BELOW TITLE) ===== --}}
            <div class="divisi-tabs justify-content-center" id="mainActivityTabs" style="margin-bottom: 28px;">
                <button class="divisi-tab-btn active" onclick="switchMainTab('pane-activity-cs', this)">
                    <i class="fas fa-clipboard-list"></i> Activity CS
                </button>
<<<<<<< Updated upstream
                @if (auth()->user()->hasHakAkses('finance_access'))
=======
                @if (auth()->user()->hasSubrole('keuangan'))
>>>>>>> Stashed changes
                    <button class="divisi-tab-btn" onclick="switchMainTab('pane-activity-keuangan', this)">
                        <i class="fas fa-wallet"></i> Activity Keuangan
                    </button>
                @endif
<<<<<<< Updated upstream
                @if (auth()->user()->hasAnyHakAkses(['sales_all_view', 'cs_supervisor']))
=======
                @if (auth()->user()->hasAnySubrole(['sales_marketing', 'hrd']))
>>>>>>> Stashed changes
                    <button class="divisi-tab-btn" onclick="switchMainTab('pane-activity-sales', this)">
                        <i class="fas fa-bullhorn"></i> Activity Sales&Marketing
                    </button>
                @endif
<<<<<<< Updated upstream
                @if (!auth()->user()->hasHakAkses('finance_access') && !auth()->user()->hasAnyHakAkses(['sales_all_view', 'cs_supervisor']))
=======
                @if (!auth()->user()->hasSubrole('keuangan') && !auth()->user()->hasAnySubrole(['sales_marketing', 'hrd']))
>>>>>>> Stashed changes
                    <button class="divisi-tab-btn" onclick="switchMainTab('pane-activity-todo', this)">
                        <i class="fas fa-tasks"></i> Activity/ToDoList
                    </button>
                @endif
            </div>

            {{-- ===== 3. TAB PANES (WITHOUT INLINE TITLE HEADERS) ===== --}}

            {{-- Pane 1: Activity CS --}}
            <div class="main-pane" id="pane-activity-cs" style="display: block;">
                @include('admin.CS.dailyactivity.form_content')
            </div>

            {{-- Agenda Panes --}}
<<<<<<< Updated upstream
            @if (auth()->user()->hasHakAkses('finance_access') || auth()->user()->hasAnyHakAkses(['sales_all_view', 'cs_supervisor']))
                @if (auth()->user()->hasHakAkses('finance_access'))
=======
            @if (auth()->user()->hasSubrole('keuangan') || auth()->user()->hasAnySubrole(['sales_marketing', 'hrd']))
                @if (auth()->user()->hasSubrole('keuangan'))
>>>>>>> Stashed changes
                    <div class="main-pane" id="pane-activity-keuangan">
                        @include('admin.CS.dailyactivity.agenda_pane', ['divisi' => 'Divisi Keuangan'])
                    </div>
                @endif
<<<<<<< Updated upstream
                @if (auth()->user()->hasAnyHakAkses(['sales_all_view', 'cs_supervisor']))
=======
                @if (auth()->user()->hasAnySubrole(['sales_marketing', 'hrd']))
>>>>>>> Stashed changes
                    <div class="main-pane" id="pane-activity-sales">
                        @include('admin.CS.dailyactivity.agenda_pane', ['divisi' => 'Sales & Marketing'])
                    </div>
                @endif
            @else
                <div class="main-pane" id="pane-activity-todo">
                    @include('admin.CS.dailyactivity.agenda_pane', ['divisi' => $divisiList[0]])
                </div>
            @endif
        @else
            {{-- Original layout for non-CS roles (marketing, etc) --}}
            <h4 class="mb-3 text-center text-primary">📅 DAILY ACTIVITY</h4>
            @include('admin.CS.dailyactivity.form_content')
        @endif
    </div>

    {{-- ===== MODAL TAMBAH AGENDA ===== --}}
    @if (isset($isCs) && $isCs)
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
                            <div id="modalDivisiLabel" style="font-weight:700;color:#4f46e5;font-size:.9rem;padding:6px 0;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label style="font-size:.8rem;color:#aaa;font-weight:700;margin-bottom:6px;display:block;">JENIS
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
                            <input type="text" id="modalDeskripsi" class="dark-input" placeholder="Catatan tambahan...">
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

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            $('#daily-activity-form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let url = form.attr('action');
                let data = form.serialize();

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Harap tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.post(url, data)
                    .done(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: response.message || 'Data berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    })
                    .fail(function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat menyimpan',
                        });
                    });
            });
        });

        @if (isset($isCs) && $isCs)
            /* ======= MAIN TABS LOGIC (WITH TITLE UPDATE) ======= */
            function switchMainTab(paneId, btn) {
                // Toggle active button class
                $('#mainActivityTabs .divisi-tab-btn').removeClass('active');
                $(btn).addClass('active');

                // Toggle pane visibility
                $('.main-pane').hide();
                $('#' + paneId).show();

                // Update the dynamic title based on the active tab
                let title = '📅 DAILY ACTIVITY';
                if (paneId === 'pane-activity-keuangan') {
                    title = '📅 Activity Keuangan';
                } else if (paneId === 'pane-activity-sales') {
                    title = '📅 Activity Sales & Marketing';
                } else if (paneId === 'pane-activity-todo') {
                    title = '📅 Activity/ToDoList';
                }
                $('#mainPageTitle').text(title);
            }

            /* ======= AGENDA LOGIC (AJAX & ACTIONS) ======= */
            const CSRF = '{{ csrf_token() }}';
            const STORE_URL = '{{ route('agenda.store') }}';
            const TOGGLE_BASE = '{{ url('agenda/toggle') }}';
            const DELETE_BASE = '{{ url('agenda') }}';

            const filters = {};
            const searches = {};

            function setFilter(filter, btn, divisiSlug) {
                filters[divisiSlug] = filter;
                $('#tabGroup-' + divisiSlug + ' .tab-btn').removeClass('active');
                $(btn).addClass('active');
                applyFilter(divisiSlug);
            }

            function doSearch(val, divisiSlug) {
                searches[divisiSlug] = val.toLowerCase();
                applyFilter(divisiSlug);
            }

            function applyFilter(divisiSlug) {
                const filter = filters[divisiSlug] || 'semua';
                const search = searches[divisiSlug] || '';

                $('#panel-' + divisiSlug + ' .periode-card').each(function() {
                    const card = $(this);
                    const tipe = card.data('tipe');
                    const showCard = filter === 'semua' || filter === tipe;
                    let hasVisible = false;

                    card.find('.todo-row').each(function() {
                        const row = $(this);
                        const matchTipe = filter === 'semua' || row.data('tipe') === filter;
                        const matchSearch = !search || (row.data('judul') && row.data('judul').toString()
                            .includes(search));
                        const show = matchTipe && matchSearch;
                        row.css('display', show ? '' : 'none');
                        if (show) hasVisible = true;
                    });
                    card.css('display', showCard ? '' : 'none');
                });
            }

            function showAddModal(divisi) {
                $('#modalDivisi').val(divisi);
                $('#modalDivisiLabel').text(divisi);
                $('#modalJudul').val('');
                $('#modalDeskripsi').val('');
                $('#modalTipe').val('harian');
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

            function slugify(str) {
                return str.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
            }

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
        <td class="text-center font-weight-bold" style="color: #666; font-size: 0.85rem; vertical-align: middle;">${no}</td>
        <td style="vertical-align: middle;">
            <div class="judul font-weight-bold" style="font-size: 0.9rem; color: #333;">${escHtml(res.judul)}</div>
            ${res.deskripsi ? `<div class="deskripsi text-muted" style="font-size: 0.78rem; margin-top: 2px;">${escHtml(res.deskripsi)}</div>` : ''}
        </td>
        <td class="text-center" style="vertical-align: middle;">
            <input type="text" class="live-edit-input live-todo-target font-weight-bold text-center" value="${res.target ? escHtml(res.target) : ''}" data-template-id="${res.template_id}" placeholder="Target...">
        </td>
        <td class="text-center" style="vertical-align: middle;">
            <input type="text" class="live-edit-input live-todo-realisasi font-weight-bold text-center" value="${res.realisasi ? escHtml(res.realisasi) : ''}" data-log-id="${res.log_id}" placeholder="Realisasi...">
        </td>
        <td class="text-center" style="vertical-align: middle;">
            <button class="check-circle" onclick="toggleCheck(${res.log_id}, this, '${divisiSlug}')" title="Tandai selesai">
                <i class="fas fa-check"></i>
            </button>
        </td>
        <td class="text-center" style="vertical-align: middle;">
            <button class="del-btn" onclick="deleteAgenda(${res.template_id}, '${divisiSlug}')" title="Hapus">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>`;
                tbody.appendChild(tr);
            }

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

            /* ======= SUB-TABS (HARI INI / RIWAYAT / REKAP) ======= */
            function switchSubTab(divisiSlug, mode, btn) {
                $('#panel-' + divisiSlug + ' .sub-tab-btn').removeClass('active');
                $(btn).addClass('active');

                if (mode === 'hari-ini') {
                    $('#sub-panel-hari-ini-' + divisiSlug).show();
                    $('#sub-panel-riwayat-' + divisiSlug).hide();
                    $('#sub-panel-rekap-' + divisiSlug).hide();
                } else if (mode === 'riwayat') {
                    $('#sub-panel-hari-ini-' + divisiSlug).hide();
                    $('#sub-panel-riwayat-' + divisiSlug).show();
                    $('#sub-panel-rekap-' + divisiSlug).hide();
                } else if (mode === 'rekap') {
                    $('#sub-panel-hari-ini-' + divisiSlug).hide();
                    $('#sub-panel-riwayat-' + divisiSlug).hide();
                    $('#sub-panel-rekap-' + divisiSlug).show();
                }
            }

            /* ======= REKAP (ALL DIVISIONS – DATE NAVIGATION & NAME FILTER) ======= */
            const REKAP_URL = '{{ route('agenda.rekap') }}';

            // Track current date (as YYYY-MM-DD string) per divisiSlug
            const rekapCurrentDate = {};

            // Helper: get YYYY-MM-DD string in LOCAL time (avoids UTC offset bugs)
            function rekapLocalDateStr(d) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            }

            // Helper: today as YYYY-MM-DD (local time)
            function rekapToday() {
                return rekapLocalDateStr(new Date());
            }

            // Helper: offset baseDate (YYYY-MM-DD) by N days, returns YYYY-MM-DD
            function rekapOffsetDate(baseDate, days) {
                const d = new Date(baseDate + 'T00:00:00');
                d.setDate(d.getDate() + days);
                return rekapLocalDateStr(d);
            }

            function changeRekapDate(divisiSlug, delta) {
                const today = rekapToday();
                const current = rekapCurrentDate[divisiSlug] || today;
                const candidate = rekapOffsetDate(current, delta);

                // Clamp between 2 days ago and today
                const twoAgo = rekapOffsetDate(today, -2);
                if (candidate < twoAgo || candidate > today) return;

                rekapCurrentDate[divisiSlug] = candidate;
                // Reset name dropdown to "Semua Karyawan"
                const srch = document.getElementById('rekap-name-search-' + divisiSlug);
                if (srch) srch.value = '';
                loadRekap(divisiSlug, candidate);
            }

            function loadRekap(divisiSlug, dateStr) {
                const container = document.getElementById('rekap-list-' + divisiSlug);
                if (!container) return;

                const today = rekapToday();
                if (!dateStr) dateStr = rekapCurrentDate[divisiSlug] || today;
                rekapCurrentDate[divisiSlug] = dateStr;

                container.innerHTML =
                    '<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat rekap semua divisi...</div>';

                $.ajax({
                    url: REKAP_URL,
                    type: 'GET',
                    data: {
                        date: dateStr
                    },
                    success: res => {
                        if (!res.success) {
                            container.innerHTML =
                                '<div class="text-center py-4 text-danger"><i class="fas fa-lock mr-1"></i> Tidak punya akses.</div>';
                            return;
                        }

                        // --- Update date navigation bar ---
                        const labelEl = document.getElementById('rekap-date-label-' + divisiSlug);
                        const subEl = document.getElementById('rekap-date-sub-' + divisiSlug);
                        const prevBtn = document.getElementById('rekap-btn-prev-' + divisiSlug);
                        const nextBtn = document.getElementById('rekap-btn-next-' + divisiSlug);

                        if (labelEl) labelEl.textContent = res.date_label || 'Hari Ini';
                        if (subEl) subEl.textContent = res.date || '';

                        if (prevBtn) {
                            const canPrev = res.can_prev;
                            prevBtn.disabled = !canPrev;
                        }
                        if (nextBtn) {
                            const canNext = res.can_next;
                            nextBtn.disabled = !canNext;
                        }

                        if (!res.data || res.data.length === 0) {
                            container.innerHTML =
                                '<div class="text-center py-5 text-muted"><i class="fas fa-chart-pie" style="font-size:2rem;display:block;margin-bottom:8px;"></i>Belum ada data rekap untuk tanggal ini.</div>';
                            return;
                        }

                        // Overall stats
                        const overallTotal = res.data.reduce((s, d) => s + d.total, 0);
                        const overallDone = res.data.reduce((s, d) => s + d.done, 0);
                        const overallPersen = overallTotal > 0 ? Math.round((overallDone / overallTotal) *
                            100) : 0;

                        let html = `
                    <div class="rekap-overall-bar" style="margin-bottom:12px;">
                        <div class="rekap-overall-card">
                            <div class="val" style="color:#3730a3;">${overallTotal}</div>
                            <div class="lbl">Total Tugas</div>
                        </div>
                        <div class="rekap-overall-card">
                            <div class="val" style="color:#059669;">${overallDone}</div>
                            <div class="lbl">Selesai</div>
                        </div>
                        <div class="rekap-overall-card">
                            <div class="val" style="color:#4f46e5;">${overallPersen}%</div>
                            <div class="lbl">Progress</div>
                        </div>
                    </div>
                `;

                        res.data.forEach(divisiGroup => {
                            const divisi = divisiGroup.divisi;
                            const total = divisiGroup.total;
                            const done = divisiGroup.done;
                            const tersisa = divisiGroup.tersisa;
                            const persen = divisiGroup.persen;

                            let divisiIcon = 'fa-layer-group';
                            if (divisi.includes('Keuangan')) divisiIcon = 'fa-wallet';
                            else if (divisi.includes('Sales') || divisi.includes('Marketing'))
                                divisiIcon = 'fa-bullhorn';
                            else if (divisi.includes('Produksi')) divisiIcon = 'fa-tools';
                            else if (divisi.includes('Web')) divisiIcon = 'fa-code';
                            else if (divisi.includes('Operasional')) divisiIcon = 'fa-cogs';
                            else if (divisi.includes('Advertis')) divisiIcon = 'fa-ad';

                            const divisiId = 'rekap-div-' + divisiSlug + '-' + divisi.replace(
                                /[^a-zA-Z0-9]/g, '_');

                            html += `
                        <div class="rekap-divisi-card" id="${divisiId}">
                            <div class="rekap-divisi-header">
                                <div class="rekap-divisi-name">
                                    <i class="fas ${divisiIcon}"></i>
                                    ${escHtml(divisi)}
                                </div>
                                <div class="rekap-stat-badges">
                                    <span class="rekap-badge rekap-badge-total">${total} Tugas</span>
                                    <span class="rekap-badge rekap-badge-done"><i class="fas fa-check mr-1"></i>${done} Selesai</span>
                                    ${tersisa > 0 ? `<span class="rekap-badge rekap-badge-sisa">${tersisa} Sisa</span>` : ''}
                                </div>
                            </div>
                            <div class="rekap-progress-wrap">
                                <div class="rekap-progress-track">
                                    <div class="rekap-progress-fill" style="width: ${persen}%"></div>
                                </div>
                                <div class="rekap-progress-label">${persen}%</div>
                            </div>
                            <div class="rekap-task-list">
                    `;

                            if (divisiGroup.tasks.length === 0) {
                                html +=
                                    `<div class="text-center py-3 text-muted" style="font-size:0.82rem;"><i class="fas fa-inbox mr-1"></i> Tidak ada tugas.</div>`;
                            } else {
                                // ---- Group tasks by user ----
                                const userGroups = {};
                                divisiGroup.tasks.forEach(task => {
                                    const u = task.user || 'Unknown';
                                    if (!userGroups[u]) userGroups[u] = [];
                                    userGroups[u].push(task);
                                });

                                Object.keys(userGroups).forEach(userName => {
                                    const userTasks = userGroups[userName];
                                    const userDone = userTasks.filter(t => t.is_done).length;
                                    const initials = userName.split(' ').map(w => w[0]).slice(0,
                                        2).join('').toUpperCase();
                                    const userLower = userName.toLowerCase();

                                    html += `
                                <div class="rekap-user-section" data-user-section="${escHtml(userLower)}">
                                    <div class="rekap-user-header">
                                        <span class="rekap-user-avatar">${escHtml(initials)}</span>
                                        ${escHtml(userName)}
                                        <span class="rekap-user-task-count">${userDone}/${userTasks.length}</span>
                                    </div>
                                    <div class="table-responsive">
                                    <table class="table table-bordered mb-0 table-sm align-middle" style="font-size:0.82rem;">
                                        <thead style="background:linear-gradient(135deg,#4f46e5,#6366f1); color:#fff;">
                                            <tr>
                                                <th style="width:38px; text-align:center; padding:6px 8px;">No</th>
                                                <th style="padding:6px 10px;">Deskripsi Task</th>
                                                <th style="width:90px; text-align:center; padding:6px 8px;">Tipe</th>
                                                <th style="width:90px; text-align:center; padding:6px 8px;">Target</th>
                                                <th style="width:90px; text-align:center; padding:6px 8px;">Realisasi</th>
                                                <th style="width:80px; text-align:center; padding:6px 8px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                                    userTasks.forEach((task, idx) => {
                                        const isDone = task.is_done;
                                        const chipClass = `rekap-chip-${task.tipe}`;
                                        const rowBg = isDone ? 'background:#f0fdf4;' :
                                            '';
                                        const titleStyle = isDone ?
                                            'text-decoration:line-through; color:#aaa;' :
                                            'color:#1a1a2e; font-weight:700;';
                                        const statusBadge = isDone ?
                                            `<span style="font-size:0.68rem;font-weight:700;padding:2px 8px;border-radius:20px;background:#d1fae5;color:#065f46;"><i class="fas fa-check mr-1"></i>Selesai</span>` :
                                            `<span style="font-size:0.68rem;font-weight:700;padding:2px 8px;border-radius:20px;background:#fef3c7;color:#92400e;">Belum</span>`;
                                        const deadlineHtml = task.deadline ?
                                            `<div style="font-size:0.68rem;color:#ef4444;margin-top:2px;"><i class="fas fa-clock mr-1"></i>${escHtml(task.deadline)}</div>` :
                                            '';

                                        html += `
                                            <tr style="${rowBg}">
                                                <td class="text-center font-weight-bold text-muted" style="padding:7px 8px;">${idx + 1}</td>
                                                <td style="padding:7px 10px;">
                                                    <div style="${titleStyle}">${escHtml(task.judul)}</div>
                                                    ${task.deskripsi ? `<div style="font-size:0.72rem;color:#999;margin-top:1px;">${escHtml(task.deskripsi)}</div>` : ''}
                                                    ${deadlineHtml}
                                                </td>
                                                <td class="text-center" style="padding:7px 8px;">
                                                    <span class="rekap-tipe-chip ${chipClass}">${escHtml(task.tipe_label)}</span>
                                                </td>
                                                <td class="text-center font-weight-bold" style="padding:7px 8px; color:#374151;">${task.target ? escHtml(task.target) : '<span style="color:#ccc;">—</span>'}</td>
                                                <td class="text-center font-weight-bold" style="padding:7px 8px; color:#059669;">${task.realisasi ? escHtml(task.realisasi) : '<span style="color:#ccc;">—</span>'}</td>
                                                <td class="text-center" style="padding:7px 8px;">${statusBadge}</td>
                                            </tr>
                                        `;
                                    });

                                    html += `
                                        </tbody>
                                    </table>
                                    </div>
                                </div>`; // close rekap-user-section
                                });
                            }

                            html += `</div></div>`; // close rekap-task-list + rekap-divisi-card
                        });

                        container.innerHTML = html;

                        // Populate name dropdown from loaded data
                        const nameSelect = document.getElementById('rekap-name-search-' + divisiSlug);
                        if (nameSelect) {
                            // Collect all unique user names
                            const allNames = new Set();
                            res.data.forEach(divisiGroup => {
                                divisiGroup.tasks.forEach(task => {
                                    if (task.user) allNames.add(task.user);
                                });
                            });
                            // Keep current selection if it still exists
                            const prevVal = nameSelect.value;
                            nameSelect.innerHTML = '<option value="">— Semua Karyawan —</option>';
                            [...allNames].sort().forEach(name => {
                                const opt = document.createElement('option');
                                opt.value = name.toLowerCase();
                                opt.textContent = name;
                                if (name.toLowerCase() === prevVal) opt.selected = true;
                                nameSelect.appendChild(opt);
                            });
                            // Re-apply filter if a name was selected
                            if (nameSelect.value) {
                                filterRekapByName(nameSelect.value, divisiSlug);
                            }
                        }
                    },
                    error: () => {
                        container.innerHTML =
                            '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat rekap.</div>';
                    }
                });
            }

            /* ======= NAME FILTER for Rekap (filters by user-section) ======= */
            function filterRekapByName(value, divisiSlug) {
                const q = (value || '').toLowerCase().trim();
                const container = document.getElementById('rekap-list-' + divisiSlug);
                if (!container) return;

                // Show/hide user-sections
                container.querySelectorAll('.rekap-user-section').forEach(section => {
                    const sectionUser = (section.dataset.userSection || '').toLowerCase();
                    // dropdown value is exact (lowercase name), so exact match; fallback to contains for safety
                    const show = !q || sectionUser === q || sectionUser.includes(q);
                    section.style.display = show ? '' : 'none';
                });

                // Show/hide division cards when all user-sections are hidden
                container.querySelectorAll('.rekap-divisi-card').forEach(card => {
                    const taskList = card.querySelector('.rekap-task-list');
                    if (!taskList) {
                        card.style.display = '';
                        return;
                    }
                    const sections = taskList.querySelectorAll('.rekap-user-section');
                    if (sections.length === 0) {
                        card.style.display = '';
                        return;
                    }
                    const hasVisible = Array.from(sections).some(s => s.style.display !== 'none');
                    card.style.display = hasVisible ? '' : 'none';
                });
            }

            let isAddingDailyTask = false;

            function addDailyTaskLive(divisi, divisiSlug) {
                if (isAddingDailyTask) return;
                isAddingDailyTask = true;

                $.post('{{ route('agenda.daily-task.store') }}', {
                        _token: CSRF,
                        divisi: divisi
                    })
                    .done(res => {
                        if (res.success) {
                            const tbody = document.getElementById('tbody-' + divisiSlug + '-harian_beda');
                            const newRowHtml = renderTaskRowHtml(res.task, divisiSlug);
                            const newRow = document.createElement('tr');
                            newRow.className = 'todo-row';
                            newRow.id = 'daily-row-' + res.task.id;
                            newRow.dataset.divisi = divisiSlug;
                            newRow.innerHTML = newRowHtml;

                            tbody.appendChild(newRow);
                            reindexRows(divisiSlug);

                            setTimeout(() => {
                                $(`#daily-row-${res.task.id} .live-task-judul`).focus();
                            }, 100);

                            showToast('Tugas harian baru ditambahkan.');
                        } else {
                            showToast('Gagal menambahkan tugas harian.');
                        }
                    })
                    .fail(() => {
                        showToast('Terjadi kesalahan, coba lagi.');
                    })
                    .always(() => {
                        isAddingDailyTask = false;
                    });
            }



            // Save existing tasks when blurred and changed
            $(document).on('focus', '.live-edit-input, .live-todo-target, .live-todo-realisasi', function() {
                $(this).data('prev-val', $(this).val());
            });

            $(document).on('change', 'input[type="date"].live-edit-input', function() {
                const prevVal = $(this).data('prev-val');
                const currVal = $(this).val();
                if (prevVal !== undefined && prevVal === currVal) {
                    return;
                }
                $(this).data('prev-val', currVal);
                const taskId = $(this).data('task-id');
                if (taskId) {
                    saveTaskLive(taskId, $(this).closest('tr'));
                }
            });

            $(document).on('blur', '.live-edit-input', function() {
                const prevVal = $(this).data('prev-val');
                const currVal = $(this).val();
                if (prevVal !== undefined && prevVal === currVal) {
                    return;
                }
                const taskId = $(this).data('task-id');
                if (taskId) {
                    saveTaskLive(taskId, $(this).closest('tr'));
                }
            });

            $(document).on('blur', '.live-todo-target', function() {
                const prevVal = $(this).data('prev-val');
                const currVal = $(this).val();
                if (prevVal !== undefined && prevVal === currVal) {
                    return;
                }
                const templateId = $(this).data('template-id');
                const target = currVal;

                $.ajax({
                    url: '{{ url('agenda/todo-template') }}/' + templateId + '/target',
                    type: 'PUT',
                    data: {
                        _token: CSRF,
                        target: target
                    },
                    success: res => {
                        if (res.success) {
                            showToast('Target disimpan.');
                        }
                    }
                });
            });

            $(document).on('blur', '.live-todo-realisasi', function() {
                const prevVal = $(this).data('prev-val');
                const currVal = $(this).val();
                if (prevVal !== undefined && prevVal === currVal) {
                    return;
                }
                const logId = $(this).data('log-id');
                const realisasi = currVal;

                if (!logId) return;

                $.ajax({
                    url: '{{ url('agenda/todo-log') }}/' + logId + '/realisasi',
                    type: 'PUT',
                    data: {
                        _token: CSRF,
                        realisasi: realisasi
                    },
                    success: res => {
                        if (res.success) {
                            showToast('Realisasi disimpan.');
                        }
                    }
                });
            });

            $(document).on('keypress', '.live-edit-input, .live-todo-target, .live-todo-realisasi', function(e) {
                if (e.key === 'Enter') {
                    $(this).blur();
                }
            });

            function saveTaskLive(taskId, rowEl) {
                const judul = rowEl.find('.live-task-judul').val()?.trim() || '';
                const deskripsi = rowEl.find('.live-task-deskripsi').val()?.trim() || '';
                const target = rowEl.find('.live-task-target').val()?.trim() || '';
                const realisasi = rowEl.find('.live-task-realisasi').val()?.trim() || '';
                const deadline = rowEl.find('.live-task-deadline').val()?.trim() || '';

                $.ajax({
                    url: '{{ url('agenda/daily-task') }}/' + taskId,
                    type: 'PUT',
                    data: {
                        _token: CSRF,
                        judul: judul,
                        deskripsi: deskripsi,
                        target: target,
                        realisasi: realisasi,
                        deadline: deadline
                    },
                    success: res => {
                        if (res.success) {
                            showToast('Perubahan disimpan.');
                        }
                    }
                });
            }

            function toggleDailyTaskCheck(taskId, btn, divisiSlug) {
                $.ajax({
                    url: '{{ url('agenda/daily-task') }}/' + taskId + '/done',
                    type: 'PATCH',
                    data: {
                        _token: CSRF
                    },
                    success: res => {
                        if (res.success) {
                            const row = btn.closest('tr');
                            const timeCell = row.querySelector('.done-time');
                            if (res.is_done) {
                                btn.classList.add('checked');
                                row.classList.add('done');
                                btn.title = 'Tandai belum selesai';
                                if (timeCell) timeCell.textContent = res.done_at;
                                showToast('Tugas selesai & diarsipkan!');
                            } else {
                                btn.classList.remove('checked');
                                row.classList.remove('done');
                                btn.title = 'Tandai selesai';
                                if (timeCell) timeCell.textContent = '-';
                            }
                        }
                    }
                });
            }

            function deleteDailyTask(taskId, divisiSlug) {
                Swal.fire({
                    title: 'Hapus tugas ini?',
                    text: 'Tugas manual akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: '{{ url('agenda/daily-task') }}/' + taskId,
                            type: 'DELETE',
                            data: {
                                _token: CSRF
                            },
                            success: res => {
                                if (res.success) {
                                    const row = document.getElementById('daily-row-' + taskId);
                                    if (row) {
                                        row.style.transition = 'opacity .3s';
                                        row.style.opacity = '0';
                                        setTimeout(() => {
                                            row.remove();
                                            reindexRows(divisiSlug);
                                        }, 300);
                                    }
                                    showToast('Tugas manual berhasil dihapus.');
                                }
                            }
                        });
                    }
                });
            }

            function reindexRows(divisiSlug) {
                const tbody = document.getElementById('tbody-' + divisiSlug + '-harian_beda');
                tbody.querySelectorAll('.todo-row').forEach((r, idx) => {
                    const noCol = r.querySelector('.col-no');
                    if (noCol) noCol.textContent = idx + 1;
                });
            }

            function renderTaskRowHtml(task, divisiSlug) {
                return `
            <td class="text-center font-weight-bold col-no" style="color: #666; font-size: 0.85rem; vertical-align: middle;"></td>
            <td class="col-task-content" style="vertical-align: middle;">
                <input type="text" class="live-edit-input live-task-judul font-weight-bold" value="${task.judul ? escHtml(task.judul) : ''}" data-task-id="${task.id}" placeholder="Nama tugas..." style="font-size: 0.9rem; color: #333;">
                <input type="text" class="live-edit-input live-task-deskripsi text-muted small mt-1" value="${task.deskripsi ? escHtml(task.deskripsi) : ''}" data-task-id="${task.id}" placeholder="Catatan tambahan (optional)..." style="font-size: 0.78rem;">
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <input type="text" class="live-edit-input live-task-target font-weight-bold text-center" value="${task.target ? escHtml(task.target) : ''}" data-task-id="${task.id}" placeholder="Target...">
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <input type="text" class="live-edit-input live-task-realisasi font-weight-bold text-center" value="${task.realisasi ? escHtml(task.realisasi) : ''}" data-task-id="${task.id}" placeholder="Realisasi...">
            </td>
            <td class="col-task-deadline text-center" style="vertical-align: middle;">
                <input type="date" class="live-edit-input live-task-deadline text-center text-danger font-weight-bold" value="${task.deadline ? escHtml(task.deadline) : ''}" data-task-id="${task.id}">
            </td>
            <td class="text-center col-check" style="vertical-align: middle;">
                <button class="check-circle ${task.is_done ? 'checked' : ''}" onclick="toggleDailyTaskCheck(${task.id}, this, '${divisiSlug}')" title="${task.is_done ? 'Tandai belum selesai' : 'Tandai selesai'}">
                    <i class="fas fa-check"></i>
                </button>
            </td>
            <td class="text-center col-action" style="vertical-align: middle;">
                <button class="del-btn btn-sm btn-action-icon" onclick="deleteDailyTask(${task.id}, '${divisiSlug}')" title="Hapus">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
            }

            function formatTime(dateTimeStr) {
                if (!dateTimeStr) return '-';
                if (dateTimeStr.length === 5 && dateTimeStr.includes(':')) return dateTimeStr;
                try {
                    const d = new Date(dateTimeStr);
                    const h = String(d.getHours()).padStart(2, '0');
                    const m = String(d.getMinutes()).padStart(2, '0');
                    return `${h}:${m}`;
                } catch (e) {
                    return '-';
                }
            }

            /* ======= RIWAYAT (HISTORY) LOGIC ======= */
            function filterRiwayat(divisi, divisiSlug) {
                loadRiwayat(divisi, divisiSlug);
            }

            function resetRiwayatFilters(divisi, divisiSlug) {
                document.getElementById('history-search-' + divisiSlug).value = '';
                const dariEl = document.getElementById('history-dari-' + divisiSlug);
                const sampaiEl = document.getElementById('history-sampai-' + divisiSlug);
                if (dariEl) dariEl.value = '';
                if (sampaiEl) sampaiEl.value = '';
                loadRiwayat(divisi, divisiSlug);
            }

            function loadRiwayat(divisi, divisiSlug) {
                const q = document.getElementById('history-search-' + divisiSlug).value.trim();
                const dariEl = document.getElementById('history-dari-' + divisiSlug);
                const sampaiEl = document.getElementById('history-sampai-' + divisiSlug);
                const dari = dariEl ? dariEl.value : '';
                const sampai = sampaiEl ? sampaiEl.value : '';

                const container = document.getElementById('history-list-' + divisiSlug);
                container.innerHTML =
                    '<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat riwayat...</div>';

                $.ajax({
                    url: '{{ route('agenda.riwayat') }}',
                    type: 'GET',
                    data: {
                        divisi: divisi,
                        q: q,
                        dari: dari,
                        sampai: sampai
                    },
                    success: res => {
                        if (!res.success || !res.data || res.data.length === 0) {
                            container.innerHTML = `
                        <div class="empty-state py-5 text-center text-muted">
                            <i class="fas fa-history" style="font-size:2rem;margin-bottom:8px;display:block;"></i>
                            Tidak ada riwayat ditemukan.
                        </div>
                    `;
                            return;
                        }

                        let html = '';
                        res.data.forEach(group => {
                            html += `
                        <div class="history-group mb-4">
                            <!-- Group Header: Date and Count -->
                            <div class="d-flex align-items-center justify-content-between mb-2 px-1" style="font-size: 0.8rem; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;">
                                <span class="text-secondary"><i class="far fa-calendar-alt mr-1"></i> ${group.date_label}</span>
                                <span class="flex-grow-1 mx-3" style="border-bottom: 2px solid #e2e8f0; height: 1px;"></span>
                                <span style="color: #4f46e5; font-weight: 800;">${group.items.length} SELESAI</span>
                            </div>
                            
                            <!-- Items List -->
                            <div class="bg-white shadow-sm border border-light" style="border-radius: 12px; overflow: hidden;">
                    `;

                            group.items.forEach((item, idx) => {
                                const timeStr = item.done_at ? new Date(item.done_at)
                                    .toLocaleTimeString('id-ID', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) : '-';

                                let badgeStyle = '';
                                let badgeLabel = '';

                                if (item.source === 'daily') {
                                    badgeStyle =
                                        'background-color: #fdf2f8; color: #db2777; border: 1px solid #fce7f3;';
                                    badgeLabel = 'Harian Beda';
                                } else {
                                    const tipe = String(item.tipe).toLowerCase();
                                    if (tipe === 'harian') {
                                        badgeStyle =
                                            'background-color: #e6fffa; color: #0d9488; border: 1px solid #ccfbf1;';
                                        badgeLabel = 'Harian';
                                    } else if (tipe === 'mingguan') {
                                        badgeStyle =
                                            'background-color: #fffbeb; color: #d97706; border: 1px solid #fef3c7;';
                                        badgeLabel = 'Mingguan';
                                    } else if (tipe === 'bulanan') {
                                        badgeStyle =
                                            'background-color: #f5f3ff; color: #7c3aed; border: 1px solid #ede9fe;';
                                        badgeLabel = 'Bulanan';
                                    } else {
                                        badgeStyle =
                                            'background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb;';
                                        badgeLabel = item.tipe;
                                    }
                                }

                                const sourceChip =
                                    `<span class="badge px-2 py-1 mr-2" style="font-size: 0.72rem; border-radius: 4px; ${badgeStyle}">${badgeLabel}</span>`;
                                const highlightedJudul = highlightText(item.judul, q);
                                const highlightedDeskripsi = item.deskripsi ? highlightText(item
                                    .deskripsi, q) : '';

                                const isLast = (idx === group.items.length - 1);
                                const borderStyle = isLast ? 'border-bottom: none !important;' :
                                    'border-bottom: 1px solid #f1f3f9;';

                                html += `
                            <div class="d-flex align-items-center justify-content-between px-3 py-3" style="font-size: 0.88rem; transition: background 0.15s; ${borderStyle} text-align: left;">
                                <!-- Left Section: Badge + Title/Desc -->
                                <div class="d-flex align-items-center flex-grow-1 min-width-0">
                                    ${sourceChip}
                                    <div class="min-width-0 text-left" style="text-align: left;">
                                        <span class="font-weight-bold text-dark text-truncate d-block" style="font-size: 0.88rem; text-align: left;">${highlightedJudul}</span>
                                        ${highlightedDeskripsi ? `<span class="text-muted small text-truncate d-block" style="font-size: 0.76rem; margin-top: 1px; text-align: left;">${highlightedDeskripsi}</span>` : ''}
                                    </div>
                                </div>
                                <!-- Right Section: Completion Time -->
                                <div class="text-end text-success font-weight-bold shrink-0 ml-3" style="font-size: 0.82rem; white-space: nowrap;">
                                    <i class="fas fa-check mr-1" style="font-size: 0.78rem;"></i> ${timeStr}
                                </div>
                            </div>
                        `;
                            });

                            html += `
                            </div>
                        </div>
                    `;
                        });

                        container.innerHTML = html;
                    },
                    error: () => {
                        container.innerHTML = `
                    <div class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat riwayat. Silakan coba lagi.
                    </div>
                `;
                    }
                });
            }

            function highlightText(text, keyword) {
                if (!keyword || !text) return escHtml(text);
                const escapedKeyword = escapeRegExp(keyword);
                const regex = new RegExp(`(${escapedKeyword})`, 'gi');
                return escHtml(text).replace(regex, '<mark class="p-0 bg-warning text-dark">$1</mark>');
            }

            function escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }

            function deleteAgenda(templateId, divisiSlug) {
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
                $('#panel-' + divisiSlug + ' .periode-card').each(function() {
                    $(this).find('.todo-row').each(function(i) {
                        $(this).find('.col-no').text(i + 1);
                    });
                });
            }

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
            }

            function showToast(msg) {
                $('.agenda-toast').remove();
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
        @endif
    </script>
@endpush
