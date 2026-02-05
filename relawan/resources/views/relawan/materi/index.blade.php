@extends('layouts.app')

@section('title', 'Pustaka Materi')

@section('content')
<div class="container pb-5">

    {{-- HEADER & PENCARIAN --}}
    <div class="row align-items-center my-4 g-3">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0">
                <i class="fas fa-book-reader me-2"></i> Pustaka Materi
            </h3>
            <p class="text-muted mb-0 small">Kumpulan dokumen, panduan, dan media untuk relawan.</p>
        </div>

        {{-- Search Box --}}
        <div class="col-md-6">
            {{-- Action mengarah ke route index relawan --}}
            <form action="{{ route('materi.index') }}" method="GET">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Cari judul materi..." value="{{ request('search') }}">
                    <button class="btn btn-primary px-4 fw-bold" type="submit">Cari</button>

                    {{-- Tombol Reset jika sedang mencari --}}
                    @if(request('search'))
                    <a href="{{ route('materi.index') }}" class="btn btn-outline-secondary" title="Reset Pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- GRID MATERI --}}
    <div class="row g-4">
        @forelse($materis as $materi)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 hover-card">
                <div class="card-body text-center p-4">

                    {{-- LOGIKA IKON --}}
                    @php
                    $ext = strtolower(pathinfo($materi->file_path, PATHINFO_EXTENSION));
                    $icon = 'fa-file';
                    $color = 'text-secondary';
                    $bg = 'bg-secondary';

                    if(in_array($ext, ['pdf'])) {
                    $icon = 'fa-file-pdf'; $color = 'text-danger'; $bg = 'bg-danger';
                    } elseif(in_array($ext, ['doc', 'docx'])) {
                    $icon = 'fa-file-word'; $color = 'text-primary'; $bg = 'bg-primary';
                    } elseif(in_array($ext, ['xls', 'xlsx'])) {
                    $icon = 'fa-file-excel'; $color = 'text-success'; $bg = 'bg-success';
                    } elseif(in_array($ext, ['ppt', 'pptx'])) {
                    $icon = 'fa-file-powerpoint'; $color = 'text-warning'; $bg = 'bg-warning';
                    } elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $icon = 'fa-image'; $color = 'text-info'; $bg = 'bg-info';
                    } elseif(in_array($ext, ['mp4', 'webm'])) {
                    $icon = 'fa-video'; $color = 'text-dark'; $bg = 'bg-dark';
                    } elseif(in_array($ext, ['mp3', 'wav'])) {
                    $icon = 'fa-music'; $color = 'text-dark'; $bg = 'bg-dark';
                    }
                    @endphp

                    {{-- Ikon Bulat --}}
                    <div class="d-inline-block p-3 rounded-circle {{ $bg }} bg-opacity-10 mb-3">
                        <i class="fas {{ $icon }} fa-3x {{ $color }}"></i>
                    </div>

                    {{-- Judul --}}
                    <h5 class="fw-bold text-dark mb-2 text-truncate" title="{{ $materi->judul }}">
                        {{ $materi->judul }}
                    </h5>

                    {{-- Tipe & Tanggal --}}
                    <div class="small text-muted mb-3">
                        <span class="badge bg-light text-dark border">{{ strtoupper($ext) }}</span>
                        <span class="mx-1">•</span>
                        {{ $materi->created_at->diffForHumans() }}
                    </div>

                    {{-- Deskripsi Pendek --}}
                    <p class="text-muted small text-truncate-2 mb-4" style="min-height: 40px;">
                        {{ $materi->deskripsi ?? 'Tidak ada keterangan tambahan.' }}
                    </p>

                    {{-- Tombol Aksi --}}
                    <div class="d-grid">
                        <a href="{{ route('materi.show', $materi->id) }}" class="btn btn-outline-primary fw-bold rounded-pill">
                            <i class="fas fa-eye me-1"></i> Buka Materi
                        </a>
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="opacity-50 mb-3">
                <i class="fas fa-folder-open fa-4x text-muted"></i>
            </div>
            <h5 class="text-muted fw-bold">
                @if(request('search'))
                Tidak ditemukan materi dengan kata kunci "{{ request('search') }}"
                @else
                Belum ada materi tersedia untuk minat Anda.
                @endif
            </h5>
            @if(request('search'))
            <a href="{{ route('materi.index') }}" class="btn btn-outline-primary mt-2">Reset Pencarian</a>
            @else
            <p class="text-muted small">Silakan cek kembali nanti atau update minat Anda.</p>
            @endif
        </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="mt-5 d-flex justify-content-center">
        {{ $materis->withQueryString()->links() }}
        {{-- withQueryString() penting agar saat pindah halaman, pencarian tidak hilang --}}
    </div>

</div>

@push('styles')
<style>
    /* Efek Hover naik sedikit */
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1) !important;
    }
</style>
@endpush

@endsection