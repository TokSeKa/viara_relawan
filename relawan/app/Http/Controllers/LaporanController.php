<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Tag;
use App\Models\Partisipasi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    // 1. HALAMAN HUB / MENU UTAMA LAPORAN
    public function index()
    {
        return view('admin.laporan.index'); // Ini sekarang jadi menu navigasi
    }

    // 2. HALAMAN FILTER REKAPITULASI
    public function indexRekap()
    {
        return view('admin.laporan.filter-rekap');
    }

    public function cetakKegiatan(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi Input
        $request->validate([
            'tgl_awal'  => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
            'status'    => 'nullable|string',
            'jenis'     => 'nullable|string',
        ]);

        // 2. Mulai Query Dasar
        $query = Kegiatan::with(['admin', 'detail'])
            ->whereBetween('tanggal_mulai', [$request->tgl_awal . ' 00:00:00', $request->tgl_akhir . ' 23:59:59']);

        // -----------------------------------------------------------
        // 3. FILTER HAK AKSES JABATAN (SECURITY LAYER)
        // -----------------------------------------------------------

        // Peta Akses
        $accessMap = [
            'admin_dana'     => ['donasi_dana'],
            'admin_darah'    => ['donasi_darah'],
            'admin_mobil'    => ['mobil'],
            'admin_acara'    => ['acara'],
            'admin_logistik' => ['donasi_barang', 'peminjaman_barang'],
        ];

        // Jika BUKAN Super Admin, terapkan filter wajib
        if ($user->jabatan !== 'admin_super') {
            if (isset($accessMap[$user->jabatan])) {
                // Paksa query hanya mencari jenis kegiatan milik dia
                $query->whereIn('detail_type', $accessMap[$user->jabatan]);
            } else {
                // Jika jabatan tidak dikenali, jangan tampilkan apa-apa
                $query->where('id', 0);
            }
        }
        // 4. Filter Tambahan dari Input User (Form)

        // Filter Status
        if ($request->has('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        // Filter Jenis Detail
        // Catatan: Jika Admin Dana memilih 'acara', query akan menghasilkan kosong 
        // karena bertabrakan dengan filter hak akses di atas (AND logic).
        if ($request->has('jenis') && $request->jenis != 'semua') {
            $query->where('detail_type', $request->jenis);
        }

        // 5. Eksekusi Query
        $laporan = $query->orderBy('tanggal_mulai', 'asc')->get();

        return view('admin.laporan.cetak-kegiatan', compact('laporan', 'request'));
    }

    // 4. PROSES CETAK / TAMPILKAN HASIL
    public function cetakPesertaKegiatan(Request $request)
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

    // 5. HALAMAN PILIH KEGIATAN (Untuk Laporan Peserta)
    public function indexPeserta()
    {
        $user = Auth::user();

        // 1. Mulai Query
        $query = Kegiatan::orderBy('tanggal_mulai', 'desc');

        // 2. Definisi Hak Akses (Mapping Jabatan -> detail_type)
        // Sesuaikan string 'donasi_dana', 'acara', dll dengan isi kolom 'detail_type' di databasemu
        $accessMap = [
            'admin_dana'     => ['donasi_dana'],
            'admin_darah'    => ['donasi_darah'],
            'admin_mobil'    => ['mobil'],
            'admin_acara'    => ['acara'],
            'admin_logistik' => ['donasi_barang', 'peminjaman_barang'], // Admin logistik pegang 2 tipe
        ];

        // 3. Terapkan Filter
        // Jika BUKAN 'admin_super', kita filter query-nya
        if ($user->jabatan !== 'admin_super') {
            if (isset($accessMap[$user->jabatan])) {
                // Ambil kegiatan yang jenisnya sesuai hak akses jabatan
                $query->whereIn('detail_type', $accessMap[$user->jabatan]);
            } else {
                // Jika jabatan tidak ada di map (misal relawan biasa nyasar ke sini),
                // kosongkan hasil biar aman
                $query->where('id', 0);
            }
        }

        // 4. Eksekusi Query
        $kegiatans = $query->get();

        return view('admin.laporan.index-peserta', compact('kegiatans'));
    }

    // 6. PROSES CETAK DAFTAR PESERTA
    public function cetakPeserta(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
        ]);

        // Ambil 1 Kegiatan Full dengan:
        // - Detail (polimorfik)
        // - Partisipasi -> User (si relawannya)
        $kegiatan = Kegiatan::with(['detail', 'partisipasis.user'])
            ->findOrFail($request->kegiatan_id);

        return view('admin.laporan.cetak-peserta', compact('kegiatan'));
    }

    // 7. HALAMAN FILTER RELAWAN (Berdasarkan Tag)
    public function indexRelawan()
    {
        // Ambil semua tag untuk dijadikan pilihan filter
        $tags = Tag::orderBy('nama_tag', 'asc')->get();
        return view('admin.laporan.index-relawan', compact('tags'));
    }

    // 8. PROSES CETAK RELAWAN POTENSIAL
    public function cetakRelawan(Request $request)
    {
        $request->validate([
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $query = User::where('jabatan', 'relawan')
            ->with('tags')
            ->withCount('partisipasis as partisipasi_count');

        // Filter Tag
        if ($request->has('tags') && count($request->tags) > 0) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->whereIn('tags.id', $request->tags);
            });
        }

        // SORTING: Sesuaikan nama dengan alias di atas (partisipasi_count)
        $relawans = $query->orderBy('partisipasi_count', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        $selectedTags = $request->has('tags')
            ? Tag::whereIn('id', $request->tags)->pluck('nama_tag')->toArray()
            : [];

        return view('admin.laporan.cetak-relawan', compact('relawans', 'selectedTags'));
    }

    // 9. CETAK LAPORAN STATISTIK TAG (MINAT)
    public function cetakTag()
    {
        // Ambil Tag + Hitung Usernya
        $tags = Tag::withCount('users') // Menghasilkan kolom 'users_count'
            ->orderBy('users_count', 'desc') // Urutkan dari yang paling banyak
            ->get();

        // Menghitung Total User (untuk persentase bar chart sederhana)
        $totalRelawan = User::where('jabatan', 'relawan')->count();

        return view('admin.laporan.cetak-tag', compact('tags', 'totalRelawan'));
    }
}
