@extends('layouts.masteradmin')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .monitoring-container {
            padding: 2rem;
            background: #f8f9fc;
            min-height: 100vh;
        }

        .premium-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: none;
            background: white;
            transition: all 0.3s ease;
        }

        .premium-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .text-purple {
            color: #6610f2;
        }

        .badge-purple {
            background-color: #6610f2;
            color: white;
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
            background-color: #f1effd !important;
        }

        .agent-row {
            background-color: #fafbfe;
            border-left: 4px solid #dddfeb;
            transition: background-color 0.2s;
        }

        .agent-row:hover {
            background-color: #f4f6fa !important;
        }
    </style>

    <div class="monitoring-container">
        <!-- Page Title -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 text-gray-800 font-weight-bold">Monitoring Chapter & Agen</h1>
                <p class="text-muted">Pantau target bulanan peserta event, realisasi closing, dan saldo keuangan cabang.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="this.parentElement.style.display='none';">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Row for KPI Cards -->
        <div class="row mb-4">
            <!-- Target Peserta Event Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card premium-card h-100 py-2" style="border-left: 5px solid #6610f2 !important;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #6610f2;">
                                    <i class="fas fa-bullseye mr-1"></i> Target Peserta Event
                                </div>
                                <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($targetEvent, 0, ',', '.') }}</div>
                                <div class="text-xs text-muted mt-1">peserta bulan ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Realisasi Saat Ini Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card premium-card h-100 py-2" style="border-left: 5px solid #1cc88a !important;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <i class="fas fa-user-check mr-1"></i> Realisasi Saat Ini
                                </div>
                                <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($realisasiEvent, 0, ',', '.') }}</div>
                                <div class="text-xs text-muted mt-1">peserta aktif terdaftar</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pencapaian Card -->
            @php
                $persenPencapaian = $targetEvent > 0 ? round(($realisasiEvent / $targetEvent) * 100, 1) : 0;
                if ($persenPencapaian > 100) $persenPencapaian = 100;
                $persenDisplay = $targetEvent > 0 ? round(($realisasiEvent / $targetEvent) * 100) : 0;
            @endphp
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card premium-card h-100 py-2" style="border-left: 5px solid #4e73df !important;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    <i class="fas fa-chart-line mr-1"></i> Pencapaian
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h3 mb-0 mr-3 font-weight-bold text-gray-800">{{ $persenDisplay }}%</div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2" style="border-radius: 5px; height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width: {{ $persenPencapaian }}%; border-radius: 5px;" 
                                                aria-valuenow="{{ $persenPencapaian }}" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-muted mt-1">dari target {{ number_format($targetEvent, 0, ',', '.') }} peserta</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration Card (Dynamic Target Setup) -->
        <div class="card premium-card mb-4">
            <div class="card-body p-4">
                <h5 class="font-weight-bold mb-3 text-dark"><i class="fas fa-cog mr-2 text-info"></i> Pengaturan Target Bulanan</h5>
                <form action="{{ route('admin.settings.target-event.update') }}" method="POST" class="form-inline">
                    @csrf
                    <div class="form-group mb-2 mr-sm-3">
                        <label for="target_event_peserta" class="sr-only">Target Peserta</label>
                        <div class="input-group shadow-sm" style="border-radius: 5px; overflow: hidden;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-bullseye text-muted"></i></span>
                            </div>
                            <input type="number" class="form-control bg-light border-0" id="target_event_peserta" name="target_event_peserta" value="{{ $targetEvent }}" placeholder="Masukkan target peserta" min="1" required style="box-shadow: none;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info mb-2 shadow-sm px-4" style="border-radius: 5px;">
                        <i class="fas fa-save mr-1"></i> Simpan Target
                    </button>
                </form>
            </div>
        </div>

        <!-- Monitoring Table Card -->
        <div class="card premium-card" style="overflow: hidden;">
            <div class="card-body p-4">
                <!-- Table Header & Search -->
                <div class="row align-items-center mb-4">
                    <div class="col-md-6">
                        <h5 class="font-weight-bold mb-0 text-dark">
                            <i class="fas fa-users mr-2 text-purple"></i> Daftar Chapter & Agen
                        </h5>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; border: 1px solid #e3e6f0;">
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
                            <tr class="text-uppercase text-muted" style="font-size: 0.85rem; border-bottom: 2px solid #e3e6f0;">
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
                                        <span class="badge badge-purple text-white px-2 py-1" style="border-radius: 6px; font-size: 0.75rem;">Chapter</span>
                                    </td>
                                    <td class="text-center font-weight-bold text-info" style="color: #4e73df !important;">
                                        {{ $ch['closing_bulan_ini'] }} peserta
                                    </td>
                                    <td class="text-center font-weight-bold text-info" style="color: #4e73df !important;">
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
                                        <td class="text-center font-weight-bold text-info" style="color: #4e73df !important;">
                                            {{ $ag['closing_bulan_ini'] }} peserta
                                        </td>
                                        <td class="text-center font-weight-bold text-info" style="color: #4e73df !important;">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Expand/Collapse Chapter Rows
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

            // Client-Side Searching
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
        });
    </script>
@endsection
