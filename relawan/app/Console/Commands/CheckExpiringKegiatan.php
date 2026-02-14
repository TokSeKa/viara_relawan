<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kegiatan;
use App\Models\Notifikasi;
use Carbon\Carbon;

class CheckExpiringKegiatan extends Command
{
    // Nama perintah yang nanti dipanggil scheduler
    protected $signature = 'kegiatan:check-expiring';

    // Deskripsi perintah
    protected $description = 'Cek kegiatan yang akan tutup pendaftarannya dalam 24 jam dan buat notifikasi otomatis';

    public function handle()
    {
        $this->info('Memulai pengecekan kegiatan...');

        // 1. Tentukan rentang waktu (24 jam dari sekarang)
        $now = Carbon::now();
        $tomorrow = Carbon::now()->addHours(24);

        // 2. Cari kegiatan yang:
        // - Status masih 'buka'
        // - Tanggal selesai pendaftarannya antara sekarang s/d 24 jam ke depan
        // - Belum pernah dinotifikasi (flag false)
        $expiringKegiatans = Kegiatan::where('status', 'buka')
            ->whereBetween('tanggal_selesai', [$now, $tomorrow])
            ->where('notified_h_minus_1', false)
            ->get();

        if ($expiringKegiatans->isEmpty()) {
            $this->info('Tidak ada kegiatan yang mendesak.');
            return;
        }

        foreach ($expiringKegiatans as $kegiatan) {
            $this->info("Memproses kegiatan: {$kegiatan->judul}");

            // 3. Buat Notifikasi Otomatis
            // Kita set target_audience 'all' atau 'kegiatan' (peserta yg sudah join saja)
            // Di sini saya contohkan 'all' agar menarik minat relawan lain yg belum daftar
            Notifikasi::create([
                'judul'           => "⏳ H-1 Penutupan: {$kegiatan->judul}",
                'pesan'           => "Pendaftaran kegiatan '{$kegiatan->judul}' akan ditutup dalam kurang dari 24 jam! Segera daftar sebelum terlambat.",
                'type'            => 'warning', // Warna kuning/oranye
                'target_audience' => 'all', // Semua user dapat info ini
                'expires_at'      => $kegiatan->tanggal_selesai, // Notif hilang saat pendaftaran tutup
                'created_by'      => $kegiatan->admin_id, // Atas nama admin pembuat
                'kegiatan_id'     => $kegiatan->id, // Link ke kegiatan (opsional, sesuaikan struktur DB mu)
            ]);

            // 4. Update Flag agar tidak diproses lagi menit berikutnya
            $kegiatan->update([
                'notified_h_minus_1' => true
            ]);
        }

        $this->info('Selesai. ' . $expiringKegiatans->count() . ' notifikasi dibuat.');
    }
}