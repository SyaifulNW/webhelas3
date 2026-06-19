<hr class="sidebar-divider d-none d-md-block">

<div class="sidebar-premium-heading">
    <i class="fas fa-layer-group me-1"></i> MANAJEMEN KERJA
</div>

@if (\App\Models\Menu::isActive('program_kerja'))
    <li class="nav-item">
        <a class="nav-link nav-link-premium premium-border-warning"
            href="{{ route('programkerja.index') }}">
            <i class="fas fa-tasks text-warning me-2"></i>
            <span>Program Kerja</span>
        </a>
    </li>
@endif

@if (\App\Models\Menu::isActive('ganchart'))
    <li class="nav-item">
        <a class="nav-link nav-link-premium premium-border-warning"
            href="{{ route('gantt.index') }}">
            <i class="fas fa-project-diagram text-warning me-2"></i>
            <span>Ganchart</span>
        </a>
    </li>
@endif

<div class="sidebar-premium-heading">
    <i class="fas fa-chart-bar me-1"></i> MONITORING & PERFORMA
</div>

<li class="nav-item {{ request()->routeIs('produksi.performance') ? 'active' : '' }}">
    <a class="nav-link nav-link-premium premium-border-success"
        href="{{ route('produksi.performance') }}">
        <i class="fas fa-chart-line text-success me-2"></i>
        <span>Performa Dashboard</span>
    </a>
</li>

<hr class="sidebar-divider d-none d-md-block">
