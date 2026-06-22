@php
    $isEkoSulis = Auth::user()->hasAnyHakAkses(['monitoring_marketing', 'marketing_ads_manager']);
@endphp

{{-- Dashboard --}}
@if (strtolower(Auth::user()->role) === 'marketing')
    @if (\App\Models\Menu::isActive('dashboard_marketing'))
        <li class="nav-item {{ request()->routeIs('marketing') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('marketing') }}" title="DASHBOARD">
                <i class="fas fa-fw fa-chart-line"></i>
                <span>DASHBOARD</span>
            </a>
        </li>
    @endif
@endif

@if (strtolower(trim(Auth::user()->role)) === 'advertising')
    @if (\App\Models\Menu::isActive('dashboard_advertising'))
        <li class="nav-item {{ request()->routeIs('advertising') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('advertising') }}">
                <i class="fas fa-fw fa-bullhorn"></i>
                <span>DASHBOARD ADVERTISING</span>
            </a>
        </li>
    @endif
@endif

{{-- Advertising Items --}}
@if (strtolower(trim(Auth::user()->role)) === 'advertising')
    @if (!$isEkoSulis && \App\Models\Menu::isActive('program_kerja'))
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('programkerja.index') }}">
                <i class="fas fa-globe me-2"></i>
                <span>Program Kerja</span>
            </a>
        </li>
    @endif

    @if (!$isEkoSulis && \App\Models\Menu::isActive('ganchart'))
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('gantt.index') }}">
                <i class="fas fa-project-diagram me-2"></i>
                <span>Ganchart</span>
            </a>
        </li>
    @endif

    <li class="nav-item {{ request()->routeIs('admin.ads-activity.*') ? 'active' : '' }}">
        <a class="nav-link text-white" href="{{ route('admin.ads-activity.index') }}">
            <i class="fas fa-fw fa-calendar-check me-2"></i>
            <span>ACTIVITY ADS</span>
        </a>
    </li>

    @if ($isEkoSulis)
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('marketing') }}">
                <i class="fas fa-fw fa-chart-line me-2"></i>
                <span>MONITORING TIM MARKETING</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('marketing-participants.index') ? 'active' : '' }}">
            <a class="nav-link text-white" href="{{ route('marketing-participants.index') }}">
                <i class="fas fa-fw fa-address-card me-2"></i>
                <span>MONITORING DATABASE MARKETING</span>
            </a>
        </li>
    @endif
@endif

{{-- Marketing Items --}}
@if (strtolower(Auth::user()->role) === 'marketing')
    <hr class="sidebar-divider my-0">

    @if (\App\Models\Menu::isActive('data_lead'))
        <li class="nav-item {{ request()->routeIs('admin.database.database') && !request()->has('view') ? 'active' : '' }}">
            <a class="nav-link text-white" href="{{ route('admin.database.database') }}">
                <i class="fas fa-table me-2"></i>
                <span>Data Lead / Prospek</span>
            </a>
        </li>
    @endif

    @if (auth()->user()->hasAnyHakAkses(['activity_marketing', 'activity_marketing_offline']) || strtolower(auth()->user()->role) === 'administrator')
        <li class="nav-item {{ request()->routeIs('marketing-participants.index') ? 'active' : '' }}">
            <a class="nav-link text-white" href="{{ route('marketing-participants.index') }}">
                <i class="fas fa-users-rectangle me-2"></i>
                <span>Database Marketing</span>
            </a>
        </li>
    @endif

    @if (auth()->user()->hasAnyHakAkses(['activity_marketing', 'activity_marketing_offline']))
        <div class="sidebar-premium-heading">
            <i class="fas fa-layer-group me-1"></i> MANAJEMEN KERJA
        </div>
        <li class="nav-item {{ request()->routeIs('programkerja.index') ? 'active' : '' }}">
            <a class="nav-link nav-link-premium premium-border-warning" href="{{ route('programkerja.index') }}">
                <i class="fas fa-tasks text-warning me-2"></i>
                <span>Program Kerja</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('gantt.index') ? 'active' : '' }}">
            <a class="nav-link nav-link-premium premium-border-warning" href="{{ route('gantt.index') }}">
                <i class="fas fa-project-diagram text-warning me-2"></i>
                <span>Ganchart</span>
            </a>
        </li>
    @endif

    @if (!auth()->user()->hasAnyHakAkses(['activity_marketing', 'activity_marketing_offline']))
        <li class="nav-item {{ request()->routeIs('marketing.penilaian.index') ? 'active' : '' }}">
            <a class="nav-link text-white" href="{{ route('marketing.penilaian.index') }}">
                <i class="fas fa-fw fa-star me-2"></i>
                <span>Penilaian Kinerja (KPI)</span>
            </a>
        </li>
    @endif

    @if (!auth()->user()->hasAnyHakAkses(['activity_marketing', 'activity_marketing_offline']))
        <li class="nav-item {{ request()->routeIs('marketing.penilaian.kpi_sosmed') ? 'active' : '' }}">
            <a class="nav-link text-white" href="{{ route('marketing.penilaian.kpi_sosmed') }}">
                <i class="fas fa-fw fa-hashtag me-2"></i>
                <span>KPI Sosmed Spesialis</span>
            </a>
        </li>
    @endif

@endif
