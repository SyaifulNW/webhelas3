<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'employee_id',
        'employee_name',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'gps_latitude',
        'gps_longitude',
        'radius_status',
        'selfie_masuk',
        'selfie_pulang',
        'status_kehadiran',
        'keterangan',
        'is_late',
        'total_jam_kerja',
    ];
}
