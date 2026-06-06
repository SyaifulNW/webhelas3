<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPerbaikan extends Model
{
    use HasFactory;

    protected $fillable = [
        'fasilitas',
        'inventaris_id',
        'fasilitas_manual',
        'kerusakan',
        'timeline',
        'progress',
        'budget',
        'realisasi_dana',
        'tanggal_selesai',
        'bukti_transfer',
    ];

    public function inventaris()
    {
        return $this->belongsTo(InventarisKantor::class, 'inventaris_id');
    }
}
