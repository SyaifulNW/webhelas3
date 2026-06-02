<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PesertaSmi;

$iin = PesertaSmi::where('nama', 'like', '%Iin%')->first();
if ($iin) {
    $creatorRole = strtolower($iin->closingCs->role ?? $iin->createdBy->role ?? $iin->salesPlan->createdBy->role ?? '');
    echo "IIN DARYATI:\n";
    echo "Approval Status: " . $iin->approval_status . "\n";
    echo "Creator Role: " . $creatorRole . "\n";
}
