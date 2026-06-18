@extends('layouts.masteradmin')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fc;
        }

        .monitoring-container {
            padding: 2rem;
            min-height: 100vh;
        }

        .premium-card {
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
            background: white;
            transition: all 0.3s ease;
        }

        .premium-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        /* Tabs styling */
        .custom-tabs {
            border-bottom: 2px solid #eaecf4;
            padding-bottom: 0px;
            display: flex;
            gap: 15px;
        }

        .custom-tabs .nav-item {
            margin-bottom: -2px;
        }

        .custom-tabs .nav-link {
            border: none;
            background: transparent;
            color: #858796;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 12px 24px;
            border-radius: 0;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .custom-tabs .nav-link:hover {
            color: #6610f2;
        }

        .custom-tabs .nav-link.active {
            color: #6610f2;
            background: transparent;
            border-bottom: 3px solid #6610f2;
        }

        .badge-purple {
            background-color: rgba(102, 16, 242, 0.1);
            color: #6610f2;
            border: 1px solid rgba(102, 16, 242, 0.2);
            font-weight: 700;
        }

        .badge-agent-pusat {
            background-color: rgba(246, 194, 62, 0.15) !important;
            color: #b58100 !important;
            border: 1px solid rgba(246, 194, 62, 0.3);
            font-weight: 700;
        }

        .toggle-chevron {
            transition: transform 0.2s ease;
        }

        .chapter-row {
            cursor: pointer;
            border-left: 4px solid #6610f2;
            transition: background-color 0.2s;
        }

        .chapter-row:hover {
            background-color: #f8f6ff !important;
        }

        .agent-row {
            background-color: #fafbfe;
            border-left: 4px solid #dddfeb;
            transition: background-color 0.2s;
        }

        .agent-row:hover {
            background-color: #f4f6fa !important;
        }

        /* Inline input styling */
        .activity-input {
            width: 75px !important;
            text-align: center;
            background: #fff;
            border: 1.5px solid #d1d3e2;
            border-radius: 8px;
            padding: 5px 8px;
            font-weight: 700;
            font-size: 0.9rem;
            color: #333;
            outline: none;
            transition: all 0.2s ease;
        }

        .activity-input:focus {
            border-color: #6610f2;
            box-shadow: 0 0 0 3px rgba(102, 16, 242, 0.1);
        }

        .form-control-inline {
            background: transparent !important;
            border: 1px solid transparent !important;
            padding: 5px 8px !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #333 !important;
            border-radius: 6px !important;
            transition: border-color 0.15s, background 0.15s !important;
            outline: none !important;
        }

        .form-control-inline:hover {
            background: rgba(102, 16, 242, 0.03) !important;
            border-color: rgba(102, 16, 242, 0.15) !important;
        }

        .form-control-inline:focus {
            background: #fff !important;
            border-color: #6610f2 !important;
            box-shadow: 0 0 0 3px rgba(102, 16, 242, 0.1) !important;
        }

        /* Evaluation Badge/Select options */
        .badge-eval {
            font-weight: 700;
            border-radius: 30px;
            padding: 6px 14px;
            font-size: 0.8rem;
            display: inline-block;
            text-align: center;
            border: none;
            outline: none;
            cursor: pointer;
            text-align-last: center;
            -webkit-appearance: none;
            appearance: none;
        }

        .badge-eval-tercapai {
            background-color: rgba(28, 200, 138, 0.12) !important;
            color: #1cc88a !important;
            border: 1px solid rgba(28, 200, 138, 0.25) !important;
        }

        .badge-eval-hampir {
            background-color: rgba(246, 194, 62, 0.15) !important;
            color: #d39e00 !important;
            border: 1px solid rgba(246, 194, 62, 0.3) !important;
        }

        .badge-eval-belum {
            background-color: rgba(231, 74, 59, 0.12) !important;
            color: #e74a3b !important;
            border: 1px solid rgba(231, 74, 59, 0.25) !important;
        }

        .badge-eval-other {
            background-color: rgba(133, 135, 150, 0.12) !important;
            color: #858796 !important;
            border: 1px solid rgba(133, 135, 150, 0.25) !important;
        }

        /* Save Toast */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .save-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            background: linear-gradient(135deg, #4b12ab 0%, #6610f2 100%);
            color: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 230px;
            box-shadow: 0 6px 24px rgba(102, 16, 242, 0.25);
            animation: slideInRight 0.3s ease;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .save-toast.hiding {
            opacity: 0;
            transform: translateX(40px);
        }
    </style>

    <div class="monitoring-container">
        <!-- Page Title -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 text-gray-800 font-weight-bold"><i class="fas fa-file-contract text-purple mr-2"></i> Monitoring Chapter & Agen</h1>
                <p class="text-muted mb-0">Pantau target bulanan peserta event, realisasi closing, dan saldo keuangan Chapter & Agen.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background-color: rgba(28, 200, 138, 0.15); color: #1cc88a;">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: #1cc88a; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Tab Links Navigation -->
        <ul class="nav nav-pills custom-tabs mb-4" id="monitoringTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-data-link" data-toggle="pill" href="#tab-data" role="tab">
                    <i class="fas fa-users"></i> Data chapter & agen
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-openhouse-link" data-toggle="pill" href="#tab-openhouse" role="tab">
                    <i class="fas fa-calendar-alt"></i> Open house chapter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-autopilot-link" data-toggle="pill" href="#tab-autopilot" role="tab">
                    <i class="fas fa-rocket"></i> AutoPilot chapter
                </a>
            </li>
        </ul>

        <!-- Tab Content Area -->
        <div class="tab-content" id="monitoringTabsContent">

            <!-- TAB 1: DATA CHAPTER & AGEN -->
            <div class="tab-pane fade show active" id="tab-data" role="tabpanel">
                
                <!-- Daftar Chapter & Agen -->
                <div class="card premium-card mb-4" style="overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-users mr-2 text-purple"></i> Daftar chapter & agen
                                </h5>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; border: 1px solid #eaecf4;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                    <input type="text" id="searchChapterAgent" class="form-control border-0" placeholder="Cari nama..." style="box-shadow: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="table-chapter-agent" style="min-width: 800px;">
                                <thead>
                                    <tr class="text-uppercase text-muted" style="font-size: 0.85rem; border-bottom: 2px solid #eaecf4;">
                                        <th style="width: 50px;"></th>
                                        <th>Nama</th>
                                        <th>Lokasi</th>
                                        <th>Tipe</th>
                                        <th class="text-center">Closing Bulan Ini</th>
                                        <th class="text-center">Peserta Aktif</th>
                                        <th class="text-right">Total Penghasilan</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($chaptersData as $ch)
                                        <!-- Chapter Row -->
                                        <tr class="chapter-row align-middle bg-light" data-id="{{ $ch['id'] }}" data-name="{{ strtolower($ch['name']) }}">
                                            <td class="text-center text-muted toggle-icon-cell" style="width: 50px;">
                                                @if(count($ch['agents']) > 0)
                                                    <i class="fas fa-chevron-right toggle-chevron" style="transition: transform 0.2s;"></i>
                                                @else
                                                    <span class="text-muted" style="font-size: 0.75rem;">•</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">
                                                {{ $ch['name'] }}
                                                @if(count($ch['agents']) > 0)
                                                    <span class="badge badge-secondary ml-1" style="font-size: 0.75rem; border-radius: 10px;">{{ count($ch['agents']) }} agen</span>
                                                @endif
                                            </td>
                                            <td>{{ $ch['location'] }}</td>
                                            <td>
                                                <span class="badge badge-purple px-2 py-1" style="border-radius: 6px; font-size: 0.75rem;">Chapter</span>
                                            </td>
                                            <td class="text-center font-weight-bold text-success">
                                                {{ $ch['closing_bulan_ini'] }} peserta
                                            </td>
                                            <td class="text-center font-weight-bold text-dark">
                                                {{ $ch['peserta_aktif'] }} peserta
                                            </td>
                                            <td class="text-right font-weight-bold text-success">
                                                Rp {{ number_format($ch['earnings'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-right font-weight-bold text-primary">
                                                Rp {{ number_format($ch['saldo'], 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <!-- Agent Rows for this Chapter -->
                                        @foreach($ch['agents'] as $ag)
                                            <tr class="agent-row child-of-{{ $ch['id'] }} align-middle" data-name="{{ strtolower($ag['name']) }}" style="display: none;">
                                                <td></td>
                                                <td class="pl-4 text-gray-700" style="padding-left: 2rem !important;">
                                                    <i class="fas fa-angle-right mr-2 text-muted"></i>{{ $ag['name'] }}
                                                </td>
                                                <td class="text-muted">{{ $ag['location'] }}</td>
                                                <td>
                                                    <span class="badge badge-light border text-dark px-2 py-1" style="border-radius: 6px; font-size: 0.75rem;">Agen</span>
                                                </td>
                                                <td class="text-center font-weight-bold text-success">
                                                    {{ $ag['closing_bulan_ini'] }} peserta
                                                </td>
                                                <td class="text-center font-weight-bold text-dark">
                                                    {{ $ag['peserta_aktif'] }} peserta
                                                </td>
                                                <td class="text-right font-weight-bold text-success">
                                                    Rp {{ number_format($ag['earnings'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-right font-weight-bold text-primary">
                                                    Rp {{ number_format($ag['saldo'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">Belum ada data chapter & agen.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Agen Pusat Card -->
                <div class="card premium-card" style="overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-building mr-2 text-warning"></i> Agen pusat
                                </h5>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; border: 1px solid #eaecf4;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                    <input type="text" id="searchAgentPusat" class="form-control border-0" placeholder="Cari nama..." style="box-shadow: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                                <thead>
                                    <tr class="text-uppercase text-muted" style="font-size: 0.85rem; border-bottom: 2px solid #eaecf4;">
                                        <th style="width: 50px;"></th>
                                        <th>Nama</th>
                                        <th>Lokasi</th>
                                        <th>Tipe</th>
                                        <th class="text-center">Closing Bulan Ini</th>
                                        <th class="text-center">Peserta Aktif</th>
                                        <th class="text-right">Total Penghasilan</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($agenPusatData as $ap)
                                        <tr class="align-middle agent-pusat-row" data-name="{{ strtolower($ap['name']) }}">
                                            <td></td>
                                            <td class="font-weight-bold text-dark">{{ $ap['name'] }}</td>
                                            <td>{{ $ap['location'] }}</td>
                                            <td>
                                                <span class="badge badge-agent-pusat px-2 py-1" style="border-radius: 6px; font-size: 0.75rem;">Agen pusat</span>
                                            </td>
                                            <td class="text-center font-weight-bold text-success">
                                                {{ $ap['closing_bulan_ini'] }} peserta
                                            </td>
                                            <td class="text-center font-weight-bold text-dark">
                                                {{ $ap['peserta_aktif'] }} peserta
                                            </td>
                                            <td class="text-right font-weight-bold text-success">
                                                Rp {{ number_format($ap['earnings'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-right font-weight-bold text-primary">
                                                Rp {{ number_format($ap['saldo'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">Belum ada data agen pusat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- TAB 2: OPEN HOUSE CHAPTER -->
            <div class="tab-pane fade" id="tab-openhouse" role="tabpanel">
                
                <!-- Period Filter Dropdown -->
                <div class="row align-items-center mb-4">
                    <div class="col-auto d-flex align-items-center">
                        <label class="mr-2 mb-0 font-weight-bold text-dark" style="font-size: 0.95rem;">
                            <i class="fas fa-calendar-alt text-muted mr-1"></i> Periode:
                        </label>
                        <select class="form-control bg-white shadow-sm periode-filter-select" style="border-radius: 8px; border: 1.5px solid #d1d3e2; font-weight: 700; width: 200px;">
                            @foreach ($periodsList as $val => $lbl)
                                <option value="{{ $val }}" {{ $periode === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- KPI Cards for Open House -->
                <div class="row mb-4">
                    <!-- Target Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card premium-card h-100 py-2" style="border-left: 5px solid #6610f2 !important;">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #6610f2;">
                                    <i class="fas fa-bullseye mr-1"></i> Target Peserta Open House
                                </div>
                                <div class="h2 mb-0 font-weight-bold text-gray-800" id="openhouse-target-card">{{ $targetOpenHouse }}</div>
                                <div class="text-xs text-muted mt-1">peserta bulan ini</div>
                            </div>
                        </div>
                    </div>

                    <!-- Realisasi Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card premium-card h-100 py-2" style="border-left: 5px solid #1cc88a !important;">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <i class="fas fa-check-circle mr-1"></i> Realisasi Saat Ini
                                </div>
                                <div class="h2 mb-0 font-weight-bold text-gray-800" id="openhouse-realisasi-card">{{ $realisasiOpenHouse }}</div>
                                <div class="text-xs text-muted mt-1">peserta terdaftar</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kekurangan Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card premium-card h-100 py-2" style="border-left: 5px solid #e74a3b !important;">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Kekurangan
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="h2 mb-0 font-weight-bold text-gray-800" id="openhouse-kurang-persen-card">{{ $kurangOpenHousePersen }}%</div>
                                    <small class="text-muted" id="openhouse-kurang-detail-card">{{ $kurangOpenHouse }} peserta masih kurang</small>
                                </div>
                                <div class="progress progress-sm" style="border-radius: 5px; height: 8px;">
                                    <div class="progress-bar bg-danger" id="openhouse-progress-bar" role="progressbar"
                                        style="width: {{ $kurangOpenHousePersen }}%; border-radius: 5px;" 
                                        aria-valuenow="{{ $kurangOpenHousePersen }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Setup Target Dynamic -->
                <div class="card premium-card mb-4" style="background-color: #fcfbfe;">
                    <div class="card-body p-3">
                        <form action="{{ route('admin.monitoring-chapter.target.update') }}" method="POST" class="form-inline">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $periode }}">
                            <input type="hidden" name="type" value="open_house">
                            <div class="form-group mb-0 mr-sm-3">
                                <label class="mr-3 font-weight-bold text-dark" style="font-size: 0.9rem;">Target bulan ini:</label>
                                <input type="number" class="form-control bg-white shadow-sm" name="target" value="{{ $targetOpenHouse }}" min="0" required style="border-radius: 8px; border: 1.5px solid #d1d3e2; font-weight: 700; width: 100px; text-align: center;">
                            </div>
                            <button type="submit" class="btn btn-dark shadow-sm px-4" style="border-radius: 8px; font-weight: 600;">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table activities -->
                <div class="card premium-card" style="overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="font-weight-bold mb-0 text-dark">
                                <i class="fas fa-table mr-2 text-purple"></i> Tabel open house chapter
                            </h5>
                            <button class="btn btn-dark shadow-sm px-3" style="border-radius: 8px; font-weight: 600;" onclick="addActivityRow('open_house')">
                                <i class="fas fa-plus mr-1"></i> Tambah Activity
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                                <thead>
                                    <tr class="text-uppercase text-muted" style="font-size: 0.85rem; border-bottom: 2px solid #eaecf4;">
                                        <th style="width: 5%;">No.</th>
                                        <th style="width: 20%;">Chapter</th>
                                        <th style="width: 20%;">Tanggal</th>
                                        <th style="width: 15%; text-align: center;">Target</th>
                                        <th style="width: 15%; text-align: center;">Realisasi</th>
                                        <th style="width: 20%; text-align: center;">Evaluasi</th>
                                        <th style="width: 5%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        function getEvalBadgeClass($eval) {
                                            $evalLower = strtolower($eval);
                                            if (strpos($evalLower, 'tercapai') !== false && strpos($evalLower, 'hampir') === false) {
                                                return 'badge-eval-tercapai';
                                            } elseif (strpos($evalLower, 'hampir') !== false) {
                                                return 'badge-eval-hampir';
                                            } elseif (strpos($evalLower, 'belum') !== false) {
                                                return 'badge-eval-belum';
                                            } else {
                                                return 'badge-eval-other';
                                            }
                                        }
                                    @endphp
                                    @forelse($openHouseActivities as $idx => $act)
                                        <tr id="activity-row-{{ $act->id }}" class="align-middle">
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                <select class="form-control-inline chapter-val font-weight-bold text-dark" onchange="updateActivityInline({{ $act->id }}, 'chapter_id', this.value)" style="width: 100% !important;">
                                                    <option value="" {{ is_null($act->chapter_id) ? 'selected' : '' }}>-- Pilih --</option>
                                                    @foreach($allChaptersList as $chapterOpt)
                                                        <option value="{{ $chapterOpt->id }}" data-location="{{ $chapterOpt->chapter ?? '' }}" {{ $act->chapter_id == $chapterOpt->id ? 'selected' : '' }}>
                                                            {{ $chapterOpt->name }}{{ $chapterOpt->role === 'agen' ? ' (Agen Pusat)' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="city-badge-container mt-1" style="{{ ($act->chapter && $act->chapter->chapter) ? '' : 'display: none;' }}">
                                                    @if($act->chapter && $act->chapter->chapter)
                                                        <span class="badge badge-light border text-muted px-2 py-1 city-badge" style="font-size: 0.75rem; border-radius: 6px;">
                                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $act->chapter->chapter }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <input type="date" class="form-control-inline date-val" value="{{ $act->tanggal }}" onchange="updateActivityInline({{ $act->id }}, 'tanggal', this.value)" style="width: 140px !important;">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="number" class="activity-input target-val" value="{{ $act->target }}" min="0" onchange="updateActivityInline({{ $act->id }}, 'target', this.value)">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="number" class="activity-input realisasi-val" value="{{ $act->realisasi }}" min="0" onchange="updateActivityInline({{ $act->id }}, 'realisasi', this.value)">
                                            </td>
                                            <td style="text-align: center;">
                                                <select class="badge-eval {{ getEvalBadgeClass($act->evaluasi) }} select-eval" onchange="updateActivityInline({{ $act->id }}, 'evaluasi', this.value)">
                                                    <option value="Belum ada peserta" {{ $act->evaluasi === 'Belum ada peserta' ? 'selected' : '' }}>Belum ada peserta</option>
                                                    <option value="Hampir tercapai" {{ $act->evaluasi === 'Hampir tercapai' ? 'selected' : '' }}>Hampir tercapai</option>
                                                    <option value="Tercapai" {{ $act->evaluasi === 'Tercapai' ? 'selected' : '' }}>Tercapai</option>
                                                    <option value="Perlu follow-up lebih intensif" {{ $act->evaluasi === 'Perlu follow-up lebih intensif' ? 'selected' : '' }}>Perlu follow-up lebih intensif</option>
                                                    @if(!in_array($act->evaluasi, ['Belum ada peserta', 'Hampir tercapai', 'Tercapai', 'Perlu follow-up lebih intensif']))
                                                        <option value="{{ $act->evaluasi }}" selected>{{ $act->evaluasi }}</option>
                                                    @endif
                                                    <option value="custom">Kustom...</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <button class="btn btn-sm btn-link text-danger" onclick="deleteActivity({{ $act->id }})" title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data aktivitas open house pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- TAB 3: AUTOPILOT CHAPTER -->
            <div class="tab-pane fade" id="tab-autopilot" role="tabpanel">
                
                <!-- Period Filter Dropdown -->
                <div class="row align-items-center mb-4">
                    <div class="col-auto d-flex align-items-center">
                        <label class="mr-2 mb-0 font-weight-bold text-dark" style="font-size: 0.95rem;">
                            <i class="fas fa-calendar-alt text-muted mr-1"></i> Periode:
                        </label>
                        <select class="form-control bg-white shadow-sm periode-filter-select" style="border-radius: 8px; border: 1.5px solid #d1d3e2; font-weight: 700; width: 200px;">
                            @foreach ($periodsList as $val => $lbl)
                                <option value="{{ $val }}" {{ $periode === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- KPI Cards for AutoPilot -->
                <div class="row mb-4">
                    <!-- Target Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card premium-card h-100 py-2" style="border-left: 5px solid #6610f2 !important;">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #6610f2;">
                                    <i class="fas fa-bullseye mr-1"></i> Target Peserta AutoPilot
                                </div>
                                <div class="h2 mb-0 font-weight-bold text-gray-800" id="autopilot-target-card">{{ $targetAutoPilot }}</div>
                                <div class="text-xs text-muted mt-1">peserta bulan ini</div>
                            </div>
                        </div>
                    </div>

                    <!-- Realisasi Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card premium-card h-100 py-2" style="border-left: 5px solid #1cc88a !important;">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <i class="fas fa-check-circle mr-1"></i> Realisasi Saat Ini
                                </div>
                                <div class="h2 mb-0 font-weight-bold text-gray-800" id="autopilot-realisasi-card">{{ $realisasiAutoPilot }}</div>
                                <div class="text-xs text-muted mt-1">peserta terdaftar</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kekurangan Card -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card premium-card h-100 py-2" style="border-left: 5px solid #e74a3b !important;">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Kekurangan
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="h2 mb-0 font-weight-bold text-gray-800" id="autopilot-kurang-persen-card">{{ $kurangAutoPilotPersen }}%</div>
                                    <small class="text-muted" id="autopilot-kurang-detail-card">{{ $kurangAutoPilot }} peserta masih kurang</small>
                                </div>
                                <div class="progress progress-sm" style="border-radius: 5px; height: 8px;">
                                    <div class="progress-bar bg-danger" id="autopilot-progress-bar" role="progressbar"
                                        style="width: {{ $kurangAutoPilotPersen }}%; border-radius: 5px;" 
                                        aria-valuenow="{{ $kurangAutoPilotPersen }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Setup Target Dynamic -->
                <div class="card premium-card mb-4" style="background-color: #fcfbfe;">
                    <div class="card-body p-3">
                        <form action="{{ route('admin.monitoring-chapter.target.update') }}" method="POST" class="form-inline">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $periode }}">
                            <input type="hidden" name="type" value="autopilot">
                            <div class="form-group mb-0 mr-sm-3">
                                <label class="mr-3 font-weight-bold text-dark" style="font-size: 0.9rem;">Target bulan ini:</label>
                                <input type="number" class="form-control bg-white shadow-sm" name="target" value="{{ $targetAutoPilot }}" min="0" required style="border-radius: 8px; border: 1.5px solid #d1d3e2; font-weight: 700; width: 100px; text-align: center;">
                            </div>
                            <button type="submit" class="btn btn-dark shadow-sm px-4" style="border-radius: 8px; font-weight: 600;">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table activities -->
                <div class="card premium-card" style="overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="font-weight-bold mb-0 text-dark">
                                <i class="fas fa-table mr-2 text-purple"></i> Tabel autopilot chapter
                            </h5>
                            <button class="btn btn-dark shadow-sm px-3" style="border-radius: 8px; font-weight: 600;" onclick="addActivityRow('autopilot')">
                                <i class="fas fa-plus mr-1"></i> Tambah Activity
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                                <thead>
                                    <tr class="text-uppercase text-muted" style="font-size: 0.85rem; border-bottom: 2px solid #eaecf4;">
                                        <th style="width: 5%;">No.</th>
                                        <th style="width: 20%;">Chapter</th>
                                        <th style="width: 20%;">Tanggal</th>
                                        <th style="width: 15%; text-align: center;">Target</th>
                                        <th style="width: 15%; text-align: center;">Realisasi</th>
                                        <th style="width: 20%; text-align: center;">Evaluasi</th>
                                        <th style="width: 5%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($autoPilotActivities as $idx => $act)
                                        <tr id="activity-row-{{ $act->id }}" class="align-middle">
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                <select class="form-control-inline chapter-val font-weight-bold text-dark" onchange="updateActivityInline({{ $act->id }}, 'chapter_id', this.value)" style="width: 100% !important;">
                                                    <option value="" {{ is_null($act->chapter_id) ? 'selected' : '' }}>-- Pilih --</option>
                                                    @foreach($allChaptersList as $chapterOpt)
                                                        <option value="{{ $chapterOpt->id }}" data-location="{{ $chapterOpt->chapter ?? '' }}" {{ $act->chapter_id == $chapterOpt->id ? 'selected' : '' }}>
                                                            {{ $chapterOpt->name }}{{ $chapterOpt->role === 'agen' ? ' (Agen Pusat)' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="city-badge-container mt-1" style="{{ ($act->chapter && $act->chapter->chapter) ? '' : 'display: none;' }}">
                                                    @if($act->chapter && $act->chapter->chapter)
                                                        <span class="badge badge-light border text-muted px-2 py-1 city-badge" style="font-size: 0.75rem; border-radius: 6px;">
                                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $act->chapter->chapter }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <input type="date" class="form-control-inline date-val" value="{{ $act->tanggal }}" onchange="updateActivityInline({{ $act->id }}, 'tanggal', this.value)" style="width: 140px !important;">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="number" class="activity-input target-val" value="{{ $act->target }}" min="0" onchange="updateActivityInline({{ $act->id }}, 'target', this.value)">
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="number" class="activity-input realisasi-val" value="{{ $act->realisasi }}" min="0" onchange="updateActivityInline({{ $act->id }}, 'realisasi', this.value)">
                                            </td>
                                            <td style="text-align: center;">
                                                <select class="badge-eval {{ getEvalBadgeClass($act->evaluasi) }} select-eval" onchange="updateActivityInline({{ $act->id }}, 'evaluasi', this.value)">
                                                    <option value="Belum ada peserta" {{ $act->evaluasi === 'Belum ada peserta' ? 'selected' : '' }}>Belum ada peserta</option>
                                                    <option value="Hampir tercapai" {{ $act->evaluasi === 'Hampir tercapai' ? 'selected' : '' }}>Hampir tercapai</option>
                                                    <option value="Tercapai" {{ $act->evaluasi === 'Tercapai' ? 'selected' : '' }}>Tercapai</option>
                                                    <option value="Perlu follow-up lebih intensif" {{ $act->evaluasi === 'Perlu follow-up lebih intensif' ? 'selected' : '' }}>Perlu follow-up lebih intensif</option>
                                                    @if(!in_array($act->evaluasi, ['Belum ada peserta', 'Hampir tercapai', 'Tercapai', 'Perlu follow-up lebih intensif']))
                                                        <option value="{{ $act->evaluasi }}" selected>{{ $act->evaluasi }}</option>
                                                    @endif
                                                    <option value="custom">Kustom...</option>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <button class="btn btn-sm btn-link text-danger" onclick="deleteActivity({{ $act->id }})" title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data aktivitas autopilot pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- SweetAlert2 and JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ---- Save Toast notification from sessionStorage ----
            if (sessionStorage.getItem('show_save_success_toast') === 'true') {
                const msg = sessionStorage.getItem('success_toast_msg') || 'Data berhasil disimpan!';
                showSaveToast(msg);
                sessionStorage.removeItem('show_save_success_toast');
                sessionStorage.removeItem('success_toast_msg');
            }

            // ---- Tab State Management ----
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            const storedTab = localStorage.getItem('monitoring_active_tab');

            let activeTabId = '#tab-data';
            if (tabParam) {
                activeTabId = '#' + tabParam;
            } else if (storedTab) {
                activeTabId = storedTab;
            }

            // Show active tab
            const tabEl = document.querySelector(`a[href="${activeTabId}"]`);
            if (tabEl) {
                $(tabEl).tab('show');
            }

            // Save tab state when navigated
            $('.custom-tabs a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                const href = $(e.target).attr('href');
                localStorage.setItem('monitoring_active_tab', href);
            });

            bindInteractiveEvents();
        });

        // ---- Bind all interactive search, period change and click handlers ----
        function bindInteractiveEvents() {
            // Handle period dropdown changes to keep tab context without page reload
            const periodFilters = document.querySelectorAll('.periode-filter-select');
            periodFilters.forEach(select => {
                select.replaceWith(select.cloneNode(true));
            });

            const newPeriodFilters = document.querySelectorAll('.periode-filter-select');
            newPeriodFilters.forEach(select => {
                select.addEventListener('change', function() {
                    const selectedPeriod = this.value;
                    newPeriodFilters.forEach(otherSelect => {
                        otherSelect.value = selectedPeriod;
                    });
                    fetchPeriodData(selectedPeriod);
                });
            });

            // Listen to target input changes to dynamically recalculate KPI as the user types
            const targetInputs = document.querySelectorAll('input[name="target"]');
            targetInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const tabEl = this.closest('.tab-pane');
                    if (tabEl) {
                        const type = tabEl.id === 'tab-autopilot' ? 'autopilot' : 'openhouse';
                        recalculateKPI(type);
                    }
                });
            });

            // Helper to find currently selected tab name
            function getActiveTab() {
                const activeLink = document.querySelector('.custom-tabs .nav-link.active');
                if (activeLink) {
                    return activeLink.getAttribute('href').replace('#', '');
                }
                return 'data';
            }

            // ---- Search Functionality in Tab 1 ----
            const searchInput = document.getElementById('searchChapterAgent');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const filter = this.value.toLowerCase().trim();
                    const allChapters = document.querySelectorAll('.chapter-row');
                    
                    allChapters.forEach(function(chRow) {
                        const chId = chRow.getAttribute('data-id');
                        const chName = chRow.getAttribute('data-name');
                        const agentRows = document.querySelectorAll(`.agent-row.child-of-${chId}`);
                        
                        let chapterMatch = chName.includes(filter);
                        let anyAgentMatch = false;

                        agentRows.forEach(function(agRow) {
                            const agName = agRow.getAttribute('data-name');
                            if (agName.includes(filter)) {
                                anyAgentMatch = true;
                                if (filter !== '') {
                                    agRow.style.display = 'table-row';
                                } else {
                                    agRow.style.display = 'none';
                                }
                            } else {
                                agRow.style.display = 'none';
                            }
                        });

                        if (chapterMatch || anyAgentMatch) {
                            chRow.style.display = 'table-row';
                            
                            if (filter === '') {
                                agentRows.forEach(function(agRow) {
                                    agRow.style.display = 'none';
                                });
                                const chevron = chRow.querySelector('.toggle-chevron');
                                if (chevron) {
                                    chevron.style.transform = 'rotate(0deg)';
                                }
                            } else {
                                const chevron = chRow.querySelector('.toggle-chevron');
                                if (chevron && anyAgentMatch) {
                                    chevron.style.transform = 'rotate(90deg)';
                                }
                            }
                        } else {
                            chRow.style.display = 'none';
                        }
                    });
                });
            }

            // ---- Search Functionality in Agen Pusat ----
            const searchAgentPusatInput = document.getElementById('searchAgentPusat');
            if (searchAgentPusatInput) {
                searchAgentPusatInput.addEventListener('input', function() {
                    const filter = this.value.toLowerCase().trim();
                    const allAgenPusatRows = document.querySelectorAll('.agent-pusat-row');
                    
                    allAgenPusatRows.forEach(function(row) {
                        const name = row.getAttribute('data-name');
                        if (name.includes(filter)) {
                            row.style.display = 'table-row';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // ---- Expand/Collapse Chapter Rows ----
            const chapterRows = document.querySelectorAll('.chapter-row');
            chapterRows.forEach(function(row) {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('a, button, input, select')) return;

                    const chapterId = this.getAttribute('data-id');
                    const agentRows = document.querySelectorAll(`.agent-row.child-of-${chapterId}`);
                    const chevron = this.querySelector('.toggle-chevron');

                    agentRows.forEach(function(agentRow) {
                        if (agentRow.style.display === 'none') {
                            agentRow.style.display = 'table-row';
                        } else {
                            agentRow.style.display = 'none';
                        }
                    });

                    if (chevron) {
                        if (chevron.style.transform === 'rotate(90deg)') {
                            chevron.style.transform = 'rotate(0deg)';
                        } else {
                            chevron.style.transform = 'rotate(90deg)';
                        }
                    }
                });
            });
        }

        // ---- Fetch Period Data via AJAX HTML Fragment Swap ----
        function fetchPeriodData(period) {
            const activeLink = document.querySelector('.custom-tabs .nav-link.active');
            const currentTab = activeLink ? activeLink.getAttribute('href').replace('#', '') : 'data';

            $.ajax({
                url: window.location.pathname,
                type: 'GET',
                data: {
                    periode: period,
                    tab: currentTab
                },
                success: function(htmlString) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(htmlString, 'text/html');

                    const tabData = document.getElementById('tab-data');
                    const newTabData = doc.getElementById('tab-data');
                    if (tabData && newTabData) {
                        tabData.innerHTML = newTabData.innerHTML;
                    }

                    const tabOpenhouse = document.getElementById('tab-openhouse');
                    const newTabOpenhouse = doc.getElementById('tab-openhouse');
                    if (tabOpenhouse && newTabOpenhouse) {
                        tabOpenhouse.innerHTML = newTabOpenhouse.innerHTML;
                    }

                    const tabAutopilot = document.getElementById('tab-autopilot');
                    const newTabAutopilot = doc.getElementById('tab-autopilot');
                    if (tabAutopilot && newTabAutopilot) {
                        tabAutopilot.innerHTML = newTabAutopilot.innerHTML;
                    }

                    const newUrl = `${window.location.pathname}?periode=${period}&tab=${currentTab}`;
                    window.history.pushState({ path: newUrl }, '', newUrl);

                    bindInteractiveEvents();
                    showSaveToast('Data periode berhasil dimuat!');
                },
                error: function() {
                    showSaveToast('Gagal memuat data periode.', 'error');
                }
            });
        }

        // ---- Recalculate summary metrics dynamically ----
        function recalculateKPI(type) {
            const tabId = type === 'autopilot' ? 'tab-autopilot' : 'tab-openhouse';
            const tabEl = document.getElementById(tabId);
            if (!tabEl) return;

            const targetInput = tabEl.querySelector('input[name="target"]');
            const targetValue = parseInt(targetInput ? targetInput.value : '0') || 0;

            let totalRealisasi = 0;
            const realisasiInputs = tabEl.querySelectorAll('.realisasi-val');
            realisasiInputs.forEach(input => {
                totalRealisasi += parseInt(input.value) || 0;
            });

            const shortage = Math.max(0, targetValue - totalRealisasi);
            const shortagePercent = targetValue > 0 ? Math.round((shortage / targetValue) * 100) : 0;

            const targetCard = document.getElementById(`${type}-target-card`);
            const realisasiCard = document.getElementById(`${type}-realisasi-card`);
            const kurangPersenCard = document.getElementById(`${type}-kurang-persen-card`);
            const kurangDetailCard = document.getElementById(`${type}-kurang-detail-card`);
            const progressBar = document.getElementById(`${type}-progress-bar`);

            if (targetCard) targetCard.textContent = targetValue;
            if (realisasiCard) realisasiCard.textContent = totalRealisasi;
            if (kurangPersenCard) kurangPersenCard.textContent = shortagePercent + '%';
            if (kurangDetailCard) kurangDetailCard.textContent = shortage + ' peserta masih kurang';
            if (progressBar) {
                progressBar.style.width = shortagePercent + '%';
                progressBar.setAttribute('aria-valuenow', shortagePercent);
            }
        }

        // ---- Update Select Evaluation Badge Class ----
        function updateSelectEvalBadgeClass(selectEl, evalVal) {
            selectEl.classList.remove('badge-eval-tercapai', 'badge-eval-hampir', 'badge-eval-belum', 'badge-eval-other');
            const evalLower = evalVal.toLowerCase();
            if (evalLower.includes('tercapai') && !evalLower.includes('hampir')) {
                selectEl.classList.add('badge-eval-tercapai');
            } else if (evalLower.includes('hampir')) {
                selectEl.classList.add('badge-eval-hampir');
            } else if (evalLower.includes('belum')) {
                selectEl.classList.add('badge-eval-belum');
            } else {
                selectEl.classList.add('badge-eval-other');
            }
        }

        // ---- Update City Badge dynamically ----
        function updateCityBadge(selectEl) {
            const container = selectEl.parentNode.querySelector('.city-badge-container');
            if (!container) return;

            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const location = selectedOption.getAttribute('data-location') || '';

            if (location && location.trim() !== '') {
                container.innerHTML = `
                    <span class="badge badge-light border text-muted px-2 py-1 city-badge" style="font-size: 0.75rem; border-radius: 6px;">
                        <i class="fas fa-map-marker-alt text-danger mr-1"></i> ${location}
                    </span>
                `;
                container.style.display = 'block';
            } else {
                container.innerHTML = '';
                container.style.display = 'none';
            }
        }

        // ---- HTML options string for chapters/agents selection ----
        const allChaptersOptionsHtml = `
            <option value="" selected>-- Pilih --</option>
            @foreach($allChaptersList as $chapterOpt)
                <option value="{{ $chapterOpt->id }}" data-location="{{ $chapterOpt->chapter ?? '' }}">
                    {{ $chapterOpt->name }}{{ $chapterOpt->role === 'agen' ? ' (Agen Pusat)' : '' }}
                </option>
            @endforeach
        `;

        // ---- Update Table Row Indexes ----
        function updateRowIndexes(tbody) {
            const rows = tbody.querySelectorAll('tr[id^="activity-row-"]');
            rows.forEach((row, i) => {
                const firstCell = row.cells[0];
                if (firstCell) {
                    firstCell.textContent = i + 1;
                }
            });
        }

        // ---- Save Toast notification ----
        function showSaveToast(message, type) {
            message = message || 'Data berhasil disimpan!';
            type = type || 'success';
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }

        // ---- Update activity row inline (AJAX) ----
        function updateActivityInline(id, field, value) {
            const selectEl = document.querySelector(`#activity-row-${id} .select-eval`);
            const prevVal = selectEl ? selectEl.getAttribute('data-prev') || selectEl.value : '';

            if (field === 'chapter_id') {
                const chapterSelect = document.querySelector(`#activity-row-${id} .chapter-val`);
                if (chapterSelect) {
                    updateCityBadge(chapterSelect);
                }
            }

            if (field === 'evaluasi' && value === 'custom') {
                Swal.fire({
                    title: 'Masukkan Evaluasi Kustom',
                    input: 'text',
                    inputPlaceholder: 'Tulis evaluasi baru...',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#6610f2',
                    cancelButtonColor: '#858796',
                    inputValidator: (val) => {
                        if (!val) {
                            return 'Evaluasi kustom tidak boleh kosong!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value.trim() !== '') {
                        const customVal = result.value.trim();
                        if (selectEl) {
                            Array.from(selectEl.options).forEach(opt => {
                                if (opt.getAttribute('data-custom') === 'true') opt.remove();
                            });
                            
                            const opt = document.createElement('option');
                            opt.value = customVal;
                            opt.textContent = customVal;
                            opt.setAttribute('data-custom', 'true');
                            selectEl.insertBefore(opt, selectEl.querySelector('option[value="custom"]'));
                            selectEl.value = customVal;
                            selectEl.setAttribute('data-prev', customVal);
                            updateSelectEvalBadgeClass(selectEl, customVal);
                        }
                        saveActivityField(id, 'evaluasi', customVal);
                    } else {
                        if (selectEl) {
                            selectEl.value = prevVal;
                        }
                    }
                });
                return;
            }

            if (selectEl && field === 'evaluasi') {
                selectEl.setAttribute('data-prev', value);
                updateSelectEvalBadgeClass(selectEl, value);
            }

            saveActivityField(id, field, value);
        }

        function saveActivityField(id, field, value) {
            const row = document.querySelector(`#activity-row-${id}`);
            if (!row) return;

            const chapter_id = row.querySelector('.chapter-val') ? row.querySelector('.chapter-val').value : null;
            const tanggal = row.querySelector('.date-val').value;
            const target = row.querySelector('.target-val').value;
            const realisasi = row.querySelector('.realisasi-val').value;
            let evaluasi = row.querySelector('.select-eval').value;
            
            if (field === 'evaluasi') {
                evaluasi = value;
            }

            const isAutopilot = row.closest('#tab-autopilot') !== null;
            const type = isAutopilot ? 'autopilot' : 'openhouse';

            $.ajax({
                url: `/admin/monitoring-chapter/activity/${id}`,
                type: 'POST',
                data: {
                    _method: 'PUT',
                    _token: '{{ csrf_token() }}',
                    chapter_id: chapter_id,
                    tanggal: tanggal,
                    target: target,
                    realisasi: realisasi,
                    evaluasi: evaluasi
                },
                success: function(response) {
                    recalculateKPI(type);
                    showSaveToast('Data berhasil disimpan!');
                },
                error: function() {
                    showSaveToast('Gagal menyimpan data.', 'error');
                }
            });
        }

        // ---- Delete activity row (AJAX) ----
        function deleteActivity(id) {
            const row = document.getElementById('activity-row-' + id);
            if (!row) return;

            const isAutopilot = row.closest('#tab-autopilot') !== null;
            const type = isAutopilot ? 'autopilot' : 'openhouse';

            Swal.fire({
                title: 'Hapus baris ini?',
                text: 'Aktivitas chapter ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: `/admin/monitoring-chapter/activity/${id}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: () => {
                        const tbody = row.parentNode;
                        row.remove();
                        if (tbody) {
                            updateRowIndexes(tbody);
                            const remainingRows = tbody.querySelectorAll('tr[id^="activity-row-"]');
                            if (remainingRows.length === 0) {
                                const msg = type === 'autopilot' 
                                    ? 'Belum ada data aktivitas autopilot pada periode ini.' 
                                    : 'Belum ada data aktivitas open house pada periode ini.';
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">${msg}</td>
                                    </tr>
                                `;
                            }
                        }
                        recalculateKPI(type);
                        showSaveToast('Data berhasil dihapus.');
                    },
                    error: () => showSaveToast('Gagal menghapus, coba lagi.', 'error')
                });
            });
        }

        // ---- Add activity row (Instantly creates inline) ----
        function addActivityRow(type) {
            $.post('{{ route("admin.monitoring-chapter.activity.store") }}', {
                _token: '{{ csrf_token() }}',
                type: type,
                chapter_id: '',
                tanggal: '{{ date("Y-m-d") }}',
                target: 0,
                realisasi: 0,
                evaluasi: 'Belum ada peserta',
                periode: '{{ $periode }}'
            })
            .done((response) => {
                if (response.success && response.activity) {
                    const activity = response.activity;
                    const tabId = type === 'open_house' ? 'tab-openhouse' : 'tab-autopilot';
                    const tbody = document.querySelector(`#${tabId} tbody`);
                    if (tbody) {
                        const emptyRowTr = tbody.querySelector('tr td.text-center.py-5');
                        if (emptyRowTr) {
                            emptyRowTr.parentNode.remove();
                        }

                        const newRowHtml = `
                            <tr id="activity-row-${activity.id}" class="align-middle">
                                <td></td>
                                <td>
                                    <select class="form-control-inline chapter-val font-weight-bold text-dark" onchange="updateActivityInline(${activity.id}, 'chapter_id', this.value)" style="width: 100% !important;">
                                        ${allChaptersOptionsHtml}
                                    </select>
                                    <div class="city-badge-container mt-1" style="display: none;"></div>
                                </td>
                                <td>
                                    <input type="date" class="form-control-inline date-val" value="${activity.tanggal}" onchange="updateActivityInline(${activity.id}, 'tanggal', this.value)" style="width: 140px !important;">
                                </td>
                                <td style="text-align: center;">
                                    <input type="number" class="activity-input target-val" value="${activity.target}" min="0" onchange="updateActivityInline(${activity.id}, 'target', this.value)">
                                </td>
                                <td style="text-align: center;">
                                    <input type="number" class="activity-input realisasi-val" value="${activity.realisasi}" min="0" onchange="updateActivityInline(${activity.id}, 'realisasi', this.value)">
                                </td>
                                <td style="text-align: center;">
                                    <select class="badge-eval badge-eval-belum select-eval" onchange="updateActivityInline(${activity.id}, 'evaluasi', this.value)">
                                        <option value="Belum ada peserta" selected>Belum ada peserta</option>
                                        <option value="Hampir tercapai">Hampir tercapai</option>
                                        <option value="Tercapai">Tercapai</option>
                                        <option value="Perlu follow-up lebih intensif">Perlu follow-up lebih intensif</option>
                                        <option value="custom">Kustom...</option>
                                    </select>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn btn-sm btn-link text-danger" onclick="deleteActivity(${activity.id})" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', newRowHtml);
                        updateRowIndexes(tbody);
                        
                        const typeKpi = type === 'open_house' ? 'openhouse' : 'autopilot';
                        recalculateKPI(typeKpi);
                        showSaveToast('Aktivitas berhasil ditambahkan!');
                    }
                }
            })
            .fail(() => {
                Swal.fire('Gagal', 'Terjadi kesalahan saat menambahkan baris.', 'error');
            });
        }


    </script>
@endsection
