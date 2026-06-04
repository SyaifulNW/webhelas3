<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Checklist Pengecekan Inventaris Kantor</title>

    <style>
        @page {
            margin: 1.5cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.4;
        }

        /* ==========================
            HEADER PERUSAHAAN
        ========================== */

        .company-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .company-header td {
            border: none;
            vertical-align: middle;
        }

        .logo {
            width: 90px;
            height: auto;
        }

        .main-title {
            text-align: center;
        }

        .main-title h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .main-title p {
            margin-top: 4px;
            color: #666;
            font-size: 10px;
        }

        .doc-info {
            text-align: right;
            font-size: 10px;
            line-height: 1.5;
        }

        .divider {
            border: none;
            border-top: 2px solid #333;
            margin: 10px 0 15px;
        }

        /* ==========================
            INFORMASI DOKUMEN
        ========================== */

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            border: none;
            padding: 3px 0;
            font-size: 10px;
        }

        /* ==========================
            TABEL INVENTARIS
        ========================== */

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .inventory-table th {
            background: #edf2f7;
            border: 1px solid #555;
            padding: 8px 5px;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .inventory-table td {
            border: 1px solid #666;
            padding: 8px 6px;
            font-size: 10px;
            vertical-align: middle;
        }

        .inventory-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .checkbox {
            font-size: 16px;
            text-align: center;
        }

        /* ==========================
            CATATAN
        ========================== */

        .notes-section {
            margin-top: 20px;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .notes-box {
            border: 1px solid #666;
            height: 100px;
        }

        /* ==========================
            APPROVAL
        ========================== */

        .approval-section {
            margin-top: 25px;
        }

        .approval-date {
            text-align: right;
            margin-bottom: 20px;
        }

        .approval-table {
            width: 100%;
            border-collapse: collapse;
        }

        .approval-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: none;
            text-align: right;
        }

        .signature-space {
            height: 70px;
            
        }

        .signature-name {
            margin-top: 5px;
            text-align: right;
        }

        /* ==========================
            FOOTER
        ========================== */

        .footer-note {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
            font-style: italic;
        }

        .footer-divider {
            margin-top: 15px;
            border: none;
            border-top: 1px solid #ccc;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 9px;
            color: #666;
        }

        .footer-table td {
            border: none;
        }

        .footer-right {
            text-align: right;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <table class="company-header">
        <tr>

            <td width="20%">
                <img src="{{ public_path('backend/helas.png') }}" class="logo">
            </td>

            <td width="60%">
                <div class="main-title">
                    <h1>Checklist Pengecekan Inventaris Kantor</h1>
                    <p>Dokumen Pemeriksaan Inventaris Bulanan</p>
                </div>
            </td>

            <td width="20%">
                <div class="doc-info">
                    <strong>No Dokumen</strong><br>
                    INV-CHK-{{ now()->format('Ym') }}<br>
                    Rev.00
                </div>
            </td>

        </tr>
    </table>

    <hr class="divider">

    {{-- INFORMASI --}}
    <table class="info-table">
        <tr>
            <td width="120"><strong>Tanggal Cetak</strong></td>
            <td width="10">:</td>
            <td>{{ $tanggalCetak }}</td>

            <td width="120"><strong>Periode</strong></td>
            <td width="10">:</td>
            <td>{{ $periode }}</td>
        </tr>

        <tr>
            <td><strong>Dicetak Oleh</strong></td>
            <td>:</td>
            <td>{{ Auth::user()->name ?? '-' }}</td>

            <td><strong>Total Inventaris</strong></td>
            <td>:</td>
            <td>{{ $inventaris->count() }} Item</td>
            
        </tr>
    </table>

    {{-- TABEL --}}
    <table class="inventory-table">

        <thead>
            <tr>
                <th style="width:40px;">No</th>
                <th>Nama Inventaris</th>
                <th>Jumlah</th>
                <th style="width:150px;">Lokasi</th>
                <th style="width:60px;">Baik</th>
                <th style="width:60px;">Rusak</th>
                <th style="width:180px;">Keterangan</th>
            </tr>
        </thead>

        <tbody>

            @forelse($inventaris as $index => $item)

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td class="left">
                        {{ $item->nama_peralatan }}
                    </td>

                    <td class="center">
                        {{ $item->jumlah }}
                    </td>

                    <td class="center">
                        {{ $item->lokasi }}
                    </td>

                    <td class="checkbox">
                        □
                    </td>

                    <td class="checkbox">
                        □
                    </td>

                    <td></td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="center">
                        Tidak ada data inventaris.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    {{-- CATATAN --}}
    <div class="notes-section">

        <div class="notes-title">
            Catatan Pemeriksaan
        </div>

        <div class="notes-box"></div>

    </div>

    {{-- TANDA TANGAN --}}
    <div class="approval-section">

        <div class="approval-date">
            ________________________, __________________
        </div>

        <table class="approval-table">

            <tr>

                <td>

                    <strong>Diperiksa Oleh</strong>

                    <div class="signature-space"></div>

                    <div class="signature-name">
                        (__________________________)
                    </div>

                    <div>
                        Petugas Pemeriksa
                    </div>

                </td>

            </tr>

        </table>

    </div>

    {{-- FOOTER --}}
    <div class="footer-note">
        * Beri tanda (✓) pada kolom Baik atau Rusak sesuai kondisi inventaris saat dilakukan pemeriksaan.
    </div>

    <hr class="footer-divider">

    <table class="footer-table">
        <tr>
            <td>
                Dokumen ini dibuat secara otomatis oleh Sistem Inventaris Kantor.
            </td>

            <td class="footer-right">
                Checklist Inventaris Bulanan
            </td>
        </tr>
    </table>

</body>

</html>