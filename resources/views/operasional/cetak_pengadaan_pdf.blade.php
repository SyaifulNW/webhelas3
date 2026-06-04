<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Pengadaan — {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        /* ── Page layout ── */
        @page {
            size: A4 portrait;
            margin: 1.2cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            color: #111;
            background: #fff;
            margin: 0;
            padding: 20px 30px;
        }

        /* ── Screen toolbar (hidden on print) ── */
        .screen-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f6c23e;
            border-radius: 10px;
            padding: 10px 18px;
            margin-bottom: 20px;
        }

        .screen-toolbar h6 {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #111;
        }

        .btn-print {
            background: #4e73df;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 20px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background: #2e59d9;
        }

        .btn-close-tab {
            background: #6c757d;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-left: 8px;
        }

        /* ── Report header ── */
        .report-header {
            text-align: center;
            margin-bottom: 14px;
        }

        .report-header h2 {
            margin: 0 0 4px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 800;
        }

        .report-header .sub {
            font-size: 11px;
            color: #444;
        }

        .divider {
            border: none;
            border-top: 2px solid #111;
            margin: 8px 0 12px;
        }

        .summary-bar {
            display: flex;
            gap: 30px;
            font-size: 11px;
            margin-bottom: 12px;
            color: #333;
        }

        .summary-bar strong {
            color: #111;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead th {
            background-color: #4e73df;
            color: #fff;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 7px 5px;
            border: 1px solid #2e59d9;
            text-align: center;
            letter-spacing: 0.4px;
        }

        tbody td {
            border: 1px solid #ccc;
            padding: 6px 5px;
            vertical-align: middle;
            font-size: 10.5px;
            word-wrap: break-word;
        }

        tbody tr:nth-child(even) td {
            background-color: #f5f8ff;
        }

        tbody tr:hover td {
            background-color: #fffde7;
        }

        tfoot td {
            background-color: #edf2fb;
            font-weight: 700;
            font-size: 11px;
            border: 1px solid #aaa;
            border-top: 2px solid #333;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 9px;
            letter-spacing: 0.3px;
        }

        .badge-iya {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-tidak {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .badge-pending {
            background: #fef3c7;
            color: #78350f;
        }

        .badge-lunas {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-beli-yes {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-beli-no {
            background: #ffedd5;
            color: #7c2d12;
        }

        .printed-at {
            text-align: right;
            font-size: 9px;
            color: #888;
            margin-top: 14px;
            font-style: italic;
        }

        .empty-note {
            text-align: center;
            padding: 28px;
            color: #888;
            font-style: italic;
            font-size: 12px;
        }

        /* ── Print overrides ── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 1.2cm;
                /* Hapus header/footer bawaan browser (tanggal, URL, nomor halaman) */
                margin-top: 1.2cm;
                margin-bottom: 1.2cm;
            }

            .screen-toolbar {
                display: none !important;
            }

            body {
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tbody tr:hover td {
                background-color: inherit;
            }
        }
    </style>
</head>

<body>

    {{-- Screen-only toolbar --}}
    <div class="screen-toolbar">
        <div>
            <h6>📋 Laporan Pengadaan Barang — {{ $namaBulan }} {{ $tahun }}</h6>
            <small style="color:#555; font-size:11px;">
                💡 Tips: Sebelum cetak, matikan <strong>"Headers and footers"</strong> di pengaturan print browser agar
                tanggal & URL tidak muncul.
            </small>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">
                🖨️ Cetak / Simpan PDF
            </button>
            <button class="btn-close-tab" onclick="window.close()">✕ Tutup</button>
        </div>
    </div>

    {{-- Report content --}}
    <div class="report-header">
        <h2>Laporan Pengadaan Barang — Sudah Dibeli</h2>
        <div class="sub">
            Periode: <strong>{{ $namaBulan }} {{ $tahun }}</strong>
        </div>
    </div>
    <hr class="divider">

    <div class="summary-bar">
        <span><strong>Total Item:</strong> {{ $items->count() }}</span>
        <span><strong>Total Budget Estimasi:</strong> Rp {{ number_format($totalBudget, 0, ',', '.') }}</span>
        <span><strong>Total Realisasi Dana:</strong> Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 180px; text-align: left;">Nama Barang</th>
                <th style="width: 55px;">Jumlah</th>
                <th style="width: 110px;">Budget Estimasi</th>
                <th style="width: 110px;">Realisasi Dana</th>
                <th style="width: 80px;">Status ACC</th>
                <th style="width: 80px;">Status Beli</th>
                <th style="width: 82px;">Tanggal Dibeli</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $item)
                @php
                    $acc = $item->acc ?? 'Pending';
                    $accClass = match ($acc) {
                        'Iya' => 'badge-iya',
                        'Tidak' => 'badge-tidak',
                        'Belum Lunas' => 'badge-lunas',
                        default => 'badge-pending',
                    };
                    $accLabel = match ($acc) {
                        'Iya' => 'Disetujui',
                        'Tidak' => 'Ditolak',
                        'Belum Lunas' => 'Belum Lunas',
                        default => 'Menunggu',
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-left" style="font-weight: 600;">{{ $item->nama_barang ?: '—' }}</td>
                    <td class="text-center">{{ $item->jumlah ?: '—' }}</td>
                    <td class="text-right">Rp {{ number_format((float) $item->budget, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if ($item->realisasi_dana !== null)
                            Rp {{ number_format((float) $item->realisasi_dana, 0, ',', '.') }}
                        @else
                            <span style="color:#aaa; font-style:italic;">—</span>
                        @endif
                    </td>
                    <td class="text-center"><span class="badge {{ $accClass }}">{{ $accLabel }}</span></td>
                    <td class="text-center"><span class="badge badge-beli-yes">Sudah Dibeli</span></td>
                    <td class="text-center">
                        {{ $item->tanggal_dibeli ? \Carbon\Carbon::parse($item->tanggal_dibeli)->format('d-m-Y') : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-note">
                        Tidak ada data pengadaan yang sudah dibeli untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($items->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right">Total</td>
                    <td class="text-right">Rp {{ number_format($totalBudget, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="printed-at">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
    </div>

</body>

</html>
