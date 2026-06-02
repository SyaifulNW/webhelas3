<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PesertaSmi;

$luqman = PesertaSmi::where('nama', 'like', '%Luqman%')->first();
if ($luqman) {
    echo "LUQMAN:\n";
    echo "ID: " . $luqman->id . "\n";
    echo "Status: " . $luqman->status . "\n";
    echo "Approval Status: " . $luqman->approval_status . "\n";
    echo "Class Name: " . ($luqman->salesPlan && $luqman->salesPlan->kelas ? $luqman->salesPlan->kelas->nama_kelas : 'null') . "\n";
}
