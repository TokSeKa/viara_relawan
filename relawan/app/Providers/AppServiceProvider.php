<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // MAPPING NAMA SUPAYA DI DATABASE JADI PENDEK
        Relation::enforceMorphMap([
            'acara'        => 'App\Models\Acara',
            'donasi_dana'  => 'App\Models\DonasiDana',
            'donasi_darah' => 'App\Models\DonasiDarah',
            'mobil'        => 'App\Models\Kendaraan',
            'donasi_barang'     => 'App\Models\DonasiBarang',
            'peminjaman_barang' => 'App\Models\PinjamBarang',
        ]);
        Paginator::useBootstrapFive();

        // View Composer: Setiap kali 'layouts.app' dirender, jalankan fungsi ini
        View::composer('layouts.app', function ($view) {
            $notifikasis = [];
            $unreadCount = 0;

            if (Auth::check()) {
                // Ambil notifikasi pakai Scope yang kita buat kemarin
                $notifikasis = Notifikasi::forUser(Auth::user())->take(10)->get();
                // Kalau mau hitung yg belum dibaca (sementara kita anggap semua unread biar gampang)
                $unreadCount = $notifikasis->count();
            }

            $genesis = User::find(1);

            $webProfile = (object) [
                'name'  => $genesis ? $genesis->name : 'Viara Maitreyawira',
                'email' => $genesis ? $genesis->email : 'info@viara.com',
                'phone' => $genesis ? $genesis->no_hp : '0812-3456-7890',
                'address' => $genesis ? $genesis->alamat : 'Batam, Indonesia',
            ];

            // Buat variabel $webProfile bisa dipakai dimanapun
            $view->with('webProfile', $webProfile);

            // Kirim variabel $globalNotif dan $globalCount ke view
            $view->with('globalNotif', $notifikasis);
            $view->with('globalCount', $unreadCount);
        });
    }
}
