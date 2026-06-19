<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    use HasFactory;

    protected $table = 'daily_tasks';

    protected $fillable = [
        'user_id',
        'divisi',
        'tanggal',
        'judul',
        'deskripsi',
        'deadline',
        'target',
        'realisasi',
        'is_done',
        'done_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_done' => 'boolean',
        'done_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
