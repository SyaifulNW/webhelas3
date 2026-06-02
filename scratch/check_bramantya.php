<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PesertaSmi;

$bram = PesertaSmi::where('nama', 'like', '%Bramantya%')->first();
if ($bram) {
    echo "BRAMANTYA:\n";
    echo "ID: " . $bram->id . "\n";
    echo "Status: " . $bram->status . "\n";
    echo "Is Lunas: " . $bram->is_lunas . "\n";
    echo "Level: " . $bram->level . "\n";
    echo "SalesPlan Level: " . ($bram->salesPlan ? $bram->salesPlan->level : 'null') . "\n";
    echo "Tanggal Masuk: " . $bram->tanggal_masuk . "\n";
    echo "SPP Nominal / SPP Payments:\n";
    for ($m = 1; $m <= 12; $m++) {
        echo "spp_$m: " . ($bram->{"spp_$m"} ?? 0) . " | ";
    }
    echo "\n";
    echo "Custom Schedule: " . json_encode($bram->spp_custom_schedule) . "\n";
    if ($bram->salesPlan) {
        echo "SalesPlan Selected Months: " . $bram->salesPlan->selected_months . "\n";
        echo "SalesPlan Tanggal Closing: " . $bram->salesPlan->tanggal_closing . "\n";
    }
} else {
    echo "Bramantya not found!\n";
}
