<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportInventaris extends Model
{
    use HasFactory;

    protected $table = 'report_inventaris';

    protected $fillable = [
        'bulan',
        'tahun',
        'tanggal_pemeriksaan',
        'nama_file',
        'file_pdf',
        'catatan',
        'uploaded_by',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'bulan'               => 'integer',
        'tahun'               => 'integer',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Human-readable period label, e.g. "Juni 2026"
     */
    public function getPeriodeAttribute(): string
    {
        return \Carbon\Carbon::create($this->tahun, $this->bulan, 1)
            ->translatedFormat('F Y');
    }
}
