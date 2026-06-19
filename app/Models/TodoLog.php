<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoLog extends Model
{
    protected $fillable = ['template_id', 'user_id', 'periode', 'is_done', 'done_at', 'realisasi'];

    public function template()
    {
        return $this->belongsTo(TodoTemplate::class, 'template_id');
    }
}
