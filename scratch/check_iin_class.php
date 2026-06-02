<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PesertaSmi;

$iin = PesertaSmi::where('nama', 'like', '%Iin%')->first();
if ($iin) {
    echo "IIN DARYATI:\n";
    echo "Class ID: " . ($iin->salesPlan ? $iin->salesPlan->kelas_id : 'null') . "\n";
    echo "Class Name: " . ($iin->salesPlan && $iin->salesPlan->kelas ? $iin->salesPlan->kelas->nama_kelas : 'null') . "\n";
}
