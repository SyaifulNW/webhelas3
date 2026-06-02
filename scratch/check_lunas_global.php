<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PesertaSmi;

$globalQuery = PesertaSmi::query();
$globalStats = $globalQuery->get();

$lunasItems = [];
$lunasCount = 0;

foreach ($globalStats as $item) {
    $isManualLunas = ($item->is_lunas == 1);
    $itemLevel = strtolower($item->level ?? $item->salesPlan->level ?? '');
    $levelNominal = str_contains($itemLevel, 'grow') ? 1500000 : 1000000;
    $countPaid = 0;
    for ($m = 1; $m <= 12; $m++) {
        if (($item->{"spp_$m"} ?? 0) >= $levelNominal) {
            $countPaid++;
        }
    }
    if ($isManualLunas || $countPaid >= 6) {
        $lunasCount++;
        $lunasItems[] = [
            'id' => $item->id,
            'nama' => $item->nama,
            'status' => $item->status,
            'is_lunas' => $item->is_lunas,
            'count_paid' => $countPaid
        ];
    }
}

echo "Lunas Count (Without filtering out Cuti/OFF): " . $lunasCount . "\n";
echo "Participants:\n";
foreach ($lunasItems as $i) {
    echo "- ID: {$i['id']}, Nama: {$i['nama']}, Status: {$i['status']}, Is Lunas: {$i['is_lunas']}, Paid: {$i['count_paid']}\n";
}
