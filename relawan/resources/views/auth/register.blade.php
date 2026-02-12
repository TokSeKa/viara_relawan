@extends('layouts.app')

@section('title', 'Daftar Jadi Relawan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                {{-- Header Card --}}
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-user-plus me-2 text-warning"></i> Pendaftaran Relawan
                    </h4>
                    <small class="text-white-50">Bergabunglah untuk membuat perubahan</small>
                </div>

                <div class="card-body p-4 bg-white">
                    {{--
                        CATATAN ROUTE:
                        Secara standar Laravel, registrasi mengarah ke route('register').
                        Jika kamu ingin mengarah ke '/admin' (custom), ubah action di bawah menjadi action="/admin" 
                    --}}
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- Input Hidden untuk Jabatan (Default: Relawan) --}}
                        {{-- Kita set otomatis jadi 'relawan', user tidak perlu memilih --}}
                        <input type="hidden" name="jabatan" value="relawan">

                        <div class="row g-3">
                            {{-- 1. Nama Lengkap --}}
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama lengkapmu">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 2. Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Alamat Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required placeholder="contoh@email.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 3. Nomor HP --}}
                            <div class="col-md-6">
                                <label for="no_hp" class="form-label fw-bold">Nomor HP (WhatsApp)</label>
                                <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                    id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxx">
                                @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 4. Password --}}
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required placeholder="Buat password aman">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 5. Konfirmasi Password --}}
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-bold">Ulangi Password</label>
                                <input type="password" class="form-control"
                                    id="password_confirmation" name="password_confirmation" required placeholder="Ketik ulang password">
                            </div>

                            {{-- Divider --}}
                            <div class="col-12">
                                <hr class="my-2">
                                <h6 class="text-muted mb-3"><i class="fas fa-id-card me-1"></i> Data Pribadi</h6>
                            </div>

                            {{-- 6. Jenis Kelamin --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Kelamin</label>
                                <select class="form-select @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" required>
                                    <option value="" disabled selected>-- Pilih Gender --</option>
                                    {{-- Value disesuaikan dengan Enum di database/migrasi --}}
                                    <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 7. Rentang Usia (Pengganti Tanggal Lahir) --}}
                            <div class="col-md-6">
                                <label for="usia_range" class="form-label fw-bold">Rentang Usia</label>
                                <select class="form-select @error('usia_range') is-invalid @enderror" name="usia_range" required>
                                    <option value="" disabled selected>-- Pilih Usia --</option>
                                    <option value="< 17" {{ old('usia_range') == '< 17' ? 'selected' : '' }}>Di bawah 17 Tahun</option>
                                    <option value="17-25" {{ old('usia_range') == '17-25' ? 'selected' : '' }}>17 - 25 Tahun</option>
                                    <option value="26-35" {{ old('usia_range') == '26-35' ? 'selected' : '' }}>26 - 35 Tahun</option>
                                    <option value="36-50" {{ old('usia_range') == '36-50' ? 'selected' : '' }}>36 - 50 Tahun</option>
                                    <option value="> 50" {{ old('usia_range') == '> 50' ? 'selected' : '' }}>Di atas 50 Tahun</option>
                                </select>
                                @error('usia_range')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 8. Alamat --}}
                            <div class="col-12">
                                <label for="alamat" class="form-label fw-bold">Alamat Domisili</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror"
                                    id="alamat" name="alamat" rows="3" required placeholder="Alamat lengkap saat ini...">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-dark w-100 py-2 fw-bold">
                                    <i class="fas fa-paper-plane me-2"></i> DAFTAR SEKARANG
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer Card: Link Login --}}
                <div class="card-footer text-center bg-light py-3">
                    <small class="text-muted">
                        Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-dark">Login di sini</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection