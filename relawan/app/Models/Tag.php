<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'is_admin_only' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tag_user');
    }

    public function kegiatans()
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_tags');
    }
}