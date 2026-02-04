<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    public function edit_tag()
    {
        // Ambil User yang sedang login
        $user = Auth::user();

        // Ambil SEMUA tag yang PUBLIK (is_admin_only = false/0)
        // Tag admin tidak boleh dipilih sendiri oleh user
        $tags = Tag::where('is_admin_only', false)->orderBy('nama_tag', 'asc')->get();

        // Ambil ID tag yang SUDAH dipilih user sebelumnya (untuk auto-check di view)
        // Hasilnya array: [1, 3, 5]
        $userTagIds = $user->tags->pluck('id')->toArray();

        return view('auth.tag-user', compact('tags', 'userTagIds'));
    }

    // 2. Proses Simpan (Sync)
    public function update_tag(Request $request)
    {
        $request->validate([
            'tags' => 'array', // Harus berupa array
            'tags.*' => 'exists:tags,id', // Pastikan ID tag valid
        ]);
        /** @var \App\Models\User $user */ // biar tags() gk show red alert
        $user = Auth::user();

        // FITUR AJAIB: sync()
        // - Kalau ID ada di array -> Disimpan
        // - Kalau ID TIDAK ada di array -> Dihapus dari user
        // - Kalau array kosong -> Semua tag user dihapus
        $user->tags()->sync($request->tags);

        return back()->with('success', 'Minat dan ketertarikan Anda berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function index_admin_user(Request $request)
    {
        // Ambil data user, urutkan dari yang terbaru
        // paginate(10) artinya 10 user per halaman
        $users = User::latest()->paginate(10);

        return view('admin.user.daftar-user', compact('users'));
    }

    // 1. Tampilkan Detail User
    public function show_admin_user($id)
    {
        $user = User::with('tags')->findOrFail($id); // Eager load tags biar muncul di view
        return view('admin.user.detail-user', compact('user'));
    }

    // 3. Tampilkan Halaman Grid Tag (Admin Mode)
    public function edit_user_tags($id)
    {
        $user = User::findOrFail($id);

        // BEDA DENGAN USER BIASA: Admin bisa lihat SEMUA tag (termasuk is_admin_only)
        $tags = Tag::orderBy('is_admin_only', 'desc') // Tag admin ditaruh paling atas
            ->orderBy('nama_tag', 'asc')
            ->get();

        $userTagIds = $user->tags->pluck('id')->toArray();

        return view('admin.user.tag-user', compact('user', 'tags', 'userTagIds'));
    }

    // 4. Proses Simpan Tag User
    public function update_user_tags(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Sync array tag (otomatis tambah/hapus)
        $user->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.users.show_admin_user', $id)->with('success', 'Tag pengguna berhasil diupdate!');
    }
}
