<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partisipasi extends Model
{
    protected $guarded = ['id'];

    // WAJIB: Biar 'data_tambahan' bisa diakses kayak array ($p->data_tambahan['nominal'])
    protected $casts = [
        'data_tambahan' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}