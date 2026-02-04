<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

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

            // Kirim variabel $globalNotif dan $globalCount ke view
            $view->with('globalNotif', $notifikasis);
            $view->with('globalCount', $unreadCount);
        });
    }
}
