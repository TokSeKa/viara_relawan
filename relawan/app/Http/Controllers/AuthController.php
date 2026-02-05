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
        // 1. Cek apakah ini User Pertama? (Genesis Logic)
        // Jika 0 user, jadi 'admin'. Jika sudah ada user, jadi 'relawan'.
        $role = User::count() === 0 ? 'admin' : 'relawan';

        // 2. Validasi Input
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'no_hp' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
        ]);

        // 3. Modifikasi Data sebelum Simpan
        // Hash Password
        $validated['password'] = Hash::make($validated['password']);

        // MASUKKAN ROLE KE ARRAY DATA
        // Pastikan nama kolom di database kamu 'jabatan'
        $validated['jabatan'] = $role;

        // 4. Simpan ke Database
        $user = User::create($validated);

        // 5. Auto Login
        Auth::login($user);

        // 6. Redirect Cerdas
        // Kalau dia Admin (User 1), lempar ke Admin Dashboard
        if ($user->jabatan == 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang! Anda terdaftar sebagai Super Admin.');
        }

        // Kalau Relawan, lempar ke Dashboard Relawan
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

        // 1. TANGKAP INPUTAN CHECKBOX 'REMEMBER'
        // $request->boolean('remember') otomatis mengembalikan true kalau dicentang, false kalau tidak.
        $remember = $request->boolean('remember');

        // 2. MASUKKAN $remember SEBAGAI PARAMETER KEDUA
        if (Auth::attempt($credentials, $remember)) {

            $request->session()->regenerate();

            // --- CEK BLOKIR SETELAH BERHASIL LOGIN ---
            if (Auth::user()->jabatan == 'blokir') {
                Auth::logout(); // Langsung logout lagi
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Email atau password salah.',
                ]);
            }

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
