<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage; // Import Storage

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
        'profile_photo_path',
        'jabatan',       // admin / relawan
        'no_hp',
        'alamat',
        'jenis_kelamin', // laki-laki / perempuan
        'usia_range',
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
            'password' => 'hashed',
        ];
    }

    // --- ACCESSOR BARU UNTUK FOTO ---
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
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
    
    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class, 'admin_id');
    }

    public function isAdmin()
    {
        return $this->jabatan === 'admin';
    }
}