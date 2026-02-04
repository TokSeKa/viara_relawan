<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acara extends Model // (Dan Acara.php isinya mirip)
{
    protected $guarded = ['id'];

    public function acara()
    {
        return $this->morphOne(Acara::class, 'detail');
    }
}
