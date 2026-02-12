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
    // 1. Cek apakah user SUDAH login?
    if (Auth::check()) {
        // Kalau Admin -> lempar ke route bernama 'admin.dashboard'
        if (Auth::user()->jabatan == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Kalau Relawan -> lempar ke route bernama 'dashboard'
        return redirect()->route('dashboard');
    }

    // 2. Kalau BELUM login (Guest) -> Tampilkan kegiatan
    return view('landing');
});

Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
Route::get('/kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('kegiatan.show');

// --- GROUP GUEST (Hanya bisa diakses kalau BELUM login) ---
Route::middleware('guest')->group(function () {
    // 1. Halaman Register (Menampilkan Form)
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

    // 2. Proses Register (Menerima Data Form tadi)
    Route::post('/register', [AuthController::class, 'register']);

    // 3. Halaman Login (Menampilkan Form)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

    // 4. Proses Login (Cek email & password)
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- AREA RELAWAN & UMUM (Bisa diakses Admin & Relawan) ---
    Route::get('/dashboard', function () {
        return view('relawan.navigasi-kegiatan'); // Mengarah ke file view baru di atas
    })->name('dashboard');
    
    // Tambahkan ini untuk polling notifikasi
    Route::get('/cek-notifikasi', [NotifikasiController::class, 'checkCount'])->name('notifikasi.check');

    Route::post('/join-kegiatan', [PartisipasiController::class, 'store'])->name('partisipasi.join');
    Route::delete('/leave-kegiatan', [PartisipasiController::class, 'destroy'])->name('partisipasi.leave');
    Route::get('/riwayat', [PartisipasiController::class, 'riwayat'])->name('riwayat');

    Route::get('/minat-saya', [UserController::class, 'edit_tag'])->name('user.tags.edit');
    Route::post('/minat-saya', [UserController::class, 'update_tag'])->name('user.tags.update');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('user.profile.update');

    // Route Index Materi untuk Relawan
    Route::get('/materi', [MateriController::class, 'indexRelawan'])->name('materi.index');
    // Route Show (Detail) yang sudah kita buat sebelumnya
    Route::get('/materi/{id}', [MateriController::class, 'show'])->name('materi.show');

    // --- AREA KHUSUS ADMIN (DENGAN ROLE SPESIFIK) ---
    Route::middleware(['auth', 'role:admin_super,admin_acara,admin_dana,admin_darah,admin_mobil,admin_logistik'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Dashboard Admin (Semua Admin Bisa Masuk)
            Route::get('/dashboard', function () {
                return view('admin.navigasi-admin');
            })->name('dashboard');

            // 1. MANAJEMEN KEGIATAN (Dibagi per Role)
            Route::prefix('kegiatan')->name('kegiatan.')->group(function () {

                // Akses Umum Admin untuk List & Detail
                Route::get('/', [KegiatanController::class, 'index_admin'])->name('index');
                Route::get('/{id}/peserta', [KegiatanController::class, 'peserta'])->name('peserta');

                // CRUD Spesifik (Dibatasi Middleware)
                Route::middleware('role:admin_super,admin_acara,admin_dana,admin_darah,admin_mobil,admin_logistik')->group(function () {
                    Route::get('/pilih-jenis', [KegiatanController::class, 'pilihJenis'])->name('pilih');
                    Route::get('/create/{jenis}', [KegiatanController::class, 'create'])->name('create');
                    Route::post('/store', [KegiatanController::class, 'store'])->name('store');
                    Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit');
                    Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
                });
            });

            // 2. KHUSUS ADMIN SUPER (Manajemen User & Sistem)
            Route::middleware('role:admin_super')->group(function () {
                Route::resource('tags', TagController::class);

                Route::prefix('users')->name('users.')->group(function () {
                    Route::get('/', [UserController::class, 'index_admin_user'])->name('index_admin_user');
                    Route::get('/{id}', [UserController::class, 'show_admin_user'])->name('show_admin_user');
                    Route::get('/{id}/tags', [UserController::class, 'edit_user_tags'])->name('tags.edit');
                    Route::put('/{id}/tags', [UserController::class, 'update_user_tags'])->name('tags.update');
                    Route::put('/{id}/jabatan', [UserController::class, 'update_jabatan'])->name('update_jabatan');
                });

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

                Route::prefix('music')->name('music.')->group(function () {
                    Route::get('/', [MusicController::class, 'index'])->name('index');
                    Route::post('/store', [MusicController::class, 'store'])->name('store');
                    Route::patch('/{id}/activate', [MusicController::class, 'toggleActive'])->name('activate');
                    Route::delete('/{id}', [MusicController::class, 'destroy'])->name('destroy');
                });
            });

            // 3. BROADCAST NOTIFIKASI & MATERI (Semua Admin)
            Route::resource('notifikasi', NotifikasiController::class);
            Route::resource('materi', MateriController::class);
        });
});
