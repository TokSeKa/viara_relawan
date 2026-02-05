<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    // 1. TAMPILKAN SEMUA (untuk admin)
    public function index()
    {
        // Saya tambah with('tags') biar query lebih efisien (Eager Loading)
        $materis = Materi::with('tags')->latest()->get();
        return view('admin.materi.index', compact('materis'));
    }

    // 2. HALAMAN UPLOAD (Hanya Admin)
    public function create()
    {
        $tags = Tag::orderBy('nama_tag', 'asc')->get(); // Ambil semua tag
        return view('admin.materi.create', compact('tags'));
    }

    // 3. PROSES SIMPAN MATERI & TAGS (Hanya Admin)
    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'file_materi' => 'required|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,webp,svg,mp3,wav,ogg,mp4,webm|max:20480',

            // VALIDASI TAGS (BARU)
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ]);

        // Upload File
        $path = $request->file('file_materi')->store('materis', 'public');

        $materi = Materi::create([
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path,
        ]);

        // SIMPAN HUBUNGAN TAGS (BARU)
        if ($request->has('tags')) {
            $materi->tags()->attach($request->tags);
        }

        return redirect()->route('admin.materi.index')->with('success', 'Materi berhasil diupload!');
    }

    // 4. HALAMAN EDIT (Hanya Admin)
    public function edit($id)
    {
        $materi = Materi::with('tags')->findOrFail($id);
        $tags   = Tag::orderBy('nama_tag', 'asc')->get(); // Daftar semua tag

        // Ambil ID tag yang sudah dipilih materi ini (untuk auto-checklist di view)
        $connectedTagIds = $materi->tags->pluck('id')->toArray();

        return view('admin.materi.edit', compact('materi', 'tags', 'connectedTagIds'));
    }

    // 5. PROSES UPDATE (Hanya Admin)
    public function update(Request $request, $id)
    {
        $materi = Materi::findOrFail($id);

        $request->validate([
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'file_materi' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,webp,svg,mp3,wav,ogg,mp4,webm|max:20480',

            // VALIDASI TAGS (BARU)
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ]);

        // Data text update
        $materi->judul = $request->judul;
        $materi->deskripsi = $request->deskripsi;

        // Logic Update File
        if ($request->hasFile('file_materi')) {
            // Hapus file lama
            if ($materi->file_path && Storage::disk('public')->exists($materi->file_path)) {
                Storage::disk('public')->delete($materi->file_path);
            }

            // Upload baru
            $path = $request->file('file_materi')->store('materis', 'public');
            $materi->file_path = $path;
        }

        $materi->save();

        // UPDATE HUBUNGAN TAGS (BARU - Pakai sync agar otomatis tambah/hapus)
        $materi->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.materi.index')->with('success', 'Materi berhasil diperbarui!');
    }

    // 6. HAPUS DATA & FILE (Hanya Admin)
    public function destroy($id)
    {
        $materi = Materi::findOrFail($id);

        // Hapus file fisiknya (Gunakan disk public)
        if ($materi->file_path && Storage::disk('public')->exists($materi->file_path)) {
            Storage::disk('public')->delete($materi->file_path);
        }

        // Hapus data di database (Tags otomatis terhapus karena cascadeOnDelete di migration)
        $materi->delete();

        return redirect()->back()->with('success', 'Materi dihapus.');
    }

    // 7. HALAMAN DETAIL / PREVIEW (Bisa Diakses Admin & Relawan)
    public function show($id)
    {
        $materi = Materi::with('tags')->findOrFail($id);
        return view('admin.materi.show', compact('materi'));
    }

    // 8. INDEX KHUSUS RELAWAN (Filter Berdasarkan Tag User)
    public function indexRelawan(Request $request)
    {
        // FITUR AJAIB: sync()
        // - Kalau ID ada di array -> Disimpan
        // - Kalau ID TIDAK ada di array -> Dihapus dari user
        // - Kalau array kosong -> Semua tag user dihapus
        // 1. Ambil ID Tag milik User yang sedang login
        // Hasilnya array, misal: [1, 3, 5] (Coding, Desain, Kemanusiaan)
        /** @var \App\Models\User $user */ // biar tags() gk show red alert
        $user = Auth::user();
        $userTagIds = $user->tags()->pluck('id')->toArray();

        // 2. Mulai Query
        $query = Materi::with('tags')->latest();

        // 3. TERAPKAN FILTER LOGIC (Grouping Query)
        // Kita pakai grouping (function($q)) supaya tidak bentrok dengan fitur Search
        $query->where(function ($q) use ($userTagIds) {

            // KONDISI A: Materi Punya Tag & Cocok dengan User
            // "Carikan materi yang punya relasi 'tags', dimana id tag-nya ada di dalam list tag user"
            $q->whereHas('tags', function ($subQuery) use ($userTagIds) {
                $subQuery->whereIn('tags.id', $userTagIds);
            })

                // KONDISI B: Materi Umum (Tidak Punya Tag Sama Sekali)
                // Biasanya materi umum (seperti Panduan Dasar / Tata Tertib) tidak dikasih tag
                // Jadi materi tanpa tag tetap harus muncul ke semua orang.
                ->orWhereDoesntHave('tags');
        });

        // 4. Fitur Pencarian (Tetap Jalan)
        if ($request->has('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $materis = $query->paginate(9);
        return view('relawan.materi.index', compact('materis'));
    }
}
