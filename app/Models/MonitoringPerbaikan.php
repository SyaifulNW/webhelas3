<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPerbaikan extends Model
{
    use HasFactory;

    protected $fillable = [
        'fasilitas',
        'kerusakan',
        'timeline',
        'progress',
        'rencana',
        'budget',
        'realisasi_dana',
        'tanggal_mulai',
        'tanggal_selesai',
        'bukti_transfer',
    ];
}
