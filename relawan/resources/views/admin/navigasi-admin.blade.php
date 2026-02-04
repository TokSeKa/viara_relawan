@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container pb-5">
    
    {{-- 1. HERO SECTION: Sambutan Admin --}}
    <div class="bg-dark text-white rounded-3 p-4 mb-5 shadow-lg position-relative overflow-hidden">
        <div class="position-relative z-1">
            <h2 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}! 👋</h2>
            <p class="mb-0 opacity-75">Selamat datang di panel administrasi. Apa yang ingin Anda kelola hari ini?</p>
        </div>
        {{-- Hiasan Background Icon --}}
        <i class="fas fa-user-shield fa-10x position-absolute top-50 end-0 translate-middle-y opacity-10 me-4"></i>
    </div>

    {{-- 2. MENU UTAMA: Manajemen Data --}}
    <h5 class="fw-bold text-dark mb-3 ps-2 border-start border-4 border-primary">Manajemen Utama</h5>
    <div class="row g-4 mb-5">
        
        {{-- Card: Daftar Kegiatan --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.kegiatan.index') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Daftar Kegiatan</h5>
                        <p class="text-muted small mb-0">Lihat, filter, dan kelola semua kegiatan.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card: Tambah Kegiatan Baru --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.kegiatan.pilih') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                        <i class="fas fa-plus-circle fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Buat Kegiatan</h5>
                        <p class="text-muted small mb-0">Tambah acara, donasi, atau pengerahan relawan baru.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card: Manajemen Tags --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.tags.index') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Master Tag</h5>
                        <p class="text-muted small mb-0">Kelola kategori minat untuk relawan.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card: Manajemen Notifikasi (BARU DITAMBAHKAN) --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.notifikasi.index') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                        <i class="fas fa-bullhorn fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Pusat Notifikasi</h5>
                        <p class="text-muted small mb-0">Kirim pengumuman ke relawan.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card: Manajemen User (BARU DITAMBAHKAN) --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.users.index_admin_user') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 me-3">
                        <i class="fas fa-users-cog fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Kelola Pengguna</h5>
                        <p class="text-muted small mb-0">Lihat daftar user dan edit minat mereka.</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

    {{-- 3. MENU AKUN: Pengaturan Pribadi --}}
    <h5 class="fw-bold text-dark mb-3 ps-2 border-start border-4 border-secondary">Pengaturan Akun</h5>
    <div class="row g-4">
        
        {{-- Card: Edit Profil --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('user.profile.edit') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-3 me-3">
                        <i class="fas fa-user-cog fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Profil Saya</h5>
                        <p class="text-muted small mb-0">Update data diri admin.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card: Logout --}}
        <div class="col-md-6 col-lg-4">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-dashboard').submit();" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift bg-danger text-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <i class="fas fa-sign-out-alt fa-2x mb-2"></i>
                        <h5 class="fw-bold mb-0">Keluar / Logout</h5>
                    </div>
                </div>
            </a>
            {{-- Form Logout Tersembunyi --}}
            <form id="logout-form-dashboard" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>

    </div>
</div>

{{-- CSS Tambahan untuk Efek Hover --}}
@push('styles')
<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush

@endsection