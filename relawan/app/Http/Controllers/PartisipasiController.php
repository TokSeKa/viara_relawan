<?php

namespace App\Http\Controllers;

use App\Models\Partisipasi;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartisipasiController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Tambah validasi catatan)
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'catatan'     => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $kegiatanId = $request->kegiatan_id;

        // 2. Cek Validasi Logika Bisnis
        $existing = Partisipasi::where('user_id', $user->id)
            ->where('kegiatan_id', $kegiatanId)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah terdaftar di kegiatan ini!');
        }

        $kegiatan = Kegiatan::find($kegiatanId);
        if ($kegiatan->status !== 'buka') {
            return back()->with('error', 'Maaf, pendaftaran kegiatan ini sudah ditutup.');
        }

        // 3. Simpan Data (Join)
        Partisipasi::create([
            'user_id'     => $user->id,
            'kegiatan_id' => $kegiatanId,
            'catatan'     => $request->catatan, // <-- Simpan Catatan Disini
        ]);

        return back()->with('success', 'Berhasil bergabung! Catatan Anda telah disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     * (Untuk Batal Join / Leave)
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
        ]);

        // Hapus partisipasi milik user yg sedang login pada kegiatan tersebut
        Partisipasi::where('user_id', Auth::id())
            ->where('kegiatan_id', $request->kegiatan_id)
            ->delete();

        return back()->with('success', 'Anda telah membatalkan partisipasi.');
    }

    /**
     * Menampilkan daftar kegiatan yang pernah diikuti user login.
     */
    public function riwayat()
    {
        $userId = Auth::id();

        // Ambil data partisipasi milik user ini, beserta data kegiatannya
        $riwayats = Partisipasi::with('kegiatan') // Eager load kegiatan biar hemat query
            ->where('user_id', $userId)
            ->latest() // Urutkan dari yang paling baru diikuti
            ->paginate(10);

        return view('relawan.riwayat-kegiatan', compact('riwayats'));
    }
}
