@extends('layouts.masteradmin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-video text-primary mr-2"></i> Monitoring Jadwal Zoom One-on-One - <span id="calendarTitleCsName" class="ml-1">{{ $isAdmin ? 'ALL TIM CS' : auth()->user()->name }}</span>
        </h1>
    </div>

    <!-- Top Widgets Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h4 class="font-weight-bold mb-2">Sistem Penjadwalan Zoom One-on-One M1T</h4>
                            <p class="mb-0 opacity-80" style="font-size: 0.9rem;">
                                CS menghubungi prospek terlebih dahulu. Jika disetujui, jadwalkan sesi Zoom One-on-One. 
                                @if(!$isAdmin)
                                    Target minimal Anda adalah <strong>4 Zoom sukses per hari</strong> untuk menjaga tingkat konversi prospek tetap optimal.
                                @else
                                    Pantau pencapaian target harian dan sebaran jadwal Zoom seluruh tim CS secara real-time.
                                @endif
                            </p>
                        </div>
                        
                        <!-- Dynamic Target Gauge / CS Dropdown -->
                        <div class="col-md-5 mt-3 mt-md-0 text-md-right">
                            @if(!$isAdmin)
                                <div class="bg-white text-dark p-3 rounded-lg shadow-sm d-inline-block text-left" style="min-width: 250px; border-radius: 12px;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="font-weight-bold text-muted" style="font-size: 0.8rem;">PROGRESS TARGET HARI INI</span>
                                        <span class="badge badge-success font-weight-bold" style="font-size: 0.85rem;">{{ $todaySchedulesCount }} / 4 Zoom</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 10px; border-radius: 6px;">
                                        <div class="progress-bar bg-gradient-success progress-bar-striped progress-bar-animated" role="progressbar" 
                                             style="width: {{ $progressPercent }}%" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted d-block font-italic" style="font-size: 0.72rem;">
                                        @if($todaySchedulesCount >= 4)
                                            🎉 Hebat! Target harian Anda tercapai!
                                        @else
                                            Ayo jadwalkan {{ 4 - $todaySchedulesCount }} Zoom lagi untuk mencapai target hari ini!
                                        @endif
                                    </small>
                                </div>
                            @else
                                <div class="bg-white text-dark p-3 rounded-lg shadow-sm d-inline-block text-left" style="min-width: 250px; border-radius: 12px;">
                                    <label class="font-weight-bold text-muted small d-block mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase;">Tim CS</label>
                                    <select id="filterCs" class="form-control form-control-sm" style="border-radius: 8px; font-weight: 700; height: 38px; color: #000;">
                                        <option value="">ALL Tim CS</option>
                                        @foreach($csUsers as $cs)
                                            <option value="{{ $cs->id }}">{{ $cs->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-dark mb-0">Kalender Interaktif</h5>
                    <!-- Color Legend -->
                    <div class="d-flex align-items-center" style="gap: 15px; font-size: 0.78rem; font-weight: 700;">
                        <span class="d-flex align-items-center" style="gap: 5px;"><span class="rounded-circle" style="width: 10px; height: 10px; background-color: #25799E; display: inline-block;"></span> Scheduled</span>
                        <span class="d-flex align-items-center" style="gap: 5px;"><span class="rounded-circle" style="width: 10px; height: 10px; background-color: #3CDE1D; display: inline-block;"></span> Done / Sukses</span>
                        <span class="d-flex align-items-center" style="gap: 5px;"><span class="rounded-circle" style="width: 10px; height: 10px; background-color: #E61717; display: inline-block;"></span> Cancelled</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="zoomCalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL EVENT --}}
<div class="modal fade" id="modalEventDetail" tabindex="-1" role="dialog" aria-labelledby="modalEventDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 pb-3" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; padding: 16px 20px;">
                <div>
                    <h5 class="modal-title font-weight-bold mb-0" id="modalEventDetailLabel" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                        <i class="fas fa-calendar-day mr-2"></i> Rincian Jadwal Zoom
                    </h5>
                    <small class="d-block mt-1 opacity-75" style="font-size: 0.78rem;">
                        Status: <span id="detailStatusBadge" class="badge text-white px-2 py-0.5 ml-1"></span>
                    </small>
                </div>
                <button type="button" class="close text-white p-0 m-0" data-dismiss="modal" aria-label="Close" style="outline:none; background: transparent; border:none; opacity:1; font-size: 1.4rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light text-dark">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px; background-color: #fff;">
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">NAMA CUSTOMER / PESERTA</small>
                        <span id="detailParticipant" class="font-weight-bold text-dark" style="font-size: 0.95rem;">-</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">TIM CS PENANGGUNG JAWAB</small>
                        <span id="detailCs" class="font-weight-bold text-dark" style="font-size: 0.9rem;">-</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">WAKTU PELAKSANAAN</small>
                        <span id="detailTime" class="font-weight-bold text-primary" style="font-size: 0.9rem;">-</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">LINK PERTEMUAN ZOOM</small>
                        <a id="detailZoomLink" href="#" target="_blank" class="btn btn-outline-primary btn-sm mt-1 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-weight: 700; font-size: 0.78rem;">
                            <i class="fas fa-video mr-1.5"></i> Buka Zoom Meeting
                        </a>
                        <span id="detailZoomLinkEmpty" class="text-muted small d-block font-italic mt-1">- Belum diset -</span>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">CATATAN / TINDAK LANJUT</small>
                        <p id="detailNotes" class="mb-0 text-muted small mt-1 font-italic">-</p>
                    </div>
                </div>
                
                <button type="button" class="btn btn-secondary btn-block border-0 shadow-sm py-2 font-weight-bold" data-dismiss="modal" style="border-radius: 10px; font-size: 0.85rem;">
                    Tutup Rincian
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Extra Styles & Script CDN for FullCalendar -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<style>
    /* Premium FullCalendar Customizer */
    .fc-theme-standard .fc-scrollgrid {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e3e6f0;
    }
    .fc .fc-toolbar-title {
        font-weight: 800;
        color: #333333;
        font-size: 1.25rem !important;
    }
    .fc .fc-button-primary {
        background-color: #2a5298 !important;
        border-color: #2a5298 !important;
        font-weight: 700;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .fc .fc-button-primary:hover {
        background-color: #1e3c72 !important;
        border-color: #1e3c72 !important;
    }
    .fc-daygrid-day-number {
        font-weight: 700;
        color: #555555;
        font-size: 0.85rem;
    }
    .fc-col-header-cell-cushion {
        font-weight: 800;
        color: #333333;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    .fc-event {
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.72rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('zoomCalendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            list: 'Daftar'
        },
        events: function(info, successCallback, failureCallback) {
            var params = {
                start: info.startStr,
                end: info.endStr
            };
            
            // Apply CS Filter for Admin
            var csFilter = document.getElementById('filterCs');
            if (csFilter) {
                if (csFilter.value) {
                    params.cs_id = csFilter.value;
                    var selectedText = csFilter.options[csFilter.selectedIndex].text;
                    $('#calendarTitleCsName').text(selectedText);
                } else {
                    $('#calendarTitleCsName').text('ALL TIM CS');
                }
            }

            $.getJSON('{{ route("zoom-schedule.events") }}', params)
                .done(function(data) {
                    successCallback(data);
                })
                .fail(function() {
                    failureCallback();
                });
        },
        eventClick: function(info) {
            var props = info.event.extendedProps;
            
            // Set details in modal
            $('#detailParticipant').text(props.participant);
            $('#detailCs').text(props.cs);
            $('#detailTime').text(info.event.start.toLocaleDateString('id-ID', {
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric'
            }) + ' @ ' + props.time);
            
            // Link zoom
            if (props.zoom_link) {
                $('#detailZoomLink').attr('href', props.zoom_link).show();
                $('#detailZoomLinkEmpty').hide();
            } else {
                $('#detailZoomLink').hide();
                $('#detailZoomLinkEmpty').show();
            }

            // Notes
            $('#detailNotes').text(props.notes);

            // Status Badge coloring
            var status = props.status.toLowerCase();
            var badge = $('#detailStatusBadge');
            badge.text(props.status);
            
            if (status === 'done') {
                badge.removeClass().addClass('badge badge-success px-2 py-1 ml-1');
            } else if (status === 'cancelled') {
                badge.removeClass().addClass('badge badge-danger px-2 py-1 ml-1');
            } else {
                badge.removeClass().addClass('badge badge-primary px-2 py-1 ml-1');
            }

            $('#modalEventDetail').modal('show');
        }
    });

    calendar.render();

    // Re-fetch events when CS Filter changes
    var csFilter = document.getElementById('filterCs');
    if (csFilter) {
        csFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    }
});
</script>
@endsection
