<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoomSchedule extends Model
{
    protected $table = 'zoom_schedules';

    protected $fillable = [
        'data_id',
        'salesplan_id',
        'cs_id',
        'scheduled_at',
        'zoom_link',
        'status',
        'notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id');
    }

    public function salesPlan()
    {
        return $this->belongsTo(SalesPlan::class, 'salesplan_id');
    }

    public function cs()
    {
        return $this->belongsTo(User::class, 'cs_id');
    }
}
