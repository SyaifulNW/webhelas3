<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PesertaSmi;

$m1tClasses = \App\Models\Kelas::whereIn('nama_kelas', [
    'Start-Up Muslim Indonesia',
    'Grow Up',
    'Mentoring 1 Tahun',
    'Mentoring 1 Tahun (M1T)',
    'M1T - Grow Up',
    'M1T - Start-Up'
])->pluck('id')->toArray();

$globalQuery = PesertaSmi::whereHas('salesPlan', function($q) use ($m1tClasses) {
    $q->whereIn('kelas_id', $m1tClasses);
});

$globalStats = $globalQuery->with(['salesPlan.createdBy', 'closingCs', 'createdBy'])->get();

echo "All participants stats:\n";
foreach ($globalStats as $item) {
    $biayaClosing = $item->total_pembayaran ?? $item->spp_awal;
    if (!$biayaClosing && ($item->biaya_pendaftaran || $item->pembayaran_spp)) {
        $biayaClosing = (float) $item->biaya_pendaftaran + (float) $item->pembayaran_spp;
    }
    if (!$biayaClosing)
        $biayaClosing = $item->biaya_pendaftaran;

    $itemLevel = strtolower($item->level ?? $item->salesPlan->level ?? '');
    $levelNominal = str_contains($itemLevel, 'grow') ? 1500000 : 1000000;
    
    $countPaid = 0;
    for ($m = 1; $m <= 12; $m++) {
        if (($item->{"spp_$m"} ?? 0) >= $levelNominal) {
            $countPaid++;
        }
    }
    
    $isLunas = ($item->is_lunas == 1 || $countPaid >= 6 || $biayaClosing >= (6 * $levelNominal));
    
    echo "- ID: {$item->id}, Nama: {$item->nama}, Level: {$itemLevel}, Biaya Closing: {$biayaClosing}, Monthly SPP Paid: {$countPaid}, Auto Lunas: " . ($isLunas ? 'YES' : 'NO') . "\n";
}
