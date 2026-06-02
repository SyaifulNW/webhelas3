<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PesertaSmi;

$iin = PesertaSmi::where('nama', 'like', '%Iin%')->first();
if ($iin) {
    echo "IIN DARYATI:\n";
    echo "ID: " . $iin->id . "\n";
    echo "Status: " . $iin->status . "\n";
    echo "Is Lunas: " . $iin->is_lunas . "\n";
    $paid = 0;
    for ($i=1; $i<=12; $i++) {
        echo "spp_$i: " . $iin->{"spp_$i"} . " | ";
        if ($iin->{"spp_$i"} >= 1000000) $paid++;
    }
    echo "\nPaid count: $paid\n\n";
}

$rasidah = PesertaSmi::where('nama', 'like', '%Rasidah%')->first();
if ($rasidah) {
    echo "RASIDAH:\n";
    echo "ID: " . $rasidah->id . "\n";
    echo "Status: " . $rasidah->status . "\n";
    echo "Is Lunas: " . $rasidah->is_lunas . "\n";
    $paid = 0;
    for ($i=1; $i<=12; $i++) {
        echo "spp_$i: " . $rasidah->{"spp_$i"} . " | ";
        if ($rasidah->{"spp_$i"} >= 1000000) $paid++;
    }
    echo "\nPaid count: $paid\n\n";
}
