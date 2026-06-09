<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoTemplate extends Model
{
    protected $fillable = ['created_by', 'judul', 'deskripsi', 'tipe', 'is_active'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(TodoLog::class, 'template_id');
    }
}
