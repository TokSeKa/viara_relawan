@extends('layouts.app')

@section('title', 'Detail Materi')

@section('content')
<div class="container pb-5">

    {{-- HEADER NAVIGASI --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">
                <i class="fas fa-eye me-2"></i> Preview Materi
            </h3>
            <p class="text-muted mb-0 small">Melihat detail konten dan file materi.</p>
        </div>

        {{-- Tombol Kembali (Cek apakah dia Admin atau User Biasa untuk arah redirect) --}}
        @if(Auth::user()->jabatan == 'admin')
        <a href="{{ route('admin.materi.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke List
        </a>
        @else
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        @endif
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- KARTU PREVIEW UTAMA --}}
            <div class="card shadow border-0 overflow-hidden">

                {{-- Header Card --}}
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-white">
                        {{ $materi->judul }}
                    </h5>
                    {{-- Badge Ekstensi File --}}
                    <span class="badge bg-white text-dark fw-bold">
                        {{ strtoupper(pathinfo($materi->file_path, PATHINFO_EXTENSION)) }}
                    </span>
                </div>

                <div class="card-body bg-light text-center p-0">

                    {{-- SIAPKAN VARIABEL --}}
                    @php
                    $path = asset('storage/' . $materi->file_path);
                    $ext = strtolower(pathinfo($materi->file_path, PATHINFO_EXTENSION));
                    @endphp

                    {{-- ========================================== --}}
                    {{-- LOGIKA SMART PREVIEW (Core Feature) --}}
                    {{-- ========================================== --}}

                    <div class="p-4">
                        {{-- 1. GAMBAR (JPG, PNG, dll) --}}
                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                        <div class="rounded shadow-sm overflow-hidden d-inline-block border bg-white">
                            <img src="{{ $path }}" class="img-fluid" alt="{{ $materi->judul }}" style="max-height: 600px;">
                        </div>

                        {{-- 2. AUDIO (MP3, WAV) --}}
                        @elseif(in_array($ext, ['mp3', 'wav', 'ogg']))
                        <div class="card border-0 bg-transparent py-5">
                            <div class="mb-4">
                                <div class="d-inline-block p-4 rounded-circle bg-white shadow-sm text-info">
                                    <i class="fas fa-music fa-5x"></i>
                                </div>
                            </div>
                            <h5 class="fw-bold mb-3">Pemutar Audio</h5>
                            <div class="w-75 mx-auto">
                                <audio controls class="w-100 shadow-sm rounded">
                                    <source src="{{ $path }}" type="audio/{{ $ext }}">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                            </div>
                        </div>

                        {{-- 3. VIDEO (MP4, WEBM) --}}
                        @elseif(in_array($ext, ['mp4', 'webm', 'ogg']))
                        <div class="ratio ratio-16x9 shadow rounded overflow-hidden bg-black">
                            <video controls>
                                <source src="{{ $path }}" type="video/{{ $ext }}">
                                Browser Anda tidak mendukung elemen video.
                            </video>
                        </div>

                        {{-- 4. PDF (IFRAME) --}}
                        @elseif($ext == 'pdf')
                        <div class="ratio ratio-1x1" style="min-height: 800px;">
                            <iframe src="{{ $path }}" class="border-0 rounded shadow-sm bg-white"></iframe>
                        </div>

                        {{-- 5. DOKUMEN OFFICE / LAINNYA (Default View) --}}
                        @else
                        <div class="py-5">
                            {{-- Logika Ikon Besar --}}
                            @if(in_array($ext, ['doc', 'docx']))
                            <i class="fas fa-file-word fa-7x text-primary mb-3"></i>
                            <h4 class="fw-bold text-primary">Microsoft Word Document</h4>
                            @elseif(in_array($ext, ['xls', 'xlsx']))
                            <i class="fas fa-file-excel fa-7x text-success mb-3"></i>
                            <h4 class="fw-bold text-success">Microsoft Excel Spreadsheet</h4>
                            @elseif(in_array($ext, ['ppt', 'pptx']))
                            <i class="fas fa-file-powerpoint fa-7x text-danger mb-3"></i>
                            <h4 class="fw-bold text-danger">PowerPoint Presentation</h4>
                            @elseif(in_array($ext, ['zip', 'rar', '7z']))
                            <i class="fas fa-file-archive fa-7x text-warning mb-3"></i>
                            <h4 class="fw-bold text-warning">Arsip Terkompresi</h4>
                            @else
                            <i class="fas fa-file-alt fa-7x text-secondary mb-3"></i>
                            <h4 class="fw-bold text-secondary">File Dokumen</h4>
                            @endif

                            <p class="text-muted mt-2">Preview tidak tersedia untuk format file ini.<br>Silakan unduh untuk membuka.</p>
                        </div>
                        @endif
                    </div>

                    {{-- TOMBOL DOWNLOAD & DESKRIPSI --}}
                    <div class="bg-white border-top p-4 text-start">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

                            {{-- Info File --}}
                            <div>
                                <small class="text-muted text-uppercase fw-bold ls-1">Nama File Fisik</small>
                                <div class="fw-bold text-dark font-monospace bg-light px-2 rounded mt-1 border">
                                    {{ basename($materi->file_path) }}
                                </div>
                            </div>

                            {{-- Tombol Download --}}
                            <div class="d-flex gap-2">
                                <a href="{{ $path }}" class="btn btn-dark px-4" download>
                                    <i class="fas fa-download me-2"></i> Download
                                </a>
                                <a href="{{ $path }}" class="btn btn-outline-dark" target="_blank" title="Buka di Tab Baru">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        @if($materi->deskripsi)
                        <hr class="my-4">
                        <div>
                            <h6 class="fw-bold text-dark"><i class="fas fa-align-left me-2"></i> Deskripsi / Keterangan</h6>
                            <p class="text-muted mb-0 mt-2" style="line-height: 1.6;">
                                {!! nl2br(e($materi->deskripsi)) !!}
                            </p>
                        </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection