@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
<div class="container pb-5">
    
    {{-- Header & Navigasi --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-user-circle me-2"></i> Detail Pengguna
        </h3>
        <a href="{{ route('admin.users.index_admin_user') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    {{-- ALERT SUKSES/ERROR --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        {{-- KOLOM KIRI: Info User (READ ONLY) --}}
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold border-bottom">
                    Informasi Akun (Read Only)
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-4">
                        
                        {{-- Foto Profil Dummy --}}
                        <div class="col-12 text-center mb-2">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary bg-opacity-10 rounded-circle text-secondary" style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                            <h5 class="fw-bold mt-3 mb-1">{{ $user->name }}</h5>
                            <span class="badge bg-warning text-dark border border-warning rounded-pill px-3">
                                {{ ucfirst($user->jabatan) }}
                            </span>
                        </div>

                        {{-- Data Pribadi --}}
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Email</label>
                            <div class="fs-6 text-dark">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Nomor HP</label>
                            <div class="fs-6 text-dark">{{ $user->no_hp }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Jenis Kelamin</label>
                            <div class="fs-6 text-dark">{{ ucfirst($user->jenis_kelamin) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase">Tanggal Lahir</label>
                            <div class="fs-6 text-dark">{{ \Carbon\Carbon::parse($user->tanggal_lahir)->format('d F Y') }}</div>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted fw-bold text-uppercase">Alamat Domisili</label>
                            <p class="fs-6 text-dark bg-light p-3 rounded border mb-0">
                                {{ $user->alamat }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Tag, Jabatan & Info --}}
        <div class="col-md-4">
            
            {{-- 1. CARD TAG (Area Kerja Admin) --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-tags me-2"></i> Tag & Minat</span>
                    <a href="{{ route('admin.users.tags.edit', $user->id) }}" class="btn btn-sm btn-light fw-bold text-dark" style="font-size: 0.75rem;">
                        <i class="fas fa-cog me-1"></i> KELOLA
                    </a>
                </div>
                <div class="card-body">
                    @if($user->tags->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($user->tags as $tag)
                                @if($tag->is_admin_only)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                        <i class="fas fa-lock me-1" style="font-size: 10px;"></i> {{ $tag->nama_tag }}
                                    </span>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                        {{ $tag->nama_tag }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-tag fa-2x mb-2 opacity-25"></i>
                            <p class="small mb-0">User ini belum memiliki tag.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. CARD KELOLA JABATAN (BARU DITAMBAHKAN DISINI) --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning bg-opacity-25 text-dark fw-bold py-3">
                    <i class="fas fa-user-shield me-2"></i> Kelola Jabatan
                </div>
                <div class="card-body">
                    
                    {{-- Cek apakah ini User Genesis (ID 1)? --}}
                    @if($user->id == 1)
                        <div class="alert alert-secondary mb-0 border-0 d-flex align-items-center">
                            <i class="fas fa-lock fa-2x me-3 opacity-50"></i>
                            <div class="small lh-sm">
                                <strong>Akun Dilindungi</strong><br>
                                Jabatan Super Admin (Genesis) tidak dapat diubah demi keamanan sistem.
                            </div>
                        </div>
                    @else
                        {{-- Form Ubah Jabatan --}}
                        <form action="{{ route('admin.users.update_jabatan', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label class="small text-muted mb-1">Pilih Jabatan</label>
                                <select name="jabatan" class="form-select">
                                    <option value="relawan" {{ $user->jabatan == 'relawan' ? 'selected' : '' }}>Relawan (User Biasa)</option>
                                    <option value="admin" {{ $user->jabatan == 'admin' ? 'selected' : '' }}>Admin (Pengelola)</option>
                                    <option value="blokir" {{ $user->jabatan == 'blokir' ? 'selected' : '' }}>Blokir</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-warning w-100 fw-bold" onclick="return confirm('Apakah Anda yakin ingin mengubah hak akses user ini?');">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                            
                            <div class="mt-2 text-center">
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    *Admin memiliki akses penuh ke sistem.
                                </small>
                            </div>
                        </form>
                    @endif

                </div>
            </div>

            {{-- 3. CARD INFO SISTEM --}}
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold text-muted mb-3 small text-uppercase">Metadata Akun</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2 d-flex justify-content-between border-bottom pb-2">
                            <span>Bergabung:</span>
                            <span class="fw-bold">{{ $user->created_at->format('d M Y') }}</span>
                        </li>
                        <li class="mb-2 d-flex justify-content-between">
                            <span>Terakhir Update:</span>
                            <span class="fw-bold">{{ $user->updated_at->diffForHumans() }}</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection