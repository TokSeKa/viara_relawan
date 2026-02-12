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
        // 1. Cek User Pertama: Jika database kosong, user pertama otomatis jadi 'admin_super'
        $role = User::count() === 0 ? 'admin_super' : 'relawan';

        // 2. Validasi Input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'no_hp' => 'required|string',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'usia_range' => 'required|string',
            'alamat' => 'required|string',
        ]);

        // 3. Modifikasi Data & Hash Password
        $validated['password'] = Hash::make($validated['password']);
        $validated['jabatan'] = $role;

        // 4. Simpan ke Database
        $user = User::create($validated);

        // 5. Auto Login
        Auth::login($user);

        // 6. Redirect Berdasarkan Role
        // Kita gunakan str_contains agar semua jenis admin (super, dana, mobil, dll) 
        // diarahkan ke dashboard admin.
        if (str_contains($user->jabatan, 'admin')) {
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang! Anda terdaftar sebagai Super Admin.');
        }

        // Jika Relawan biasa
        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat bergabung.');
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

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {

            $request->session()->regenerate();

            if (Auth::user()->jabatan == 'blokir') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda diblokir. Hubungi admin.',
                ]);
            }

            if (str_contains(Auth::user()->jabatan, 'admin')) {
                return redirect()->intended('/admin/dashboard');
            }
            return redirect()->route('dashboard')->with('success', 'Login Berhasil!');
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
