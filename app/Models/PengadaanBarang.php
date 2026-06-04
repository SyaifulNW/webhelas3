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
        'status_beli',
        'tanggal_dibeli',
        'is_inventory_created',
        'bukti_transfer',
        'pengajuan_anggaran_id',
    ];

    protected $casts = [
        'is_inventory_created' => 'boolean',
        'tanggal_dibeli'       => 'date',
    ];

    public function pengajuanAnggaran()
    {
        return $this->belongsTo(PengajuanAnggaran::class);
    }

    public function buktiFotos()
    {
        return $this->hasMany(\App\Models\PengadaanBuktiTransfer::class);
    }

    /**
     * Sinkronisasi ke Inventaris Kantor saat status_beli = "Sudah Dibeli".
     * Dipanggil oleh controller saat operasional mengubah status_beli.
     */
    public function syncToInventaris(): bool
    {
        // Hanya jika sudah dibeli dan belum pernah dibuat
        if ($this->status_beli !== 'Sudah Dibeli' || $this->is_inventory_created) {
            return false;
        }

        InventarisKantor::create([
            'nama_peralatan'    => $this->nama_barang ?: 'Barang Pengadaan',
            'jumlah'            => $this->jumlah ?: 1,
            'lokasi'            => '-',
            'status'            => 'Normal',
            'keterangan'        => 'Dari pengadaan barang. Budget: Rp ' . number_format((float)$this->budget, 0, ',', '.'),
            'tanggal_pembelian' => now()->toDateString(),
        ]);

        $this->update([
            'is_inventory_created' => true,
            'tanggal_dibeli'       => now()->toDateString(),
        ]);

        return true;
    }
}
