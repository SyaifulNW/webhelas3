<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$allPending = \App\Models\PesertaSmi::where(function($q) {
    $q->where('approval_status', 'Pending')
      ->orWhereNull('approval_status');
})->count();

$roles = ['reseller', 'chapter', 'agen', 'chapter '];
$restrictedPending = \App\Models\PesertaSmi::where(function($q) {
    $q->where('approval_status', 'Pending')
      ->orWhereNull('approval_status');
})
->where(function ($q) use ($roles) {
    $q->whereHas('closingCs', function ($sq) use ($roles) {
        $sq->whereIn('role', $roles);
    })->orWhereHas('createdBy', function ($sq) use ($roles) {
        $sq->whereIn('role', $roles);
    })->orWhereHas('salesPlan.createdBy', function ($sq) use ($roles) {
        $sq->whereIn('role', $roles);
    });
})->count();

echo "All Pending: $allPending\n";
echo "Restricted Pending: $restrictedPending\n";
