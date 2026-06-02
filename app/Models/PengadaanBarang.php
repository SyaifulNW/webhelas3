<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanBarang extends Model
{
    protected $fillable = [
        'nama_barang',
        'jumlah',
        'budget',
        'realisasi_dana',
        'acc',
        'is_inventory_created',
        'bukti_transfer',
        'pengajuan_anggaran_id',
    ];

    protected $casts = [
        'is_inventory_created' => 'boolean',
    ];

    /**
     * Relasi ke PengajuanAnggaran yang dibuat otomatis saat pengadaan dibuat.
     */
    public function pengajuanAnggaran()
    {
        return $this->belongsTo(PengajuanAnggaran::class);
    }

    /**
     * Relasi ke multiple bukti transfer.
     */
    public function buktiFotos()
    {
        return $this->hasMany(\App\Models\PengadaanBuktiTransfer::class);
    }

    /**
     * Sinkronisasi ke Inventaris Kantor jika ACC disetujui dan belum pernah dibuat.
     * Dipanggil setelah acc berubah jadi 'Iya'.
     */
    public function syncToInventaris(): bool
    {
        // Hanya jika disetujui dan belum pernah dibuat
        if ($this->acc !== 'Iya' || $this->is_inventory_created) {
            return false;
        }

        InventarisKantor::create([
            'nama_peralatan'   => $this->nama_barang ?: 'Barang Pengadaan',
            'jumlah'           => $this->jumlah ?: 1,
            'lokasi'           => '-',
            'status'           => 'Normal',
            'keterangan'       => 'Dari pengadaan barang.',
            'tanggal_pembelian' => now()->toDateString(),
        ]);

        $this->update(['is_inventory_created' => true]);

        return true;
    }
}
