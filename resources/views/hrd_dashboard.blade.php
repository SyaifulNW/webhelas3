@extends('layouts.masteradmin')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="container-fluid px-2 pb-5">
    <h3 class="mb-4 py-3 fw-bold text-dark text-center" style="letter-spacing: 2px;">
        <i class="fas fa-users-cog me-2 text-success"></i> DASHBOARD HRD
    </h3>

    <ul class="nav nav-tabs border-0 mt-2 mb-0 d-flex flex-wrap gap-0 justify-content-center px-4" id="adminDashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold premium-tab color-success" id="sdm-tab" data-toggle="tab" data-target="#sdm" type="button" role="tab" aria-controls="sdm" aria-selected="true" style="border-radius: 10px 10px 0 0 !important;">
                <i class="fas fa-users-cog"></i> <span>Manajemen SDM</span>
            </button>
        </li>
    </ul>

    <div class="premium-divider-container" style="margin-bottom: 0;">
        <div class="premium-divider">
            <div id="tab-indicator" class="divider-indicator bg-success-indicator" style="width: 200px; left: calc(50% - 100px);"></div>
        </div>
    </div>

    <div class="tab-content bg-white shadow-sm border" id="dashboardTabsContent" style="border-radius: 0 0 10px 10px; position: relative;">
        <!-- Loading Overlay -->
        <div id="iframe-loading" class="d-none" style="position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(255,255,255,0.7); z-index: 10; display: flex; align-items: center; justify-content: center; height: 300px;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <!-- SDM -->
        <div class="tab-pane fade show active" id="sdm" role="tabpanel" aria-labelledby="sdm-tab">
            <div class="sub-nav-container d-flex gap-2 p-3 bg-white border-bottom flex-wrap" style="border-radius: 0;">
                <button class="btn sub-tab-btn btn-karyawan active-sub" onclick="switchSdmSub(this, '{{ route('hr') }}?section=karyawan&embed=true')">
                    <i class="fas fa-users me-1"></i> Data Karyawan
                </button>
                <button class="btn sub-tab-btn btn-absensi" onclick="switchSdmSub(this, '{{ route('hr') }}?section=absensi&embed=true')">
                    <i class="fas fa-calendar-check me-1"></i> Absensi & Izin
                </button>
                <button class="btn sub-tab-btn btn-kpi" onclick="switchSdmSub(this, '{{ route('admin.penilaian-cs.index') }}?embed=true')">
                    <i class="fas fa-star me-1"></i> Penilaian KPI
                </button>
                <button class="btn sub-tab-btn btn-settings" onclick="switchSdmSub(this, '{{ route('hr') }}?section=settings&embed=true')">
                    <i class="fas fa-cogs me-1"></i> Pengaturan Absensi
                </button>
            </div>
            <iframe id="iframe-sdm" src="{{ route('hr') }}?section=karyawan&embed=true" style="width:100%; height:950px; border:none; border-radius: 0 0 10px 10px;" onload="resizeIframe(this); hideLoading();"></iframe>
        </div>
    </div>
</div>

<style>
    .premium-tab {
        padding: 12px 35px !important;
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        background: #f1f3f9;
        color: #4e73df;
        border: none !important;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        justify-content: center;
        min-width: 200px;
    }
    .premium-tab.active {
        color: white !important;
        border-bottom: none !important;
        transform: none !important;
        box-shadow: none !important;
        margin-bottom: -1px !important;
        z-index: 10;
        background: #28a745 !important;
    }
    .premium-divider {
        height: 2px;
        background: #e3e6f0;
        position: relative;
    }
    .divider-indicator {
        position: absolute;
        height: 4px;
        background: #28a745;
        border-radius: 10px;
        top: -1px;
    }
    .sub-nav-container {
        background: #f8f9fc !important;
    }
    .sub-tab-btn {
        background: white;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 10px 25px;
        border-radius: 12px !important;
        transition: all 0.3s ease;
        border: 2px solid !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .btn-karyawan { border-color: #1cc88a !important; color: #1cc88a !important; }
    .btn-karyawan:hover, .btn-karyawan.active-sub {
        background: #1cc88a !important; color: white !important;
    }
    .btn-absensi { border-color: #36b9cc !important; color: #36b9cc !important; }
    .btn-absensi:hover, .btn-absensi.active-sub {
        background: #36b9cc !important; color: white !important;
    }
    .btn-kpi { border-color: #4e73df !important; color: #4e73df !important; }
    .btn-kpi:hover, .btn-kpi.active-sub {
        background: #4e73df !important; color: white !important;
    }
    .btn-settings { border-color: #6c757d !important; color: #6c757d !important; }
    .btn-settings:hover, .btn-settings.active-sub {
        background: #6c757d !important; color: white !important;
    }
    iframe {
        display: block;
        background-color: #f8f9fc;
    }
</style>

<script>
    function switchSdmSub(btn, url) {
        showLoading();
        document.querySelectorAll('#sdm .sub-tab-btn').forEach(b => b.classList.remove('active-sub'));
        btn.classList.add('active-sub');
        document.getElementById('iframe-sdm').src = url;
    }
    function showLoading() {
        document.getElementById('iframe-loading').classList.remove('d-none');
    }
    function hideLoading() {
        document.getElementById('iframe-loading').classList.add('d-none');
    }
    function resizeIframe(obj) {
        try {
            if(obj.contentWindow.document.body.scrollHeight > 500) {
                obj.style.height = obj.contentWindow.document.body.scrollHeight + 100 + 'px';
            }
        } catch(e) {}
    }
</script>
@endsection
