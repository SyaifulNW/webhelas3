<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mom extends Model
{
    use HasFactory;

    protected $table = 'moms';

    protected $fillable = [
        'tanggal',
        'keterangan',
        'deadline',
        'pic',
        'target',
        'hasil',
        'status',
        'unit',
        'created_by',
    ];

    /**
     * Relationship to the user who created this MoM record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
