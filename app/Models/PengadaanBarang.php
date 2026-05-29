<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanBarang extends Model
{
    protected $fillable = [
        'nama_barang',
        'jumlah',
        'progress',
        'budget',
        'acc',
        'bukti_transfer',
    ];
}
