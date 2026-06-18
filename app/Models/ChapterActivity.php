<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'chapter_id',
        'tanggal',
        'target',
        'realisasi',
        'evaluasi',
        'periode',
    ];

    public function chapter()
    {
        return $this->belongsTo(User::class, 'chapter_id');
    }
}
