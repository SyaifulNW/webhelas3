@if (\App\Models\Menu::isActive('data_calon_peserta'))
    <li
        class="nav-item {{ request()->routeIs('admin.database.database') && request('view') == 'me' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.database.database', ['view' => 'me']) }}">
            <span><strong>DATABASE CALON
                    PESERTA{{ $userRole === 'cs-mbc' ? '' : ' M1T' }}</strong></span>
        </a>
    </li>
@endif
<!-- 
@if (!in_array($userRole, ['chapter', 'agen', 'cs-mbc', 'reseller']))
    <li class="nav-item {{ request()->routeIs('zoom-schedule.calendar') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('zoom-schedule.calendar') }}">
            <i class="fas fa-fw fa-video"></i>
            <span><strong>ONE-ON-ONE ZOOM</strong></span>
        </a>
    </li>
@endif -->

@if (\App\Models\Menu::isActive('daily_activity') && !in_array($userRole, ['reseller', 'chapter', 'agen']))
    <li
        class="nav-item {{ request()->routeIs('admin.dailyactivity.index') || request()->routeIs('manager.penilaian-cs.index') ? 'active' : '' }}">
        <a class="nav-link"
            href="{{ route('admin.dailyactivity.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>DAILY ACTIVITY</span>
        </a>
    </li>
@endif

@if ($userRole === 'cs-mbc')
    @if (\App\Models\Menu::isActive('sales_plan'))
        <li
            class="nav-item {{ request()->routeIs('admin.data-peserta.unified') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.data-peserta.unified') }}"
                title="DATA PESERTA">
                <i class="fas fa-fw fa-id-card"></i>
                <span><strong>DATA PESERTA</strong></span>
            </a>
        </li>
    @endif
@endif

{{-- Menu Khusus Chapter, Reseller & Agen --}}
@if ($userRole === 'chapter' || $userRole === 'reseller' || $userRole === 'agen')

    <li class="nav-item {{ request()->routeIs('peserta-smi.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('peserta-smi.index') }}" title="SPP Peserta M1T">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span style="text-transform: none;"><strong>SPP Peserta M1T</strong></span>
            @if ($pendingM1TCount > 0)
                <span
                    class="badge badge-pending-yellow badge-pulse ml-2">{{ $pendingM1TCount }}</span>
            @endif
        </a>
    </li>

    @if ($userRole === 'chapter')
        <li class="nav-item {{ request()->routeIs('chapter.reseller.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('chapter.reseller.index') }}">
                <i class="fas fa-fw fa-users-cog"></i>
                <span><strong>MANAJEMEN AGEN</strong></span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('programkerja.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('programkerja.index') }}">
                <i class="fas fa-fw fa-tasks"></i>
                <span><strong>PROGRAM KERJA</strong></span>
            </a>
        </li>

        @if ($userRole !== 'chapter')
            @if ($userRole !== 'chapter')
                <li class="nav-item {{ request()->routeIs('gantt.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('gantt.index') }}">
                        <i class="fas fa-fw fa-project-diagram"></i>
                        <span><strong>GANCHART</strong></span>
                    </a>
                </li>
            @endif
        @endif
    @endif
@endif

@if (!in_array($userRole, ['marketing', 'hrd', 'advertising', 'produksi', 'operasional']))
    @if (\App\Models\Menu::isActive('sales_plan'))

        @if ($userRole == 'administrator')
            {{-- Administrator handled in admin.blade.php --}}
        @else
            {{-- SALES PLAN MBC (LAINNYA) --}}
            @if (
                !in_array($userRole, ['cs-smi', 'reseller', 'chapter', 'operasional', 'cs-mbc', 'agen']))
                @if ($userRole === 'cs-mbc')
                    {{-- Simple link for CS-MBC (same as administrator) --}}
                    <li class="nav-item {{ request('type') == 'mbc' ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ route('admin.salesplan.index', ['type' => 'mbc']) }}"
                            title="DATA PESERTA MBC">
                            <i class="fas fa-fw fa-users"></i>
                            <span><strong>DATA PESERTA MBC</strong></span>
                        </a>
                    </li>
                @else
                    {{-- Collapsible for others --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'mbc' || (request()->has('kelas') && request('kelas') != 'Start-Up Muslim Indonesia') ? '' : 'collapsed' }}"
                            href="#" data-toggle="collapse" data-target="#collapseMBC"
                            aria-expanded="{{ request('type') == 'mbc' || (request()->has('kelas') && request('kelas') != 'Start-Up Muslim Indonesia') ? 'true' : 'false' }}"
                            aria-controls="collapseMBC">
                            <i class="fas fa-fw fa-users"></i>
                            <span><strong>DATA PESERTA MBC</strong></span>
                        </a>
                        <div id="collapseMBC"
                            class="collapse {{ request('type') == 'mbc' || (request()->has('kelas') && request('kelas') != 'Start-Up Muslim Indonesia') ? 'show' : '' }}"
                            aria-labelledby="headingMBC" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                @if (Auth::user()->hasSubrole('sales_all_view'))
                                    <a class="collapse-item {{ request('type') == 'mbc' && !request('kelas') ? 'active' : '' }}"
                                        href="{{ route('admin.salesplan.index', ['type' => 'mbc']) }}">DATA
                                        PESERTA ALL</a>
                                @endif

                                <h6 class="collapse-header">Daftar Kelas MBC:</h6>

                                @if (auth()->user()->hasSubrole('mbc_sekolah_kaya_only'))
                                    <a class="collapse-item {{ request('kelas') == 'Sekolah Kaya' ? 'active' : '' }}"
                                        href="{{ route('admin.salesplan.index', ['kelas' => 'Sekolah Kaya', 'type' => 'mbc']) }}">
                                        Sekolah Kaya
                                    </a>
                                @else
                                    @foreach ($kelas as $item)
                                        @if ($item->nama_kelas != 'Sekolah Kaya' && $item->nama_kelas != 'Start-Up Muslim Indonesia')
                                            <a class="collapse-item {{ request('kelas') == $item->nama_kelas ? 'active' : '' }}"
                                                href="{{ route('admin.salesplan.index', ['kelas' => $item->nama_kelas, 'type' => 'mbc']) }}">
                                                {{ $item->nama_kelas }}
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </li>
                @endif
            @endif

            {{-- SALES PLAN SMI / M1T (LAINNYA) --}}
            @if (in_array($userRole, ['cs-smi', 'cs-mbc']) ||
                    Auth::user()->hasAnySubrole(['sales_all_view', 'exempt_transfer']))
                {{-- Operasional handled in its own block --}}
                @if ($userRole === 'operasional')
                    {{-- Do nothing --}}
                @elseif(Auth::user()->hasAnySubrole(['sales_all_view', 'cs_supervisor']))
                    {{-- 1. DATA PESERTA M1T (Sales Plan) --}}
                    @if ($userRole !== 'cs-mbc')
                        <li
                            class="nav-item {{ request('type') == 'smi' && request('kelas') == 'Start-Up Muslim Indonesia' ? 'active' : '' }}">
                            <a class="nav-link"
                                href="{{ route('admin.salesplan.index', ['type' => 'smi', 'kelas' => 'Start-Up Muslim Indonesia']) }}"
                                title="DATA PESERTA M1T">
                                <i class="fas fa-fw fa-users"></i>
                                <span style="text-transform: none;"><strong>DATA PESERTA
                                        M1T</strong></span>
                            </a>
                        </li>
                    @endif

                    {{-- 2. PESERTA M1T (Peserta SMI) --}}
                    <li
                        class="nav-item {{ request()->routeIs('peserta-smi.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('peserta-smi.index') }}"
                            title="SPP Peserta M1T">
                            <i class="fas fa-fw fa-user-graduate"></i>
                            <span style="text-transform: none;"><strong>SPP Peserta M1T</strong></span>
                            @if ($pendingM1TCount > 0)
                                <span
                                    class="badge badge-pending-yellow badge-pulse ml-2">{{ $pendingM1TCount }}</span>
                            @endif
                        </a>
                    </li>

                    {{-- PENARIKAN DOMPET (KHUSUS LINDA) --}}
                    @if (Auth::user()->hasSubrole('sales_all_view'))
                        @php
                            $pendingWalletWD = \App\Models\WalletTransaction::where(
                                'type',
                                'withdrawal',
                            )
                                ->where('status', 'pending')
                                ->count();
                        @endphp
                        <li
                            class="nav-item {{ request()->routeIs('admin.wallet.*') ? 'active' : '' }}">
                            <a class="nav-link d-flex align-items-center"
                                href="{{ route('admin.wallet.index') }}" title="PENARIKAN DOMPET">
                                <i class="fas fa-fw fa-wallet mr-2"></i>
                                <div class="d-flex align-items-center">
                                    <span><strong>PENARIKAN DOMPET</strong></span>
                                    @if ($pendingWalletWD > 0)
                                        <span
                                            class="badge badge-danger badge-pulse ml-2">{{ $pendingWalletWD }}</span>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endif
                @elseif(Auth::user()->hasSubrole('cs_pusat') && !Auth::user()->hasAnySubrole(['sales_all_view', 'cs_supervisor']))
                    <li
                        class="nav-item {{ request()->routeIs('peserta-smi.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('peserta-smi.index') }}"
                            title="SPP Peserta M1T">
                            <i class="fas fa-fw fa-user-graduate"></i>
                            <span style="text-transform: none;"><strong>SPP Peserta M1T</strong></span>
                            @if ($pendingM1TCount > 0)
                                <span
                                    class="badge badge-pending-yellow badge-pulse ml-2">{{ $pendingM1TCount }}</span>
                            @endif
                        </a>
                    </li>
                @else
                    <li
                        class="nav-item {{ request()->routeIs('peserta-smi.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('peserta-smi.index') }}"
                            title="SPP Peserta M1T">
                            <i class="fas fa-fw fa-user-graduate"></i>
                            <span style="text-transform: none;"><strong>SPP Peserta M1T</strong></span>
                        </a>
                    </li>
                @endif
            @endif
        @endif

        {{-- Setting for Reseller --}}
        @if ($userRole === 'reseller')
            <li class="nav-item {{ request()->routeIs('reseller.setting.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reseller.setting.index') }}"
                    title="MANAJEMEN AGEN">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span><strong>MANAJEMEN AGEN</strong></span>
                </a>
            </li>
        @endif

        {{-- Menu Keuangan Linda --}}
        @if (\App\Models\Menu::isActive('keuangan_besar') && Auth::user()->hasSubrole('finance_access'))
            <li
                class="nav-item {{ request()->routeIs(['admin.keuangan.laba-rugi', 'admin.keuangan.kas', 'admin.keuangan.pengajuan-anggaran', 'admin.keuangan.zakat']) ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapseKeuangan"
                    aria-expanded="{{ request()->routeIs(['admin.keuangan.laba-rugi', 'admin.keuangan.kas', 'admin.keuangan.pengajuan-anggaran', 'admin.keuangan.zakat']) ? 'true' : 'false' }}"
                    aria-controls="collapseKeuangan">
                    <i class="fas fa-fw fa-wallet"></i>
                    <span><strong>KEUANGAN BESAR</strong></span>
                </a>
                <div id="collapseKeuangan"
                    class="collapse {{ request()->routeIs(['admin.keuangan.laba-rugi', 'admin.keuangan.kas', 'admin.keuangan.pengajuan-anggaran', 'admin.keuangan.zakat']) ? 'show' : '' }}"
                    aria-labelledby="headingKeuangan" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('admin.keuangan.laba-rugi') ? 'active' : '' }}"
                            href="{{ route('admin.keuangan.laba-rugi') }}">Laporan Laba Rugi</a>
                        <a class="collapse-item {{ request()->routeIs('admin.keuangan.kas') ? 'active' : '' }}"
                            href="{{ route('admin.keuangan.kas') }}">Kas</a>
                        <a class="collapse-item {{ request()->routeIs('admin.keuangan.pengajuan-anggaran') ? 'active' : '' }}"
                            href="{{ route('admin.keuangan.pengajuan-anggaran') }}">Pengajuan
                            Anggaran</a>
                        <a class="collapse-item {{ request()->routeIs('admin.keuangan.zakat') ? 'active' : '' }}"
                            href="{{ route('admin.keuangan.zakat') }}">Zakat</a>
                    </div>
                </div>
            </li>
        @endif

    @endif
@endif
