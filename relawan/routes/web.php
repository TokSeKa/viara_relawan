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

    // 2. Kalau BELUM login (Guest) -> Tampilkan Login
    return view('auth.login');
});


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
    Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::get('/kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('kegiatan.show');

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

    // --- AREA KHUSUS ADMIN (DIPAGARI MIDDLEWARE 'admin') ---
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', function () {
            return view('admin.navigasi-admin');
        })->name('dashboard');

        // Manajemen Kegiatan
        Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
            Route::get('/', [KegiatanController::class, 'index_admin'])->name('index');
            Route::get('/pilih-jenis', [KegiatanController::class, 'pilihJenis'])->name('pilih');
            Route::get('/create/{jenis}', [KegiatanController::class, 'create'])->name('create');
            Route::post('/store', [KegiatanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
            Route::get('/{id}/peserta', [KegiatanController::class, 'peserta'])->name('peserta');
        });

        // Manajemen Tags
        Route::resource('tags', TagController::class); // name otomatis admin.tags.index, dll karena prefix

        // Manajemen Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index_admin_user'])->name('index_admin_user');
            Route::get('/{id}', [UserController::class, 'show_admin_user'])->name('show_admin_user');
            Route::get('/{id}/tags', [UserController::class, 'edit_user_tags'])->name('tags.edit');
            Route::put('/{id}/tags', [UserController::class, 'update_user_tags'])->name('tags.update');
            Route::put('/{id}/jabatan', [UserController::class, 'update_jabatan'])->name('update_jabatan');
        });

        // Manajemen Notifikasi
        Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
            Route::get('/', [NotifikasiController::class, 'index'])->name('index');
            Route::get('/buat', [NotifikasiController::class, 'create'])->name('create');
            Route::post('/', [NotifikasiController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [NotifikasiController::class, 'edit'])->name('edit');
            Route::put('/{id}', [NotifikasiController::class, 'update'])->name('update');
            Route::delete('/{id}', [NotifikasiController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Materi
        Route::resource('materi', MateriController::class);

        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/cetak-kegiatan', [LaporanController::class, 'cetakKegiatan'])->name('cetak_kegiatan');
        });
    });
});
