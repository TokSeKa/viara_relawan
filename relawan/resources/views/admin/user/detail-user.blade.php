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
                        {{-- FOTO PROFIL --}}
                        <div class="col-12 text-center mb-2">
                            <div class="mb-3 d-inline-block position-relative">
                                <img src="{{ $user->profile_photo_url }}"
                                    class="rounded-circle object-fit-cover shadow-sm border"
                                    width="120" height="120"
                                    alt="{{ $user->name }}">
                            </div>
                            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>

                            {{-- LOGIKA BADGE JABATAN BARU --}}
                            @if($user->jabatan == 'relawan')
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3">
                                RELAWAN
                            </span>
                            @elseif($user->jabatan == 'blokir')
                            <span class="badge bg-danger text-white border border-danger rounded-pill px-3">
                                BLOKIR
                            </span>
                            @elseif(str_contains($user->jabatan, 'admin'))
                            {{-- Mengubah 'admin_dana' menjadi 'ADMIN DANA' --}}
                            <span class="badge bg-dark text-white border border-dark rounded-pill px-3">
                                {{ strtoupper(str_replace('_', ' ', $user->jabatan)) }}
                            </span>
                            @endif
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
                            <label class="small text-muted fw-bold text-uppercase">Rentang Usia</label>
                            <div class="fs-6 text-dark">{{ $user->usia_range ?? '-' }}</div>
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
            {{-- 1. CARD TAG --}}
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

            {{-- 2. CARD KELOLA JABATAN --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning bg-opacity-25 text-dark fw-bold py-3">
                    <i class="fas fa-user-shield me-2"></i> Kelola Jabatan
                </div>
                <div class="card-body">
                    @if($user->id == 1)
                    <div class="alert alert-secondary mb-0 border-0 d-flex align-items-center">
                        <i class="fas fa-lock fa-2x me-3 opacity-50"></i>
                        <div class="small lh-sm">
                            <strong>Akun Dilindungi</strong><br>
                            Jabatan Super Admin (Genesis) tidak dapat diubah.
                        </div>
                    </div>
                    @else
                    <form action="{{ route('admin.users.update_jabatan', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="small text-muted mb-1 fw-bold">Pilih Jabatan</label>
                            <select name="jabatan" class="form-select border-warning">
                                <optgroup label="User Biasa">
                                    <option value="relawan" {{ $user->jabatan == 'relawan' ? 'selected' : '' }}>Relawan (User Biasa)</option>
                                </optgroup>
                                <optgroup label="Level Admin">
                                    <option value="admin_super" {{ $user->jabatan == 'admin_super' ? 'selected' : '' }}>Admin Super (Akses Penuh)</option>
                                    <option value="admin_dana" {{ $user->jabatan == 'admin_dana' ? 'selected' : '' }}>Admin Dana</option>
                                    <option value="admin_darah" {{ $user->jabatan == 'admin_darah' ? 'selected' : '' }}>Admin Donor Darah</option>
                                    <option value="admin_mobil" {{ $user->jabatan == 'admin_mobil' ? 'selected' : '' }}>Admin Transportasi</option>
                                    <option value="admin_acara" {{ $user->jabatan == 'admin_acara' ? 'selected' : '' }}>Admin Acara</option>
                                    <option value="admin_logistik" {{ $user->jabatan == 'admin_logistik' ? 'selected' : '' }}>Admin Logistik (Barang)</option>
                                </optgroup>
                                <optgroup label="Lainnya">
                                    <option value="blokir" {{ $user->jabatan == 'blokir' ? 'selected' : '' }}>Blokir Akses</option>
                                </optgroup>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold" onclick="return confirm('Apakah Anda yakin ingin mengubah hak akses user ini?');">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- 3. CARD INFO --}}
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