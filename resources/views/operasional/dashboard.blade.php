@extends('layouts.masteradmin')

@section('content')
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
            background-color: #f28d8d !important;
            color: #000;
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
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h1 class="h3 text-gray-800 font-weight-bold">Dashboard Operasional</h1>
                <p class="text-muted">Selamat datang kembali, {{ $csName }}!</p>
            </div>
            <div class="col-md-6 text-right">
                <!-- Filter removed -->
            </div>
        </div>


        <!-- Navigation Tabs (Main) -->
        <ul class="nav nav-tabs border-0 mt-2 mb-0 d-flex flex-wrap gap-0 justify-content-center px-4" id="opMainTabs"
            role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold premium-tab color-primary" id="inventaris-tab" data-toggle="tab"
                    data-target="#inventaris" type="button" role="tab">
                    <i class="fas fa-building"></i> <span>Inventaris Kantor</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold premium-tab color-warning" id="kebutuhan-mbc-tab" data-toggle="tab"
                    data-target="#kebutuhan-mbc" type="button" role="tab">
                    <i class="fas fa-chalkboard-teacher"></i> <span>Monitoring Perbaikan</span>
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
                    <div class="card-header text-center font-weight-bold"
                        style="background-color: #00ffff; color: #000; border: 2px solid #000; text-transform: uppercase; letter-spacing: 1px;">
                        LIST INVENTARIS KANTOR
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-boxes mr-2 text-info"></i> Data
                                Inventaris</h5>
                            <button class="btn btn-info btn-sm shadow-sm"
                                style="border-radius: 8px; background-color: #00ffff; color: #000; border: 1px solid #000;"
                                id="btnTambahInventarisInline">
                                <i class="fas fa-plus mr-1"></i> Tambah Inventaris
                            </button>
                        </div>

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
                                            style="background-color: #00ffff !important; color: #000 !important; border: 1px solid #000 !important; vertical-align: middle; width: 170px;">
                                            <div class="mb-1 font-weight-bold"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px;">PERALATAN</div>
                                            <input type="text" id="filter-peralatan-inventaris"
                                                class="form-control form-control-sm mx-auto text-center"
                                                placeholder="Cari..."
                                                style="border-radius: 20px; font-weight: 700; width: 140px; height: 28px; padding: 2px 10px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
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
                                                <textarea class="form-control-inline text-center inventaris-live-edit auto-resize" data-field="nama_peralatan"
                                                    rows="1" placeholder="(Tulis Barang)">{{ $item->nama_peralatan }}</textarea>
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
            </div>

            <!-- Tab 3: Monitoring Perbaikan -->
            <div class="tab-pane fade" id="kebutuhan-mbc" role="tabpanel">
                <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-header monitoring-header text-center">
                        LIST MONITORING KERUSAKAN FASILITAS GEDUNG
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-tools mr-2 text-warning"></i>
                                Data Monitoring</h5>
                            <button class="btn btn-primary btn-sm shadow-sm" style="border-radius: 8px;"
                                id="btnTambahPerbaikanInline">
                                <i class="fas fa-plus mr-1"></i> Tambah Data
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-monitoring mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">NO.</th>
                                        <th style="width: 180px;">FASILITAS</th>
                                        <th>KERUSAKAN</th>
                                        <th class="text-center" style="width: 150px;">
                                            TIMELINE
                                            <div class="d-flex flex-column align-items-center mt-2">
                                                <input type="date" id="filter-timeline-start"
                                                    class="form-control form-control-sm mx-auto text-center"
                                                    placeholder="Mulai"
                                                    style="border-radius: 20px; font-weight: 700; width: 120px; height: 28px; padding: 2px 5px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" />
                                                <input type="date" id="filter-timeline-end"
                                                    class="form-control form-control-sm mx-auto text-center mt-1"
                                                    placeholder="Selesai"
                                                    style="border-radius: 20px; font-weight: 700; width: 120px; height: 28px; padding: 2px 5px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" />
                                                <div class="d-flex gap-1 mt-1" style="gap: 4px;">
                                                    <button id="filter-timeline-btn" class="btn btn-sm btn-primary"
                                                        title="Cari" style="border-radius: 6px; padding: 2px 8px;"><i
                                                            class="fas fa-search"></i></button>
                                                    <button id="filter-timeline-reset" class="btn btn-sm btn-primary"
                                                        title="Tampilkan Semua"
                                                        style="border-radius: 6px; padding: 2px 8px;"><i
                                                            class="fas fa-sync-alt"></i></button>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="text-center" style="width: 160px; vertical-align: middle;">
                                            <div class="mb-1 text-white font-weight-bold"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px;">PROGRESS</div>
                                            <select id="filter-progress" class="form-control form-control-sm mx-auto"
                                                style="border-radius: 20px; font-weight: 700; text-align: center; text-align-last: center; width: 140px; cursor: pointer; height: 28px; padding: 2px 10px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                <option value="all">-- Semua --</option>
                                                <option value="Pengajuan Dana">Pengajuan Dana</option>
                                                <option value="Cari Vendor">Cari Vendor</option>
                                                <option value="Pending">Pending</option>
                                                <option value="On Progress">On Progress</option>
                                                <option value="Selesai">Selesai</option>
                                            </select>
                                        </th>
                                        <th>RENCANA</th>
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
                                                <textarea class="form-control-inline font-weight-bold live-edit auto-resize" data-field="fasilitas" rows="1"
                                                    placeholder="(Tulis Data Baru)">{{ $item->fasilitas }}</textarea>
                                            </td>
                                            <td class="text-truncate-cell">
                                                <textarea class="form-control-inline live-edit auto-resize kerusakan-field" data-field="kerusakan" rows="1"
                                                    spellcheck="false">{{ $item->kerusakan }}</textarea>
                                                <button class="btn-lihat-detail mt-1"
                                                    onclick="openDetailPopup('Kerusakan', this.previousElementSibling.value)">Lihat
                                                    Detail</button>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <input type="date"
                                                        class="form-control-inline text-center live-edit"
                                                        data-field="tanggal_mulai" value="{{ $item->tanggal_mulai }}"
                                                        style="width: 120px;">
                                                    <span class="my-1 text-muted"
                                                        style="font-size: 0.75rem; line-height: 1;">s/d</span>
                                                    <input type="date"
                                                        class="form-control-inline text-center live-edit"
                                                        data-field="tanggal_selesai" value="{{ $item->tanggal_selesai }}"
                                                        style="width: 120px;">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $statusClass = '';
                                                    if ($item->progress == 'Pengajuan Dana') {
                                                        $statusClass = 'bg-status-pengajuan-dana';
                                                    } elseif ($item->progress == 'Cari Vendor') {
                                                        $statusClass = 'bg-status-cari-vendor';
                                                    } elseif ($item->progress == 'Pending') {
                                                        $statusClass = 'bg-status-pending';
                                                    } elseif ($item->progress == 'On Progress') {
                                                        $statusClass = 'bg-status-on-progress';
                                                    } elseif ($item->progress == 'Selesai') {
                                                        $statusClass = 'bg-status-selesai';
                                                    }
                                                @endphp
                                                <select
                                                    class="form-control-inline live-edit status-dropdown {{ $statusClass }}"
                                                    data-field="progress">
                                                    <option value="Pengajuan Dana"
                                                        {{ $item->progress == 'Pengajuan Dana' ? 'selected' : '' }}
                                                        class="bg-status-pengajuan-dana">Pengajuan Dana</option>
                                                    <option value="Cari Vendor"
                                                        {{ $item->progress == 'Cari Vendor' ? 'selected' : '' }}
                                                        class="bg-status-cari-vendor">Cari Vendor</option>
                                                    <option value="Pending"
                                                        {{ $item->progress == 'Pending' ? 'selected' : '' }}
                                                        class="bg-status-pending">Pending</option>
                                                    <option value="On Progress"
                                                        {{ $item->progress == 'On Progress' ? 'selected' : '' }}
                                                        class="bg-status-on-progress">On Progress</option>
                                                    <option value="Selesai"
                                                        {{ $item->progress == 'Selesai' ? 'selected' : '' }}
                                                        class="bg-status-selesai">Selesai</option>
                                                </select>
                                            </td>
                                            <td class="text-truncate-cell">
                                                <textarea class="form-control-inline live-edit auto-resize rencana-field" data-field="rencana" rows="1"
                                                    spellcheck="false">{{ $item->rencana }}</textarea>
                                                <button class="btn-lihat-detail mt-1"
                                                    onclick="openDetailPopup('Rencana', this.previousElementSibling.value)">Lihat
                                                    Detail</button>
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
                                                                onclick="openUploadModal({{ $item->id }}, '{{ $item->fasilitas }}', 'monitoring')">Ganti</a>
                                                        </div>
                                                </div>@else<div class="d-flex flex-column align-items-center"><button
                                                            type="button"
                                                            class="btn btn-outline-primary btn-xs px-2 shadow-sm font-weight-bold"
                                                            onclick="openUploadModal({{ $item->id }}, '{{ $item->fasilitas }}', 'monitoring')"
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
                                            <td colspan="10" class="text-center py-5 text-muted">Belum ada data
                                                monitoring perbaikan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f8f9fc;">
                                        <td colspan="6" class="text-right font-weight-bold"
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
                    <div class="card-header text-center font-weight-bold"
                        style="background-color: #f28d8d; color: #000; border: 2px solid #000;">
                        LIST PENGADAAN BARANG
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="font-weight-bold mb-0 text-dark"><i
                                    class="fas fa-shopping-cart mr-2 text-success"></i> Data Pengadaan</h5>
                            <button class="btn btn-success btn-sm shadow-sm" style="border-radius: 8px;"
                                id="btnTambahPengadaanInline">
                                <i class="fas fa-plus mr-1"></i> Tambah Barang
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-monitoring mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">NO.</th>
                                        <th>NAMA BARANG</th>
                                        <th class="text-center" style="width: 120px;">JUMLAH</th>
                                        <th class="text-center" style="width: 170px; vertical-align: middle;">
                                            <div class="mb-1 text-white font-weight-bold"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px;">PROGRESS</div>
                                            <select id="filter-progress-pengadaan"
                                                class="form-control form-control-sm mx-auto"
                                                style="border-radius: 20px; font-weight: 700; text-align: center; text-align-last: center; width: 140px; cursor: pointer; height: 28px; padding: 2px 10px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                <option value="all">-- Semua --</option>
                                                <option value="Pengajuan Dana">Pengajuan Dana</option>
                                                <option value="Cari Vendor">Cari Vendor</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Terealisasi">Terealisasi</option>
                                            </select>
                                        </th>
                                        <th class="text-right" style="width: 160px;">BUDGET</th>
                                        <th class="text-center" style="width: 170px; vertical-align: middle;">
                                            <div class="mb-1 text-white font-weight-bold"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px;">ACC</div>
                                            <select id="filter-acc-pengadaan" class="form-control form-control-sm mx-auto"
                                                style="border-radius: 20px; font-weight: 700; text-align: center; text-align-last: center; width: 140px; cursor: pointer; height: 28px; padding: 2px 10px; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.2); background-color: #fff; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                <option value="all">-- Semua --</option>
                                                <option value="Iya">Iya</option>
                                                <option value="Tidak">Tidak</option>
                                            </select>
                                        </th>
                                        <th class="text-center">Bukti Transfer</th>
                                        <th class="text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="pengadaan-table-body">
                                    @php $totalBudgetPengadaan = 0; @endphp
                                    @forelse($pengadaanBarang as $key => $item)
                                        @php $totalBudgetPengadaan += (float)$item->budget; @endphp
                                        <tr data-id="{{ $item->id }}">
                                            <td class="text-center no-col">{{ $loop->iteration }}</td>
                                            <td>
                                                <textarea class="form-control-inline font-weight-bold pengadaan-live-edit auto-resize" data-field="nama_barang"
                                                    rows="1" placeholder="(Tulis Nama Barang)">{{ $item->nama_barang }}</textarea>
                                            </td>
                                            <td>
                                                <textarea class="form-control-inline text-center pengadaan-live-edit auto-resize" data-field="jumlah" rows="1">{{ $item->jumlah }}</textarea>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $progClass = '';
                                                    if ($item->progress == 'Pengajuan Dana') {
                                                        $progClass = 'bg-status-pengajuan-dana';
                                                    } elseif ($item->progress == 'Cari Vendor') {
                                                        $progClass = 'bg-status-cari-vendor';
                                                    } elseif ($item->progress == 'Pending') {
                                                        $progClass = 'bg-status-pending';
                                                    } elseif ($item->progress == 'Terealisasi') {
                                                        $progClass = 'bg-status-selesai';
                                                    }
                                                @endphp
                                                <select
                                                    class="form-control-inline pengadaan-live-edit status-dropdown {{ $progClass }}"
                                                    data-field="progress">
                                                    <option value="Pengajuan Dana"
                                                        {{ $item->progress == 'Pengajuan Dana' ? 'selected' : '' }}
                                                        class="bg-status-pengajuan-dana">Pengajuan Dana</option>
                                                    <option value="Cari Vendor"
                                                        {{ $item->progress == 'Cari Vendor' ? 'selected' : '' }}
                                                        class="bg-status-cari-vendor">Cari Vendor</option>
                                                    <option value="Pending"
                                                        {{ $item->progress == 'Pending' ? 'selected' : '' }}
                                                        class="bg-status-pending">Pending</option>
                                                    <option value="Terealisasi"
                                                        {{ $item->progress == 'Terealisasi' ? 'selected' : '' }}
                                                        class="bg-status-selesai">Terealisasi</option>
                                                </select>
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
                                            <td class="text-center">
                                                @php
                                                    $accClass = '';
                                                    if ($item->acc == 'Iya') {
                                                        $accClass = 'bg-status-selesai';
                                                    } elseif ($item->acc == 'Tidak') {
                                                        $accClass = 'bg-status-tidak';
                                                    } else {
                                                        $accClass = 'bg-status-pending';
                                                    }
                                                @endphp
                                                <select
                                                    class="form-control-inline pengadaan-live-edit status-dropdown {{ $accClass }}"
                                                    data-field="acc">
                                                    <option value="" {{ $item->acc == '' ? 'selected' : '' }}>-
                                                        Pilih -</option>
                                                    <option value="Iya" {{ $item->acc == 'Iya' ? 'selected' : '' }}
                                                        class="bg-status-selesai">Iya</option>
                                                    <option value="Tidak" {{ $item->acc == 'Tidak' ? 'selected' : '' }}
                                                        class="bg-status-tidak">Tidak</option>
                                                </select>
                                            </td>
                                            <td class="text-center align-middle bukti-transfer-cell">
                                                @if ($item->bukti_transfer)
                                                    <div class="position-relative d-inline-block">
                                                        <img src="{{ asset($item->bukti_transfer) }}"
                                                            alt="Bukti Transfer"
                                                            class="img-thumbnail shadow-sm preview-image"
                                                            style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                                            onclick="previewImage('{{ asset($item->bukti_transfer) }}', 'Bukti Transfer - {{ $item->nama_barang }}')"
                                                            title="Klik untuk memperbesar">
                                                    </div>
                                                    <div class="mt-1">
                                                        <a href="javascript:void(0)"
                                                            class="small text-primary font-weight-bold"
                                                            onclick="openUploadModal({{ $item->id }}, '{{ $item->nama_barang }}')">
                                                            Ganti
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="d-flex flex-column align-items-center">
                                                        <button type="button"
                                                            class="btn btn-outline-primary btn-xs px-2 shadow-sm font-weight-bold"
                                                            onclick="openUploadModal({{ $item->id }}, '{{ $item->nama_barang }}')"
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
                                        <td colspan="4" class="text-right font-weight-bold">TOTAL ESTIMASI BUDGET
                                            PENGADAAN</td>
                                        <td class="text-right font-weight-bold text-danger" style="font-size: 1.1rem;">
                                            Rp <span
                                                id="grand-total-budget-pengadaan">{{ number_format($totalBudgetPengadaan, 0, ',', '.') }}</span>
                                        </td>
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
                        colspan = 10;
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
                        fasilitas: "",
                        progress: "Pending"
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-plus mr-1"></i> Tambah Data');
                        $('.empty-row').remove();

                        const item = res.data; // Assuming controller returns the object
                        const rowCount = $('#monitoring-table-body tr').length + 1;

                        const newRow = `
                        <tr data-id="${item.id}">
                            <td class="text-center no-col">${rowCount}</td>
                            <td><textarea class="form-control-inline font-weight-bold live-edit auto-resize" data-field="fasilitas" rows="1" placeholder="(Tulis Data Baru)">${item.fasilitas || ''}</textarea></td>
                            <td class="text-truncate-cell">
                                <textarea class="form-control-inline live-edit auto-resize" data-field="kerusakan" rows="1" spellcheck="false"></textarea>
                                <button class="btn-lihat-detail mt-1" onclick="openDetailPopup('Kerusakan', this.previousElementSibling.value)">Lihat Detail</button>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <input type="date" class="form-control-inline text-center live-edit" data-field="tanggal_mulai" style="width: 120px;">
                                    <span class="my-1 text-muted" style="font-size: 0.75rem; line-height: 1;">s/d</span>
                                    <input type="date" class="form-control-inline text-center live-edit" data-field="tanggal_selesai" style="width: 120px;">
                                </div>
                            </td>
                            <td class="text-center">
                                <select class="form-control-inline live-edit status-dropdown bg-status-pending" data-field="progress">
                                    <option value="Pengajuan Dana" class="bg-status-pengajuan-dana">Pengajuan Dana</option>
                                    <option value="Cari Vendor" class="bg-status-cari-vendor">Cari Vendor</option>
                                    <option value="Pending" selected class="bg-status-pending">Pending</option>
                                    <option value="On Progress" class="bg-status-on-progress">On Progress</option>
                                    <option value="Selesai" class="bg-status-selesai">Selesai</option>
                                </select>
                            </td>
                            <td class="text-truncate-cell">
                                <textarea class="form-control-inline live-edit auto-resize" data-field="rencana" rows="1" spellcheck="false"></textarea>
                                <button class="btn-lihat-detail mt-1" onclick="openDetailPopup('Rencana', this.previousElementSibling.value)">Lihat Detail</button>
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
                                        onclick="openUploadModal(${item.id}, '${item.fasilitas || 'Perbaikan'}', 'monitoring')" style="font-size: 0.7rem; padding: 2px 5px;">
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
                            '<i class="fas fa-plus mr-1"></i> Tambah Data');
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
                        fasilitas: getRawValue('fasilitas'),
                        kerusakan: getRawValue('kerusakan'),
                        tanggal_mulai: getRawValue('tanggal_mulai'),
                        tanggal_selesai: getRawValue('tanggal_selesai'),
                        progress: getRawValue('progress'),
                        rencana: getRawValue('rencana'),
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
                            if (value === 'Pengajuan Dana') input.addClass(
                                'bg-status-pengajuan-dana');
                            else if (value === 'Cari Vendor') input.addClass(
                                'bg-status-cari-vendor');
                            else if (value === 'Pending') input.addClass('bg-status-pending');
                            else if (value === 'On Progress') input.addClass(
                                'bg-status-on-progress');
                            else if (value === 'Selesai') input.addClass('bg-status-selesai');

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
                const startRaw = $('#filter-timeline-start').val();
                const endRaw = $('#filter-timeline-end').val();

                // Konversi ke objek Date untuk perbandingan akurat (hanya jika diisi)
                const startDate = startRaw ? new Date(startRaw) : null;
                const endDate = endRaw ? new Date(endRaw) : null;

                $('#monitoring-table-body tr').each(function() {
                    const tr = $(this);
                    if (tr.hasClass('empty-row') || tr.hasClass('filtered-empty-row')) return;

                    const rowProgress = tr.find('[data-field="progress"]').val();
                    const rowStartRaw = tr.find('[data-field="tanggal_mulai"]').val();
                    const rowEndRaw = tr.find('[data-field="tanggal_selesai"]').val();

                    let keep = true;

                    // Filter progress
                    if (progressFilter !== 'all' && rowProgress !== progressFilter) {
                        keep = false;
                    }

                    // Filter timeline — hanya aktif jika setidaknya satu tanggal filter diisi
                    if (keep && (startDate || endDate)) {
                        const rowStart = rowStartRaw ? new Date(rowStartRaw) : null;
                        const rowEnd = rowEndRaw ? new Date(rowEndRaw) : null;

                        // Baris ditampilkan jika ada overlap antara range baris dan range filter
                        // Overlap: rowStart <= endDate AND rowEnd >= startDate
                        if (startDate && rowEnd && rowEnd < startDate) keep = false;
                        if (endDate && rowStart && rowStart > endDate) keep = false;

                        // Jika baris tidak punya tanggal sama sekali, sembunyikan saat filter aktif
                        if (!rowStart && !rowEnd) keep = false;
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

            // Filter progress: langsung apply saat berubah (tetap mempertimbangkan timeline aktif)
            $('#filter-progress').on('change', function() {
                applyMonitoringFilter();
            });

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
                                            <td colspan="10" class="text-center py-5 text-muted">Belum ada data monitoring perbaikan.</td>
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
                            <td class="text-center">
                                <select class="form-control-inline pengadaan-live-edit status-dropdown bg-status-pending" data-field="progress">
                                    <option value="Pengajuan Dana" class="bg-status-pengajuan-dana">Pengajuan Dana</option>
                                    <option value="Cari Vendor" class="bg-status-cari-vendor">Cari Vendor</option>
                                    <option value="Pending" selected class="bg-status-pending">Pending</option>
                                    <option value="Terealisasi" class="bg-status-selesai">Terealisasi</option>
                                </select>
                            </td>
                            <td class="text-right">
                                <div class="d-flex align-items-center justify-content-end">
                                    <span class="mr-1 text-success font-weight-bold">Rp</span>
                                    <input type="text" class="form-control-inline text-right text-success font-weight-bold pengadaan-live-edit pengadaan-budget-input" data-field="budget" value="0">
                                </div>
                            </td>
                            <td class="text-center">
                                <select class="form-control-inline pengadaan-live-edit status-dropdown bg-status-pending" data-field="acc">
                                    <option value="" selected>- Pilih -</option>
                                    <option value="Iya" class="bg-status-selesai">Iya</option>
                                    <option value="Tidak" class="bg-status-tidak">Tidak</option>
                                </select>
                            </td>
                            <td class="text-center align-middle bukti-transfer-cell">
                                <div class="d-flex flex-column align-items-center">
                                    <button type="button" class="btn btn-outline-primary btn-xs px-2 shadow-sm font-weight-bold"
                                        onclick="openUploadModal(${item.id}, '${item.nama_barang}')" style="font-size: 0.7rem; padding: 2px 5px;">
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
                        $('#filter-progress-pengadaan').trigger('change');

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

                        // Update dropdown color if it was a status change
                        if (field === 'progress') {
                            input.removeClass(
                                'bg-status-pengajuan-dana bg-status-cari-vendor bg-status-pending bg-status-selesai'
                            );
                            if (value === 'Pengajuan Dana') input.addClass(
                                'bg-status-pengajuan-dana');
                            else if (value === 'Cari Vendor') input.addClass(
                                'bg-status-cari-vendor');
                            else if (value === 'Pending') input.addClass('bg-status-pending');
                            else if (value === 'Terealisasi') input.addClass(
                                'bg-status-selesai');

                            // Trigger dynamic row filtering if progress changed
                            $('#filter-progress-pengadaan').trigger('change');
                        }

                        if (field === 'acc') {
                            input.removeClass(
                                'bg-status-selesai bg-status-tidak bg-status-pending');
                            if (value === 'Iya') input.addClass('bg-status-selesai');
                            else if (value === 'Tidak') input.addClass('bg-status-tidak');
                            else input.addClass('bg-status-pending');

                            // Trigger dynamic row filtering if acc changed
                            $('#filter-progress-pengadaan').trigger('change');
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
                $('#pengadaan-table-body tr').not('.filtered-out, .empty-pengadaan-row, .filtered-empty-row').find(
                    '.pengadaan-budget-input').each(function() {
                    let valStr = $(this).val() || '0';
                    let valNum = parseFloat(valStr.replace(/\./g, '')) || 0;
                    total += valNum;
                });
                $('#grand-total-budget-pengadaan').text(new Intl.NumberFormat('id-ID').format(total));
            }

            $(document).on('change', '#filter-progress-pengadaan, #filter-acc-pengadaan', function() {
                const progressFilter = $('#filter-progress-pengadaan').val();
                const accFilter = $('#filter-acc-pengadaan').val();

                $('#pengadaan-table-body tr').each(function() {
                    const tr = $(this);
                    if (tr.hasClass('empty-pengadaan-row') || tr.hasClass('filtered-empty-row'))
                        return;

                    const rowProgress = tr.find('[data-field="progress"]').val();
                    const rowAcc = tr.find('[data-field="acc"]').val();

                    const progressMatch = progressFilter === 'all' || rowProgress ===
                        progressFilter;
                    const accMatch = accFilter === 'all' || rowAcc === accFilter;

                    if (progressMatch && accMatch) {
                        tr.removeClass('filtered-out');
                    } else {
                        tr.addClass('filtered-out');
                    }
                });

                updatePagination('pengadaan-table-body', 'pengadaan-pagination', 1);
            });

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
                                <textarea class="form-control-inline text-center inventaris-live-edit auto-resize" data-field="nama_peralatan" rows="1" placeholder="(Tulis Barang)"></textarea>
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
                    $('#modalUploadBukti label.font-weight-bold').text('Pilih File Bukti (Gambar)');
                    $('#inputUploadBukti').attr('accept', 'image/*');
                    $('#modalUploadBukti .small.text-muted').html(
                        '<i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG, JPEG. Maks: 2MB.');
                    $('#inputUploadBukti').next('.custom-file-label').removeClass("selected").html(
                        'Pilih file gambar...');
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
            // 5. Timeline filter for monitoring — search button
            $('#filter-timeline-btn').on('click', function() {
                const startRaw = $('#filter-timeline-start').val();
                const endRaw = $('#filter-timeline-end').val();
                if (!startRaw && !endRaw) {
                    Swal.fire('Perhatian', 'Pilih setidaknya satu tanggal untuk memfilter timeline.',
                        'warning');
                    return;
                }
                applyMonitoringFilter();
            });

            // Refresh button — reset semua filter monitoring dan tampilkan semua data
            $('#filter-timeline-reset').on('click', function() {
                $('#filter-timeline-start').val('');
                $('#filter-timeline-end').val('');
                $('#filter-progress').val('all');
                $('#monitoring-table-body tr').each(function() {
                    const tr = $(this);
                    if (!tr.hasClass('empty-row') && !tr.hasClass('filtered-empty-row')) {
                        tr.removeClass('filtered-out');
                    }
                });
                updatePagination('monitoring-table-body', 'monitoring-pagination', 1);
                updateGrandTotal();
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
@endsection
