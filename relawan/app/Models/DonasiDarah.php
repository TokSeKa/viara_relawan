<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonasiDarah extends Model
{
    protected $guarded = ['id'];

    // WAJIB: JSON Array Golongan Darah
    protected $casts = [
        'golongan_darah_needed' => 'array',
    ];

    public function kegiatan()
    {
        return $this->morphOne(Kegiatan::class, 'detail');
    }
}
