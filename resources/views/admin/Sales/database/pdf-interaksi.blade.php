<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Interaksi Follow Up</title>
    <style>
        @page {
            margin: 30px 40px 30px 40px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .page-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-info {
            font-size: 11px;
            color: #475569;
            margin: 0;
        }
        .meta-info strong {
            color: #0f172a;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        tr {
            page-break-inside: avoid;
        }
        th {
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            padding: 8px 10px;
            border: 1px solid #1d4ed8;
            text-align: left;
            letter-spacing: 0.3px;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            vertical-align: top;
            background-color: #ffffff;
        }
        .row-even td {
            background-color: #f8fafc;
        }
        
        /* Participant Column Style */
        .peserta-name {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
        }
        .peserta-detail {
            font-size: 8.5px;
            color: #475569;
            margin-bottom: 3px;
        }
        .peserta-detail strong {
            color: #334155;
        }
        
        /* Follow Up Cards Column Style */
        .fu-container {
            display: block;
        }
        .fu-card {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            background-color: #ffffff;
            margin-bottom: 6px;
            padding: 6px 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            page-break-inside: avoid;
        }
        .fu-card:last-child {
            margin-bottom: 0;
        }
        .fu-header {
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 5px;
            zoom: 1;
        }
        .fu-header:after {
            content: '';
            display: block;
            clear: both;
        }
        .fu-title {
            float: left;
            font-size: 8px;
            font-weight: 800;
            color: #2563eb;
        }
        .fu-date {
            float: right;
            font-size: 7.5px;
            color: #64748b;
            font-weight: 600;
        }
        .fu-channels {
            margin-bottom: 4px;
        }
        .badge {
            display: inline-block;
            font-size: 7px;
            font-weight: 800;
            padding: 1px 4px;
            border-radius: 3px;
            margin-right: 3px;
            text-transform: uppercase;
        }
        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
            border: 0.5px solid #bbf7d0;
        }
        .badge-inactive {
            background-color: #f1f5f9;
            color: #94a3b8;
            border: 0.5px solid #e2e8f0;
        }
        .fu-text {
            font-size: 8.5px;
            margin-top: 3px;
            color: #334155;
            word-wrap: break-word;
        }
        .fu-text strong {
            color: #475569;
        }
        
        .no-data {
            color: #94a3b8;
            font-style: italic;
            text-align: center;
            padding: 15px 0;
            font-size: 10px;
        }

        /* Tindak Lanjut Table */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 25px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 3px solid #3b82f6;
            padding-left: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="border: none; padding: 0;">
                    <h1 class="page-title">Riwayat Interaksi Follow Up Leads</h1>
                    <p class="meta-info">
                        <strong>CS:</strong> {{ $csName }} &nbsp;|&nbsp; 
                        <strong>Periode:</strong> {{ $bulan ? \Carbon\Carbon::create()->month($bulan)->isoFormat('MMMM') : 'Semua Bulan' }} {{ $tahun }}
                        @if(!empty($kelasName))
                            &nbsp;|&nbsp; <strong>Kelas:</strong> {{ $kelasName }}
                        @endif
                        @if(!empty($statusName))
                            &nbsp;|&nbsp; <strong>Status:</strong> {{ $statusName }}
                        @endif
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">NO</th>
                <th style="width: 30%;">INFORMASI PESERTA</th>
                <th style="width: 65%;">RIWAYAT INTERAKSI (1 - 10)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            @php 
                $isEven = $index % 2 == 0; 
                $sp = $item->salesplan ? $item->salesplan->first() : null;
                $source = $sp ?: $item;

                // Check if this source has any filled follow ups
                $hasAnyFu = false;
                for($i=1; $i<=10; $i++) {
                    $hasil = "fu{$i}_hasil";
                    $tl = "fu{$i}_tindak_lanjut";
                    $wa = "fu{$i}_wa";
                    $telp = "fu{$i}_telp";
                    if (!empty($source->$hasil) || !empty($source->$tl) || $source->$wa || $source->$telp) {
                        $hasAnyFu = true;
                    }
                }
            @endphp
            <tr class="{{ $isEven ? 'row-even' : '' }}">
                <td style="text-align: center; font-weight: bold; color: #475569; font-size: 10px;">{{ $index + 1 }}</td>
                <td>
                    <div class="peserta-name">{{ $item->nama ?: '-' }}</div>
                    <div class="peserta-detail"><strong>No. WA:</strong> {{ $item->no_wa ?: '-' }}</div>
                    <div class="peserta-detail"><strong>Kelas:</strong> {{ $item->kelas ? $item->kelas->nama_kelas : '-' }}</div>
                    <div class="peserta-detail"><strong>Status:</strong> <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $item->status_peserta) }}</span></div>
                    <div class="peserta-detail"><strong>CS Penginput:</strong> {{ $item->created_by ?: '-' }}</div>
                </td>
                <td>
                    @if($hasAnyFu)
                        <div class="fu-container">
                            @for($i=1; $i<=10; $i++)
                                @php
                                    $hasil = "fu{$i}_hasil";
                                    $tl = "fu{$i}_tindak_lanjut";
                                    $wa = "fu{$i}_wa";
                                    $telp = "fu{$i}_telp";
                                    $fuAt = "fu{$i}_at";
                                @endphp
                                @if(!empty($source->$hasil) || !empty($source->$tl) || $source->$wa || $source->$telp)
                                    <div class="fu-card">
                                        <div class="fu-header">
                                            <span class="fu-title">FOLLOW UP {{ $i }}</span>
                                            <span class="fu-date">
                                                {{ $source->$fuAt ? \Carbon\Carbon::parse($source->$fuAt)->format('d/m/Y H:i') : '-' }}
                                            </span>
                                        </div>
                                        <div class="fu-channels">
                                            <span class="badge {{ $source->$wa ? 'badge-active' : 'badge-inactive' }}">WA {{ $source->$wa ? '✔' : '-' }}</span>
                                            <span class="badge {{ $source->$telp ? 'badge-active' : 'badge-inactive' }}">TELP {{ $source->$telp ? '✔' : '-' }}</span>
                                        </div>
                                        @if(!empty($source->$hasil))
                                            <div class="fu-text"><strong>Hasil:</strong> {{ $source->$hasil }}</div>
                                        @endif
                                        @if(!empty($source->$tl))
                                            <div class="fu-text"><strong>Tindak Lanjut:</strong> {{ $source->$tl }}</div>
                                        @endif
                                    </div>
                                @endif
                            @endfor
                        </div>
                    @else
                        <div class="no-data">Belum ada riwayat follow up pada bulan ini</div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="no-data" style="padding: 30px 0; font-size: 11px;">Data tidak ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $tindakLanjutList = [];
        foreach($items as $item) {
            $sp = $item->salesplan ? $item->salesplan->first() : null;
            $source = $sp ?: $item;
            $latestTl = null;
            $latestDate = null;
            for($i=10; $i>=1; $i--){
                $tl = "fu{$i}_tindak_lanjut";
                $fuAt = "fu{$i}_at";
                if(!empty($source->$tl) && $source->$tl != '-') {
                    // Check if it's within the selected month and year
                    $isMatchPeriode = true;
                    if ($bulan && $tahun) {
                        try {
                            $itemDate = \Carbon\Carbon::parse($source->$fuAt);
                            if ($itemDate->month != $bulan || $itemDate->year != $tahun) {
                                $isMatchPeriode = false;
                            }
                        } catch (\Exception $e) {
                            $isMatchPeriode = false;
                        }
                    }

                    if ($isMatchPeriode) {
                        $latestTl = $source->$tl;
                        $latestDate = $source->$fuAt;
                        break;
                    }
                }
            }
            if($latestTl) {
                $tindakLanjutList[] = [
                    'nama' => $item->nama,
                    'tindak_lanjut' => $latestTl,
                    'no_wa' => $item->no_wa,
                    'tanggal' => $latestDate ? \Carbon\Carbon::parse($latestDate)->format('d/m/Y') : '-'
                ];
            }
        }
    @endphp

    @if(count($tindakLanjutList) > 0)
    <div class="section-title">Daftar Tindak Lanjut Terdekat</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">NO</th>
                <th style="width: 15%; text-align: center;">TANGGAL FU</th>
                <th style="width: 25%;">NAMA PESERTA</th>
                <th style="width: 15%;">NO WA</th>
                <th style="width: 40%;">TINDAK LANJUT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tindakLanjutList as $index => $tl)
            @php $isEven = $index % 2 == 0; @endphp
            <tr class="{{ $isEven ? 'row-even' : '' }}">
                <td style="text-align: center; font-weight: bold; color: #475569;">{{ $index + 1 }}</td>
                <td style="text-align: center; font-weight: 600; color: #334155;">{{ $tl['tanggal'] }}</td>
                <td style="font-weight: bold; color: #0f172a;">{{ $tl['nama'] ?: '-' }}</td>
                <td>{{ $tl['no_wa'] ?: '-' }}</td>
                <td style="color: #334155; font-weight: 500;">{{ $tl['tindak_lanjut'] ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
