<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model // (Dan Acara.php isinya mirip)
{
    protected $guarded = ['id'];
    
    // Khusus Kendaraan, 'butuh_supir' itu boolean
    protected $casts = [
        'butuh_supir' => 'boolean',
    ];

    public function kegiatan()
    {
        return $this->morphOne(Kegiatan::class, 'detail');
    }
}
