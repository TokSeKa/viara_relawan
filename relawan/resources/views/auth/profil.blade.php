@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                {{-- Header --}}
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-user-circle me-2"></i> Profil Saya
                    </h4>
                    <small class="text-white-50">Perbarui informasi akun Anda</small>
                </div>

                <div class="card-body p-4 bg-white">

                    {{-- Alert Sukses Update --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('user.profile.update') }}">
                        @csrf
                        @method('PUT') {{-- PENTING: Untuk update data --}}

                        {{-- Tampilkan Jabatan (Read Only) --}}
                        <div class="text-center mb-4">
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill text-uppercase fw-bold">
                                {{ $user->jabatan ?? 'User' }}
                            </span>
                        </div>

                        <div class="row g-3">
                            {{-- 1. Nama Lengkap --}}
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 2. Email (Biasanya Readonly atau perlu verifikasi ulang, tapi disini kita open edit) --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Alamat Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 3. Nomor HP --}}
                            <div class="col-md-6">
                                <label for="no_hp" class="form-label fw-bold">Nomor HP (WhatsApp)</label>
                                <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                    id="no_hp" name="no_hp"
                                    value="{{ old('no_hp', $user->no_hp) }}" required>
                                @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 4. Password Baru (Opsional) --}}
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">Password Baru <small class="text-muted fw-normal">(Opsional)</small></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Kosongkan jika tidak diganti">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 5. Konfirmasi Password --}}
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-bold">Ulangi Password</label>
                                <input type="password" class="form-control"
                                    id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <h6 class="text-muted mb-3"><i class="fas fa-id-card me-1"></i> Data Pribadi</h6>
                            </div>

                            {{-- 6. Jenis Kelamin --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Kelamin</label>
                                <select class="form-select @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" required>
                                    <option value="" disabled>-- Pilih Gender --</option>
                                    <option value="laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 7. Tanggal Lahir --}}
                            <div class="col-md-6">
                                <label for="tanggal_lahir" class="form-label fw-bold">Tanggal Lahir</label>
                                <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                    id="tanggal_lahir" name="tanggal_lahir"
                                    value="{{ old('tanggal_lahir', \Carbon\Carbon::parse($user->tanggal_lahir)->format('Y-m-d')) }}" required>
                                @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 8. Alamat --}}
                            <div class="col-12">
                                <label for="alamat" class="form-label fw-bold">Alamat Domisili</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror"
                                    id="alamat" name="alamat" rows="3" required>{{ old('alamat', $user->alamat) }}</textarea>
                                @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="col-12 mt-4 d-flex justify-content-between">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4 fw-bold">
                                    <i class="fas fa-arrow-left me-2"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">
                                    <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection