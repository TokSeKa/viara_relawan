@extends('layouts.app')

@section('title', 'Masuk ke Portal Relawan')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">

                {{-- Header Card --}}
                <div class="card-header bg-dark text-white text-center py-4">
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-sign-in-alt me-2 text-warning"></i> Login Relawan
                    </h4>
                    <small class="text-white-50">Selamat datang kembali!</small>
                </div>

                <div class="card-body p-4 bg-white">
                    {{-- Alert jika ada error login (email/password salah) --}}
                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> Email atau password salah.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- 1. Input Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-secondary"></i></span>
                                <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan email kamu">
                            </div>
                            {{-- Error validation per field --}}
                            @error('email')
                            <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- 2. Input Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-secondary"></i></span>
                                <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror"
                                    id="password" name="password" required placeholder="Masukkan password">
                            </div>
                            @error('password')
                            <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- 3. Checkbox Remember Me --}}
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label text-secondary small" for="remember">Ingat Saya di perangkat ini</label>
                        </div>

                        {{-- Tombol Login --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark fw-bold py-2 shadow-sm">
                                MASUK SEKARANG <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>

                    </form>
                </div>

                {{-- Footer Card: Link ke Register --}}
                <div class="card-footer text-center bg-light py-3">
                    <p class="mb-0 text-muted small">
                        Belum punya akun? <br>
                        <a href="{{ route('register') }}" class="text-decoration-none fw-bold text-dark fs-6">
                            Daftar Jadi Relawan <i class="fas fa-user-plus ms-1"></i>
                        </a>
                    </p>
                </div>

            </div>

            {{-- Link Kembali ke Home (Opsional) --}}
            <div class="text-center mt-3">
                <a href="{{ url('/') }}" class="text-decoration-none text-muted small">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</div>
@endsection