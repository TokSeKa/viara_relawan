@extends('layouts.app')

@section('title', 'Pusat Laporan')

@section('content')
<div class="container pb-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fas fa-file-contract me-2"></i> Pusat Laporan
            </h3>
            <p class="text-muted mb-0">Silakan pilih jenis laporan yang ingin dicetak atau diekspor.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row g-4">

        {{-- 1. LAPORAN REKAPITULASI (Kegiatan & Detail) --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.laporan.rekap') }}" class="card h-100 text-decoration-none shadow hover-card border-0">
                <div class="card-body p-4 text-center">
                    <div class="d-inline-block p-3 rounded-circle bg-primary bg-opacity-10 mb-3 text-primary">
                        <i class="fas fa-list-alt fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Rekapitulasi Kegiatan</h5>
                    <p class="text-muted small">
                        Daftar lengkap kegiatan berdasarkan periode tanggal, status, dan jenis kegiatan (Donasi, Acara, dll).
                    </p>
                    <span class="btn btn-sm btn-outline-primary rounded-pill px-4">Buka Filter</span>
                </div>
            </a>
        </div>

        {{-- 2. LAPORAN PESERTA / RELAWAN --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.laporan.index_peserta') }}" class="card h-100 text-decoration-none shadow hover-card border-0">
                <div class="card-body p-4 text-center">
                    <div class="d-inline-block p-3 rounded-circle bg-success bg-opacity-10 mb-3 text-success">
                        <i class="fas fa-users-viewfinder fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Data Peserta & Relawan</h5>
                    <p class="text-muted small">
                        Cek siapa saja yang mendaftar di kegiatan tertentu. Lengkap dengan data detail peserta.
                    </p>
                    <span class="btn btn-sm btn-outline-success rounded-pill px-4">Pilih Kegiatan</span>
                </div>
            </a>
        </div>

        {{-- 3. LAPORAN POTENSI RELAWAN --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.laporan.index_relawan') }}" class="card h-100 text-decoration-none shadow hover-card border-0">
                <div class="card-body p-4 text-center">
                    <div class="d-inline-block p-3 rounded-circle bg-warning bg-opacity-10 mb-3 text-warning-emphasis">
                        <i class="fas fa-user-tag fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Potensi Relawan</h5>
                    <p class="text-muted small">
                        Analisis relawan berdasarkan minat/tag dan tingkat keaktifan (ranking partisipasi).
                    </p>
                    <span class="btn btn-sm btn-outline-warning text-dark rounded-pill px-4">Analisis Minat</span>
                </div>
            </a>
        </div>

        {{-- 4. LAPORAN STATISTIK TAG --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.laporan.cetak_tag') }}" target="_blank" class="card h-100 text-decoration-none shadow hover-card border-0">
                <div class="card-body p-4 text-center">
                    <div class="d-inline-block p-3 rounded-circle bg-danger bg-opacity-10 mb-3 text-danger">
                        <i class="fas fa-chart-bar fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Statistik Minat (Tag)</h5>
                    <p class="text-muted small">
                        Grafik distribusi minat relawan. Lihat kategori mana yang paling populer.
                    </p>
                    <span class="btn btn-sm btn-outline-danger rounded-pill px-4">Lihat Grafik</span>
                </div>
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-card {
        transition: transform 0.2s;
    }

    .hover-card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush
@endsection