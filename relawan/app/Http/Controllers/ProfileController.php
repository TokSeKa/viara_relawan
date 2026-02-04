<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('auth.profil', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            // Email harus unique, TAPI abaikan (ignore) user yang sedang login saat ini
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'tanggal_lahir' => 'required|date',
            // Password nullable (boleh kosong), kalau diisi minimal 8 char & confirmed
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update data dasar
        $user->name = $request->name;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;
        $user->alamat = $request->alamat;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->tanggal_lahir = $request->tanggal_lahir;

        // Cek apakah user input password baru?
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        /** @var \App\Models\User $user */
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
