@extends('layouts.app')

@section('title', 'Pilih Jenis Kegiatan')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Mau buat kegiatan apa hari ini?</h3>
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row g-4">
        @php $jabatan = Auth::user()->jabatan; @endphp

        {{-- 1. DONASI DANA --}}
        @if($jabatan == 'admin_super' || $jabatan == 'admin_dana')
        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'donasi_dana') }}" class="card h-100 text-decoration-none shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-hand-holding-usd fa-3x text-success mb-3"></i>
                    <h5 class="text-dark fw-bold">Donasi Dana</h5>
                    <p class="text-muted small">Galang dana untuk bencana atau bantuan sosial.</p>
                </div>
            </a>
        </div>
        @endif

        {{-- 2. DONOR DARAH --}}
        @if($jabatan == 'admin_super' || $jabatan == 'admin_darah')
        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'donasi_darah') }}" class="card h-100 text-decoration-none shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-heartbeat fa-3x text-danger mb-3"></i>
                    <h5 class="text-dark fw-bold">Donor Darah</h5>
                    <p class="text-muted small">Kegiatan donor darah bekerja sama dengan PMI.</p>
                </div>
            </a>
        </div>
        @endif

        {{-- 3. MOBIL --}}
        @if($jabatan == 'admin_super' || $jabatan == 'admin_mobil')
        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'mobil') }}" class="card h-100 text-decoration-none shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-truck-pickup fa-3x text-primary mb-3"></i>
                    <h5 class="text-dark fw-bold">Pinjam Armada</h5>
                    <p class="text-muted small">Peminjaman mobil/pickup untuk distribusi.</p>
                </div>
            </a>
        </div>
        @endif

        {{-- 4. ACARA --}}
        @if($jabatan == 'admin_super' || $jabatan == 'admin_acara')
        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'acara') }}" class="card h-100 text-decoration-none shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-calendar-alt fa-3x text-warning mb-3"></i>
                    <h5 class="text-dark fw-bold">Acara / Event</h5>
                    <p class="text-muted small">Seminar, Workshop, atau kerja bakti.</p>
                </div>
            </a>
        </div>
        @endif

        {{-- 5. DONASI BARANG (LOGISTIK) --}}
        @if($jabatan == 'admin_super' || $jabatan == 'admin_logistik')
        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'donasi_barang') }}" class="card h-100 text-decoration-none shadow-sm hover-card border-info">
                <div class="card-body text-center p-4">
                    <i class="fas fa-box-open fa-3x text-info mb-3"></i>
                    <h5 class="text-dark fw-bold">Donasi Barang</h5>
                    <p class="text-muted small">Pengumpulan sembako, pakaian, atau alat ibadah.</p>
                </div>
            </a>
        </div>

        {{-- 6. PINJAM BARANG (LOGISTIK) --}}
        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'peminjaman_barang') }}" class="card h-100 text-decoration-none shadow-sm hover-card border-info">
                <div class="card-body text-center p-4">
                    <i class="fas fa-tools fa-3x text-secondary mb-3"></i>
                    <h5 class="text-dark fw-bold">Pinjam Barang</h5>
                    <p class="text-muted small">Peminjaman tenda, kursi, atau alat sound system.</p>
                </div>
            </a>
        </div>
        @endif

    </div>
</div>
@endsection