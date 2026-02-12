<?php

namespace App\Http\Controllers;

use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    // 1. Tampilkan Daftar Musik ke Admin
    public function index()
    {
        $musics = Music::latest()->get();
        return view('admin.music.index', compact('musics'));
    }

    // 2. Simpan Musik Baru & Otomatis Aktifkan
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'file_musik' => 'required|mimes:mp3,wav,ogg|max:10240', // Max 10MB
            'deskripsi' => 'nullable|string',
        ]);

        // Simpan file ke storage/app/public/musics
        $path = $request->file('file_musik')->store('musics', 'public');

        // Matikan semua musik yang sedang aktif
        Music::query()->update(['is_active' => false]);

        // Buat data baru dan set jadi aktif
        Music::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Musik baru berhasil diupload dan diaktifkan!');
    }

    // 3. Ganti Musik yang Aktif (Toggle)
    public function toggleActive($id)
    {
        // Matikan semua
        Music::query()->update(['is_active' => false]);

        // Aktifkan yang dipilih
        $music = Music::findOrFail($id);
        $music->update(['is_active' => true]);

        return redirect()->back()->with('success', "Sekarang memutar: {$music->judul}");
    }

    // 4. Hapus Musik & File Fisiknya
    public function destroy($id)
    {
        $music = Music::findOrFail($id);

        // Jangan hapus jika sedang aktif (opsional, untuk keamanan)
        if ($music->is_active) {
            return redirect()->back()->with('error', 'Matikan musik dulu sebelum dihapus.');
        }

        // Hapus file dari folder storage
        if (Storage::disk('public')->exists($music->file_path)) {
            Storage::disk('public')->delete($music->file_path);
        }

        $music->delete();

        return redirect()->back()->with('success', 'Musik berhasil dihapus dari koleksi.');
    }
}