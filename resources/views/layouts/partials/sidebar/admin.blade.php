{{-- 1. DASHBOARD CEO --}}
@if (\App\Models\Menu::isActive('dashboard_admin'))
    <li class="nav-item {{ request()->routeIs('administrator') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('administrator') }}" title="DASHBOARD CEO">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span><strong>DASHBOARD CEO</strong></span>
        </a>
    </li>
@endif

{{-- 2. DATABASE (COMBINED) --}}
@if (\App\Models\Menu::isActive('database_cs'))
    <li class="nav-item {{ request()->routeIs('admin.database.database') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.database.database') }}" title="DATABASE">
            <i class="fas fa-fw fa-database"></i>
            <span><strong>DATABASE</strong></span>
        </a>
    </li>
@endif

{{-- 3. DATA PESERTA (Unified MBC & M1T) --}}
@if (\App\Models\Menu::isActive('sales_plan'))
    <li class="nav-item {{ request()->routeIs('admin.data-peserta.unified') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.data-peserta.unified') }}" title="DATA PESERTA">
            <i class="fas fa-fw fa-id-card"></i>
            <span><strong>DATA PESERTA</strong></span>
            @if (isset($pendingM1TCount) && $pendingM1TCount > 0)
                <span
                    class="badge badge-pending-yellow badge-pulse ml-2">{{ $pendingM1TCount }}</span>
            @endif
        </a>
    </li>
@endif

{{-- 7. MONITORING CHAPTER --}}
<li
    class="nav-item {{ request()->routeIs('admin.monitoring-chapter') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.monitoring-chapter') }}"
        title="MONITORING CHAPTER">
        <i class="fas fa-fw fa-project-diagram"></i>
        <span><strong>MONITORING CHAPTER</strong></span>
    </a>
</li>

{{-- 8. SETTING --}}
@if (\App\Models\Menu::isActive('settings'))
    <li class="nav-item {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.settings.index') }}" title="SETTING">
            <i class="fas fa-fw fa-cog"></i>
            <span><strong>SETTING</strong></span>
        </a>
    </li>
@endif

{{-- 9. SETTING JADWAL --}}
<li class="nav-item {{ request()->routeIs('admin.kelas.index') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.kelas.index') }}" title="SETTING JADWAL">
        <i class="fas fa-fw fa-calendar-alt"></i>
        <span><strong>SETTING JADWAL</strong></span>
    </a>
</li>

{{-- 10. MINUTES OF MEETING (MoM) — Administrator: rekap semua divisi, read-only --}}
<li class="nav-item {{ request()->routeIs('admin.mom.index') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.mom.index') }}" title="Minutes of Meeting (MoM)">
        <i class="fas fa-fw fa-clipboard-list"></i>
        <span><strong>Minutes of Meeting (MoM)</strong></span>
    </a>
</li>

{{-- Extra Admin Menu --}}
@if (\App\Models\Menu::isActive('activity_cs'))
    <li class="nav-item {{ request()->routeIs('admin.activity-cs.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.activity-cs.index') }}" title="ACTIVITY CS">
            <i class="fas fa-fw fa-list-check"></i>
            <span><strong>ACTIVITY CS</strong></span>
        </a>
    </li>
@endif
