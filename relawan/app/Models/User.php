<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // --- SESUAI MIGRASI KAMU ---
        'jabatan',       // admin / relawan
        'no_hp',
        'alamat',
        'jenis_kelamin', // laki-laki / perempuan
        'tanggal_lahir',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Saya hapus 'email_verified_at' karena di migrasimu kolomnya tidak ada
            'password' => 'hashed',
            
            // PENTING: Ubah tanggal lahir jadi object Date (Carbon)
            // Biar bisa dipanggil: $user->tanggal_lahir->format('d F Y')
            'tanggal_lahir' => 'date', 
        ];
    }

    // --- RELATIONS ---

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_user');
    }

    public function partisipasis()
    {
        return $this->hasMany(Partisipasi::class);
    }
    
    // Tambahan: Relasi untuk Admin (Kegiatan yang dibuat oleh admin ini)
    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class, 'admin_id');
    }

    // --- HELPER FUNCTION (Opsional tapi berguna) ---
    
    // Cara pakai: if($user->isAdmin()) { ... }
    public function isAdmin()
    {
        return $this->jabatan === 'admin';
    }
}