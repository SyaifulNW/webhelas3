@extends('layouts.masteradmin')

@section('content')
    @if (strtolower(auth()->user()->role) === 'administrator')
        <style>
            /* Hide all "Tambah" buttons */
            #btnTambahInventarisInline,
            #btnTambahPerbaikanInline,
            #btnTambahPengadaanInline,
            .delete-inventaris-btn,
            .delete-btn,
            .delete-pengadaan-btn,
            .fa-trash-alt,
            .fa-upload,
            a[onclick*="openUploadModal"],
            button[onclick*="openUploadModal"] {
                display: none !important;
            }

            /* Hide the empty action column (last th/td) in all 3 tables */
            .table-monitoring thead tr th:last-child,
            .table-monitoring tbody tr td:last-child,
            .table-monitoring tfoot tr td:last-child {
                display: none !important;
            }

            /* Style disabled fields so they don't look disabled, but are clean read-only */
            .table-monitoring tbody select:disabled,
            .table-monitoring tbody textarea:disabled,
            .table-monitoring tbody input:disabled {
                background-color: transparent !important;
                border: none !important;
                color: #333 !important;
                cursor: default !important;
                resize: none !important;
                appearance: none !important;
                /* Hide select dropdown arrow if disabled */
                -webkit-appearance: none !important;
                -moz-appearance: none !important;
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Disable all inputs, textareas, selects inside table bodies ONLY (keep header filters active!)
                function disableAllFields() {
                    document.querySelectorAll(
                        '.table-monitoring tbody input, .table-monitoring tbody textarea, .table-monitoring tbody select'
                    ).forEach(function(el) {
                        el.disabled = true;
                    });
                    // Hide any upload or delete elements
                    document.querySelectorAll(
                        '.delete-inventaris-btn, .delete-btn, .delete-pengadaan-btn, a[onclick*="openUploadModal"], button[onclick*="openUploadModal"]'
                    ).forEach(function(el) {
                        el.style.setProperty('display', 'none', 'important');
                    });
                }

                // Initial call
                disableAllFields();

                // MutationObserver to watch for pagination or dynamic row redraws
                const observer = new MutationObserver(function(mutations) {
                    disableAllFields();
                });

                document.querySelectorAll('.table-monitoring').forEach(function(table) {
                    observer.observe(table, {
                        childList: true,
                        subtree: true
                    });
                });
            });
        </script>
    @endif

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --premium-blue: #4e73df;
            --premium-success: #1cc88a;
            --premium-warning: #f6c23e;
            --premium-dark: #2c3e50;
            --glass-bg: rgba(255, 255, 255, 0.9);
        }

        .dashboard-container {
            padding: 2rem;
            background: #f8f9fc;
            min-height: 100vh;
        }

        .welcome-card {
            background: linear-gradient(135deg, var(--premium-blue) 0%, #224abe 100%);
            color: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 10px 30px rgba(78, 115, 223, 0.2);
            position: relative;
            overflow: hidden;
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .nav-pills-premium {
            background: #fff;
            padding: 0.5rem;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: inline-flex;
            margin-bottom: 2rem;
        }

        .nav-pills-premium .nav-link {
            border-radius: 50px;
            padding: 0.8rem 2rem;
            color: #5a5c69;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .nav-pills-premium .nav-link.active {
            background: var(--premium-blue);
            color: white;
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        }

        .table-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-premium thead {
            background: #f8f9fc;
            color: #4e73df;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .table-premium th {
            padding: 1.25rem !important;
            border: none !important;
        }

        .table-premium td {
            padding: 1.25rem !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f3f9 !important;
        }

        .badge-premium {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .performance-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border: 10px solid #f8f9fc;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .performance-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--premium-blue);
        }

        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .bg-soft-success {
            background-color: rgba(28, 200, 138, 0.1);
        }

        /* Premium Colored Tabs Styling (Horizontal - Attached) */
        .premium-tab {
            border-radius: 0px !important;
            /* Square for joined look */
            padding: 12px 25px !important;
            font-size: 1rem !important;
            font-weight: 700 !important;
            background: #f1f3f9;
            color: #4e73df;
            border: none !important;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-right: 2px !important;
            /* Tiny gap for border-left visibility */
            justify-content: center;
            min-width: 160px;
            height: 100%;
            border-left: 4px solid #ddd !important;
            /* Default border */
        }

        /* Round ends of the whole tab bar */
        .nav-item:first-child .premium-tab {
            border-top-left-radius: 10px !important;
            border-left: none !important;
        }

        .nav-item:last-child .premium-tab {
            border-top-right-radius: 10px !important;
        }

        .premium-tab i {
            font-size: 1.2rem;
        }

        /* Active State & Specific Colors - "Melekat" Fixed */
        .premium-tab.active {
            color: white !important;
            border-bottom: none !important;
            transform: none !important;
            /* Don't float */
            box-shadow: none !important;
            margin-bottom: -1px !important;
            /* Touch the line below */
            z-index: 10;
        }

        /* Menjadikan warna dasar semua tab solid dan tegas */
        .premium-tab.color-primary {
            background: #4e73df !important;
            border-left-color: #2e59d9 !important;
            color: white !important;
        }

        .premium-tab.color-danger {
            background: #e74a3b !important;
            border-left-color: #be2617 !important;
            color: white !important;
        }

        .premium-tab.color-warning {
            background: #f6c23e !important;
            border-left-color: #df9c00 !important;
            color: #111 !important;
        }

        .premium-tab.color-info {
            background: #6610f2 !important;
            border-left-color: #520dc2 !important;
            color: white !important;
        }

        .premium-tab.color-success {
            background: #1cc88a !important;
            border-left-color: #13855c !important;
            color: white !important;
        }

        .premium-tab.color-dark {
            background: #2c3e50 !important;
            border-left-color: #1a252f !important;
            color: white !important;
        }

        /* Active State: Sedikit lebih terang dan menonjol */
        .premium-tab.active {
            filter: brightness(1.1) !important;
            box-shadow: 0 -3px 8px rgba(0, 0, 0, 0.1) !important;
        }

        /* Warna teks khusus untuk active tab agar tetap terbaca */
        .premium-tab.active.color-warning {
            color: #111 !important;
        }

        /* Inactive State: Sedikit lebih gelap agar membedakan dengan active */
        .premium-tab:not(.active) {
            filter: brightness(0.85) contrast(1.1);
            opacity: 0.95;
        }

        .premium-tab:hover:not(.active) {
            filter: brightness(0.95);
            transform: translateY(-2px);
        }

        .premium-divider-container {
            padding: 0;
            margin-top: -1px;
            /* Overlap with tabs */
            margin-bottom: 2rem;
            z-index: 2;
        }

        /* Divider Styling */
        .premium-divider-container {
            padding: 0 2rem;
            margin-bottom: 2.5rem;
        }

        .premium-divider {
            height: 2px;
            background: #e3e6f0;
            border-radius: 10px;
            position: relative;
        }

        .divider-indicator {
            position: absolute;
            height: 4px;
            background: #4e73df;
            border-radius: 10px;
            top: -1px;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            box-shadow: 0 2px 8px rgba(78, 115, 223, 0.4);
            width: 100px;
            left: 0;
        }

        /* Color-specific indicators (optional, matching the active tab color) */
        .bg-primary-indicator {
            background: #4e73df !important;
            box-shadow: 0 2px 8px rgba(78, 115, 223, 0.4);
        }

        .bg-danger-indicator {
            background: #e74a3b !important;
            box-shadow: 0 2px 8px rgba(231, 74, 59, 0.4);
        }

        .bg-warning-indicator {
            background: #f6c23e !important;
            box-shadow: 0 2px 8px rgba(246, 194, 62, 0.4);
        }

        .bg-info-indicator {
            background: #36b9cc !important;
            box-shadow: 0 2px 8px rgba(54, 185, 204, 0.4);
        }

        .bg-success-indicator {
            background: #1cc88a !important;
            box-shadow: 0 2px 8px rgba(28, 200, 138, 0.4);
        }

        .bg-dark-indicator {
            background: #2c3e50 !important;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.4);
        }

        .op-card {
            border-radius: 15px !important;
            border: none !important;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
        }

        .op-card-title {
            font-size: 1.25rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #ffffff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .op-card-text {
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .op-card-icon {
            opacity: 0.4;
            filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.3));
        }

        /* Monitoring Perbaikan Table Styling */
        .monitoring-header {
            background-color: #4e73df !important;
            color: #f9f1f1ff;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px !important;
            border: 2px solid #000;
        }

        .table-responsive {
            max-height: 68vh;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .table-monitoring {
            border-collapse: separate;
            border-spacing: 0;
            border: 2px solid #000 !important;
        }

        .table-monitoring th,
        .table-monitoring td {
            border: 1px solid #000 !important;
            vertical-align: middle;
        }

        .table-monitoring thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #4e73df !important;
            /* Blue shading */
            color: #fff !important;
            /* White font */
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            border: 1px solid #000 !important;
            background-clip: padding-box;
        }

        .table-monitoring tbody td {
            background-color: #ffffff;
        }

        .table-monitoring tbody tr:hover td {
            background-color: #f8f9fc;
        }

        .table-monitoring .form-control-inline {
            background: transparent;
            border: none;
            padding: 4px 8px;
            width: 100%;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .table-monitoring .form-control-inline:focus {
            background: #fff;
            border: 1px solid var(--premium-blue);
            box-shadow: 0 0 5px rgba(78, 115, 223, 0.2);
        }

        /* Dropdown colors */
        .status-dropdown {
            font-weight: 700;
            border-radius: 20px !important;
            padding: 4px 15px !important;
            font-size: 0.8rem;
            border: 1px solid rgba(0, 0, 0, 0.2) !important;
            /* Borders made clear */
            appearance: auto !important;
            /* Show arrow */
            text-align: center;
            width: auto !important;
            min-width: 140px;
            cursor: pointer;
        }

        .bg-status-pengajuan-dana {
            background-color: #4f46e5 !important;
            color: #fff !important;
        }

        .bg-status-cari-vendor {
            background-color: #f6c23e !important;
            color: #fff !important;
        }

        .bg-status-pending {
            background-color: #858796 !important;
            color: #fff !important;
        }

        .bg-status-on-progress {
            background-color: #36b9cc !important;
            color: #fff !important;
        }

        .bg-status-selesai {
            background-color: #1cc88a !important;
            color: #fff !important;
        }

        .bg-status-tidak {
            background-color: #e74a3b !important;
            color: #fff !important;
        }

        .saving-indicator {
            display: none;
            font-size: 0.7rem;
            color: #1cc88a;
            margin-left: 5px;
        }


        /* Preview Image Styles */
        .preview-image {
            transition: all 0.3s ease;
        }

        .preview-image:hover {
            transform: scale(1.05);
            border-color: #4e73df;
        }

        .preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
            border-radius: 0.25rem;
        }

        .position-relative:hover .preview-overlay {
            opacity: 1;
        }

        /* Premium Table Typography and Auto-resizing Textareas */
        .table-monitoring textarea.form-control-inline {
            resize: none;
            overflow: hidden;
            word-wrap: break-word;
            white-space: pre-wrap;
            min-height: 38px;
            height: auto;
            line-height: 1.4;
            display: block;
            font-family: inherit;
        }

        /* Custom premium pagination styles matching SB Admin 2 style */
        .pagination {
            margin: 0;
            white-space: nowrap;
            justify-content: flex-end;
        }

        .page-item .page-link {
            color: #4e73df;
            background-color: #fff;
            border: 1px solid #dddfeb;
            margin: 0;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.2s ease-in-out;
        }

        .page-item .page-link:hover {
            color: #224abe;
            background-color: #eaecf4;
            border-color: #dddfeb;
            text-decoration: none;
        }

        .page-item.active .page-link {
            z-index: 3;
            color: #fff !important;
            background-color: #4e73df !important;
            border-color: #4e73df !important;
        }

        .page-item.disabled .page-link {
            color: #858796;
            pointer-events: none;
            background-color: #fff;
            border-color: #dddfeb;
        }

        .page-item:first-child .page-link {
            border-top-left-radius: 0.35rem;
            border-bottom-left-radius: 0.35rem;
        }

        .page-item:last-child .page-link {
            border-top-right-radius: 0.35rem;
            border-bottom-right-radius: 0.35rem;
        }

        .table-monitoring textarea.text-center {
            text-align: center;
        }

        .table-monitoring input[type="date"].form-control-inline {
            min-width: 120px;
        }

        /* ===== FASILITAS CELL (SELECT2 + MANUAL INPUT TOGGLE) ===== */
        .fasilitas-cell-wrapper {
            min-width: 210px;
        }

        .fasilitas-manual-container .input-group,
        .inventaris-select-container .input-group {
            flex-wrap: nowrap !important;
            display: flex !important;
            width: 100% !important;
        }

        .fasilitas-manual-container .manual-input {
            font-size: 0.82rem !important;
            height: 31px !important;
            border-top-left-radius: 4px !important;
            border-bottom-left-radius: 4px !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .btn-switch-to-select,
        .btn-switch-to-manual {
            height: 31px !important;
            padding: 0 10px !important;
            font-size: 0.8rem !important;
            white-space: nowrap !important;
            border-top-right-radius: 4px !important;
            border-bottom-right-radius: 4px !important;
            border: 1px solid #ced4da !important;
            background-color: #6c757d !important;
            color: #fff !important;
            transition: all 0.2s !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .btn-switch-to-select:hover,
        .btn-switch-to-manual:hover {
            background-color: #5a6268 !important;
            color: #fff !important;
        }

        .inventaris-select-container .select2-container {
            width: 100% !important;
        }

        .inventaris-select-container .select2-selection--single {
            height: 31px !important;
            border: 1px solid #ced4da !important;
            border-top-left-radius: 4px !important;
            border-bottom-left-radius: 4px !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            position: relative !important;
        }

        .inventaris-select-container .select2-selection__rendered {
            line-height: 29px !important;
            font-size: 0.82rem;
            padding-left: 8px !important;
            padding-right: 25px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            display: block !important;
        }

        .inventaris-select-container .select2-selection__clear {
            position: absolute !important;
            right: 25px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            font-weight: bold !important;
            color: #e74a3b !important;
            font-size: 1.1rem !important;
            z-index: 10 !important;
            cursor: pointer !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        .inventaris-select-container .select2-selection__arrow {
            height: 29px !important;
        }

        .btn-back-to-select {
            height: 31px;
            padding: 0 8px;
            font-size: 0.75rem;
            white-space: nowrap;
        }

        .deadline-label {
            font-size: 0.68rem;
            color: #888;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 4px;
            margin-bottom: 1px;
        }

        /* ===== TEXT DETAIL POPUP ===== */
        .text-truncate-cell {
            max-width: 200px;
        }

        /* Textarea di kolom kerusakan & rencana: tampil terbatas, bisa diedit */
        .text-truncate-cell textarea.form-control-inline {
            max-height: 48px;
            /* ~2 baris */
            overflow: hidden;
            resize: none;
            transition: max-height 0.2s ease;
            display: block;
            width: 100%;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            -webkit-spell-check: false;
            spellcheck: false;
        }

        /* Saat fokus: expand penuh agar bisa edit dengan nyaman */
        .text-truncate-cell textarea.form-control-inline:focus {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--premium-blue) !important;
            box-shadow: 0 0 5px rgba(78, 115, 223, 0.2) !important;
        }

        .btn-lihat-detail {
            font-size: 0.68rem;
            padding: 1px 7px;
            border-radius: 10px;
            font-weight: 700;
            letter-spacing: 0.3px;
            border: 1px solid #4e73df;
            color: #4e73df;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-block;
            line-height: 1.6;
        }

        .btn-lihat-detail:hover {
            background: #4e73df;
            color: #fff;
        }

        /* Modal Detail Popup */
        #detail-popup-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        #detail-popup-overlay.active {
            display: flex;
        }

        #detail-popup-box {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 480px;
            padding: 0;
            overflow: hidden;
            animation: popupIn 0.2s ease;
        }

        @keyframes popupIn {
            from {
                transform: scale(0.92);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        #detail-popup-box .popup-header {
            background: #4e73df;
            color: #fff;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #detail-popup-box .popup-header .popup-title {
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        #detail-popup-box .popup-header .popup-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: #fff;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            font-size: 1rem;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        #detail-popup-box .popup-header .popup-close:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        #detail-popup-box .popup-body {
            padding: 20px 24px;
            font-size: 0.9rem;
            color: #333;
            line-height: 1.7;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 60vh;
            overflow-y: auto;
        }
    </style>

    <div class="dashboard-container">
        @if (auth()->user()->role !== 'administrator')
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h1 class="h3 text-gray-800 font-weight-bold">Dashboard Operasional</h1>
                    <p class="text-muted">Selamat datang kembali, {{ $csName }}!</p>
                </div>
                <div class="col-md-6 text-right">
                    <!-- Filter removed -->
                </div>
            </div>
        @endif


        <!-- Navigation Tabs (Main) -->
        <ul class="nav nav-tabs border-0 mt-2 mb-0 d-flex flex-wrap gap-0 justify-content-center px-4" id="opMainTabs"
            role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold premium-tab color-primary" id="inventaris-tab" data-toggle="tab"
                    data-target="#inventaris" type="button" role="tab">
                    <i class="fas fa-building"></i> <span>Monitoring Inventaris</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold premium-tab color-warning" id="kebutuhan-mbc-tab" data-toggle="tab"
                    data-target="#kebutuhan-mbc" type="button" role="tab">
                    <i class="fas fa-chalkboard-teacher"></i> <span>Perbaikan</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold premium-tab color-success" id="kebutuhan-m1t-tab" data-toggle="tab"
                    data-target="#kebutuhan-m1t" type="button" role="tab">
                    <i class="fas fa-user-graduate"></i> <span>Pengadaan Barang</span>
                </button>
            </li>
        </ul>

        <!-- Premium Divider with Dynamic Indicator -->
        <div class="premium-divider-container">
            <div class="premium-divider">
                <div id="tab-indicator" class="divider-indicator"></div>
            </div>
        </div>

        <div class="tab-content" id="opMainTabsContent">
            <!-- Tab 2: Inventaris -->
            <div class="tab-pane fade show active" id="inventaris" role="tabpanel">
                <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-boxes mr-2 text-info"></i> Data
                                Monitoring
                                Inventaris</h5>
                            <div class="d-flex gap-2" style="gap: 8px;">
                                <button class="btn btn-info btn-sm shadow-sm"
                                    style="border-radius: 8px; background-color: #00ffff; color: #000; border: 1px solid #000;"
                                    id="btnTambahInventarisInline">
                                    <i class="fas fa-plus mr-1"></i> Tambah Inventaris
                                </button>
                                <a href="{{ route('inventaris-kantor.checklist-pdf') }}" class="btn btn-sm shadow-sm"
                                    style="border-radius: 8px; background-color: #4e73df; color: #fff; border: 1px solid #2e59d9; text-decoration: none;"
                                    target="_blank">
                                    <i class="fas fa-print mr-1"></i> Pengecekan Bulanan
                                </a>
                                {{-- Upload Report button hidden (not used) --}}
                                <button class="btn btn-sm shadow-sm" id="btnUploadReport"
                                    style="display:none; border-radius: 8px; background-color: #1cc88a; color: #fff; border: 1px solid #13855c;"
                                    data-toggle="modal" data-target="#modalUploadReport">
                                    <i class="fas fa-upload mr-1"></i> Upload Report
                                </button>
                            </div>
                        </div>

                        {{-- Status pengecekan bulan berjalan (hidden - not used) --}}

                        <div class="table-responsive">
                            <table class="table table-monitoring mb-0">
                                <thead>
                                    <tr style="background-color: #00ffff;">
                                        <th class="text-center"
                                            style="width: 50px; background-color: #00ffff !important; color: #000 !important; border: 1px solid #000 !important; vertical-align: middle;">
                                            No</th>
                                        <th class="text-center"
                                            style="background-color: #00ffff !important; color: #000 !important; border: 1px solid #000 !important; vertical-align: middle; width: 170px;">
                                            <div class="mb-1 font-weight-bold"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px;">INVENTARIS</div>
                                            <input type="text" id="filter-peralatan-inventaris"
                                                class="form-control form-control-sm mx-auto text-center"
                                                placeholder="Cari..."
                                                style="border-radius: 20px; font-weight: 700; width: 140px; height: 28px; padding: 2px 10px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        </th>
                                        <th class="text-center"
                                            style="background-color: #00ffff !important; color: #000 !important; border: 1px solid #000 !important; vertical-align: middle; width: 170px;">
                                            <div class="mb-1 font-weight-bold"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px;">LOKASI</div>
                                            <select id="filter-lokasi-inventaris"
                                                class="form-control form-control-sm mx-auto"
                                                style="border-radius: 20px; font-weight: 700; text-align: center; text-align-last: center; width: 140px; cursor: pointer; height: 28px; padding: 2px 10px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                <option value="all">-- Semua --</option>
                                                <option value="Ruang depan">Ruang depan</option>
                                                <option value="Kamar Mandi Bawah">Kamar Mandi Bawah</option>
                                                <option value="Ruang kelas/ aula">Ruang kelas/ aula</option>
                                                <option value="Kamar atas">Kamar atas</option>
                                                <option value="Ruang atas">Ruang atas</option>
                                                <option value="Kamar bawah">Kamar bawah</option>
                                                <option value="Kamar mandi atas">Kamar mandi atas</option>
                                            </select>
                                        </th>
                                        <th class="text-center"
                                            style="background-color: #00ffff !important; color: #000 !important; border: 1px solid #000 !important; vertical-align: middle; width: 100px;">
                                            <div class="mb-1 font-weight-bold"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px;">JUMLAH</div>
                                            <div style="height: 28px;"></div>
                                        </th>
                                        <th class="text-center"
                                            style="background-color: #00ffff !important; color: #000 !important; border: 1px solid #000 !important; vertical-align: middle; width: 170px;">
                                            <div class="mb-1 font-weight-bold"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px;">STATUS</div>
                                            <select id="filter-status-inventaris"
                                                class="form-control form-control-sm mx-auto"
                                                style="border-radius: 20px; font-weight: 700; text-align: center; text-align-last: center; width: 140px; cursor: pointer; height: 28px; padding: 2px 10px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                <option value="all">-- Semua --</option>
                                                <option value="Normal">Normal</option>
                                                <option value="Rusak">Rusak</option>
                                                <option value="Perbaikan">Perbaikan</option>
                                            </select>
                                        </th>
                                        <th class="text-center"
                                            style="background-color: #00ffff !important; color: #000 !important; border: 1px solid #000 !important;">
                                            KETERANGAN</th>
                                        <th class="text-center"
                                            style="width: 50px; background-color: #00ffff !important; color: #000 !important; border: 1px solid #000 !important;">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="inventaris-table-body">
                                    @forelse($inventarisKantor as $key => $item)
                                        <tr data-id="{{ $item->id }}">
                                            <td class="text-center no-col">{{ $loop->iteration }}</td>
                                            <td>
                                                <textarea class="form-control-inline text-center inventaris-live-edit auto-resize" data-field="nama_peralatan"
                                                    rows="1" placeholder="(Tulis Barang)">{{ $item->nama_peralatan }}</textarea>
                                            </td>
                                            <td class="text-center font-weight-bold align-middle"
                                                style="background: #fff;">
                                                <select
                                                    class="form-control-inline text-center font-weight-bold inventaris-live-edit status-dropdown"
                                                    data-field="lokasi"
                                                    style="background-color: #fff !important; color: #000 !important; border: 1px solid rgba(0,0,0,0.1) !important; min-width: 150px;">
                                                    <option value="">-- Pilih Lokasi --</option>
                                                    <option value="Ruang depan"
                                                        {{ $item->lokasi == 'Ruang depan' ? 'selected' : '' }}>Ruang depan
                                                    </option>
                                                    <option value="Kamar Mandi Bawah"
                                                        {{ $item->lokasi == 'Kamar Mandi Bawah' ? 'selected' : '' }}>Kamar
                                                        Mandi Bawah</option>
                                                    <option value="Ruang kelas/ aula"
                                                        {{ $item->lokasi == 'Ruang kelas/ aula' ? 'selected' : '' }}>Ruang
                                                        kelas/ aula</option>
                                                    <option value="Kamar atas"
                                                        {{ $item->lokasi == 'Kamar atas' ? 'selected' : '' }}>Kamar atas
                                                    </option>
                                                    <option value="Ruang atas"
                                                        {{ $item->lokasi == 'Ruang atas' ? 'selected' : '' }}>Ruang atas
                                                    </option>
                                                    <option value="Kamar bawah"
                                                        {{ $item->lokasi == 'Kamar bawah' ? 'selected' : '' }}>Kamar bawah
                                                    </option>
                                                    <option value="Kamar mandi atas"
                                                        {{ $item->lokasi == 'Kamar mandi atas' ? 'selected' : '' }}>Kamar
                                                        mandi atas</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="form-control-inline text-center inventaris-live-edit auto-resize" data-field="jumlah"
                                                    rows="1">{{ $item->jumlah }}</textarea>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $statusClass = 'bg-status-selesai';
                                                    if ($item->status == 'Rusak') {
                                                        $statusClass = 'bg-status-tidak';
                                                    } elseif ($item->status == 'Perbaikan') {
                                                        $statusClass = 'bg-status-cari-vendor';
                                                    }
                                                @endphp
                                                <select
                                                    class="form-control-inline inventaris-live-edit status-dropdown {{ $statusClass }}"
                                                    data-field="status">
                                                    <option value="Normal"
                                                        {{ $item->status == 'Normal' ? 'selected' : '' }}
                                                        class="bg-status-selesai">Normal</option>
                                                    <option value="Rusak"
                                                        {{ $item->status == 'Rusak' ? 'selected' : '' }}
                                                        class="bg-status-tidak">Rusak</option>
                                                    <option value="Perbaikan"
                                                        {{ $item->status == 'Perbaikan' ? 'selected' : '' }}
                                                        class="bg-status-cari-vendor">Perbaikan</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="form-control-inline inventaris-live-edit auto-resize" data-field="keterangan" rows="1">{{ $item->keterangan }}</textarea>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-link text-danger p-0 delete-inventaris-btn"
                                                    data-id="{{ $item->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-inventaris-row">
                                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data
                                                inventaris kantor.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div id="inventaris-pagination" class="mt-3 d-flex justify-content-center"></div>
                    </div>
                </div>

                {{-- ── Riwayat Report Pengecekan (hidden - not used) ── --}}
                <div style="display:none;">
                    @if (session('success_report'))
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success_report') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px; overflow: hidden;">
                        <div class="card-header text-center font-weight-bold"
                            style="background-color: #4e73df; color: #fff; border: none; text-transform: uppercase; letter-spacing: 1px;">
                            <i class="fas fa-archive mr-2"></i> Riwayat Report Pengecekan Inventaris
                        </div>
                        <div class="card-body p-4">
                            @if ($reportInventaris->isEmpty())
                                <p class="text-muted text-center py-3">Belum ada report yang diunggah.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-monitoring mb-0" id="tbl-riwayat-report">
                                        <thead>
                                            <tr>
                                                <th class="text-center"
                                                    style="width:50px; background-color:#4e73df !important; color:#fff !important; border:1px solid #000 !important;">
                                                    No</th>
                                                <th
                                                    style="background-color:#4e73df !important; color:#fff !important; border:1px solid #000 !important;">
                                                    Periode</th>
                                                <th
                                                    style="background-color:#4e73df !important; color:#fff !important; border:1px solid #000 !important;">
                                                    Tanggal Pemeriksaan</th>
                                                <th
                                                    style="background-color:#4e73df !important; color:#fff !important; border:1px solid #000 !important;">
                                                    Nama File</th>
                                                <th
                                                    style="background-color:#4e73df !important; color:#fff !important; border:1px solid #000 !important;">
                                                    Upload Oleh</th>
                                                <th
                                                    style="background-color:#4e73df !important; color:#fff !important; border:1px solid #000 !important;">
                                                    Tanggal Upload</th>
                                                <th class="text-center"
                                                    style="width:160px; background-color:#4e73df !important; color:#fff !important; border:1px solid #000 !important;">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reportInventaris as $i => $rpt)
                                                <tr>
                                                    <td class="text-center">{{ $i + 1 }}</td>
                                                    <td>{{ $rpt->periode }}</td>
                                                    <td class="text-center">
                                                        {{ $rpt->tanggal_pemeriksaan->format('d-m-Y') }}
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-file-pdf text-danger mr-1"></i>
                                                        {{ $rpt->nama_file }}
                                                        @if ($rpt->catatan)
                                                            <br><small class="text-muted"><i
                                                                    class="fas fa-comment mr-1"></i>{{ $rpt->catatan }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ optional($rpt->uploader)->name ?? '-' }}</td>
                                                    <td class="text-center">{{ $rpt->created_at->format('d-m-Y H:i') }}
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center" style="gap:4px;">
                                                            {{-- Lihat --}}
                                                            <a href="{{ asset('storage/' . $rpt->file_pdf) }}"
                                                                target="_blank" class="btn btn-primary btn-xs"
                                                                style="padding:3px 8px; font-size:0.75rem; border-radius:6px;"
                                                                title="Lihat PDF">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            {{-- Download --}}
                                                            <a href="{{ route('inventaris-kantor.report.download', $rpt->id) }}"
                                                                class="btn btn-info btn-xs"
                                                                style="padding:3px 8px; font-size:0.75rem; border-radius:6px; color:#fff;"
                                                                title="Download PDF">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            {{-- Hapus --}}
                                                            <form method="POST"
                                                                action="{{ route('inventaris-kantor.report.destroy', $rpt->id) }}"
                                                                onsubmit="return confirm('Hapus report ini?')"
                                                                style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-xs"
                                                                    style="padding:3px 8px; font-size:0.75rem; border-radius:6px;"
                                                                    title="Hapus">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>{{-- end hidden riwayat report --}}

                {{-- ── Modal Upload Report (hidden - not used) ── --}}
                <div style="display:none;">
                    <div class="modal fade" id="modalUploadReport" tabindex="-1" role="dialog"
                        aria-labelledby="modalUploadReportLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content" style="border-radius:14px; overflow:hidden;">
                                <div class="modal-header" style="background:#1cc88a; color:#fff;">
                                    <h5 class="modal-title font-weight-bold" id="modalUploadReportLabel">
                                        <i class="fas fa-upload mr-2"></i> Upload Report Pengecekan Inventaris
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                        style="color:#fff; opacity:1;">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('inventaris-kantor.report.upload') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        @if ($errors->any())
                                            <div class="alert alert-danger py-2">
                                                <ul class="mb-0 pl-3">
                                                    @foreach ($errors->all() as $err)
                                                        <li style="font-size:0.85rem;">{{ $err }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="form-row">
                                            <div class="form-group col-6">
                                                <label class="font-weight-bold" style="font-size:0.85rem;">Bulan Periode
                                                    <span class="text-danger">*</span></label>
                                                <select name="bulan" class="form-control form-control-sm" required>
                                                    <option value="">-- Pilih Bulan --</option>
                                                    @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $num => $nama)
                                                        <option value="{{ $num }}"
                                                            {{ old('bulan', now()->month) == $num ? 'selected' : '' }}>
                                                            {{ $nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-6">
                                                <label class="font-weight-bold" style="font-size:0.85rem;">Tahun Periode
                                                    <span class="text-danger">*</span></label>
                                                <select name="tahun" class="form-control form-control-sm" required>
                                                    @for ($y = now()->year; $y >= now()->year - 3; $y--)
                                                        <option value="{{ $y }}"
                                                            {{ old('tahun', now()->year) == $y ? 'selected' : '' }}>
                                                            {{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold" style="font-size:0.85rem;">Tanggal Pemeriksaan
                                                <span class="text-danger">*</span></label>
                                            <input type="date" name="tanggal_pemeriksaan"
                                                class="form-control form-control-sm"
                                                value="{{ old('tanggal_pemeriksaan', now()->format('Y-m-d')) }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold" style="font-size:0.85rem;">Upload File PDF
                                                <span class="text-danger">*</span></label>
                                            <input type="file" name="file_pdf" class="form-control form-control-sm"
                                                accept=".pdf" required>
                                            <small class="text-muted">Format: PDF. Maksimal 10 MB.</small>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold" style="font-size:0.85rem;">Catatan <span
                                                    class="text-muted">(opsional)</span></label>
                                            <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Catatan tambahan..."
                                                maxlength="1000">{{ old('catatan') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer py-2">
                                        <button type="button" class="btn btn-secondary btn-sm"
                                            data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-cloud-upload-alt mr-1"></i> Upload
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>{{-- end hidden modal upload report --}}
            </div>

            <!-- Tab 3: Monitoring Perbaikan -->
            <div class="tab-pane fade" id="kebutuhan-mbc" role="tabpanel">
                <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-4 text-dark"><i class="fas fa-tools mr-2 text-warning"></i>
                            Data Perbaikan</h5>

                        <!-- Filter Deadline Bar -->
                        <div class="d-flex align-items-center mb-2 flex-wrap" style="gap: 10px;">
                            <span class="font-weight-bold text-muted" style="font-size:0.82rem; white-space:nowrap;"><i
                                    class="fas fa-calendar-alt mr-1"></i> Filter Deadline:</span>
                            <input type="date" id="filter-timeline-date" class="form-control form-control-sm"
                                style="border-radius: 20px; font-weight: 600; width: 150px; height: 32px; padding: 2px 10px; font-size: 0.8rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" />
                            <button id="filter-timeline-btn" class="btn btn-primary btn-sm" title="Filter Deadline"
                                style="border-radius: 20px; padding: 4px 16px; font-size:0.8rem;">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                            <button id="filter-timeline-reset" class="btn btn-outline-secondary btn-sm"
                                title="Reset Filter" style="border-radius: 20px; padding: 4px 16px; font-size:0.8rem;">
                                <i class="fas fa-sync-alt mr-1"></i>
                            </button>
                        </div>

                        <!-- Filter Search Fasilitas (Live Search) -->
                        <div class="d-flex align-items-center mb-3 flex-wrap" style="gap: 10px;">
                            <span class="font-weight-bold text-muted" style="font-size:0.82rem; white-space:nowrap;"><i
                                    class="fas fa-search mr-1"></i> Cari Fasilitas:</span>
                            <input type="text" id="filter-fasilitas-search" class="form-control form-control-sm"
                                placeholder="Ketik nama fasilitas..."
                                style="border-radius: 20px; font-weight: 600; width: 250px; height: 32px; padding: 2px 10px; font-size: 0.8rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" />
                            <button id="filter-fasilitas-reset" class="btn btn-outline-secondary btn-sm"
                                title="Reset Filter Fasilitas"
                                style="border-radius: 20px; padding: 4px 16px; font-size:0.8rem;">
                                <i class="fas fa-sync-alt mr-1"></i>
                            </button>
                        </div>

                        <!-- Tombol Tambah Data di kanan atas tabel -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <!-- Legenda Icon Fasilitas -->
                            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                <span class="font-weight-bold text-muted mr-1" style="font-size: 0.78rem;">Keterangan icon
                                    kolom Fasilitas:</span>
                                <span class="badge badge-light border shadow-sm d-inline-flex align-items-center"
                                    style="font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; gap: 5px;">
                                    <i class="fas fa-keyboard text-secondary" style="font-size: 0.8rem;"></i>
                                    <span class="text-dark">Ketik nama fasilitas manual</span>
                                </span>
                                <span class="badge badge-light border shadow-sm d-inline-flex align-items-center"
                                    style="font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; gap: 5px;">
                                    <i class="fas fa-list text-secondary" style="font-size: 0.8rem;"></i>
                                    <span class="text-dark">Pilih fasilitas dari list inventaris</span>
                                </span>
                            </div>
                            <button class="btn btn-primary btn-sm shadow-sm" style="border-radius: 8px;"
                                id="btnTambahPerbaikanInline">
                                <i class="fas fa-plus mr-1"></i> Perbaikan
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-monitoring mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">NO.</th>
                                        <th style="width: 200px;">FASILITAS</th>
                                        <th>RENCANA PERBAIKAN</th>
                                        <th class="text-center" style="width: 160px;">
                                            <div class="mb-1 font-weight-bold"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px;">PROGRESS</div>
                                            <select id="filter-progress" class="form-control form-control-sm mx-auto"
                                                style="border-radius: 20px; font-weight: 700; text-align: center; text-align-last: center; width: 140px; cursor: pointer; height: 28px; padding: 2px 10px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                <option value="all">-- Semua --</option>
                                                <option value="Pengajuan dana">Pengajuan dana</option>
                                                <option value="Pengerjaan">Pengerjaan</option>
                                                <option value="Selesai">Selesai</option>
                                            </select>
                                        </th>
                                        <th class="text-center" style="width: 130px;">BUDGET</th>
                                        <th class="text-center" style="width: 130px;">REALISASI DANA</th>
                                        <th class="text-center" style="width: 120px;">LPJ</th>
                                        <th class="text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="monitoring-table-body">
                                    @php
                                        $totalBudget = 0;
                                        $totalRealisasi = 0;
                                    @endphp
                                    @forelse($monitoringPerbaikan as $key => $item)
                                        @php
                                            $totalBudget += (float) $item->budget;
                                            $totalRealisasi += (float) $item->realisasi_dana;
                                        @endphp
                                        <tr data-id="{{ $item->id }}">
                                            <td class="text-center no-col">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fasilitas-cell-wrapper">
                                                    {{-- SELECT2 container (shown when inventaris dipilih) --}}
                                                    <div class="inventaris-select-container"
                                                        style="{{ $item->inventaris_id ? '' : 'display:none;' }}">
                                                        <div class="input-group input-group-sm">
                                                            <div style="flex-grow: 1;">
                                                                <select
                                                                    class="form-control form-control-sm select2-inventaris live-edit"
                                                                    data-field="inventaris_id" style="width:100%;">
                                                                    @if ($item->inventaris_id)
                                                                        <option value="{{ $item->inventaris_id }}"
                                                                            selected>
                                                                            {{ optional($item->inventaris)->nama_peralatan }}{{ optional($item->inventaris)->lokasi ? ' (' . optional($item->inventaris)->lokasi . ')' : '' }}
                                                                        </option>
                                                                    @else
                                                                        <option value=""></option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-switch-to-manual" type="button"
                                                                    title="Ketik manual">
                                                                    <i class="fas fa-keyboard"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- MANUAL INPUT container (shown when manual input / empty) --}}
                                                    <div class="fasilitas-manual-container"
                                                        style="{{ $item->inventaris_id ? 'display:none;' : '' }}">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text"
                                                                class="form-control form-control-sm live-edit manual-input"
                                                                data-field="fasilitas_manual"
                                                                value="{{ $item->fasilitas_manual ?? $item->fasilitas }}"
                                                                placeholder="Tulis nama fasilitas..." autocomplete="off">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-switch-to-select" type="button"
                                                                    title="Pilih dari inventaris">
                                                                    <i class="fas fa-list"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Deadline --}}
                                                <div class="deadline-label">Deadline</div>
                                                <input type="date" class="form-control-inline text-center live-edit"
                                                    data-field="tanggal_selesai" value="{{ $item->tanggal_selesai }}"
                                                    style="width:110px; font-size:0.72rem;">
                                            </td>
                                            <td class="text-truncate-cell">
                                                <textarea class="form-control-inline live-edit auto-resize kerusakan-field" data-field="kerusakan" rows="1"
                                                    spellcheck="false">{{ $item->kerusakan }}</textarea>
                                                <button class="btn-lihat-detail mt-1"
                                                    onclick="openDetailPopup('Rencana Perbaikan', this.previousElementSibling.value)">Lihat
                                                    Detail</button>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $statusClass = '';
                                                    if (
                                                        $item->progress == 'Pengajuan dana' ||
                                                        $item->progress == 'Pengajuan Dana'
                                                    ) {
                                                        $statusClass = 'bg-status-pengajuan-dana';
                                                    } elseif (
                                                        $item->progress == 'Pengerjaan' ||
                                                        $item->progress == 'On Progress' ||
                                                        $item->progress == 'Cari Vendor'
                                                    ) {
                                                        $statusClass = 'bg-status-on-progress';
                                                    } elseif ($item->progress == 'Selesai') {
                                                        $statusClass = 'bg-status-selesai';
                                                    } else {
                                                        $statusClass = 'bg-status-pending';
                                                    }
                                                @endphp
                                                <select
                                                    class="form-control-inline live-edit status-dropdown {{ $statusClass }}"
                                                    data-field="progress">
                                                    @if (
                                                        !in_array($item->progress, [
                                                            'Pengajuan dana',
                                                            'Pengajuan Dana',
                                                            'Pengerjaan',
                                                            'On Progress',
                                                            'Cari Vendor',
                                                            'Selesai',
                                                        ]) && !empty($item->progress))
                                                        <option value="{{ $item->progress }}" selected
                                                            class="bg-status-pending">{{ $item->progress }}</option>
                                                    @endif
                                                    <option value="Pengajuan dana"
                                                        {{ $item->progress == 'Pengajuan dana' || $item->progress == 'Pengajuan Dana' ? 'selected' : '' }}
                                                        class="bg-status-pengajuan-dana">Pengajuan dana</option>
                                                    <option value="Pengerjaan"
                                                        {{ $item->progress == 'Pengerjaan' || $item->progress == 'On Progress' || $item->progress == 'Cari Vendor' ? 'selected' : '' }}
                                                        class="bg-status-on-progress">Pengerjaan</option>
                                                    <option value="Selesai"
                                                        {{ $item->progress == 'Selesai' ? 'selected' : '' }}
                                                        class="bg-status-selesai">Selesai</option>
                                                </select>
                                            </td>
                                            <td class="text-right">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span class="mr-1 text-success font-weight-bold">Rp</span>
                                                    <input type="text"
                                                        class="form-control-inline text-right text-success font-weight-bold live-edit budget-input"
                                                        data-field="budget"
                                                        value="{{ number_format((float) $item->budget, 0, ',', '.') }}">
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span class="mr-1 text-success font-weight-bold">Rp</span>
                                                    <input type="text"
                                                        class="form-control-inline text-right text-success font-weight-bold live-edit realisasi-input"
                                                        data-field="realisasi_dana"
                                                        value="{{ number_format((float) $item->realisasi_dana, 0, ',', '.') }}">
                                                </div>
                                            </td>
                                            <td class="text-center align-middle bukti-transfer-cell">
                                                @if ($item->bukti_transfer)
                                                    <div class="d-flex flex-column align-items-center"><a
                                                            href="{{ asset($item->bukti_transfer) }}" target="_blank"
                                                            class="text-danger" title="Lihat Dokumen LPJ (PDF)"><i
                                                                class="far fa-file-pdf fa-2x mb-1 hover-scale transition-all"></i>
                                                            <div class="small font-weight-bold">Lihat LPJ</div>
                                                        </a>
                                                        <div class="mt-1"><a href="javascript:void(0)"
                                                                class="small text-primary font-weight-bold"
                                                                onclick="openUploadModal({{ $item->id }}, '{{ addslashes($item->inventaris_id ? optional($item->inventaris)->nama_peralatan : $item->fasilitas_manual ?? $item->fasilitas) }}', 'monitoring')">Ganti</a>
                                                        </div>
                                                </div>@else<div class="d-flex flex-column align-items-center"><button
                                                            type="button"
                                                            class="btn btn-outline-primary btn-xs px-2 shadow-sm font-weight-bold"
                                                            onclick="openUploadModal({{ $item->id }}, '{{ addslashes($item->inventaris_id ? optional($item->inventaris)->nama_peralatan : $item->fasilitas_manual ?? $item->fasilitas) }}', 'monitoring')"
                                                            style="font-size: 0.7rem; padding: 2px 5px;"><i
                                                                class="fas fa-upload mr-1"></i> Upload</button></div>
                                                @endif
                                            </td>
                                            <td style="display:none; overflow:hidden;" class="ignore-me">
                                                <div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-link text-danger p-0 delete-btn"
                                                    data-id="{{ $item->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="8" class="text-center py-5 text-muted">Belum ada data
                                                monitoring perbaikan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f8f9fc;">
                                        <td colspan="4" class="text-right font-weight-bold"
                                            style="white-space: nowrap; padding-right: 15px;">TOTAL ESTIMASI DANA</td>
                                        <td class="text-right font-weight-bold text-danger"
                                            style="font-size: 1.1rem; white-space: nowrap;">
                                            Rp <span
                                                id="grand-total-budget">{{ number_format($totalBudget, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-right font-weight-bold text-danger"
                                            style="font-size: 1.1rem; white-space: nowrap;">
                                            Rp <span
                                                id="grand-total-realisasi">{{ number_format($totalRealisasi, 0, ',', '.') }}</span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="monitoring-pagination" class="mt-3 d-flex justify-content-center"></div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Pengadaan Barang -->
            <div class="tab-pane fade" id="kebutuhan-m1t" role="tabpanel">
                <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="font-weight-bold mb-0 text-dark"><i
                                    class="fas fa-shopping-cart mr-2 text-success"></i> Data Pengadaan</h5>
                            <div class="d-flex" style="gap: 8px;">
                                <button class="btn btn-success btn-sm shadow-sm" style="border-radius: 8px;"
                                    id="btnTambahPengadaanInline">
                                    <i class="fas fa-plus mr-1"></i> Tambah Barang
                                </button>
                                <button class="btn btn-warning btn-sm shadow-sm" style="border-radius: 8px; color: #fff;"
                                    data-toggle="modal" data-target="#modalCetakPengadaan">
                                    <i class="fas fa-print mr-1"></i> Cetak Laporan
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-monitoring mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">NO.</th>
                                        <th>NAMA BARANG</th>
                                        <th class="text-center" style="width: 120px;">JUMLAH</th>
                                        <th class="text-right" style="width: 160px;">BUDGET</th>
                                        <th class="text-right" style="width: 160px;">REALISASI DANA</th>
                                        <th class="text-center" style="width: 150px;">STATUS ACC</th>
                                        <th class="text-center" style="width: 140px;">STATUS BELI</th>
                                        <th class="text-center">
                                            BUKTI TRANSFER
                                            <div class="text-white-50"
                                                style="font-size: 0.65rem; font-weight: 400; margin-top: 2px;">Max. 2
                                                Gambar</div>
                                        </th>
                                        <th class="text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="pengadaan-table-body">
                                    @php $totalBudgetPengadaan = 0; @endphp
                                    @forelse($pengadaanBarang as $key => $item)
                                        @continue($item->is_inventory_created)
                                        @php $totalBudgetPengadaan += (float) $item->budget; @endphp
                                        <tr data-id="{{ $item->id }}">
                                            <td class="text-center no-col">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-start" style="gap:4px;">
                                                    <textarea class="form-control-inline font-weight-bold pengadaan-live-edit auto-resize" data-field="nama_barang"
                                                        rows="1" placeholder="(Tulis Nama Barang)">{{ $item->nama_barang }}</textarea>
                                                    @if ($item->is_inventory_created)
                                                        <span class="badge-masuk-inventaris"
                                                            title="Sudah masuk ke Inventaris Kantor"
                                                            style="color:#1cc88a; font-size:0.8rem; flex-shrink:0; margin-top:3px;">
                                                            <i class="fas fa-check-circle"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <textarea class="form-control-inline text-center pengadaan-live-edit auto-resize" data-field="jumlah" rows="1">{{ $item->jumlah }}</textarea>
                                            </td>
                                            <td class="text-right">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span class="mr-1 text-success font-weight-bold">Rp</span>
                                                    <input type="text"
                                                        class="form-control-inline text-right text-success font-weight-bold pengadaan-live-edit pengadaan-budget-input"
                                                        data-field="budget"
                                                        value="{{ number_format((float) $item->budget, 0, ',', '.') }}">
                                                </div>
                                            </td>
                                            {{-- REALISASI DANA: diisi oleh Linda saat approve, read-only di operasional --}}
                                            <td class="text-right align-middle">
                                                @if ($item->realisasi_dana !== null)
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <span class="mr-1 text-primary font-weight-bold">Rp</span>
                                                        <span
                                                            class="font-weight-bold text-primary">{{ number_format((float) $item->realisasi_dana, 0, ',', '.') }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted font-italic"
                                                        style="font-size: 0.78rem; opacity: 0.6;">Menunggu
                                                        persetujuan</span>
                                                @endif
                                            </td>
                                            {{-- ACC: Read-only badge, dikontrol oleh Linda via Pengajuan Anggaran --}}
                                            <td class="text-center align-middle">
                                                @php
                                                    $acc = $item->acc ?? 'Pending';
                                                    if ($acc === 'Iya') {
                                                        $accBadgeStyle = 'background:#1cc88a; color:#fff;';
                                                        $accIcon = 'fa-check-circle';
                                                        $accLabel = 'Disetujui';
                                                    } elseif ($acc === 'Tidak') {
                                                        $accBadgeStyle = 'background:#e74a3b; color:#fff;';
                                                        $accIcon = 'fa-times-circle';
                                                        $accLabel = 'Ditolak';
                                                    } elseif ($acc === 'Belum Lunas') {
                                                        $accBadgeStyle = 'background:#36b9cc; color:#fff;';
                                                        $accIcon = 'fa-hand-holding-usd';
                                                        $accLabel = 'Belum Lunas';
                                                    } else {
                                                        $accBadgeStyle = 'background:#f6c23e; color:#000;';
                                                        $accIcon = 'fa-clock';
                                                        $accLabel = 'Menunggu ACC';
                                                    }
                                                @endphp
                                                <span class="badge shadow-sm px-3 py-2"
                                                    style="{{ $accBadgeStyle }} border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                                                    <i class="fas {{ $accIcon }} mr-1"></i>{{ $accLabel }}
                                                </span>
                                            </td>
                                            {{-- STATUS BELI: hanya aktif jika ACC = Iya --}}
                                            <td class="text-center align-middle">
                                                @php
                                                    $statusBeli = $item->status_beli ?? 'Belum Dibeli';
                                                    $isApproved = $item->acc === 'Iya';
                                                    $statusBeliStyle =
                                                        $statusBeli === 'Sudah Dibeli'
                                                            ? 'background:#1cc88a; color:#fff;'
                                                            : 'background:#fd7e14; color:#fff;';
                                                @endphp
                                                @if ($isApproved)
                                                    <select class="pengadaan-status-beli-select"
                                                        data-id="{{ $item->id }}"
                                                        style="{{ $statusBeliStyle }} border: none; border-radius: 20px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 10px; cursor: pointer; outline: none; -webkit-appearance: none; appearance: none; box-shadow: 0 1px 3px rgba(0,0,0,0.15); text-align: center; text-align-last: center; width: 120px;">
                                                        <option value="Belum Dibeli"
                                                            {{ $statusBeli === 'Belum Dibeli' ? 'selected' : '' }}>Belum
                                                            Dibeli</option>
                                                        <option value="Sudah Dibeli"
                                                            {{ $statusBeli === 'Sudah Dibeli' ? 'selected' : '' }}>Sudah
                                                            Dibeli</option>
                                                    </select>
                                                @else
                                                    <span class="badge shadow-sm px-3 py-2"
                                                        style="background:#fd7e14; color:#fff; border-radius: 20px; font-size: 0.72rem; font-weight: 700; cursor: not-allowed; opacity: 0.7;">
                                                        Belum Dibeli
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle bukti-transfer-cell"
                                                style="min-width: 120px;">
                                                @php $fotos = $item->buktiFotos; @endphp
                                                @if ($fotos->isNotEmpty())
                                                    <div class="d-flex flex-wrap justify-content-center align-items-center"
                                                        style="gap: 4px;">
                                                        @foreach ($fotos as $foto)
                                                            @php
                                                                $isPdf =
                                                                    strtolower(
                                                                        pathinfo($foto->file_path, PATHINFO_EXTENSION),
                                                                    ) === 'pdf';
                                                            @endphp
                                                            @if ($isPdf)
                                                                <a href="{{ asset($foto->file_path) }}" target="_blank"
                                                                    class="text-danger" title="Lihat PDF"
                                                                    style="display:inline-block; position:relative; width:44px; height:44px; text-align:center; vertical-align:middle; line-height:44px; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                                                                    <i class="far fa-file-pdf fa-2x"
                                                                        style="vertical-align:middle;"></i>
                                                                    <button type="button"
                                                                        class="btn-hapus-bukti-foto position-absolute"
                                                                        style="top:-5px; right:-5px; width:16px; height:16px; border-radius:50%; background:#e74a3b; border:none; color:#fff; font-size:9px; line-height:1; padding:0; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                                                                        data-id="{{ $item->id }}"
                                                                        data-bukti-id="{{ $foto->id }}"
                                                                        title="Hapus foto ini"
                                                                        onclick="event.stopPropagation();">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </a>
                                                            @else
                                                                <div class="position-relative d-inline-block bukti-foto-item"
                                                                    data-bukti-id="{{ $foto->id }}">
                                                                    <img src="{{ asset($foto->file_path) }}"
                                                                        alt="Bukti"
                                                                        class="img-thumbnail shadow-sm preview-image"
                                                                        style="width: 44px; height: 44px; object-fit: cover; cursor: pointer;"
                                                                        onclick="previewImage('{{ asset($foto->file_path) }}', 'Bukti Transfer - {{ $item->nama_barang }}')"
                                                                        title="Klik untuk memperbesar">
                                                                    <button type="button"
                                                                        class="btn-hapus-bukti-foto position-absolute"
                                                                        style="top:-5px; right:-5px; width:16px; height:16px; border-radius:50%; background:#e74a3b; border:none; color:#fff; font-size:9px; line-height:1; padding:0; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                                                                        data-id="{{ $item->id }}"
                                                                        data-bukti-id="{{ $foto->id }}"
                                                                        title="Hapus foto ini">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        {{-- Tombol + untuk tambah foto, hanya jika belum 2 --}}
                                                        @if ($fotos->count() < 2)
                                                            <button type="button"
                                                                class="btn-tambah-bukti-foto d-flex align-items-center justify-content-center"
                                                                style="width: 22px; height: 22px; border-radius: 50%; border: none; background: #4e73df; color: #fff; cursor: pointer; font-size: 0.7rem; flex-shrink:0; padding:0;"
                                                                data-id="{{ $item->id }}"
                                                                data-nama="{{ $item->nama_barang }}"
                                                                title="Tambah Foto Bukti">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="d-flex flex-column align-items-center">
                                                        <button type="button"
                                                            class="btn-tambah-bukti-foto btn btn-outline-primary btn-xs px-2 shadow-sm font-weight-bold"
                                                            data-id="{{ $item->id }}"
                                                            data-nama="{{ $item->nama_barang }}"
                                                            style="font-size: 0.7rem; padding: 2px 5px;">
                                                            <i class="fas fa-upload mr-1"></i> Upload
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-link text-danger p-0 delete-pengadaan-btn"
                                                    data-id="{{ $item->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-pengadaan-row">
                                            <td colspan="8" class="text-center py-5 text-muted">Belum ada data
                                                pengadaan barang.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f8f9fc;">
                                        <td colspan="3" class="text-right font-weight-bold">TOTAL ESTIMASI BUDGET
                                            PENGADAAN</td>
                                        <td class="text-right font-weight-bold text-danger" style="font-size: 1.1rem;">
                                            Rp <span
                                                id="grand-total-budget-pengadaan">{{ number_format($totalBudgetPengadaan, 0, ',', '.') }}</span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="pengadaan-pagination" class="mt-3 d-flex justify-content-center"></div>
                    </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Bukti Khusus -->
    <div class="modal fade" id="modalUploadBukti" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg text-left">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-upload mr-2"></i>Upload Bukti Transfer</h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="formUploadBukti" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="mb-3 text-dark">Mengunggah bukti untuk: <strong id="namaPengajuanUpload"
                                class="text-success"></strong></p>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark">Pilih File Bukti (Gambar)</label>
                            <div class="custom-file">
                                <input type="file" name="bukti_transfer" class="custom-file-input"
                                    id="inputUploadBukti" accept="image/*" required>
                                <label class="custom-file-label" for="inputUploadBukti">Pilih file gambar...</label>
                            </div>
                            <div class="mt-2 small text-muted">
                                <i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG, JPEG. Maks: 2MB.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button class="btn btn-secondary btn-sm px-4" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-success btn-sm px-4 shadow-sm" type="submit">
                            <i class="fas fa-cloud-upload-alt mr-1"></i> Simpan Bukti
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Preview Gambar -->
    <div class="modal fade" id="modalPreviewImage" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg bg-transparent">
                <div class="modal-header border-0 bg-dark text-white rounded-top" style="opacity: 0.9;">
                    <h5 class="modal-title" id="previewTitle">Bukti Transfer</h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-0 bg-dark rounded-bottom" style="opacity: 0.9;">
                    <img id="imageFullPreview" src="" class="img-fluid w-100 rounded-bottom" alt="Preview">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Teks (Kerusakan / Rencana) -->
    <div id="detail-popup-overlay" onclick="closeDetailPopup(event)">
        <div id="detail-popup-box">
            <div class="popup-header">
                <span class="popup-title" id="detail-popup-title">Detail</span>
                <button class="popup-close" onclick="closeDetailPopup(null)" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="popup-body" id="detail-popup-content"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ====================== CLIENT-SIDE PAGINATION SYSTEM ======================
            const tablePages = {};

            function renderPaginationHTML(totalPages, currentPage) {
                let html = '<ul class="pagination pagination-sm mb-0 shadow-sm rounded">';
                const prevDisabled = currentPage === 1 ? 'disabled' : '';
                html +=
                    `<li class="page-item ${prevDisabled}"><a class="page-link" href="#" data-page="${currentPage - 1}">‹</a></li>`;

                let pages = [];
                if (totalPages <= 12) {
                    for (let i = 1; i <= totalPages; i++) pages.push(i);
                } else {
                    if (currentPage <= 8) {
                        for (let i = 1; i <= 10; i++) pages.push(i);
                        pages.push('...');
                        pages.push(totalPages - 1);
                        pages.push(totalPages);
                    } else if (currentPage >= totalPages - 7) {
                        pages.push(1);
                        pages.push(2);
                        pages.push('...');
                        for (let i = totalPages - 9; i <= totalPages; i++) pages.push(i);
                    } else {
                        pages.push(1);
                        pages.push(2);
                        pages.push('...');
                        for (let i = currentPage - 3; i <= currentPage + 3; i++) pages.push(i);
                        pages.push('...');
                        pages.push(totalPages - 1);
                        pages.push(totalPages);
                    }
                }

                pages.forEach(p => {
                    if (p === '...') {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    } else {
                        const activeClass = p === currentPage ? 'active' : '';
                        html +=
                            `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
                    }
                });

                const nextDisabled = currentPage === totalPages ? 'disabled' : '';
                html +=
                    `<li class="page-item ${nextDisabled}"><a class="page-link" href="#" data-page="${currentPage + 1}">›</a></li>`;
                html += '</ul>';
                return html;
            }

            function handleEmptyFilteredRows(tableBodyId, totalActive, totalOriginal) {
                const tableBody = $(`#${tableBodyId}`);
                tableBody.find('.filtered-empty-row, .filtered-empty-inventaris-row').remove();

                if (totalActive === 0 && totalOriginal > 0) {
                    let colspan = 8;
                    let msg = 'Tidak ada data sesuai filter.';
                    let className = 'filtered-empty-row';

                    if (tableBodyId === 'inventaris-table-body') {
                        colspan = 7;
                        const statusFilter = $('#filter-status-inventaris').val() || 'all';
                        const lokasiFilter = $('#filter-lokasi-inventaris').val() || 'all';
                        const peralatanFilter = ($('#filter-peralatan-inventaris').val() || '').trim();

                        let filtersApplied = [];
                        if (statusFilter !== 'all') filtersApplied.push(`status "${statusFilter}"`);
                        if (lokasiFilter !== 'all') filtersApplied.push(`lokasi "${lokasiFilter}"`);
                        if (peralatanFilter !== '') filtersApplied.push(`peralatan "${peralatanFilter}"`);

                        msg = 'Tidak ada data inventaris';
                        if (filtersApplied.length > 0) {
                            msg += ` dengan ${filtersApplied.join(', ')}`;
                        }
                        msg += '.';
                        className = 'filtered-empty-inventaris-row';
                    } else if (tableBodyId === 'monitoring-table-body') {
                        colspan = 9;
                        const filterValue = $('#filter-progress').val() || 'all';
                        msg = `Tidak ada data perbaikan dengan progress "${filterValue}".`;
                        className = 'filtered-empty-row';
                    } else if (tableBodyId === 'pengadaan-table-body') {
                        colspan = 8;
                        msg = 'Tidak ada data pengadaan sesuai filter.';
                        className = 'filtered-empty-row';
                    }

                    tableBody.append(`
                    <tr class="${className}">
                        <td colspan="${colspan}" class="text-center py-5 text-muted">${msg}</td>
                    </tr>
                `);
                }
            }

            window.updatePagination = function(tableBodyId, paginationContainerId, forcePage = null) {
                const tableBody = $(`#${tableBodyId}`);
                const paginationContainer = $(`#${paginationContainerId}`);
                const itemsPerPage = 20;

                const allRows = tableBody.find('tr').not(
                    '.empty-row, .empty-inventaris-row, .empty-pengadaan-row, .filtered-empty-row, .filtered-empty-inventaris-row'
                );
                const activeRows = allRows.not('.filtered-out');
                const totalActive = activeRows.length;

                handleEmptyFilteredRows(tableBodyId, totalActive, allRows.length);

                if (totalActive <= itemsPerPage) {
                    paginationContainer.html('').hide();
                    tablePages[tableBodyId] = 1;
                    activeRows.show();
                    allRows.filter('.filtered-out').hide();

                    activeRows.each(function(index) {
                        $(this).find('.no-col').text(index + 1);
                    });

                    if (tableBodyId === 'monitoring-table-body') updateGrandTotal();
                    if (tableBodyId === 'pengadaan-table-body') updateGrandTotalPengadaan();
                    return;
                }

                paginationContainer.show();

                const totalPages = Math.ceil(totalActive / itemsPerPage);
                let currentPage = tablePages[tableBodyId] || 1;
                if (forcePage !== null) {
                    currentPage = forcePage;
                }
                if (currentPage > totalPages) {
                    currentPage = totalPages;
                }
                if (currentPage < 1) {
                    currentPage = 1;
                }
                tablePages[tableBodyId] = currentPage;

                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;

                allRows.hide();

                activeRows.each(function(index) {
                    const tr = $(this);
                    if (index >= startIndex && index < endIndex) {
                        tr.show();
                    }
                    tr.find('.no-col').text(index + 1);
                });

                const paginationHTML = renderPaginationHTML(totalPages, currentPage);
                paginationContainer.html(paginationHTML);

                paginationContainer.find('.page-link').off('click').on('click', function(e) {
                    e.preventDefault();
                    const clickedPage = $(this).data('page');
                    if (clickedPage && !$(this).parent().hasClass('disabled')) {
                        updatePagination(tableBodyId, paginationContainerId, clickedPage);
                    }
                });

                if (tableBodyId === 'monitoring-table-body') updateGrandTotal();
                if (tableBodyId === 'pengadaan-table-body') updateGrandTotalPengadaan();
            };

            // ====================== AUTO RESIZE TEXTAREA FUNCTIONS ======================
            function resizeTextarea(el) {
                if (!el) return;
                el.style.height = 'auto';
                el.style.height = el.scrollHeight + 'px';
            }

            // Initialize auto-resize for all textareas on load
            setTimeout(function() {
                $('.auto-resize').each(function() {
                    resizeTextarea(this);
                });
            }, 600);

            // Recalculate heights when tabs are shown
            $('button[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $('.auto-resize').each(function() {
                    resizeTextarea(this);
                });
            });

            // Dynamic resize on input
            $(document).on('input', '.auto-resize', function() {
                resizeTextarea(this);
            });

            // ====================== SELECT2 INVENTARIS INITIALIZATION ======================
            function initSelect2(element) {
                // Destroy previous instance if any (safe for re-init)
                if (element.hasClass('select2-hidden-accessible')) {
                    element.select2('destroy');
                }
                element.select2({
                    theme: 'bootstrap4',
                    placeholder: '-- Cari fasilitas... --',
                    allowClear: true,
                    dropdownParent: $('body'),
                    dropdownAutoWidth: false,
                    width: '100%',
                    minimumInputLength: 0,
                    ajax: {
                        url: "{{ route('inventaris-kantor.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term || ''
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.results
                            };
                        },
                        cache: true
                    }
                });
            }

            // Initialize Select2 on document ready for existing rows
            initSelect2($('.select2-inventaris'));

            // Toggle to manual input field
            $(document).on('click', '.btn-switch-to-manual', function() {
                const td = $(this).closest('td');
                const selectContainer = td.find('.inventaris-select-container');
                const manualContainer = td.find('.fasilitas-manual-container');
                const manualInput = td.find('.manual-input');
                const selectEl = td.find('.select2-inventaris');

                selectContainer.hide();
                manualContainer.show();

                // Reset select2 value
                selectEl.val(null).trigger('change.select2');

                setTimeout(function() {
                    manualInput.focus();
                }, 50);
            });

            // Toggle to select2 search
            $(document).on('click', '.btn-switch-to-select', function() {
                const td = $(this).closest('td');
                const selectContainer = td.find('.inventaris-select-container');
                const manualContainer = td.find('.fasilitas-manual-container');
                const manualInput = td.find('.manual-input');
                const selectEl = td.find('.select2-inventaris');

                manualContainer.hide();
                selectContainer.show();

                // Clear manual input and trigger update
                manualInput.val('');
                manualInput.trigger('change');

                // Re-initialize Select2 and open dropdown
                initSelect2(selectEl);
                setTimeout(function() {
                    selectEl.select2('open');
                }, 50);
            });

            // ====================== LIVE EDIT MONITORING ======================

            // 1. Add New Row - Refresh-less
            $('#btnTambahPerbaikanInline').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menambah...');

                $.ajax({
                    url: "{{ route('monitoring-perbaikan.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        inventaris_id: "",
                        fasilitas_manual: "",
                        progress: "Pengajuan dana"
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-plus mr-1"></i> Perbaikan');
                        $('.empty-row').remove();

                        const item = res.data; // Assuming controller returns the object
                        const rowCount = $('#monitoring-table-body tr').length + 1;

                        const newRow = `
                        <tr data-id="${item.id}">
                            <td class="text-center no-col">${rowCount}</td>
                            <td>
                                <div class="fasilitas-cell-wrapper">
                                    {{-- SELECT2 container (hidden by default on new rows) --}}
                                    <div class="inventaris-select-container" style="display:none;">
                                        <div class="input-group input-group-sm">
                                            <div style="flex-grow: 1;">
                                                <select class="form-control form-control-sm select2-inventaris live-edit" data-field="inventaris_id" style="width:100%;">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                            <div class="input-group-append">
                                                <button class="btn btn-switch-to-manual" type="button" title="Ketik manual">
                                                    <i class="fas fa-keyboard"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- MANUAL INPUT container (shown by default on new rows) --}}
                                    <div class="fasilitas-manual-container" style="display:block;">
                                        <div class="input-group input-group-sm">
                                            <input type="text"
                                                   class="form-control form-control-sm live-edit manual-input"
                                                   data-field="fasilitas_manual"
                                                   value=""
                                                   placeholder="Tulis nama fasilitas..."
                                                   autocomplete="off">
                                            <div class="input-group-append">
                                                <button class="btn btn-switch-to-select" type="button" title="Pilih dari inventaris">
                                                    <i class="fas fa-list"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="deadline-label">Deadline</div>
                                <input type="date" class="form-control-inline text-center live-edit" data-field="tanggal_selesai" style="width:110px; font-size:0.72rem;">
                            </td>
                            <td class="text-truncate-cell">
                                <textarea class="form-control-inline live-edit auto-resize" data-field="kerusakan" rows="1" spellcheck="false"></textarea>
                                <button class="btn-lihat-detail mt-1" onclick="openDetailPopup('Kerusakan', this.previousElementSibling.value)">Lihat Detail</button>
                            </td>
                            <td class="text-center">
                                <select class="form-control-inline live-edit status-dropdown bg-status-pengajuan-dana" data-field="progress">
                                    <option value="Pengajuan dana" selected class="bg-status-pengajuan-dana">Pengajuan dana</option>
                                    <option value="Pengerjaan" class="bg-status-on-progress">Pengerjaan</option>
                                    <option value="Selesai" class="bg-status-selesai">Selesai</option>
                                </select>
                            </td>
                            <td class="text-right">
                                <div class="d-flex align-items-center justify-content-end">
                                    <span class="mr-1 text-success font-weight-bold">Rp</span>
                                    <input type="text" class="form-control-inline text-right text-success font-weight-bold live-edit budget-input" data-field="budget" value="0">
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="d-flex align-items-center justify-content-end">
                                    <span class="mr-1 text-success font-weight-bold">Rp</span>
                                    <input type="text" class="form-control-inline text-right text-success font-weight-bold live-edit realisasi-input" data-field="realisasi_dana" value="0">
                                </div>
                            </td>
                            <td class="text-center align-middle bukti-transfer-cell">
                                <div class="d-flex flex-column align-items-center">
                                    <button type="button" class="btn btn-outline-primary btn-xs px-2 shadow-sm font-weight-bold"
                                        onclick="openUploadModal(${item.id}, 'Perbaikan', 'monitoring')" style="font-size: 0.7rem; padding: 2px 5px;">
                                        <i class="fas fa-upload mr-1"></i> Upload
                                    </button>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-link text-danger p-0 delete-btn" data-id="${item.id}"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    `;
                        $('#monitoring-table-body').prepend(newRow);

                        // Initialize Select2 on the new row
                        initSelect2($('#monitoring-table-body tr').first().find(
                            '.select2-inventaris'));

                        // Re-index numbers
                        $('#monitoring-table-body tr').each(function(index) {
                            $(this).find('.no-col').text(index + 1);
                        });

                        // Trigger filter check to position the new row appropriately
                        $('#filter-progress').trigger('change');

                        setTimeout(() => {
                            $('#monitoring-table-body tr').first().find('.auto-resize')
                                .each(function() {
                                    resizeTextarea(this);
                                });
                        }, 50);
                    },
                    error: function() {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-plus mr-1"></i> Pilih perbaikan');
                    }
                });
            });

            // 2. Live Update on Blur or Change
            $(document).on('change', '.live-edit', function() {
                const input = $(this);
                const tr = input.closest('tr');
                const id = tr.data('id');
                const field = input.data('field');
                let value = input.val();

                // Format budget or realisasi input display nicely on change
                if (field === 'budget' || field === 'realisasi_dana') {
                    let cleanVal = value.toString().replace(/\D/g, '');
                    value = cleanVal ? new Intl.NumberFormat('id-ID').format(cleanVal) : '0';
                    input.val(value);
                }

                // Show temporary loading indicator or style
                input.css('background-color', '#fff9db');

                let sendValue = value;
                if (field === 'budget' || field === 'realisasi_dana') {
                    sendValue = value.toString().replace(/\./g, '');
                }

                // Helper to get raw numeric values for request payload
                const getRawValue = (elField) => {
                    let raw = tr.find(`[data-field="${elField}"]`).val() || '';
                    if (elField === 'budget' || elField === 'realisasi_dana') {
                        raw = raw.toString().replace(/\./g, '');
                    }
                    return raw;
                };

                $.ajax({
                    url: "/monitoring-perbaikan/update/" + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "PUT",
                        [field]: sendValue,
                        inventaris_id: getRawValue('inventaris_id'),
                        fasilitas_manual: getRawValue('fasilitas_manual'),
                        kerusakan: getRawValue('kerusakan'),
                        tanggal_mulai: getRawValue('tanggal_mulai'),
                        tanggal_selesai: getRawValue('tanggal_selesai'),
                        progress: getRawValue('progress'),
                        budget: getRawValue('budget'),
                        realisasi_dana: getRawValue('realisasi_dana'),
                    },
                    success: function(res) {
                        input.css('background-color', 'transparent');
                        updateGrandTotal();

                        // Update dropdown color if it was a status change
                        if (field === 'progress') {
                            input.removeClass(
                                'bg-status-pengajuan-dana bg-status-cari-vendor bg-status-pending bg-status-on-progress bg-status-selesai'
                            );
                            if (value === 'Pengajuan dana' || value === 'Pengajuan Dana') {
                                input.addClass('bg-status-pengajuan-dana');
                            } else if (value === 'Pengerjaan' || value === 'On Progress' ||
                                value === 'Cari Vendor') {
                                input.addClass('bg-status-on-progress');
                            } else if (value === 'Selesai') {
                                input.addClass('bg-status-selesai');
                            } else {
                                input.addClass('bg-status-pending');
                            }

                            // Trigger dynamic row filtering if progress changed
                            $('#filter-progress').trigger('change');
                        }

                        // Toast notification
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Tersimpan'
                        });
                    },
                    error: function() {
                        input.css('background-color', '#ffe3e3');
                        Swal.fire('Error', 'Gagal menyimpan perubahan', 'error');
                    }
                });
            });

            // 3. Update Grand Total locally (based on active filtered items)
            function updateGrandTotal() {
                let totalBudget = 0;
                let totalRealisasi = 0;
                $('#monitoring-table-body tr').not('.filtered-out, .empty-row, .filtered-empty-row').each(
                    function() {
                        let budgetValStr = $(this).find('.budget-input').val() || '0';
                        let budgetValNum = parseFloat(budgetValStr.replace(/\./g, '')) || 0;
                        totalBudget += budgetValNum;

                        let realisasiValStr = $(this).find('.realisasi-input').val() || '0';
                        let realisasiValNum = parseFloat(realisasiValStr.replace(/\./g, '')) || 0;
                        totalRealisasi += realisasiValNum;
                    });
                $('#grand-total-budget').text(new Intl.NumberFormat('id-ID').format(totalBudget));
                $('#grand-total-realisasi').text(new Intl.NumberFormat('id-ID').format(totalRealisasi));
            }

            // ====================== FILTER MONITORING (PROGRESS + TIMELINE TERINTEGRASI) ======================

            // Fungsi utama filter monitoring — gabungkan progress + timeline
            function applyMonitoringFilter() {
                const progressFilter = $('#filter-progress').val() || 'all';
                const filterDate = $('#filter-timeline-date').val();
                const fasilitasSearch = $('#filter-fasilitas-search').val().toLowerCase().trim();

                // Konversi ke objek Date untuk perbandingan akurat (hanya jika diisi)
                const targetDate = filterDate ? new Date(filterDate) : null;

                $('#monitoring-table-body tr').each(function() {
                    const tr = $(this);
                    if (tr.hasClass('empty-row') || tr.hasClass('filtered-empty-row')) return;

                    const rowProgress = tr.find('[data-field="progress"]').val();
                    const rowDeadline = tr.find('[data-field="tanggal_selesai"]').val();

                    let rowFasilitas = '';
                    const selectEl = tr.find('[data-field="inventaris_id"]');
                    const isManualVisible = tr.find('.fasilitas-manual-container').is(':visible');
                    if (isManualVisible || !selectEl.val()) {
                        rowFasilitas = tr.find('[data-field="fasilitas_manual"]').val() || '';
                    } else {
                        rowFasilitas = selectEl.find('option:selected').text() || '';
                    }
                    rowFasilitas = rowFasilitas.toLowerCase();

                    let keep = true;

                    // Filter progress
                    if (progressFilter !== 'all') {
                        const normFilter = progressFilter.toLowerCase();
                        const normRow = (rowProgress || '').toLowerCase();
                        let isMatch = false;
                        if (normFilter === 'pengajuan dana' && (normRow === 'pengajuan dana' || normRow ===
                                'pengajuan dana')) {
                            isMatch = true;
                        } else if (normFilter === 'pengerjaan' && (normRow === 'pengerjaan' || normRow ===
                                'on progress' || normRow === 'cari vendor')) {
                            isMatch = true;
                        } else if (normFilter === 'selesai' && normRow === 'selesai') {
                            isMatch = true;
                        } else if (normRow === normFilter) {
                            isMatch = true;
                        }
                        if (!isMatch) {
                            keep = false;
                        }
                    }

                    // Filter deadline — tampilkan hanya data dengan tanggal_selesai yang sama
                    if (keep && targetDate) {
                        const deadline = rowDeadline ? new Date(rowDeadline) : null;

                        if (deadline) {
                            // Bandingkan tanggal saja (abaikan waktu)
                            if (deadline.toDateString() !== targetDate.toDateString()) {
                                keep = false;
                            }
                        } else {
                            // Jika baris tidak punya deadline, sembunyikan saat filter aktif
                            keep = false;
                        }
                    }

                    // Filter fasilitas (live search)
                    if (keep && fasilitasSearch !== '') {
                        if (!rowFasilitas.includes(fasilitasSearch)) {
                            keep = false;
                        }
                    }

                    if (keep) {
                        tr.removeClass('filtered-out');
                    } else {
                        tr.addClass('filtered-out');
                    }
                });

                updatePagination('monitoring-table-body', 'monitoring-pagination', 1);
                updateGrandTotal();
            }

            // Filter progress sudah dipindah ke handler di atas (baris 2930)
            // Tidak perlu handler terpisah lagi

            // 4. Delete Data - Refresh-less
            $(document).on('click', '.delete-btn', function() {
                const btn = $(this);
                const tr = btn.closest('tr');
                const id = btn.data('id');

                Swal.fire({
                    title: 'Hapus data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/monitoring-perbaikan/destroy/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                tr.fadeOut(400, function() {
                                    $(this).remove();
                                    updatePagination('monitoring-table-body',
                                        'monitoring-pagination');

                                    if ($('#monitoring-table-body tr').not(
                                            '.empty-row, .filtered-empty-row')
                                        .length === 0) {
                                        $('#monitoring-table-body').append(`
                                        <tr class="empty-row">
                                            <td colspan="9" class="text-center py-5 text-muted">Belum ada data perbaikan.</td>
                                        </tr>
                                    `);
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // ====================== LIVE EDIT PENGADAAN BARANG ======================

            // 1. Add New Row - Refresh-less
            $('#btnTambahPengadaanInline').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menambah...');

                $.ajax({
                    url: "{{ route('pengadaan-barang.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        nama_barang: "",
                        progress: "Pending"
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-plus mr-1"></i> Tambah Barang');
                        $('.empty-pengadaan-row').remove();

                        const item = res.data;
                        const rowCount = $('#pengadaan-table-body tr').length + 1;

                        const newRow = `
                        <tr data-id="${item.id}">
                            <td class="text-center no-col">${rowCount}</td>
                            <td><textarea class="form-control-inline font-weight-bold pengadaan-live-edit auto-resize" data-field="nama_barang" rows="1" placeholder="(Tulis Nama Barang)">${item.nama_barang || ''}</textarea></td>
                            <td><textarea class="form-control-inline text-center pengadaan-live-edit auto-resize" data-field="jumlah" rows="1"></textarea></td>
                            <td class="text-right">
                                <div class="d-flex align-items-center justify-content-end">
                                    <span class="mr-1 text-success font-weight-bold">Rp</span>
                                    <input type="text" class="form-control-inline text-right text-success font-weight-bold pengadaan-live-edit pengadaan-budget-input" data-field="budget" value="0">
                                </div>
                            </td>
                            <td class="text-right align-middle">
                                <span class="text-muted font-italic" style="font-size: 0.78rem; opacity: 0.6;">Menunggu persetujuan</span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge shadow-sm px-3 py-2"
                                    style="background:#f6c23e; color:#000; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                                    <i class="fas fa-clock mr-1"></i>Menunggu ACC
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge shadow-sm px-3 py-2"
                                    style="background:#fd7e14; color:#fff; border-radius: 20px; font-size: 0.72rem; font-weight: 700; cursor: not-allowed; opacity: 0.7;">
                                    Belum Dibeli
                                </span>
                            </td>
                            <td class="text-center align-middle bukti-transfer-cell" style="min-width: 120px;">
                                <div class="d-flex flex-column align-items-center">
                                    <button type="button" class="btn-tambah-bukti-foto btn btn-outline-primary btn-xs px-2 shadow-sm font-weight-bold"
                                        data-id="${item.id}"
                                        data-nama="${item.nama_barang || ''}"
                                        style="font-size: 0.7rem; padding: 2px 5px;">
                                        <i class="fas fa-upload mr-1"></i> Upload
                                    </button>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-link text-danger p-0 delete-pengadaan-btn" data-id="${item.id}"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    `;
                        $('#pengadaan-table-body').prepend(newRow);

                        setTimeout(() => {
                            $('#pengadaan-table-body tr').first().find('.auto-resize')
                                .each(function() {
                                    resizeTextarea(this);
                                });
                        }, 50);
                    },
                    error: function() {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-plus mr-1"></i> Tambah Barang');
                    }
                });
            });

            // 2. Live Update on Blur or Change
            $(document).on('change', '.pengadaan-live-edit', function() {
                const input = $(this);
                const tr = input.closest('tr');
                const id = tr.data('id');
                const field = input.data('field');
                let value = input.val();

                // Format budget input display nicely on change
                if (field === 'budget') {
                    let cleanVal = value.toString().replace(/\D/g, '');
                    value = cleanVal ? new Intl.NumberFormat('id-ID').format(cleanVal) : '0';
                    input.val(value);
                }

                input.css('background-color', '#fff9db');

                let sendValue = value;
                if (field === 'budget') {
                    sendValue = value.toString().replace(/\./g, '');
                }

                $.ajax({
                    url: "/pengadaan-barang/update/" + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "PUT",
                        [field]: sendValue
                    },
                    success: function(res) {
                        input.css('background-color', 'transparent');
                        updateGrandTotalPengadaan();

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Tersimpan'
                        });
                    },
                    error: function() {
                        input.css('background-color', '#ffe3e3');
                        Swal.fire('Error', 'Gagal menyimpan perubahan', 'error');
                    }
                });
            });

            // 3. Update Grand Total locally
            // function updateGrandTotalPengadaan() {
            //     let total = 0;
            //     $('.pengadaan-budget-input').each(function() {
            //         let valStr = $(this).val() || '0';
            //         let valNum = parseFloat(valStr.replace(/\./g, '')) || 0;
            //         total += valNum;
            //     });
            //     $('#grand-total-budget-pengadaan').text(new Intl.NumberFormat('id-ID').format(total));
            // }

            // 3. Update Grand Total locally (based on active filtered items)
            function updateGrandTotalPengadaan() {
                let total = 0;
                // Use all rows (including those in hidden tab) — check data-id exists to skip empty/filter rows
                $('#pengadaan-table-body tr[data-id]').not(
                    '.filtered-out, .empty-pengadaan-row, .filtered-empty-row').find(
                    '.pengadaan-budget-input').each(function() {
                    let valStr = $(this).val() || '0';
                    let valNum = parseFloat(valStr.replace(/\./g, '')) || 0;
                    total += valNum;
                });
                $('#grand-total-budget-pengadaan').text(new Intl.NumberFormat('id-ID').format(total));
            }

            // 4. Delete Data - Refresh-less
            $(document).on('click', '.delete-pengadaan-btn', function() {
                const btn = $(this);
                const tr = btn.closest('tr');
                const id = btn.data('id');

                Swal.fire({
                    title: 'Hapus data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/pengadaan-barang/destroy/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                tr.fadeOut(400, function() {
                                    $(this).remove();
                                    updatePagination('pengadaan-table-body',
                                        'pengadaan-pagination');

                                    if ($('#pengadaan-table-body tr').not(
                                            '.empty-pengadaan-row, .filtered-empty-row'
                                        ).length === 0) {
                                        $('#pengadaan-table-body').append(`
                                        <tr class="empty-pengadaan-row">
                                            <td colspan="8" class="text-center py-5 text-muted">Belum ada data pengadaan barang.</td>
                                        </tr>
                                    `);
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // ====================== BUKTI FOTO PENGADAAN ======================

            // Input file tersembunyi untuk tambah foto
            $('body').append(
                '<input type="file" id="inputTambahBuktiFoto" accept="image/*,.pdf" style="display:none;">');

            var _currentBuktiId = null;
            var _currentBuktiNama = null;

            // Klik tombol + / Upload
            $(document).on('click', '.btn-tambah-bukti-foto', function() {
                _currentBuktiId = $(this).data('id');
                _currentBuktiNama = $(this).data('nama');

                // Enforce limit 2 foto
                const cell = $('#pengadaan-table-body tr[data-id="' + _currentBuktiId +
                    '"] .bukti-transfer-cell');
                const jumlahFoto = cell.find('.bukti-foto-item').length;
                if (jumlahFoto >= 2) {
                    Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        })
                        .fire({
                            icon: 'warning',
                            title: 'Maksimal 2 gambar'
                        });
                    return;
                }

                $('#inputTambahBuktiFoto').val('').trigger('click');
            });

            // Saat file dipilih, langsung upload
            $(document).on('change', '#inputTambahBuktiFoto', function() {
                const file = this.files[0];
                if (!file || !_currentBuktiId) return;

                const formData = new FormData();
                formData.append('bukti_transfer', file);
                formData.append('_token', '{{ csrf_token() }}');

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });

                $.ajax({
                    url: '/pengadaan-barang/' + _currentBuktiId + '/add-bukti',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (!res.success) {
                            Swal.fire('Gagal', res.message || 'Gagal upload foto', 'error');
                            return;
                        }

                        const cell = $('#pengadaan-table-body tr[data-id="' + _currentBuktiId +
                            '"] .bukti-transfer-cell');
                        const addBtn = cell.find('.btn-tambah-bukti-foto');

                        // Hapus tombol Upload jika masih berbentuk tombol outline (belum ada foto)
                        if (addBtn.hasClass('btn-outline-primary')) {
                            cell.empty();
                            cell.append(
                                '<div class="d-flex flex-wrap justify-content-center align-items-center" style="gap:4px;"></div>'
                            );
                        }

                        // Buat elemen foto baru
                        const isPdf = res.file_url.toLowerCase().endsWith('.pdf');
                        const newFoto = isPdf ? `
                            <a href="${res.file_url}" target="_blank" class="text-danger bukti-foto-item" data-bukti-id="${res.bukti_id}" style="display:inline-block; position:relative; width:44px; height:44px; text-align:center; vertical-align:middle; line-height:44px; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                                <i class="far fa-file-pdf fa-2x" style="vertical-align:middle;"></i>
                                <button type="button" class="btn-hapus-bukti-foto position-absolute"
                                    style="top:-5px;right:-5px;width:16px;height:16px;border-radius:50%;background:#e74a3b;border:none;color:#fff;font-size:9px;line-height:1;padding:0;cursor:pointer;display:flex;align-items:center;justify-content:center;"
                                    data-id="${_currentBuktiId}" data-bukti-id="${res.bukti_id}" title="Hapus foto ini" onclick="event.stopPropagation();">
                                    <i class="fas fa-times"></i>
                                </button>
                            </a>` : `
                            <div class="position-relative d-inline-block bukti-foto-item" data-bukti-id="${res.bukti_id}">
                                <img src="${res.file_url}" alt="Bukti"
                                    class="img-thumbnail shadow-sm preview-image"
                                    style="width:44px;height:44px;object-fit:cover;cursor:pointer;"
                                    onclick="previewImage('${res.file_url}', 'Bukti Transfer - ${_currentBuktiNama || ''}')"
                                    title="Klik untuk memperbesar">
                                <button type="button" class="btn-hapus-bukti-foto position-absolute"
                                    style="top:-5px;right:-5px;width:16px;height:16px;border-radius:50%;background:#e74a3b;border:none;color:#fff;font-size:9px;line-height:1;padding:0;cursor:pointer;display:flex;align-items:center;justify-content:center;"
                                    data-id="${_currentBuktiId}" data-bukti-id="${res.bukti_id}" title="Hapus foto ini">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>`;

                        const plusBtn = `
                            <button type="button" class="btn-tambah-bukti-foto d-flex align-items-center justify-content-center"
                                style="width:22px;height:22px;border-radius:50%;border:none;background:#4e73df;color:#fff;cursor:pointer;font-size:0.7rem;flex-shrink:0;padding:0;"
                                data-id="${_currentBuktiId}" data-nama="${_currentBuktiNama || ''}" title="Tambah Foto Bukti">
                                <i class="fas fa-plus"></i>
                            </button>`;

                        // Jika belum ada wrapper flex, buat
                        let wrapper = cell.find('.d-flex.flex-wrap');
                        if (wrapper.length === 0) {
                            cell.html(
                                '<div class="d-flex flex-wrap justify-content-center align-items-center" style="gap:4px;"></div>'
                            );
                            wrapper = cell.find('.d-flex.flex-wrap');
                        }

                        // Hapus tombol + lama, insert foto baru, tambah tombol + baru di akhir
                        wrapper.find('.btn-tambah-bukti-foto').remove();
                        wrapper.append(newFoto);

                        // Hanya tampilkan tombol + jika belum 2 foto
                        const totalFoto = wrapper.find('.bukti-foto-item').length;
                        if (totalFoto < 2) {
                            wrapper.append(plusBtn);
                        }

                        Toast.fire({
                            icon: 'success',
                            title: 'Foto berhasil ditambahkan'
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengupload foto', 'error');
                    }
                });
            });

            // Hapus foto individual
            $(document).on('click', '.btn-hapus-bukti-foto', function(e) {
                e.stopPropagation();
                const btn = $(this);
                const id = btn.data('id');
                const buktiId = btn.data('bukti-id');
                const fotoItem = btn.closest('.bukti-foto-item');

                Swal.fire({
                    title: 'Hapus foto ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: '/pengadaan-barang/' + id + '/bukti/' + buktiId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (!res.success) return;
                            fotoItem.remove();

                            // Jika tidak ada foto tersisa, tampilkan tombol Upload
                            const cell = $('#pengadaan-table-body tr[data-id="' + id +
                                '"] .bukti-transfer-cell');
                            const wrapper = cell.find('.d-flex.flex-wrap');
                            const remainingFotos = wrapper.find('.bukti-foto-item')
                                .length;

                            if (remainingFotos === 0) {
                                cell.html(`
                                    <div class="d-flex flex-column align-items-center">
                                        <button type="button" class="btn-tambah-bukti-foto btn btn-outline-primary btn-xs px-2 shadow-sm font-weight-bold"
                                            data-id="${id}" data-nama=""
                                            style="font-size:0.7rem;padding:2px 5px;">
                                            <i class="fas fa-upload mr-1"></i> Upload
                                        </button>
                                    </div>`);
                            } else {
                                // Foto berkurang dari 2 → tampilkan tombol (+) kembali
                                if (wrapper.find('.btn-tambah-bukti-foto').length ===
                                    0) {
                                    wrapper.append(`
                                        <button type="button" class="btn-tambah-bukti-foto d-flex align-items-center justify-content-center"
                                            style="width:22px;height:22px;border-radius:50%;border:none;background:#4e73df;color:#fff;cursor:pointer;font-size:0.7rem;flex-shrink:0;padding:0;"
                                            data-id="${id}" data-nama="" title="Tambah Foto Bukti">
                                            <i class="fas fa-plus"></i>
                                        </button>`);
                                }
                            }
                            Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 1200,
                                    timerProgressBar: true
                                })
                                .fire({
                                    icon: 'success',
                                    title: 'Foto dihapus'
                                });
                        }
                    });
                });
            });
            // ====================== STATUS BELI PENGADAAN ======================
            $(document).on('change', '.pengadaan-status-beli-select', function() {
                const $sel = $(this);
                const id = $sel.data('id');
                const val = $sel.val();
                const $tr = $sel.closest('tr');

                $sel.css('opacity', '0.5');
                $.ajax({
                    url: '/pengadaan-barang/update/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        status_beli: val
                    },
                    success: function(res) {
                        $sel.css('opacity', '1');

                        // Update warna dropdown sesuai nilai
                        if (val === 'Sudah Dibeli') {
                            $sel.css({
                                'background': '#1cc88a',
                                'color': '#fff'
                            });
                        } else {
                            $sel.css({
                                'background': '#fd7e14',
                                'color': '#fff'
                            });
                        }

                        // Jika baru masuk inventaris, tampilkan badge ✅ lalu fade-out dan sembunyikan baris
                        if (res.synced_to_inventory) {
                            const $namaTd = $tr.find('[data-field="nama_barang"]').closest(
                                'td');
                            if ($namaTd.find('.badge-masuk-inventaris').length === 0) {
                                $namaTd.append(
                                    '<span class="badge-masuk-inventaris ml-1" ' +
                                    'style="color:#1cc88a; font-size:0.8rem;" ' +
                                    'title="Sudah masuk ke Inventaris Kantor">' +
                                    '<i class="fas fa-check-circle"></i></span>'
                                );
                            }

                            // Tampilkan centang sebentar, fade-out, lalu remove dari DOM (baris bawah naik otomatis)
                            setTimeout(function() {
                                $tr.css({
                                    transition: 'opacity 1.4s ease',
                                    opacity: '0'
                                });
                                setTimeout(function() {
                                    $tr.remove();
                                    updatePagination('pengadaan-table-body',
                                        'pengadaan-pagination');
                                    updateGrandTotalPengadaan();
                                    if ($('#pengadaan-table-body tr').not(
                                            '.empty-pengadaan-row, .filtered-empty-row'
                                        ).length === 0) {
                                        $('#pengadaan-table-body').append(
                                            '<tr class="empty-pengadaan-row"><td colspan="8" class="text-center py-5 text-muted">Belum ada data pengadaan barang.</td></tr>'
                                        );
                                    }
                                }, 1500);
                            }, 900);
                        }

                        const msg = val === 'Sudah Dibeli' ?
                            'Sudah dibeli — masuk inventaris!' : 'Tersimpan';
                        Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 800,
                                timerProgressBar: true
                            })
                            .fire({
                                icon: 'success',
                                title: msg
                            });
                    },
                    error: function() {
                        $sel.css('opacity', '1');
                        Swal.fire('Error', 'Gagal menyimpan status beli', 'error');
                    }
                });
            });

            // ====================== LIVE EDIT INVENTARIS KANTOR ======================
            $('#btnTambahInventarisInline').on('click', function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menambah...');

                $.ajax({
                    url: "{{ route('inventaris-kantor.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        lokasi: "",
                        nama_peralatan: "",
                        jumlah: ""
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-plus mr-1"></i> Tambah Inventaris');
                        $('.empty-inventaris-row').remove();

                        const item = res.data;
                        const rowCount = $('#inventaris-table-body tr').length + 1;

                        const newRow = `
                        <tr data-id="${item.id}">
                            <td class="text-center no-col">${rowCount}</td>
                            <td>
                                <textarea class="form-control-inline text-center inventaris-live-edit auto-resize" data-field="nama_peralatan" rows="1" placeholder="(Tulis Inventaris)"></textarea>
                            </td>
                            <td class="text-center font-weight-bold align-middle" style="background: #fff;">
                                <select class="form-control-inline text-center font-weight-bold inventaris-live-edit status-dropdown" data-field="lokasi" style="background-color: #fff !important; color: #000 !important; border: 1px solid rgba(0,0,0,0.1) !important; min-width: 150px;">
                                    <option value="">-- Pilih Lokasi --</option>
                                    <option value="Ruang depan">Ruang depan</option>
                                    <option value="Kamar Mandi Bawah">Kamar Mandi Bawah</option>
                                    <option value="Ruang kelas/ aula">Ruang kelas/ aula</option>
                                    <option value="Kamar atas">Kamar atas</option>
                                    <option value="Ruang atas">Ruang atas</option>
                                    <option value="Kamar bawah">Kamar bawah</option>
                                    <option value="Kamar mandi atas">Kamar mandi atas</option>
                                </select>
                            </td>
                            <td>
                                                <textarea class="form-control-inline text-center inventaris-live-edit auto-resize" data-field="jumlah" rows="1"></textarea>
                            </td>
                            <td class="text-center">
                                <select class="form-control-inline inventaris-live-edit status-dropdown bg-status-selesai" data-field="status">
                                    <option value="Normal" selected class="bg-status-selesai">Normal</option>
                                    <option value="Rusak" class="bg-status-tidak">Rusak</option>
                                    <option value="Perbaikan" class="bg-status-cari-vendor">Perbaikan</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="form-control-inline inventaris-live-edit auto-resize" data-field="keterangan" rows="1"></textarea>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-link text-danger p-0 delete-inventaris-btn" data-id="${item.id}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                        $('#inventaris-table-body').prepend(newRow);

                        // Re-index numbers
                        $('#inventaris-table-body tr').each(function(index) {
                            $(this).find('.no-col').text(index + 1);
                        });

                        // Trigger filter status check
                        applyInventarisFilters();

                        setTimeout(() => {
                            $('#inventaris-table-body tr').first().find('.auto-resize')
                                .each(function() {
                                    resizeTextarea(this);
                                });
                        }, 50);
                    },
                    error: function() {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-plus mr-1"></i> Tambah Inventaris');
                    }
                });
            });

            $(document).on('change', '.inventaris-live-edit', function() {
                const input = $(this);
                const tr = input.closest('tr');
                const id = tr.data('id');
                const field = input.data('field');
                const value = input.val();

                input.css('background-color', '#fff9db');

                $.ajax({
                    url: "/inventaris-kantor/update/" + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "PUT",
                        [field]: value
                    },
                    success: function(res) {
                        input.css('background-color', 'transparent');

                        if (field === 'status') {
                            input.removeClass(
                                'bg-status-selesai bg-status-tidak bg-status-cari-vendor');
                            if (value === 'Normal') input.addClass('bg-status-selesai');
                            else if (value === 'Rusak') input.addClass('bg-status-tidak');
                            else if (value === 'Perbaikan') input.addClass(
                                'bg-status-cari-vendor');

                            // Trigger dynamic row filtering if status changed
                            applyInventarisFilters();
                        }

                        if (field === 'lokasi' || field === 'nama_peralatan') {
                            applyInventarisFilters();
                        }

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Tersimpan'
                        });
                    }
                });
            });

            $(document).on('click', '.delete-inventaris-btn', function() {
                const btn = $(this);
                const tr = btn.closest('tr');
                const id = btn.data('id');
                Swal.fire({
                    title: 'Hapus data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/inventaris-kantor/destroy/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                tr.fadeOut(400, function() {
                                    $(this).remove();
                                    updatePagination('inventaris-table-body',
                                        'inventaris-pagination');

                                    if ($('#inventaris-table-body tr').not(
                                            '.empty-inventaris-row, .filtered-empty-inventaris-row'
                                        ).length === 0) {
                                        $('#inventaris-table-body').append(`
                                        <tr class="empty-inventaris-row">
                                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data inventaris kantor.</td>
                                        </tr>
                                    `);
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // ====================== FILTER INVENTARIS HANDLER (STATUS, LOKASI, PERALATAN) ======================
            function applyInventarisFilters() {
                const statusFilter = $('#filter-status-inventaris').val() || 'all';
                const lokasiFilter = $('#filter-lokasi-inventaris').val() || 'all';
                const peralatanSearch = ($('#filter-peralatan-inventaris').val() || '').toLowerCase().trim();

                $('#inventaris-table-body tr').each(function() {
                    const tr = $(this);
                    if (tr.hasClass('empty-inventaris-row') || tr.hasClass('filtered-empty-inventaris-row'))
                        return;

                    const rowStatus = tr.find('[data-field="status"]').val();
                    const rowLokasi = tr.find('[data-field="lokasi"]').val() || '';
                    const rowPeralatan = (tr.find('[data-field="nama_peralatan"]').val() || '')
                        .toLowerCase().trim();

                    let matchesStatus = (statusFilter === 'all' || rowStatus === statusFilter);
                    let matchesLokasi = (lokasiFilter === 'all' || rowLokasi === lokasiFilter);
                    let matchesPeralatan = rowPeralatan.includes(peralatanSearch);

                    if (matchesStatus && matchesLokasi && matchesPeralatan) {
                        tr.removeClass('filtered-out');
                    } else {
                        tr.addClass('filtered-out');
                    }
                });

                updatePagination('inventaris-table-body', 'inventaris-pagination', 1);
            }

            $('#filter-status-inventaris, #filter-lokasi-inventaris').on('change', function() {
                applyInventarisFilters();
            });

            $('#filter-peralatan-inventaris').on('input', function() {
                applyInventarisFilters();
            });

            // ====================== TAB LOGIC ======================
            function moveIndicator(target) {
                const indicator = document.getElementById('tab-indicator');
                const divider = document.querySelector('.premium-divider');
                const tab = target instanceof jQuery ? target[0] : target;
                if (!indicator || !divider || !tab) return;
                const tabRect = tab.getBoundingClientRect();
                const dividerRect = divider.getBoundingClientRect();
                const leftPos = tabRect.left - dividerRect.left + (tabRect.width / 2) - 50;
                indicator.style.left = leftPos + 'px';
                indicator.className = 'divider-indicator';
                if (tab.classList.contains('color-primary')) indicator.classList.add('bg-primary-indicator');
                if (tab.classList.contains('color-danger')) indicator.classList.add('bg-danger-indicator');
                if (tab.classList.contains('color-warning')) indicator.classList.add('bg-warning-indicator');
                if (tab.classList.contains('color-info')) indicator.classList.add('bg-info-indicator');
                if (tab.classList.contains('color-success')) indicator.classList.add('bg-success-indicator');
                if (tab.classList.contains('color-dark')) indicator.classList.add('bg-dark-indicator');
            }

            setTimeout(() => {
                const activeTab = document.querySelector('.premium-tab.active');
                if (activeTab) moveIndicator(activeTab);
            }, 500);

            $('button[data-toggle="tab"]').on('show.bs.tab', function(e) {
                moveIndicator(e.target);
            });

            window.openUploadModal = function(id, name, type) {
                $('#namaPengajuanUpload').text(name);
                const actionUrl = (type === 'monitoring') ?
                    '/monitoring-perbaikan/' + id + '/upload-bukti' :
                    '/pengadaan-barang/' + id + '/upload-bukti';
                $('#formUploadBukti').attr('action', actionUrl);
                $('#formUploadBukti').data('id', id);
                $('#formUploadBukti').data('name', name);
                $('#formUploadBukti').data('type', type || 'pengadaan');

                // Toggle modal labels and file filter based on target
                if (type === 'monitoring') {
                    $('#modalUploadBukti .modal-title').html(
                        '<i class="fas fa-file-pdf mr-2"></i>Upload File LPJ (PDF)');
                    $('#modalUploadBukti label.font-weight-bold').text('Pilih File LPJ (PDF)');
                    $('#inputUploadBukti').attr('accept', '.pdf');
                    $('#modalUploadBukti .small.text-muted').html(
                        '<i class="fas fa-info-circle mr-1"></i> Format: PDF. Maks: 10MB.');
                    $('#inputUploadBukti').next('.custom-file-label').removeClass("selected").html(
                        'Pilih file PDF...');
                } else {
                    $('#modalUploadBukti .modal-title').html(
                        '<i class="fas fa-upload mr-2"></i>Upload Bukti Transfer');
                    $('#modalUploadBukti label.font-weight-bold').text('Pilih File Bukti (Gambar / PDF)');
                    $('#inputUploadBukti').attr('accept', 'image/*,.pdf');
                    $('#modalUploadBukti .small.text-muted').html(
                        '<i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG, JPEG, PDF. Maks: 10MB.');
                    $('#inputUploadBukti').next('.custom-file-label').removeClass("selected").html(
                        'Pilih file gambar atau PDF...');
                }

                $('#inputUploadBukti').val('');
                $('#modalUploadBukti').modal('show');
            }

            window.previewImage = function(url, title) {
                $('#imageFullPreview').attr('src', url);
                $('#previewTitle').text(title);
                $('#modalPreviewImage').modal('show');
            }

            // Handle custom-file-input label
            $(document).on('change', '.custom-file-input', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            // Format budget dynamically as user types
            $(document).on('input', '.pengadaan-budget-input, .budget-input, .realisasi-input', function() {
                let input = $(this);
                let clean = input.val().replace(/\D/g, ''); // Keep only digits
                if (clean) {
                    input.val(new Intl.NumberFormat('id-ID').format(clean));
                } else {
                    input.val('');
                }
            });

            // Intercept form submission to perform AJAX upload
            $('#formUploadBukti').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const actionUrl = form.attr('action');
                const id = form.data('id');
                const name = form.data('name');
                const type = actionUrl.includes('monitoring-perbaikan') ? 'monitoring' : 'pengadaan';
                const submitBtn = form.find('button[type="submit"]');

                const originalBtnHtml = type === 'monitoring' ?
                    '<i class="fas fa-cloud-upload-alt mr-1"></i> Simpan LPJ' :
                    '<i class="fas fa-cloud-upload-alt mr-1"></i> Simpan Bukti';

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

                const formData = new FormData(this);

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        submitBtn.prop('disabled', false).html(originalBtnHtml);
                        $('#modalUploadBukti').modal('hide');

                        if (res.success) {
                            const tr = type === 'monitoring' ?
                                $(`#monitoring-table-body tr[data-id="${id}"]`) :
                                $(`#pengadaan-table-body tr[data-id="${id}"]`);

                            let newCellHtml = '';
                            if (type === 'monitoring') {
                                newCellHtml = `
                                <div class="d-flex flex-column align-items-center">
                                    <a href="${res.bukti_transfer}" target="_blank" class="text-danger" title="Lihat Dokumen LPJ (PDF)">
                                        <i class="far fa-file-pdf fa-2x mb-1 hover-scale transition-all"></i>
                                        <div class="small font-weight-bold">Lihat LPJ</div>
                                    </a>
                                    <div class="mt-1">
                                        <a href="javascript:void(0)" class="small text-primary font-weight-bold"
                                            onclick="openUploadModal(${id}, '${name}', '${type}')">
                                            Ganti
                                        </a>
                                    </div>
                                </div>
                            `;
                            } else {
                                const isPdf = res.bukti_transfer.toLowerCase().endsWith('.pdf');
                                if (isPdf) {
                                    newCellHtml = `
                                    <div class="d-flex flex-column align-items-center">
                                        <a href="${res.bukti_transfer}" target="_blank" class="text-danger" title="Lihat PDF" style="display:inline-block; position:relative; width:44px; height:44px; text-align:center; vertical-align:middle; line-height:44px; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                                            <i class="far fa-file-pdf fa-2x" style="vertical-align:middle;"></i>
                                        </a>
                                        <div class="mt-1">
                                            <a href="javascript:void(0)" class="small text-primary font-weight-bold"
                                                onclick="openUploadModal(${id}, '${name}', '${type}')">
                                                Ganti
                                            </a>
                                        </div>
                                    </div>
                                    `;
                                } else {
                                    newCellHtml = `
                                    <div class="position-relative d-inline-block">
                                        <img src="${res.bukti_transfer}" alt="Bukti Transfer"
                                            class="img-thumbnail shadow-sm preview-image"
                                            style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                            onclick="previewImage('${res.bukti_transfer}', 'Bukti Transfer - ${name}')"
                                            title="Klik untuk memperbesar">
                                    </div>
                                    <div class="mt-1">
                                        <a href="javascript:void(0)" class="small text-primary font-weight-bold"
                                            onclick="openUploadModal(${id}, '${name}', '${type}')">
                                            Ganti
                                        </a>
                                    </div>
                                    `;
                                }
                            }
                            tr.find('.bukti-transfer-cell').html(newCellHtml);

                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: res.message
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Gagal mengunggah berkas',
                                'error');
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalBtnHtml);
                        let errMsg = 'Gagal mengunggah berkas.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errMsg, 'error');
                    }
                });
            });
            // Initialize client-side pagination on page load
            updatePagination('inventaris-table-body', 'inventaris-pagination');
            updatePagination('monitoring-table-body', 'monitoring-pagination');
            updatePagination('pengadaan-table-body', 'pengadaan-pagination');

            // 5. Deadline filter for monitoring — search button
            $('#filter-timeline-btn').on('click', function() {
                const filterDate = $('#filter-timeline-date').val();
                if (!filterDate) {
                    Swal.fire('Perhatian', 'Pilih tanggal untuk memfilter deadline.',
                        'warning');
                    return;
                }
                applyMonitoringFilter();
            });

            // Reset deadline filter
            $('#filter-timeline-reset').on('click', function() {
                $('#filter-timeline-date').val('');
                applyMonitoringFilter();
            });

            // 6. Live Search Fasilitas - filter saat user mengetik
            $('#filter-fasilitas-search').on('input', function() {
                applyMonitoringFilter();
            });

            // Reset filter fasilitas
            $('#filter-fasilitas-reset').on('click', function() {
                $('#filter-fasilitas-search').val('');
                applyMonitoringFilter();
            });

            // 7. Filter Progress (di header tabel) - trigger filter saat berubah
            $('#filter-progress').on('change', function() {
                applyMonitoringFilter();
            });

        });

        // ===== DETAIL POPUP =====
        window.openDetailPopup = function(label, text) {
            document.getElementById('detail-popup-title').textContent = label;
            document.getElementById('detail-popup-content').textContent = text || '(Tidak ada data)';
            document.getElementById('detail-popup-overlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        window.closeDetailPopup = function(event) {
            if (event === null || event.target === document.getElementById('detail-popup-overlay')) {
                document.getElementById('detail-popup-overlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('detail-popup-overlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>

    {{-- ── Modal Cetak Laporan Pengadaan ── --}}
    <div class="modal fade" id="modalCetakPengadaan" tabindex="-1" role="dialog"
        aria-labelledby="modalCetakPengadaanLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header" style="background: #f6c23e; color: #111;">
                    <h5 class="modal-title font-weight-bold" id="modalCetakPengadaanLabel">
                        <i class="fas fa-print mr-2"></i> Cetak Laporan Pengadaan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="color: #111; opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- form GET → buka di tab baru untuk window.print() --}}
                <form method="GET" action="{{ route('pengadaan-barang.cetak-laporan') }}" target="_blank">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold" style="font-size: 0.85rem;">Bulan</label>
                            <select name="bulan" class="form-control form-control-sm">
                                <option value="all">— Semua Bulan —</option>
                                @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $num => $nama)
                                    <option value="{{ $num }}" {{ now()->month == $num ? 'selected' : '' }}>
                                        {{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold" style="font-size: 0.85rem;">Tahun <span
                                    class="text-danger">*</span></label>
                            <select name="tahun" class="form-control form-control-sm" required>
                                @for ($y = now()->year; $y >= now()->year - 3; $y--)
                                    <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm font-weight-bold" style="color: #111;">
                            <i class="fas fa-print mr-1"></i> Tampilkan &amp; Cetak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
