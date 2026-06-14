<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $pdfTitle }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333333;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        @page {
            margin: 1cm 1cm 1cm 1cm;
        }
        .header-container {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 3px solid #2e59d9;
            padding-bottom: 8px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .title-block {
            text-align: left;
        }
        .title-block h2 {
            margin: 0 0 4px 0;
            color: #1e3a8a;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-block p {
            margin: 0;
            color: #555555;
            font-size: 9px;
            font-weight: 500;
        }
        .meta-block {
            text-align: right;
            font-size: 8px;
            color: #666666;
        }
        
        /* Stats Table */
        .summary-box {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-box td {
            border: 1px solid #e3e6f0;
            padding: 6px 10px;
            background-color: #f8f9fc;
            text-align: center;
        }
        .summary-title {
            font-size: 8px;
            text-transform: uppercase;
            color: #858796;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .summary-value {
            font-size: 13px;
            font-weight: bold;
            color: #2e59d9;
        }
        .summary-value.success { color: #1cc88a; }
        .summary-value.danger { color: #e74a3b; }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #2e59d9;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            padding: 5px 3px;
            border: 1px solid #1d3db0;
            text-align: center;
            vertical-align: middle;
        }
        .data-table td {
            padding: 5px 3px;
            border: 1px solid #e3e6f0;
            vertical-align: middle;
            font-size: 8.5px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        
        /* Badge styling */
        .badge {
            display: inline-block;
            padding: 1px 3px;
            font-size: 6.5px;
            font-weight: bold;
            border-radius: 2px;
            text-transform: uppercase;
            text-align: center;
            margin-right: 2px;
        }
        .badge-primary { background-color: #e8f0fe; color: #1a73e8; }
        .badge-success { background-color: #e6f4ea; color: #137333; }
        .badge-danger { background-color: #fce8e6; color: #c5221f; }
        .badge-warning { background-color: #fef7e0; color: #b06000; }
        .badge-secondary { background-color: #f1f3f4; color: #5f6368; }
        
        /* Month Checklist Cells */
        .cell-blue {
            background-color: #e8f0fe !important;
            color: #1a73e8;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }
        .cell-green {
            background-color: #e6f4ea !important;
            color: #137333;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }
        .cell-red {
            background-color: #fce8e6 !important;
            color: #c5221f;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }
        .cell-empty {
            text-align: center;
            color: #babcbe;
        }
        
        /* Legend Block */
        .legend-container {
            margin-top: 15px;
            padding: 8px 12px;
            background-color: #ffffff;
            border: 1px solid #e3e6f0;
            border-radius: 6px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 20px;
            font-size: 8px;
        }
        .legend-color-box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: middle;
        }
    </style>
</head>
<body>

    <!-- Header Block -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="title-block">
                    <h2>{{ $pdfTitle }}</h2>
                    <p>Sistem Informasi Helas &bull; Program Mentoring 1 Tahun (M1T)</p>
                </td>
                <td class="meta-block">
                    Tanggal Cetak: {{ date('d F Y H:i') }}<br>
                    Dicetak Oleh: {{ auth()->user()->name }} ({{ strtoupper(auth()->user()->role) }})
                </td>
            </tr>
        </table>
    </div>

    <!-- Statistik & Keuangan Keanggotaan Side-by-Side Summary Tables -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse; border: none;">
        <tr>
            <!-- Statistik Keanggotaan -->
            <td style="width: 48%; vertical-align: top; border: none; padding: 0 10px 0 0;">
                <table class="stat-card-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th colspan="2" style="background-color: #4e73df; color: #ffffff; font-weight: bold; font-size: 8px; padding: 4px 8px; text-align: left; border: 1px solid #4e73df;">
                                STATISTIK KEANGGOTAAN
                            </th>
                        </tr>
                        <tr style="background-color: #f8f9fc; font-size: 6.5px; color: #858796; font-weight: bold;">
                            <th style="padding: 3px 6px; text-align: left; border: 1px solid #e3e6f0;">STATUS PESERTA</th>
                            <th style="padding: 3px 6px; text-align: center; border: 1px solid #e3e6f0; width: 60px;">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: 500; font-size: 7.5px;">Total Peserta Keseluruhan (Aktif & OFF)</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: bold; color: #4e73df; font-size: 8px;">{{ number_format($stats['total'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: 500; font-size: 7.5px;">Total Peserta Aktif</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: bold; color: #1cc88a; font-size: 8px;">{{ number_format($stats['aktif'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: 500; font-size: 7.5px;">Total Peserta OFF</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: bold; color: #e74a3b; font-size: 8px;">{{ number_format($stats['cuti'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: 500; font-size: 7.5px;">Peserta Lunas Keseluruhan</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: bold; color: #36b9cc; font-size: 8px;">{{ number_format($stats['lunas'] ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: 500; font-size: 7.5px;">Peserta Belum Approve</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: bold; color: #f6c23e; font-size: 8px;">{{ number_format($stats['pending'] ?? 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            
            <!-- Keuangan Keanggotaan -->
            <td style="width: 52%; vertical-align: top; border: none; padding: 0 0 0 10px;">
                @php
                    $monthName = $stats['filter_month_name'] ?? '';
                    $showMonth = $sppMonth !== 'all' ? true : false;
                @endphp
                <table class="stat-card-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th colspan="3" style="background-color: #36b9cc; color: #ffffff; font-weight: bold; font-size: 8px; padding: 4px 8px; text-align: left; border: 1px solid #36b9cc;">
                                KEUANGAN KEANGGOTAAN
                            </th>
                        </tr>
                        <tr style="background-color: #f8f9fc; font-size: 6.5px; color: #858796; font-weight: bold;">
                            <th style="padding: 3px 6px; text-align: left; border: 1px solid #e3e6f0;">KATEGORI</th>
                            <th style="padding: 3px 6px; text-align: center; border: 1px solid #e3e6f0; width: 45px;">JUMLAH</th>
                            <th style="padding: 3px 6px; text-align: right; border: 1px solid #e3e6f0; width: 85px;">NOMINAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: 500; font-size: 7.5px;">
                                Peserta Baru Closing
                                @if($showMonth && $monthName)
                                    <span style="background-color: #1cc88a; color: white; padding: 1px 3px; border-radius: 2px; font-size: 6px; font-weight: bold; margin-left: 2px;">{{ $monthName }}</span>
                                @endif
                            </td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: 500; font-size: 7.5px;">{{ number_format($stats['count_closing'] ?? 0) }}</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: right; font-weight: bold; color: #4e73df; font-size: 7.5px;">Rp {{ number_format($stats['nominal_closing'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: 500; font-size: 7.5px;">
                                Peserta Lama Bayar SPP
                                @if($showMonth && $monthName)
                                    <span style="background-color: #1cc88a; color: white; padding: 1px 3px; border-radius: 2px; font-size: 6px; font-weight: bold; margin-left: 2px;">{{ $monthName }}</span>
                                @endif
                            </td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: 500; font-size: 7.5px;">{{ number_format($stats['count_spp'] ?? 0) }}</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: right; font-weight: bold; color: #1cc88a; font-size: 7.5px;">Rp {{ number_format($stats['nominal_spp'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr style="background-color: rgba(78, 115, 223, 0.05);">
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: bold; color: #4e73df; font-size: 7.5px;">Total Pembayaran Masuk</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: bold; color: #4e73df; font-size: 7.5px;">{{ number_format(($stats['count_closing'] ?? 0) + ($stats['count_spp'] ?? 0)) }}</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: right; font-weight: bold; color: #4e73df; font-size: 7.5px;">Rp {{ number_format(($stats['nominal_closing'] ?? 0) + ($stats['nominal_spp'] ?? 0), 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: 500; font-size: 7.5px;">
                                Potensi Belum Bayar
                                @if($showMonth && $monthName)
                                    <span style="background-color: #1cc88a; color: white; padding: 1px 3px; border-radius: 2px; font-size: 6px; font-weight: bold; margin-left: 2px;">{{ $monthName }}</span>
                                @endif
                            </td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: 500; font-size: 7.5px;">{{ number_format($stats['count_belum'] ?? 0) }}</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: right; font-weight: bold; color: #f6c23e; font-size: 7.5px;">Rp {{ number_format($stats['nominal_belum'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; font-weight: 500; font-size: 7.5px;">Peserta Menunggak</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: center; font-weight: 500; font-size: 7.5px;">{{ number_format($stats['count_menunggak'] ?? 0) }}</td>
                            <td style="padding: 4px 6px; border: 1px solid #e3e6f0; text-align: right; font-weight: bold; color: #e74a3b; font-size: 7.5px;">Rp {{ number_format($stats['nominal_menunggak'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%">No</th>
                <th style="width: 25%; text-align: left; padding-left: 6px;">Nama Peserta</th>
                <th style="width: 10%">Biaya Closing</th>
                <th style="width: 12%">PIC CS</th>
                @php
                    $monthsAbbr = [
                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                        7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                    ];
                @endphp
                @for($m = 1; $m <= 12; $m++)
                    <th style="width: 4.1%">{{ $monthsAbbr[$m] }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    $isManualLunas = ($item->is_lunas == 1);
                    $isAutoLunas = false;
                    $itemLevel = strtolower($item->level ?? $item->salesPlan->level ?? '');
                    $levelNominal = str_contains($itemLevel, 'grow') ? 1500000 : 1000000;
                    
                    if ($item->tanggal_masuk) {
                        $startJoin = \Carbon\Carbon::parse($item->tanggal_masuk)->startOfMonth();
                        $countPaid = 0;
                        for ($m = 0; $m < 12; $m++) {
                            $checkM = $startJoin->copy()->addMonths($m);
                            $mNum = (int) $checkM->format('n');
                            if (($item->{"spp_$mNum"} ?? 0) >= $levelNominal) {
                                $countPaid++;
                            }
                        }
                        if ($countPaid >= 6) {
                            $isAutoLunas = true;
                        }
                    }
                    $isAllPaid = $isManualLunas || $isAutoLunas;
                    
                    $biayaClosing = $item->total_pembayaran ?? $item->spp_awal;
                    if (!$biayaClosing && ($item->biaya_pendaftaran || $item->pembayaran_spp)) {
                        $biayaClosing = (float) $item->biaya_pendaftaran + (float) $item->pembayaran_spp;
                    }
                    if (!$biayaClosing) $biayaClosing = $item->biaya_pendaftaran;
                    
                    $picName = $item->cs_name ?: ($item->closingCs->name ?? ($item->salesPlan ? $item->salesPlan->created_by_name : ($item->createdBy->name ?? '-')));
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="padding-left: 6px;">
                        <span style="font-weight: bold; color: #1a202c; font-size: 9px;">{{ $item->nama }}</span>
                        @if($item->nama_2)
                            <span style="font-size: 7px; color: #718096;">({{ $item->nama_2 }})</span>
                        @endif
                        <div style="margin-top: 2px;">
                            @if($item->status == 'Aktif')
                                <span class="badge badge-success">Aktif</span>
                            @elseif(in_array($item->status, ['Cuti', 'OFF', 'off']))
                                <span class="badge badge-danger">OFF</span>
                            @else
                                <span class="badge badge-warning">{{ $item->status }}</span>
                            @endif
                            
                            @if($item->level)
                                <span class="badge badge-primary" style="background-color: #ebf8ff; color: #2b6cb0;">{{ $item->level }}</span>
                            @endif
                            
                            @if($item->tanggal_masuk)
                                <span style="font-size: 7px; color: #4a5568;">Masuk: {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/y') }}</span>
                            @endif
                            
                            @if($isAllPaid)
                                <span class="badge badge-primary" style="background-color: #2e59d9; color: #ffffff; font-weight: bold;">LUNAS</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center font-weight-bold" style="font-size: 8.5px; color: #2d3748;">
                        Rp {{ number_format((float) $biayaClosing, 0, ',', '.') }}
                    </td>
                    <td class="text-center" style="font-size: 8px; color: #4a5568; font-weight: 500;">
                        {{ $picName }}
                    </td>
                    
                    {{-- 12 Month SPP columns --}}
                    @for($i = 1; $i <= 12; $i++)
                        @php
                            $isVisible = true;
                            if (in_array($item->status, ['Cuti', 'OFF', 'off']) || $item->status == 'Lulus') {
                                $isVisible = false;
                            }
                            
                            $selectedMonths = [];
                            $excludedMonths = [];
                            if ($item->salesPlan) {
                                $sel = $item->salesPlan->selected_months;
                                if (is_array($sel)) {
                                    $selectedMonths = $sel;
                                } else if (is_string($sel)) {
                                    $selectedMonths = json_decode($sel, true) ?? [];
                                }
                            }
                            if (isset($selectedMonths['excluded_months'])) {
                                $excludedMonths = $selectedMonths['excluded_months'];
                                unset($selectedMonths['excluded_months']);
                            }
                            
                            $isPlanChecked = false;
                            $effectiveDate = null;
                            if ($item->salesPlan) {
                                if ($item->salesPlan->tanggal_closing) {
                                    $effectiveDate = \Carbon\Carbon::parse($item->salesPlan->tanggal_closing);
                                } else {
                                    $effectiveDate = $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk) : $item->salesPlan->updated_at;
                                }
                            } else {
                                $effectiveDate = $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk) : $item->created_at;
                            }

                            if ($effectiveDate && $effectiveDate->format('Y') == $yearFilter && (int)$effectiveDate->format('n') == $i) {
                                $isPlanChecked = true;
                            }

                            if (isset($selectedMonths[$yearFilter]) && in_array((int) $i, $selectedMonths[$yearFilter])) {
                                $isPlanChecked = true;
                            }

                            if ($yearFilter == 2026 && $i <= 3) {
                                $isPlanChecked = false;
                            }

                            if (isset($excludedMonths[$yearFilter]) && in_array((int) $i, $excludedMonths[$yearFilter])) {
                                $isPlanChecked = false;
                            }
                            
                            $customSchedule = $item->spp_custom_schedule ?? [];
                            $plannedNominal = null;
                            foreach ($customSchedule as $sch) {
                                if ($sch['month'] == $i && ($sch['year'] ?? $yearFilter) == $yearFilter) {
                                    $plannedNominal = $sch['nominal'];
                                    break;
                                }
                            }
                            
                            $isPaid = ($item->{"spp_$i"} >= $levelNominal);
                            if ($isPaid && $item->{"tanggal_spp_$i"}) {
                                $paymentYear = \Carbon\Carbon::parse($item->{"tanggal_spp_$i"})->format('Y');
                                if ($paymentYear != $yearFilter) {
                                    $isPaid = false;
                                }
                            }

                            // Arrears check
                            $isMenunggakThisMonth = false;
                            if ($item->status === 'Aktif' && !$isAllPaid && !$isPaid) {
                                $joinMonth = 1;
                                if ($item->tanggal_masuk) {
                                    try {
                                        $joinDate = \Carbon\Carbon::parse($item->tanggal_masuk);
                                        if ($joinDate->year == $yearFilter) {
                                            $joinMonth = (int)$joinDate->month;
                                        }
                                    } catch (\Exception $e) {}
                                }
                                
                                $activeMonthVal = $sppMonth;
                                if (!$activeMonthVal || $activeMonthVal === 'all') {
                                    $activeMonthVal = date('n');
                                }
                                $currentActiveMonth = (int)$activeMonthVal;
                                
                                if ($i >= $joinMonth && $i < $currentActiveMonth) {
                                    $isClosingInMonth = false;
                                    if ($item->salesPlan) {
                                        $effDate = null;
                                        if ($item->salesPlan->tanggal_closing) {
                                            $effDate = \Carbon\Carbon::parse($item->salesPlan->tanggal_closing);
                                        } else {
                                            $effDate = $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk) : $item->salesPlan->updated_at;
                                        }
                                        if ($effDate && (int)$effDate->month === $i && (int)$effDate->year == $yearFilter) {
                                            $isClosingInMonth = true;
                                        }
                                    } else {
                                        $effDate = $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk) : $item->created_at;
                                        if ($effDate && (int)$effDate->month === $i && (int)$effDate->year == $yearFilter) {
                                            $isClosingInMonth = true;
                                        }
                                    }
                                    
                                    $isPlannedInMonth = false;
                                    foreach ((array)$customSchedule as $sch) {
                                        if ($sch['month'] == $i && ($sch['year'] ?? $yearFilter) == $yearFilter) {
                                            $isPlannedInMonth = true;
                                            break;
                                        }
                                    }
                                    if (!$isPlannedInMonth && $item->salesPlan) {
                                        $selectedMonths = $item->salesPlan->selected_months;
                                        if (is_string($selectedMonths)) {
                                            $selectedMonths = json_decode($selectedMonths, true) ?? [];
                                        }
                                        if (isset($selectedMonths[$yearFilter]) && is_array($selectedMonths[$yearFilter])) {
                                            if (in_array($i, $selectedMonths[$yearFilter])) {
                                                $isPlannedInMonth = true;
                                            }
                                        }
                                    }
                                    
                                    $isBlueInMonth = $isClosingInMonth || $isPlannedInMonth;
                                    if (!$isBlueInMonth) {
                                        $isMenunggakThisMonth = true;
                                    }
                                }
                            }
                            
                            $cellClass = 'cell-empty';
                            $cellContent = '';
                            
                            if ($isVisible) {
                                if ($isPaid || $isPlanChecked) {
                                    if ($isAllPaid || $isPlanChecked || $plannedNominal) {
                                        $cellClass = 'cell-blue';
                                        $cellContent = '&#10004;'; // Blue check
                                    } else {
                                        $cellClass = 'cell-green';
                                        $cellContent = '&#10004;'; // Green check
                                    }
                                } elseif ($plannedNominal) {
                                    $cellClass = 'cell-blue';
                                    $cellContent = 'P'; // Planned
                                } elseif ($isMenunggakThisMonth) {
                                    $cellClass = 'cell-red';
                                    $cellContent = '&#10008;'; // Arrears cross
                                } else {
                                    $cellClass = 'cell-empty';
                                    $cellContent = '-';
                                }
                            } else {
                                $cellClass = 'cell-empty';
                                $cellContent = '-';
                            }
                        @endphp
                        <td class="{{ $cellClass }}">
                            {!! $cellContent !!}
                        </td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="16" class="text-center" style="padding: 20px; color: #858796;">
                        Tidak ada data peserta ditemukan untuk filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Legend Block -->
    <div class="legend-container">
        <div style="font-weight: bold; margin-bottom: 4px; font-size: 8px; color: #2e59d9;">LEGENDA STATUS PEMBAYARAN SPP:</div>
        <div class="legend-item">
            <span class="legend-color-box" style="background-color: #e8f0fe; border: 1px solid #1a73e8;"></span>
            <span style="font-weight: bold; color: #1a73e8;">&#10004; (Centang Biru):</span> Closing Awal / Status Lunas Program
        </div>
        <div class="legend-item">
            <span class="legend-color-box" style="background-color: #e6f4ea; border: 1px solid #137333;"></span>
            <span style="font-weight: bold; color: #137333;">&#10004; (Centang Hijau):</span> Pembayaran SPP Manual Bulanan
        </div>
        <div class="legend-item">
            <span class="legend-color-box" style="background-color: #fce8e6; border: 1px solid #c5221f;"></span>
            <span style="font-weight: bold; color: #c5221f;">&#10008; (Silang Merah):</span> Menunggak SPP
        </div>
        <div class="legend-item">
            <span style="font-weight: bold; color: #333;">P:</span> Rencana Pembayaran Terjadwal (Planned)
        </div>
    </div>

</body>
</html>
