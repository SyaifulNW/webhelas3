<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarisKantor extends Model
{
    use HasFactory;

    protected $fillable = [
        'lokasi', 
        'nama_peralatan', 
        'status', 
        'keterangan', 
        'jumlah', 
        'tanggal_pembelian'
    ];
}
