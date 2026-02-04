<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data tag, urutkan terbaru, paginate 10 per halaman
        $tags = Tag::latest()->paginate(10);
        return view('admin.tag.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Arahkan ke folder: resources/views/admin/tag/create.blade.php
        return view('admin.tag.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tag' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Tag::create([
            'nama_tag' => $request->nama_tag,
            'slug' => Str::slug($request->nama_tag),
            'deskripsi' => $request->deskripsi,
            // Checkbox HTML: kalau dicentang kirim "1", kalau tidak tidak terkirim apa2.
            // Gunakan $request->has() atau boolean() agar aman.
            'is_admin_only' => $request->boolean('is_admin_only'),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        //
    }

    // Menampilkan Form Edit
    public function edit($id)
    {
        $tag = Tag::findOrFail($id); // Cari data atau error 404
        return view('admin.tag.edit', compact('tag'));
    }

    // Proses Simpan Perubahan
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tag' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $tag = Tag::findOrFail($id);

        $tag->update([
            'nama_tag' => $request->nama_tag,
            'slug' => Str::slug($request->nama_tag), // Update slug juga biar sinkron
            'deskripsi' => $request->deskripsi,
            'is_admin_only' => $request->boolean('is_admin_only'),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);

        // 1. CEK DULU: Apakah Tag ini dipake di Kegiatan?
        // Asumsi: Kamu punya relasi 'kegiatans' di model Tag
        // Kalau belum punya relasi, bisa skip pengecekan ini atau pakai query manual DB
        if ($tag->kegiatans()->count() > 0) {
            return back()->with('error', 'Gagal hapus! Tag ini sedang digunakan oleh beberapa kegiatan.');
        }

        // 2. Kalau aman (nol penggunaan), baru hapus
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'Tag berhasil dihapus permanen.');
    }
}
