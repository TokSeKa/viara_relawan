<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Partisipasi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // 1. DASHBOARD LAPORAN (Halaman Pilih Periode/Jenis Laporan)
    public function index()
    {
        return view('admin.laporan.index');
    }

    // 2. PROSES CETAK / TAMPILKAN HASIL
    public function cetakKegiatan(Request $request)
    {
        // Validasi Input
        $request->validate([
            'tgl_awal'  => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
            'status'    => 'nullable|string', // Buka, Tutup, Selesai, Semua
            'jenis'     => 'nullable|string', // donasi_dana, acara, dll
        ]);

        // Mulai Query
        // with('detail') penting agar data polimorfik (anaknya) terambil
        $query = Kegiatan::with(['admin', 'detail'])
            ->whereBetween('tanggal_mulai', [$request->tgl_awal . ' 00:00:00', $request->tgl_akhir . ' 23:59:59']);

        // Filter Status (Jika user tidak pilih 'semua')
        if ($request->has('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        // Filter Jenis Detail (Jika user tidak pilih 'semua')
        if ($request->has('jenis') && $request->jenis != 'semua') {
            $query->where('detail_type', $request->jenis);
        }

        // Eksekusi Query
        $laporan = $query->orderBy('tanggal_mulai', 'asc')->get();

        // Kirim data laporan & data request (untuk judul laporan) ke view
        return view('admin.laporan.cetak-kegiatan', compact('laporan', 'request'));
    }
    // 2. CETAK LAPORAN KEGIATAN (Filter Tanggal)
    public function cetakKegiatan1(Request $request)
    {
        $request->validate([
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
        ]);

        $laporan = Kegiatan::with('admin')
            ->whereBetween('tanggal_mulai', [$request->tgl_awal, $request->tgl_akhir])
            ->get();

        // return view('admin.laporan.cetak-kegiatan', compact('laporan'));
        // Atau download PDF nanti
    }

    // 3. CETAK LAPORAN RELAWAN AKTIF
    public function cetakRelawan1()
    {
        // Contoh logika: Relawan dengan poin partisipasi tertinggi
        $relawans = User::where('jabatan', 'relawan')
            ->withCount('partisipasi') // Hitung berapa kali ikut kegiatan
            ->orderBy('partisipasi_count', 'desc')
            ->limit(50)
            ->get();

        // return view('admin.laporan.cetak-relawan', compact('relawans'));
    }

    // jenis laporan yg mau kubuat:
    // 1. laporan kegiatan (berdasarkan periode) || filter berdasarkan status || bisa cetak pdf
    // 2. laporan relawan (berdasarkan periode) || bisa cetak pdf || relawan yg paling banyak parsipasi? tag yg paling banyak dipakai?
    // 3. laporan tag? tag yg paling banyak dipakai? tag yg paling banyak ikut kegiatan? tag yg paling banyak ikut kegiatan relawan tertentu? tag yg paling banyak ikut kegiatan kategori tertentu? 
}
