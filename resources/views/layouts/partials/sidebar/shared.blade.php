<<<<<<< Updated upstream
@if ($userRole === 'administrator' || Auth::user()->hasHakAkses('gantt_cross_view'))
    @if (Auth::user()->hasHakAkses('gantt_cross_view'))
=======
@if ($userRole === 'administrator' || Auth::user()->hasAnySubrole(['hrd', 'keuangan', 'sales_marketing']))
    @if (Auth::user()->hasAnySubrole(['hrd', 'keuangan', 'sales_marketing']))
>>>>>>> Stashed changes
        {{-- Unified Program Kerja & Gantt Chart --}}
        @if (
            (\App\Models\Menu::isActive('program_kerja') || \App\Models\Menu::isActive('ganchart')) &&
                $userRole !== 'administrator')
            <li class="nav-item {{ request()->routeIs('programkerja.unified') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('programkerja.unified') }}"
                    title="PROGRAM KERJA">
                    <i class="fas fa-fw fa-tasks"></i>
                    <span><strong>PROGRAM KERJA</strong></span>
                </a>
            </li>
        @endif
    @else
        {{-- Standard separate menus --}}
        @if (\App\Models\Menu::isActive('program_kerja') && $userRole !== 'administrator')
            {{-- Program Kerja --}}
            <li class="nav-item {{ request()->routeIs('programkerja.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('programkerja.index') }}" title="Program Kerja">
                    <i class="fas fa-fw fa-tasks"></i>
                    <span>Program Kerja</span>
                </a>
            </li>
        @endif
        @if (\App\Models\Menu::isActive('ganchart') && $userRole !== 'administrator')
            {{-- Ganchart --}}
            <li class="nav-item {{ request()->routeIs('gantt.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('gantt.index') }}" title="Ganchart">
                    <i class="fas fa-fw fa-project-diagram"></i>
                    <span>Ganchart</span>
                </a>
            </li>
        @endif
    @endif

    @if (\App\Models\Menu::isActive('jadwal_kelas') && $userRole !== 'administrator')
        {{-- Jadwal Kelas --}}
        <li class="nav-item {{ request()->routeIs('admin.kelas.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kelas.index') }}" title="JADWAL KELAS">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>JADWAL KELAS</span>
            </a>
        </li>
    @endif

<<<<<<< Updated upstream
    @if (Auth::user()->hasHakAkses('cs_supervisor') || Auth::user()->hasHakAkses('finance_kecil'))
=======
    @if (Auth::user()->hasSubrole('hrd') || Auth::user()->hasSubrole('finance_kecil'))
>>>>>>> Stashed changes
        {{-- Menu Keuangan Kecil --}}
        @if (\App\Models\Menu::isActive('keuangan_kecil'))
            <li class="nav-item {{ request()->routeIs(['admin.keuangan.kas-kecil.index', 'admin.keuangan.zakat']) && !request()->routeIs(['admin.keuangan.laba-rugi', 'admin.keuangan.kas', 'admin.keuangan.pengajuan-anggaran']) ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseKeuanganYasmin"
                    aria-expanded="{{ request()->routeIs(['admin.keuangan.kas-kecil.index', 'admin.keuangan.zakat']) && !request()->routeIs(['admin.keuangan.laba-rugi', 'admin.keuangan.kas', 'admin.keuangan.pengajuan-anggaran']) ? 'true' : 'false' }}"
                    aria-controls="collapseKeuanganYasmin" title="KEUANGAN KECIL">
                    <i class="fas fa-fw fa-wallet"></i>
                    <span><strong>KEUANGAN KECIL</strong></span>
                </a>
                <div id="collapseKeuanganYasmin"
                    class="collapse {{ request()->routeIs(['admin.keuangan.kas-kecil.index', 'admin.keuangan.zakat']) && !request()->routeIs(['admin.keuangan.laba-rugi', 'admin.keuangan.kas', 'admin.keuangan.pengajuan-anggaran']) ? 'show' : '' }}"
                    aria-labelledby="headingKeuangan" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('admin.keuangan.kas-kecil.index') ? 'active' : '' }}"
                            href="{{ route('admin.keuangan.kas-kecil.index') }}">Kas Kecil</a>
                        <a class="collapse-item {{ request()->routeIs('admin.keuangan.zakat') ? 'active' : '' }}"
                            href="{{ route('admin.keuangan.zakat') }}">Zakat</a>
                    </div>
                </div>
            </li>
        @endif
    @endif

    {{-- Minutes of Meeting (MoM) — untuk Linda, Yasmin --}}
<<<<<<< Updated upstream
    @if (Auth::user()->hasHakAkses('gantt_cross_view'))
=======
    @if (Auth::user()->hasAnySubrole(['hrd', 'keuangan', 'sales_marketing']))
>>>>>>> Stashed changes
        <li class="nav-item {{ request()->routeIs('admin.mom.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.mom.index') }}"
                title="Minutes of Meeting (MoM)">
                <i class="fas fa-fw fa-clipboard-list"></i>
                <span><strong>Minutes of Meeting (MoM)</strong></span>
            </a>
        </li>
    @endif

    {{-- Penilaian Karyawan (HRD) --}}
    @if (
        \App\Models\Menu::isActive('penilaian_karyawan') &&
<<<<<<< Updated upstream
            ($userRole !== 'administrator' || Auth::user()->hasHakAkses('cs_supervisor')) &&
            !Auth::user()->hasHakAkses('gantt_cross_view'))
=======
            ($userRole !== 'administrator' || Auth::user()->hasSubrole('hrd')) &&
            !Auth::user()->hasAnySubrole(['hrd', 'keuangan', 'sales_marketing']))
>>>>>>> Stashed changes
        <li
            class="nav-item {{ request()->routeIs('hr.dashboard') || request()->routeIs('manager.penilaian-cs.index') ? 'active' : '' }}">
            <a class="nav-link text-white" href="{{ route('hr.dashboard') }}">
                <i class="fa-solid fa-list-user me-2"></i>
                <span>HRD</span>
            </a>
        </li>
    @endif
@endif

@if (strtolower(auth()->user()->role) !== 'administrator' &&
        strtolower(auth()->user()->role) !== 'produksi' &&
        strtolower(auth()->user()->role) !== 'operasional' &&
        !in_array($userRole, ['reseller', 'chapter', 'agen']) &&
<<<<<<< Updated upstream
        !Auth::user()->hasHakAkses('sales_all_view'))
=======
        !Auth::user()->hasSubrole('sales_marketing'))
>>>>>>> Stashed changes
    <li
        class="nav-item {{ request()->routeIs('admin.keuangan.pengajuan-anggaran') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.keuangan.pengajuan-anggaran') }}">
            <i class="fas fa-fw fa-file-invoice-dollar"></i>
            <span><strong>PENGAJUAN ANGGARAN</strong></span>
        </a>
    </li>
@endif

<<<<<<< Updated upstream
@if (Auth::user()->hasHakAkses('cs_supervisor') && \App\Models\Menu::isActive('settings'))
=======
@if (Auth::user()->hasSubrole('hrd') && \App\Models\Menu::isActive('settings'))
>>>>>>> Stashed changes
    <li class="nav-item {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.settings.index') }}" title="SETTING">
            <i class="fas fa-fw fa-cog"></i>
            <span><strong>SETTING</strong></span>
        </a>
    </li>
@endif

{{-- Menu SDM khusus Yasmin --}}
<<<<<<< Updated upstream
@if (Auth::user()->hasHakAkses('cs_supervisor'))
=======
@if (Auth::user()->hasSubrole('hrd'))
>>>>>>> Stashed changes
    <li class="nav-item {{ request()->routeIs(['hr', 'admin.penilaian-cs.index']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse"
            data-target="#collapseSdmYasmin"
            aria-expanded="{{ request()->routeIs(['hr', 'admin.penilaian-cs.index']) ? 'true' : 'false' }}"
            aria-controls="collapseSdmYasmin" title="SDM">
            <i class="fas fa-fw fa-users-cog"></i>
            <span><strong>SDM</strong></span>
        </a>
        <div id="collapseSdmYasmin"
            class="collapse {{ request()->routeIs(['hr', 'admin.penilaian-cs.index']) ? 'show' : '' }}"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request('section') === 'karyawan' && request()->routeIs('hr') ? 'active' : '' }}"
                    href="{{ route('hr', ['section' => 'karyawan']) }}">
                    <i class="fas fa-users mr-1"></i> Data Karyawan
                </a>
                <a class="collapse-item {{ request('section') === 'absensi' && request()->routeIs('hr') ? 'active' : '' }}"
                    href="{{ route('hr', ['section' => 'absensi']) }}">
                    <i class="fas fa-calendar-check mr-1"></i> Absensi & Izin
                </a>
                <a class="collapse-item {{ request()->routeIs('admin.penilaian-cs.index') ? 'active' : '' }}"
                    href="{{ route('admin.penilaian-cs.index') }}">
                    <i class="fas fa-star mr-1"></i> Penilaian KPI
                </a>
                <a class="collapse-item {{ request('section') === 'settings' && request()->routeIs('hr') ? 'active' : '' }}"
                    href="{{ route('hr', ['section' => 'settings']) }}">
                    <i class="fas fa-cogs mr-1"></i> Pengaturan Absensi
                </a>
            </div>
        </div>
    </li>
@endif

{{-- Menu MoM — semua role internal kecuali administrator (sudah ada di blok khusus administrator),
     chapter, reseller, agen, dan variannya --}}
@php
    $momUserRole = strtolower(trim(auth()->user()->role ?? ''));
    $momBlocked =
        $momUserRole === 'administrator' ||
<<<<<<< Updated upstream
        Auth::user()->hasHakAkses('gantt_cross_view') ||
=======
        Auth::user()->hasAnySubrole(['hrd', 'keuangan', 'sales_marketing']) ||
>>>>>>> Stashed changes
        in_array($momUserRole, \App\Http\Controllers\Admin\Operations\MomController::BLOCKED_ROLES) ||
        str_starts_with($momUserRole, 'chapter_');
@endphp
@if (!$momBlocked)
    <li class="nav-item {{ request()->routeIs('admin.mom.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.mom.index') }}" title="Minutes of Meeting (MoM)">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span><strong>Minutes of Meeting (MoM)</strong></span>
        </a>
    </li>
@endif

{{-- Menu Agenda (hanya Linda) --}}
<<<<<<< Updated upstream
@if (Auth::user()->hasHakAkses('sales_all_view'))
=======
@if (Auth::user()->hasSubrole('sales_marketing'))
>>>>>>> Stashed changes
    <li class="nav-item {{ request()->routeIs('agenda.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('agenda.index') }}" title="AGENDA">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span><strong>AGENDA</strong></span>
        </a>
    </li>
@endif
