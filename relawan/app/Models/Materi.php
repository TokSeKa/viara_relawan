<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materis';

    protected $fillable = [
        'judul',
        'deskripsi',
        'file_path',
    ];

    // RELASI KE TAGS (Many to Many)
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'materi_tags', 'materi_id', 'tag_id');
    }
}