@extends('layouts.app')

@section('title', 'Dashboard Relawan')

@section('content')
<div class="container pb-5">

    {{-- 1. HERO SECTION --}}
    <div class="bg-primary bg-gradient text-white rounded-3 p-4 mb-5 shadow-lg d-flex justify-content-between align-items-center overflow-hidden">
        <div class="me-3">
            <h2 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}! 👋</h2>
            <p class="mb-0 opacity-75">Siap menebar kebaikan hari ini? Temukan kegiatan yang cocok untukmu.</p>
        </div>
        <div class="d-flex align-items-center justify-content-center">
            <i class="fas fa-hand-holding-heart fa-4x text-white-50"></i>
        </div>
    </div>

    {{-- 2. MENU UTAMA: Aktivitas --}}
    <h5 class="fw-bold text-dark mb-3 ps-2 border-start border-4 border-primary">Aktivitas Saya</h5>
    <div class="row g-4 mb-5">

        {{-- Card: Cari Kegiatan --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('kegiatan.index') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                        <i class="fas fa-search fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Cari Kegiatan</h5>
                        <p class="text-muted small mb-0">Temukan event sosial terbaru.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card: Riwayat Kegiatan --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('riwayat') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                        <i class="fas fa-history fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Riwayat Saya</h5>
                        <p class="text-muted small mb-0">Lihat kegiatan yang pernah diikuti.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card: Minat & Skill --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('user.tags.edit') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Minat & Skill</h5>
                        <p class="text-muted small mb-0">Atur preferensi topik kegiatan.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card: Materi & Panduan (BARU) --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('materi.index') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    {{-- Warna Hijau (Success) agar beda dari yang lain --}}
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                        <i class="fas fa-book-reader fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Materi & Panduan</h5>
                        <p class="text-muted small mb-0">Akses dokumen dan video panduan.</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

    {{-- 3. MENU AKUN --}}
    <h5 class="fw-bold text-dark mb-3 ps-2 border-start border-4 border-secondary">Pengaturan Akun</h5>
    <div class="row g-4">

        {{-- Card: Edit Profil --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('user.profile.edit') }}" class="card h-100 text-decoration-none shadow-sm border-0 hover-lift">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-3 me-3">
                        <i class="fas fa-user-edit fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Edit Profil</h5>
                        <p class="text-muted small mb-0">Perbarui data diri dan kontak.</p>
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
            <form id="logout-form-dashboard" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>

    </div>
</div>

@push('styles')
<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }
</style>
@endpush

@endsection