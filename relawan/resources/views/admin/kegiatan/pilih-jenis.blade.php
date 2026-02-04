@extends('layouts.app')

@section('title', 'Pilih Jenis Kegiatan')

@section('content')
<div class="container">
    {{-- BAGIAN HEADER: Judul di kiri, Tombol Kembali di kanan --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Mau buat kegiatan apa hari ini?</h3>
        
        {{-- Ini Tombol Kembali ke Route Index --}}
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row g-4">
        
        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'donasi_dana') }}" class="card h-100 text-decoration-none shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-hand-holding-usd fa-3x text-success mb-3"></i>
                    <h5 class="text-dark fw-bold">Donasi Dana</h5>
                    <p class="text-muted small">Galang dana untuk bencana atau bantuan sosial.</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'donasi_darah') }}" class="card h-100 text-decoration-none shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-heartbeat fa-3x text-danger mb-3"></i>
                    <h5 class="text-dark fw-bold">Donor Darah</h5>
                    <p class="text-muted small">Kegiatan donor darah bekerja sama dengan PMI.</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'mobil') }}" class="card h-100 text-decoration-none shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-truck-pickup fa-3x text-primary mb-3"></i>
                    <h5 class="text-dark fw-bold">Pinjam Armada</h5>
                    <p class="text-muted small">Peminjaman mobil/pickup untuk distribusi.</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.kegiatan.create', 'acara') }}" class="card h-100 text-decoration-none shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-calendar-alt fa-3x text-warning mb-3"></i>
                    <h5 class="text-dark fw-bold">Acara / Event</h5>
                    <p class="text-muted small">Seminar, Workshop, atau kerja bakti.</p>
                </div>
            </a>
        </div>

    </div>
</div>
@endsection