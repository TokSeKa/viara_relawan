<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    // Jika nama tabel di migrasimu adalah 'musics', baris ini opsional
    protected $table = 'musics';

    protected $fillable = ['judul', 'deskripsi', 'file_path', 'is_active'];

    // Menambah casting agar 'is_active' otomatis terbaca sebagai true/false (boolean)
    // bukan angka 1 atau 0 saat diakses di Blade atau Controller
    protected $casts = [
        'is_active' => 'boolean',
    ];
}