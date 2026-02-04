<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kegiatan extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Biar gak kena error mass assignment

    // 1. CASTING (Penting buat Tanggal)
    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    // 2. RELASI POLYMORPHIC (Konek ke Anak)
    public function detail()
    {
        return $this->morphTo();
    }

    // 3. RELASI LAINNYA
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'kegiatan_tags');
    }

    public function partisipasis()
    {
        return $this->hasMany(Partisipasi::class);
    }
    
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
