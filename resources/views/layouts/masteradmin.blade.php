<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('backend/Helas.jpg') }}" type="image/jpeg">

    <title>MBC CS | Dashboard</title>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />

    <!-- Custom fonts -->
    <link href="{{ asset('backend/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Custom styles -->
    <link href="{{ asset('backend/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- jQuery WAJIB PALING ATAS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Sidebar Desktop */
        .marquee {
            width: 100%;
            overflow: hidden;
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
            color: #fff;
            font-weight: bold;
            padding: 8px 0;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .marquee p {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: marquee 15s linear infinite;
            font-size: 20px;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .sidebar {
            background: linear-gradient(180deg, #1e3a8a 0%, #2563eb 100%);
            min-height: 100vh;
            width: 16rem !important;
            transition: all 0.3s ease-in-out;

            /* Fixed / Sticky Sidebar Desktop */
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1020;
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .sidebar.toggled {
            width: 6.5rem !important;
        }

        .sidebar::-webkit-scrollbar {
            display: none;
            /* Sembunyikan scrollbar sidebar agar lebih rapi */
        }

        /* Sidebar Mobile */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                /* hidden default */
                width: 220px;
                height: 100vh;
                z-index: 1050;
                transition: all 0.3s ease-in-out;
            }

            .sidebar.active {
                left: 0;
                /* show when active */
            }

            #content-wrapper {
                margin-left: 0 !important;
                padding: 1rem;
            }

            .navbar {
                padding: 0.5rem 1rem;
            }

            .navbar .btn {
                font-size: 1.2rem;
            }
        }

        /* Responsive text & spacing */
        body {
            font-size: 0.95rem;
        }

        @media (max-width: 576px) {
            body {
                font-size: 0.9rem;
            }

            .sidebar-brand img {
                height: 45px;
            }
        }

        /* Topbar Marquee */
        .topbar-marquee-container {
            flex-grow: 1;
            overflow: hidden;
            white-space: nowrap;
            margin: 0 15px;
            display: flex;
            align-items: center;
        }

        .topbar-marquee-text {
            display: inline-block;
            padding-left: 100%;
            /* Animasi Gerak + Animasi Warna RGB */
            animation: topbarMarqueeAnim 30s linear infinite, rgbFlow 3s linear infinite;
            font-size: 1.6rem;
            font-weight: 900;
            letter-spacing: 2px;
            /* RGB Rainbow Gradient */
            background: linear-gradient(90deg, #ff0000, #ff8000, #ffff00, #00ff00, #0080ff, #0000ff, #8000ff, #ff0080, #ff0000);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.4));
        }

        @keyframes rgbFlow {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 100% 50%;
            }
        }

        @keyframes topbarMarqueeAnim {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Premium Nav Styles for Produksi */
        .nav-link-premium {
            background: rgba(255, 255, 255, 0.08) !important;
            margin: 5px 12px !important;
            border-radius: 10px !important;
            padding: 12px 15px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: flex !important;
            align-items: center !important;
            border-left: 4px solid transparent !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-link-premium:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            text-decoration: none !important;
        }

        .nav-link-premium i {
            font-size: 1.1rem !important;
            width: 25px;
            text-align: center;
        }

        .nav-link-premium span {
            font-weight: 700 !important;
            letter-spacing: 0.5px;
            font-size: 0.85rem !important;
            text-transform: uppercase;
        }

        .premium-border-warning {
            border-left-color: #ffc107 !important;
        }

        .premium-border-success {
            border-left-color: #2ecc71 !important;
        }

        .premium-border-info {
            border-left-color: #3498db !important;
        }

        .sidebar-premium-heading {
            color: rgba(255, 255, 255, 0.6) !important;
            font-weight: 800 !important;
            font-size: 0.65rem !important;
            letter-spacing: 1.5px !important;
            margin-top: 20px !important;
            margin-bottom: 8px !important;
            padding-left: 20px !important;
            text-transform: uppercase;
        }

        /* 🚀 Sidebar Navigation Box Styles (Kotak-kotak) */
        .sidebar .nav-item {
            margin: 0 10px 6px 10px !important;
            transition: all 0.2s ease-in-out;
        }

        .sidebar .nav-item .nav-link {
            background: rgba(255, 255, 255, 0.07) !important;
            border-radius: 8px !important;
            padding: 12px 15px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center;
            text-align: center;
            min-height: 48px;
        }

        /* 🔄 Dinamis: Teks muncul saat EXPAND, Ikon muncul saat MINIMIZE */
        .sidebar .nav-item .nav-link i,
        .sidebar .nav-item .nav-link .fas,
        .sidebar .nav-item .nav-link .fa-solid {
            display: none !important;
            /* Default: Sembunyikan ikon saat Expand */
            font-size: 1.1rem !important;
            margin: 0 !important;
        }

        .sidebar .nav-item .nav-link span,
        .sidebar .nav-item .nav-link strong {
            display: inline-block !important;
            /* Default: Tampilkan teks saat Expand */
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            width: 100%;
            white-space: normal;
        }

        /* ↔️ Saat Sidebar Diciutkan (Minimized / Toggled) */
        .sidebar.toggled .nav-item {
            margin: 0 5px 6px 5px !important;
        }

        .sidebar.toggled .nav-item .nav-link i,
        .sidebar.toggled .nav-item .nav-link .fas,
        .sidebar.toggled .nav-item .nav-link .fa-solid {
            display: inline-block !important;
            /* Tampilkan ikon saat Minimize */
        }

        .sidebar.toggled .nav-item .nav-link span,
        .sidebar.toggled .nav-item .nav-link strong {
            display: none !important;
            /* Sembunyikan teks saat Minimize */
        }

        .sidebar .nav-item .nav-link:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-decoration: none !important;
        }

        /* 🔥 Warna Aktif Rapi */
        .sidebar .nav-item.active .nav-link {
            background: #ffffff !important;
            color: #1a3c7a !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;
            border: 1px solid #ffffff !important;
        }

        .sidebar .nav-item.active .nav-link i,
        .sidebar .nav-item.active .nav-link span,
        .sidebar .nav-item.active .nav-link strong {
            color: #1a3c7a !important;
        }

        .sidebar-divider {
            margin: 10px 20px !important;
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        /* 🖼️ Logo Adjustment (Maximize/Minimize) */
        .sidebar-brand-icon img {
            transition: all 0.3s ease !important;
        }

        .sidebar.toggled .sidebar-brand-icon img {
            height: 40px !important;
            /* Ukuran lebih kecil saat minimize */
        }

        /* Pulse effect for notification */
        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(231, 74, 59, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(231, 74, 59, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(231, 74, 59, 0);
            }
        }

        /* Pulse & Bounce effect for notification */
        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(231, 74, 59, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(231, 74, 59, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(231, 74, 59, 0);
            }
        }

        @keyframes notify-bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-4px);
            }

            60% {
                transform: translateY(-2px);
            }
        }

        @keyframes pulse-yellow {
            0% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 rgba(246, 194, 62, 0.7);
            }

            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 12px rgba(246, 194, 62, 0);
            }

            100% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 rgba(246, 194, 62, 0);
            }
        }

        .badge-pulse {
            animation: pulse-red 2s infinite, notify-bounce 4s infinite;
            border-radius: 50% !important;
            width: 18px !important;
            height: 18px !important;
            min-width: 18px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            font-size: 0.65rem !important;
            font-weight: 800 !important;
            border: 1.5px solid #fff !important;
            background-color: #e74a3b !important;
            color: #fff !important;
            flex-shrink: 0 !important;
            line-height: 1 !important;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("accordionSidebar");
            const toggleBtn = document.getElementById("sidebarToggleTop");

            if (toggleBtn) {
                toggleBtn.addEventListener("click", function() {
                    sidebar.classList.toggle("active");
                });
            }
        });
    </script>
</head>

<body id="page-top">
    @include('sweetalert::alert')

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @if (!request()->has('embed'))
            <ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">
                @php
                    $userRole = strtolower(trim(Auth::user()->role));
                    $userName = Auth::user()->name;
                    $nama = $userName ?? '';
                    $namaSMI = [];

                    $cacheVersion = \Cache::get('pending_m1t_cache_version', 1);
                    $pendingM1TCount = \Cache::remember('pending_m1t_count_' . Auth::id() . '_v' . $cacheVersion, 3600, function () use ($userRole, $userName) {
                        if (
                            $userRole === 'administrator' ||
                            Auth::user()->hasSubrole('cs_pusat')
                        ) {
                            return \App\Models\PesertaSmi::where(function ($q) {
                                $q->where('approval_status', 'Pending')->orWhereNull('approval_status');
                            })
                                ->where(function ($q) {
                                    $q->whereHas('closingCs', function ($sq) {
                                        $sq->whereIn('role', ['reseller', 'chapter', 'agen']);
                                    })
                                        ->orWhereHas('createdBy', function ($sq) {
                                            $sq->whereIn('role', ['reseller', 'chapter', 'agen']);
                                        })
                                        ->orWhereHas('salesPlan.createdBy', function ($sq) {
                                            $sq->whereIn('role', ['reseller', 'chapter', 'agen']);
                                        });
                                })
                                ->count();
                        }
                        return 0;
                    });
                @endphp

                @include('layouts.partials.sidebar.brand')

                <!-- 1. Menu Administrator -->
                @if ($userRole === 'administrator')
                    @include('layouts.partials.sidebar.admin')

                <!-- 2. Menu Marketing & Advertising -->
                @elseif (in_array($userRole, ['marketing', 'advertising']))
                    @include('layouts.partials.sidebar.marketing')

                <!-- 3. Menu Produksi -->
                @elseif ($userRole === 'produksi')
                    @include('layouts.partials.sidebar.produksi')

                <!-- 4. Menu Operasional -->
                @elseif ($userRole === 'operasional')
                    @include('layouts.partials.sidebar.operasional')

                <!-- 5. Menu Manager & HRD -->
                @elseif (in_array($userRole, ['manager', 'hrd']))
                    @include('layouts.partials.sidebar.manager_hrd')

                <!-- 6. Menu Default (CS & Jaringan Jualan Cabang) -->
                @else
                    @include('layouts.partials.sidebar.cs_cabang')
                @endif

                {{-- Menu Activity/ToDoList (Untuk divisi selain CS, Admin, Chapter, Agen, Reseller) --}}
                @if (
                    !in_array($userRole, ['administrator', 'cs-mbc', 'cs-smi', 'chapter', 'reseller', 'agen']) &&
                    !str_starts_with($userRole, 'chapter_')
                )
                    <li class="nav-item {{ request()->routeIs('agenda.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('agenda.index') }}" title="Activity/ToDoList">
                            <i class="fas fa-fw fa-tasks"></i>
                            <span><strong>Activity/ToDoList</strong></span>
                        </a>
                    </li>
                @endif

                <!-- 7. Menu Shared / Bersama -->
                @include('layouts.partials.sidebar.shared')
                <hr class="sidebar-divider d-none d-md-block" />

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"
                        style="background: rgba(255,255,255,0.2); color: #fff;"></button>
                </div>
            </ul>
        @endif
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                @if (!request()->has('embed'))
                    <!-- Topbar -->
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"
                        style="position: sticky; top: 0; z-index: 1100;">
                        <!-- Sidebar Toggle (Topbar) -->
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>

                        @if (!in_array($userRole, ['chapter', 'reseller']))
                            <!-- Topbar Running Text -->
                            <div class="topbar-marquee-container d-none d-md-flex">
                                <div class="topbar-marquee-text">
                                    @if (request()->routeIs('admin.dailyactivity.*') || request()->is('admin/dailyactivity*'))
                                        📝 JANGAN LUPA MENGISI DAILY ACTIVITY SETIAP JAM 15.00 , SEMANGAT... 💪
                                    @else
                                        ✨ SELAMAT DATANG DI HELAS CORP. SELAMAT BEKERJA, ✨
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Topbar Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                            <li class="nav-item dropdown no-arrow d-sm-none">
                                <a class="nav-link dropdown-toggle" href="#" id="searchDropdown"
                                    role="button" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <i class="fas fa-search fa-fw"></i>
                                </a>
                                <!-- Dropdown - Messages -->
                                <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                    aria-labelledby="searchDropdown">
                                    <form class="form-inline mr-auto w-100 navbar-search">
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-light border-0 small"
                                                placeholder="Search for..." aria-label="Search"
                                                aria-describedby="basic-addon2" />
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button">
                                                    <i class="fas fa-search fa-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </li>

                            <!-- ================== NAVBAR NOTIFIKASI (DIHAPUS SESUAI REQUEST) ================== -->
                            {{-- @if (auth()->user()->role !== 'administrator')
                                <li class="nav-item mx-1">
                                    <a class="nav-link position-relative notif-bell" href="{{ route('notifikasi.index') }}">
                                        <i class="fas fa-bell fa-lg text-primary"></i>
                                        @if (isset($notifCount) && $notifCount > 0)
                                        <span class="badge badge-pill badge-danger badge-counter pulse-badge">
                                            {{ $notifCount }}
                                        </span>
                                        @endif
                                    </a>
                                </li>
                                @endif --}}

                            <!-- ================== NAVBAR PESAN MASUK (ADMIN) ================== -->
                            @if (auth()->user()->role === 'administrator')
                                <li class="nav-item mx-1">
                                    <a class="nav-link position-relative notif-message"
                                        href="{{ route('admin.messages.index') }}">
                                        <i class="fas fa-envelope fa-lg text-primary"></i>
                                        @if (isset($messageCount) && $messageCount > 0)
                                            <span class="badge badge-pill badge-danger badge-counter pulse-badge">
                                                {{ $messageCount }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            <!-- ================== STYLE BADGE ================== -->
                            <style>
                                /* Lonceng & Pesan */
                                .notif-bell,
                                .notif-message {
                                    display: flex;
                                    align-items: center;
                                }

                                .badge-counter {
                                    font-size: 0.65rem;
                                    padding: 3px 6px;
                                }

                                .pulse-badge {
                                    position: absolute;
                                    top: 9px;
                                    right: 6px;
                                    min-width: 18px;
                                    height: 18px;
                                    font-size: 0.7rem;
                                    padding: 0;
                                    border-radius: 50%;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    animation: pulse 1.5s infinite;
                                }

                                @keyframes pulse {
                                    0% {
                                        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
                                    }

                                    70% {
                                        box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
                                    }

                                    100% {
                                        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
                                    }
                                }

                                .notif-bell:hover i {
                                    color: #f59e0b;
                                    transform: scale(1.1);
                                    transition: 0.3s;
                                }

                                .notif-message:hover i {
                                    color: #2563eb;
                                    transform: scale(1.1);
                                    transition: 0.3s;
                                }
                            </style>

                            <div class="topbar-divider d-none d-sm-block"></div>

                            <!-- Nav Item - User Information -->
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline d-flex align-items-center">
                                        <span class="badge badge-info px-2 py-1 shadow-sm mr-2"
                                            style="font-size: 10px; font-weight: 800;">{{ Auth::user()->role === 'reseller' ? 'AGEN' : strtoupper(Auth::user()->role) }}</span>
                                        @if (Auth::user()->chapter)
                                            <span class="badge badge-light border shadow-sm px-2 py-1 mr-2"
                                                style="font-size: 10px; color: #5a5c69; font-weight: 700;">
                                                @if (Auth::user()->kategori === 'Agen Pusat')
                                                    KOTA {{ strtoupper(Auth::user()->chapter) }}
                                                @else
                                                    CHAPTER {{ strtoupper(Auth::user()->chapter) }}
                                                @endif
                                            </span>
                                        @endif
                                        <span
                                            class="text-gray-700 font-weight-bold small">{{ Auth::user()->name }}</span>
                                    </span>
                                    @if (Auth::user()->photo)
                                        <img class="img-profile rounded-circle"
                                            src="{{ asset(Auth::user()->photo) }}"
                                            style="object-fit: cover; width:35px; height:35px;">
                                    @else
                                        <img class="img-profile rounded-circle"
                                            src="{{ asset('backend/img/undraw_profile.svg') }}"
                                            style="width:35px; height:35px;">
                                    @endif
                                </a>
                                <!-- Dropdown - User Information -->
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown"
                                    style="z-index: 9999 !important; min-width: 160px;">
                                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Profile
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" data-toggle="modal"
                                        data-target="#logoutModal">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>
                @endif
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid"
                    @if (request()->has('embed')) style="padding: 0; margin: 0; width: 100%; max-width: 100%;" @endif>
                    <!-- Isi Konten -->
                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <!--
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Muslim Bisnis Coaching - 2025 </span>
                    </div>
                </div>
            </footer>
            -->
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <!--
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    -->

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Apakah anda yakin ingi Keluar ?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">X</span>
                    </button>
                </div>
                <div class="modal-body">
                    Pilih "Logout" Jika anda ingin keluar dari sistem.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        Cancel
                    </button>
                    <!-- Logout Redirect Login -->
                    <a class="btn btn-primary" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery WAJIB PALING ATAS (MOVED TO HEAD) -->

    <!-- Bootstrap (harus setelah jQuery, include Popper.js) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('backend/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- SB Admin (butuh jQuery) -->
    <script src="{{ asset('backend/js/sb-admin-2.min.js') }}"></script>

    <!-- ChartJS -->
    <script src="{{ asset('backend/vendor/chart.js/Chart.min.js') }}"></script>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/fb703282bd.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    <script>
        $(document).ready(function() {
            $("#close").click(function() {
                $("#exampleModal").modal("hide");
            });
        });
    </script>

    <!-- Database Realtime Notification script -->
    <script>
        $(document).ready(function() {
            // CSS styles for pulsing dot
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    @keyframes pulse-red {
                        0% {
                            transform: scale(0.9);
                            box-shadow: 0 0 0 0 rgba(231, 74, 59, 0.9);
                        }
                        70% {
                            transform: scale(1.1);
                            box-shadow: 0 0 0 8px rgba(231, 74, 59, 0);
                        }
                        100% {
                            transform: scale(0.9);
                            box-shadow: 0 0 0 0 rgba(231, 74, 59, 0);
                        }
                    }
                    .db-pulse-badge {
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        width: 16px !important;
                        height: 16px !important;
                        min-width: 16px !important;
                        max-width: 16px !important;
                        padding: 0 !important;
                        background-color: #e74a3b !important; /* Red color */
                        color: white !important;
                        font-size: 10px !important;
                        font-weight: 700 !important;
                        border-radius: 50% !important;
                        margin-right: 8px !important;
                        vertical-align: middle !important;
                        box-shadow: 0 0 0 0 rgba(231, 74, 59, 0.9);
                        animation: pulse-red 1.5s infinite !important;
                        flex-shrink: 0 !important;
                    }
                `)
                .appendTo('head');

            let originalTitle = document.title;
            let lastCountKey = 'last_db_count_' + '{{ auth()->check() ? auth()->user()->id : 0 }}';
            let isInitialLoad = true;

            function checkNewDatabase() {
                $.ajax({
                    url: "{{ route('admin.database.realtime-count') }}",
                    method: 'GET',
                    success: function(response) {
                        if (response && typeof response.count !== 'undefined') {
                            let currentCount = parseInt(response.count);
                            let lastCount = localStorage.getItem(lastCountKey);

                            // If not set, initialize with current count
                            if (lastCount === null) {
                                localStorage.setItem(lastCountKey, currentCount);
                                lastCount = currentCount;
                            } else {
                                lastCount = parseInt(lastCount);
                            }

                            if (isInitialLoad) {
                                // On initial load, synchronize count to avoid showing stale notifications
                                localStorage.setItem(lastCountKey, currentCount);
                                lastCount = currentCount;
                                isInitialLoad = false;
                            }

                            // Always hide icons for database links to keep sidebar clean as requested
                            $('a[href*="database"]').each(function() {
                                $(this).find('i').hide();
                            });

                            let isOnDatabasePage = window.location.pathname.includes('/database');

                            if (currentCount > lastCount) {
                                let diff = currentCount - lastCount;

                                // CS is on another page or database page. Update tab title and sidebar pulsing badge!
                                document.title = '(' + diff + ') ' + originalTitle;

                                $('a[href*="database"]').each(function() {
                                    let $badge = $(this).find('.db-pulse-badge');
                                    if ($badge.length) {
                                        $badge.text(diff);
                                    } else {
                                        $(this).prepend('<span class="db-pulse-badge">' + diff +
                                            '</span>');
                                    }
                                });

                                if (isOnDatabasePage) {
                                    // CS is on the database page. Show a premium toast alert to notify them once!
                                    let lastShownToastCount = sessionStorage.getItem(
                                        'last_shown_toast_count');
                                    if (lastShownToastCount === null || parseInt(lastShownToastCount) <
                                        currentCount) {
                                        sessionStorage.setItem('last_shown_toast_count', currentCount);

                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'info',
                                            title: 'Ada ' + diff + ' data baru masuk via Link!',
                                            showConfirmButton: true,
                                            confirmButtonText: 'Segarkan Halaman',
                                            timer: 15000,
                                            timerProgressBar: true,
                                            showCloseButton: true,
                                            didOpen: (toast) => {
                                                toast.addEventListener('mouseenter', Swal
                                                    .stopTimer)
                                                toast.addEventListener('mouseleave', Swal
                                                    .resumeTimer)
                                            }
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                localStorage.setItem(lastCountKey,
                                                    currentCount);
                                                window.location.reload();
                                            }
                                        });
                                    }
                                }
                            } else {
                                // If they are on the database page and no new data is present, synchronize localStorage
                                if (isOnDatabasePage) {
                                    localStorage.setItem(lastCountKey, currentCount);
                                }

                                // Reset title and remove badge when count is <= lastCount
                                document.title = originalTitle;
                                $('.db-pulse-badge').remove();
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Realtime DB count check failed');
                    }
                });
            }

            // Check immediately on load
            checkNewDatabase();

            // Poll every 15 seconds
            setInterval(checkNewDatabase, 15000);
        });
    </script>
    @if (request()->has('embed'))
        <script>
            $(document).ready(function() {
                function patchFormsAndLinks() {
                    // 1. Add embed=1 hidden inputs to all forms
                    $('form').each(function() {
                        if ($(this).find('input[name="embed"]').length === 0) {
                            $(this).append('<input type="hidden" name="embed" value="1">');
                        }
                    });

                    // 2. Append embed=1 to all normal links
                    $('a').each(function() {
                        var href = $(this).attr('href');
                        if (href && href !== '#' && href !== '' && href.indexOf('javascript:') !== 0 && href
                            .indexOf('tel:') !== 0 && href.indexOf('mailto:') !== 0) {
                            if (href.indexOf('?') === -1) {
                                $(this).attr('href', href + '?embed=1');
                            } else if (href.indexOf('embed=1') === -1) {
                                $(this).attr('href', href + '&embed=1');
                            }
                        }
                    });
                }

                // Initial patch on page load
                patchFormsAndLinks();

                // Keep patching when DOM changes via AJAX
                $(document).ajaxComplete(function() {
                    patchFormsAndLinks();
                });

                // Safe observer in case of dynamic modal / dropdown inserts
                var observer = new MutationObserver(function() {
                    patchFormsAndLinks();
                });
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            });
        </script>
    @endif
</body>

</html>
