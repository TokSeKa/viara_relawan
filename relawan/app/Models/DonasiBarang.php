<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonasiBarang extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi balik ke Kegiatan (Polymorphic)
     */
    public function kegiatan()
    {
        return $this->morphOne(Kegiatan::class, 'detail');
    }
}