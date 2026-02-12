<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('auth.profil', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'usia_range' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
            // 'photo' tetap divalidasi jika user tidak pakai cropper
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Tambahan validasi untuk string base64 hasil crop
            'cropped_image' => 'nullable|string',
        ]);

        // 2. Update Data Text
        $user->name = $request->name;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;
        $user->alamat = $request->alamat;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->usia_range = $request->usia_range;

        // 3. Update Password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 4. Logika Penyimpanan Foto
        // PRIORITAS A: Cek apakah ada kiriman foto hasil Crop (Base64)
        if ($request->filled('cropped_image')) {
            $base64Image = $request->cropped_image;

            // Memisahkan header base64 (data:image/jpeg;base64,...) dari datanya
            // Contoh format: data:image/jpeg;base64,/9j/4AAQSkZJRg...
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $image = substr($base64Image, strpos($base64Image, ',') + 1);
                $extension = strtolower($type[1]); // jpeg, png, dll

                if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    return back()->withErrors(['photo' => 'Format gambar tidak valid.']);
                }

                $image = base64_decode($image);

                if ($image === false) {
                    return back()->withErrors(['photo' => 'Gagal memproses data gambar.']);
                }

                // Hapus foto lama jika ada
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // Buat nama file unik
                $fileName = 'profile-photos/' . Str::random(40) . '.' . $extension;

                // Simpan ke storage public
                Storage::disk('public')->put($fileName, $image);
                $user->profile_photo_path = $fileName;
            }
        }
        // PRIORITAS B: Skenario Fallback (Jika user upload biasa tanpa lewat modal crop)
        elseif ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        // 5. Simpan Perubahan ke Database
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
