<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PesertaSmi;

$iin = PesertaSmi::where('nama', 'like', '%Iin%')->first();
if ($iin) {
    $m1tClasses = \App\Models\Kelas::whereIn('nama_kelas', [
        'Start-Up Muslim Indonesia',
        'Grow Up',
        'Mentoring 1 Tahun',
        'Mentoring 1 Tahun (M1T)',
        'M1T - Grow Up',
        'M1T - Start-Up'
    ])->pluck('id')->toArray();

    $inQuery = PesertaSmi::where('id', $iin->id)
        ->whereHas('salesPlan', function($q) use ($m1tClasses) {
            $q->whereIn('kelas_id', $m1tClasses);
        })->exists();
    echo "Exists in base query: " . ($inQuery ? 'YES' : 'NO') . "\n";

    $p = PesertaSmi::with(['salesPlan.createdBy', 'closingCs', 'createdBy'])->find($iin->id);
    
    // Filter approved
    $creatorRole = strtolower($p->closingCs->role ?? $p->createdBy->role ?? $p->salesPlan->createdBy->role ?? '');
    $needsApproval = in_array($creatorRole, ['reseller', 'chapter', 'agen']);
    echo "Creator Role: $creatorRole\n";
    echo "Needs Approval: " . ($needsApproval ? 'YES' : 'NO') . "\n";
    echo "Approval Status: " . $p->approval_status . "\n";
    if ($needsApproval) {
        $approved = $p->approval_status === 'Approved';
    } else {
        $approved = true;
    }
    echo "Passes approval filter: " . ($approved ? 'YES' : 'NO') . "\n";

    $isManualLunas = ($p->is_lunas == 1);
    $itemLevel = strtolower($p->level ?? $p->salesPlan->level ?? '');
    $levelNominal = str_contains($itemLevel, 'grow') ? 1500000 : 1000000;
    $countPaid = 0;
    for ($m = 1; $m <= 12; $m++) {
        if (($p->{"spp_$m"} ?? 0) >= $levelNominal) {
            $countPaid++;
        }
    }
    echo "Is Manual Lunas: " . ($isManualLunas ? 'YES' : 'NO') . "\n";
    echo "Item Level: $itemLevel\n";
    echo "Level Nominal: $levelNominal\n";
    echo "Count Paid: $countPaid\n";
    $lunas = $isManualLunas || ($countPaid >= 6);
    echo "Is Lunas: " . ($lunas ? 'YES' : 'NO') . "\n";
}
