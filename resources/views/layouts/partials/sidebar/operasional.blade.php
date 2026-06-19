<li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('home') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span><strong>DASHBOARD OPERASIONAL</strong></span>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.database.database') ? 'active' : '' }}">
    <a class="nav-link"
        href="{{ route('admin.database.database', ['view_type' => 'chapter']) }}">
        <i class="fas fa-fw fa-database"></i>
        <span><strong>DATABASE</strong></span>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('programkerja.index') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('programkerja.index') }}">
        <i class="fas fa-fw fa-tasks"></i>
        <span><strong>PROGRAM KERJA</strong></span>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.monitoring-chapter') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.monitoring-chapter') }}" title="MONITORING CHAPTER">
        <i class="fas fa-fw fa-project-diagram"></i>
        <span><strong>MONITORING CHAPTER</strong></span>
    </a>
</li>

{{-- Setting for Operasional (chapter & agen management only) --}}
@if (\App\Models\Menu::isActive('settings'))
    <li class="nav-item {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.settings.index') }}" title="SETTING">
            <i class="fas fa-fw fa-cog"></i>
            <span><strong>SETTING</strong></span>
        </a>
    </li>
@endif
