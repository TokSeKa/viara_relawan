<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Password default untuk semua akun testing
        // Hash::make('12345678')
        $password = Hash::make('12345678');

        $users = [
            // 1. Super Admin (Pemilik / Yayasan)
            [
                'name' => 'Yayasan Maitreyawira',
                'email' => 'maitreyawira@gmail.com',
                'password' => $password,
                'jabatan' => 'admin_super',
                'no_hp' => '081100001111',
                'alamat' => 'Jl. Maitreyawira No. 1, Batam',
                'jenis_kelamin' => 'laki-laki',
                'usia_range' => 'dewasa',
            ],

            // 2. Bot Admin (Untuk Testing Fitur Admin Umum)
            [
                'name' => 'Bot Admin',
                'email' => 'admin@test.com',
                'password' => $password,
                'jabatan' => 'admin_super', // Diberi akses super agar leluasa testing
                'no_hp' => '081234567890',
                'alamat' => 'Markas Bot Testing',
                'jenis_kelamin' => 'laki-laki',
                'usia_range' => '20-30',
            ],

            // 3. Bot Relawan (Untuk Testing Fitur Relawan)
            [
                'name' => 'Bot Relawan',
                'email' => 'relawan@test.com',
                'password' => $password,
                'jabatan' => 'relawan',
                'no_hp' => '089876543210',
                'alamat' => 'Jl. Relawan Sejati No. 45',
                'jenis_kelamin' => 'perempuan',
                'usia_range' => '17-25',
            ],

            // 4. Relawan User Biasa
            [
                'name' => 'Dylan Alamsyah', // Contoh nama user
                'email' => 'email_user@gmail.com',
                'password' => $password,
                'jabatan' => 'relawan',
                'no_hp' => '08522223333',
                'alamat' => 'Perumahan Warga Blok A',
                'jenis_kelamin' => 'laki-laki',
                'usia_range' => '20-30',
            ],

            // --- TAMBAHAN: AKUN SPESIFIK JABATAN (Agar bisa tes fitur spesifik) ---

            // 5. Admin Logistik (Barang)
            [
                'name' => 'Petugas Gudang Logistik',
                'email' => 'logistik@viara.com',
                'password' => $password,
                'jabatan' => 'admin_logistik',
                'no_hp' => '081299998888',
                'alamat' => 'Gudang Penyimpanan',
                'jenis_kelamin' => 'laki-laki',
                'usia_range' => 'dewasa',
            ],

            // 6. Admin Acara
            [
                'name' => 'Koordinator Acara',
                'email' => 'acara@viara.com',
                'password' => $password,
                'jabatan' => 'admin_acara',
                'no_hp' => '081277776666',
                'alamat' => 'Kantor Event Organizer',
                'jenis_kelamin' => 'perempuan',
                'usia_range' => '20-30',
            ],
        ];

        // Looping untuk insert data
        foreach ($users as $user) {
            // updateOrCreate: Mencegah error duplicate entry jika seeder dijalankan 2x
            User::updateOrCreate(
                ['email' => $user['email']], // Cek berdasarkan email
                $user // Data yang akan diupdate/create
            );
        }
    }
}
