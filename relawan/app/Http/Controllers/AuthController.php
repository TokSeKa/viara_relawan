<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan Form Register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses Register
    public function register(Request $request)
    {
        // 1. Validasi (Sama kayak logic kamu sebelumnya)
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'no_hp' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'jabatan' => 'required' // Hidden field tadi
        ]);

        // 2. Simpan ke Database
        // Password wajib di-Hash!
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // 3. Auto Login setelah daftar (Opsional, biar UX enak)
        Auth::login($user);

        // 4. Redirect ke Dashboard
        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
    }

    // Tampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 1. TANGKAP INPUTAN CHECKBOX 'REMEMBER'
        // $request->boolean('remember') otomatis mengembalikan true kalau dicentang, false kalau tidak.
        $remember = $request->boolean('remember');

        // 2. MASUKKAN $remember SEBAGAI PARAMETER KEDUA
        if (Auth::attempt($credentials, $remember)) {

            $request->session()->regenerate();

            // Cek Role buat redirect
            if (Auth::user()->jabatan == 'admin') {
                return redirect()->intended('/admin/dashboard');
            }
            return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
