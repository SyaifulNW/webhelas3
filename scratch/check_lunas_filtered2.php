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

// Filter approved
$globalStats = $globalStats->filter(function($item) {
    $creatorRole = strtolower($item->closingCs->role ?? $item->createdBy->role ?? $item->salesPlan->createdBy->role ?? '');
    $needsApproval = in_array($creatorRole, ['reseller', 'chapter', 'agen']);
    if ($needsApproval) {
        return $item->approval_status === 'Approved';
    }
    return true;
});

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

echo "Filtered Lunas Count: " . $lunasCount . "\n";
echo "Participants:\n";
foreach ($lunasItems as $i) {
    echo "- ID: {$i['id']}, Nama: {$i['nama']}, Status: {$i['status']}, Is Lunas: {$i['is_lunas']}, Paid: {$i['count_paid']}\n";
}
