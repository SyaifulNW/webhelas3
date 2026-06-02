<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanBuktiTransfer extends Model
{
    protected $fillable = ['pengadaan_barang_id', 'file_path'];

    public function pengadaanBarang()
    {
        return $this->belongsTo(PengadaanBarang::class);
    }
}
