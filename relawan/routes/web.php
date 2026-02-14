<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PartisipasiController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MusicController;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->jabatan == 'admin_super') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return view('landing');
});

Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
Route::get('/kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('kegiatan.show');

// --- GROUP GUEST ---
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// --- GROUP AUTH ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- AREA RELAWAN ---
    Route::get('/dashboard', function () {
        return view('relawan.navigasi-kegiatan');
    })->name('dashboard');

    Route::get('/cek-notifikasi', [NotifikasiController::class, 'checkCount'])->name('notifikasi.check');
    Route::post('/join-kegiatan', [PartisipasiController::class, 'store'])->name('partisipasi.join');
    Route::delete('/leave-kegiatan', [PartisipasiController::class, 'destroy'])->name('partisipasi.leave');
    Route::get('/riwayat', [PartisipasiController::class, 'riwayat'])->name('riwayat');

    Route::get('/minat-saya', [UserController::class, 'edit_tag'])->name('user.tags.edit');
    Route::post('/minat-saya', [UserController::class, 'update_tag'])->name('user.tags.update');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('user.profile.update');

    Route::get('/materi', [MateriController::class, 'indexRelawan'])->name('materi.index');
    Route::get('/materi/{id}', [MateriController::class, 'show'])->name('materi.show');

    // --- AREA KHUSUS ADMIN (Semua Role Admin) ---
    Route::middleware(['auth', 'role:admin_super,admin_acara,admin_dana,admin_darah,admin_mobil,admin_logistik'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', function () {
                return view('admin.navigasi-admin');
            })->name('dashboard');

            // 1. MANAJEMEN KEGIATAN
            Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
                Route::get('/', [KegiatanController::class, 'index_admin'])->name('index');
                Route::get('/{id}/peserta', [KegiatanController::class, 'peserta'])->name('peserta');

                // CRUD Spesifik
                Route::get('/pilih-jenis', [KegiatanController::class, 'pilihJenis'])->name('pilih');
                Route::get('/create/{jenis}', [KegiatanController::class, 'create'])->name('create');
                Route::post('/store', [KegiatanController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit');
                Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
            });

            // 2. BROADCAST NOTIFIKASI & MATERI
            Route::resource('notifikasi', NotifikasiController::class);
            Route::resource('materi', MateriController::class);

            // 3. --- LAPORAN (SEKARANG BISA DIAKSES SEMUA ADMIN) ---
            Route::prefix('laporan')->name('laporan.')->group(function () {
                Route::get('/', [LaporanController::class, 'index'])->name('index');
                Route::get('/rekap', [LaporanController::class, 'indexRekap'])->name('rekap');
                Route::get('/cetak-kegiatan', [LaporanController::class, 'cetakKegiatan'])->name('cetak_kegiatan');

                Route::get('/peserta', [LaporanController::class, 'indexPeserta'])->name('index_peserta');
                Route::get('/cetak-peserta', [LaporanController::class, 'cetakPeserta'])->name('cetak_peserta');

                Route::get('/relawan', [LaporanController::class, 'indexRelawan'])->name('index_relawan');
                Route::get('/cetak-relawan', [LaporanController::class, 'cetakRelawan'])->name('cetak_relawan');
                Route::get('/cetak-tag', [LaporanController::class, 'cetakTag'])->name('cetak_tag');
            });

            // 4. KHUSUS ADMIN SUPER (Manajemen User & Sistem)
            Route::middleware('role:admin_super')->group(function () {
                Route::resource('tags', TagController::class);

                Route::prefix('users')->name('users.')->group(function () {
                    Route::get('/', [UserController::class, 'index_admin_user'])->name('index_admin_user');
                    Route::get('/{id}', [UserController::class, 'show_admin_user'])->name('show_admin_user');
                    Route::get('/{id}/tags', [UserController::class, 'edit_user_tags'])->name('tags.edit');
                    Route::put('/{id}/tags', [UserController::class, 'update_user_tags'])->name('tags.update');
                    Route::put('/{id}/jabatan', [UserController::class, 'update_jabatan'])->name('update_jabatan');
                });

                Route::prefix('music')->name('music.')->group(function () {
                    Route::get('/', [MusicController::class, 'index'])->name('index');
                    Route::post('/store', [MusicController::class, 'store'])->name('store');
                    Route::patch('/{id}/activate', [MusicController::class, 'toggleActive'])->name('activate');
                    Route::delete('/{id}', [MusicController::class, 'destroy'])->name('destroy');
                });
            });
        });
});
