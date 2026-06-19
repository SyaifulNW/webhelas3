@if (strtolower(Auth::user()->role) === 'manager')
    @if (\App\Models\Menu::isActive('dashboard_manager'))
        <li class="nav-item {{ request()->routeIs('manager') ? 'active' : '' }}">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-briefcase"></i>
                <span>DASHBOARD MANAGER</span>
            </a>
        </li>
    @endif
@endif

@if (strtolower(Auth::user()->role) === 'hrd')
    @if (\App\Models\Menu::isActive('dashboard_hr'))
        <li class="nav-item {{ request()->routeIs('hr') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('hr') }}">
                <i class="fas fa-fw fa-briefcase"></i>
                <span>DASHBOARD HR</span>
            </a>
        </li>
    @endif
@endif
