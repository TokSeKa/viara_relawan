<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notifikasi extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Relasi (Opsional, buat info tambahan)
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    // --- FITUR UTAMA: SCOPE UNTUK FILTER USER ---
    // Cara panggil nanti: Notifikasi::forUser(Auth::user())->get();
    public function scopeForUser($query, $user)
    {
        // 1. Ambil ID Tag & Kegiatan User (Array)
        $userTagIds = $user->tags->pluck('id')->toArray(); // Contoh: [1, 3]
        $userKegiatanIds = $user->partisipasis->pluck('kegiatan_id')->toArray(); // Contoh: [5, 9]

        return $query->where(function ($q) use ($userTagIds, $userKegiatanIds) {
            // A. Cek Tanggal Kadaluarsa
            $q->where('expires_at', '>', now())
                ->orWhereNull('expires_at');
        })
            ->where(function ($mainQuery) use ($userTagIds, $userKegiatanIds) {

                // 1. Notif untuk SEMUA
                $mainQuery->where('target_audience', 'all')

                    // 2. Notif untuk TAG (Disini logic loop-nya)
                    ->orWhere(function ($sub) use ($userTagIds) {
                        $sub->where('target_audience', 'tag')
                            ->where(function ($q) use ($userTagIds) {
                                // LOOPING MANUAL: Cek apakah salah satu tag user ada di dalam string "1,2,3" di DB
                                foreach ($userTagIds as $tagId) {
                                    // FIND_IN_SET('1', '1,5,9') -> True
                                    $q->orWhereRaw("FIND_IN_SET(?, tag_id)", [$tagId]);
                                }
                            });
                    })

                    // 3. Notif untuk KEGIATAN (Logic loop sama)
                    ->orWhere(function ($sub) use ($userKegiatanIds) {
                        $sub->where('target_audience', 'kegiatan')
                            ->where(function ($q) use ($userKegiatanIds) {
                                foreach ($userKegiatanIds as $kegId) {
                                    $q->orWhereRaw("FIND_IN_SET(?, kegiatan_id)", [$kegId]);
                                }
                            });
                    });
            })
            ->latest();
    }
}
