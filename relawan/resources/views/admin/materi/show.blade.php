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

            <div class="card shadow border-0 overflow-hidden">

                {{-- Header Card --}}
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-white">
                        {{ $materi->judul }}
                    </h5>

                    {{-- Badge Ekstensi File (Hanya muncul jika ada file) --}}
                    @if($materi->file_path)
                    <span class="badge bg-white text-dark fw-bold">
                        {{ strtoupper(pathinfo($materi->file_path, PATHINFO_EXTENSION)) }}
                    </span>
                    @else
                    <span class="badge bg-info text-dark fw-bold">LINK / TEXT</span>
                    @endif
                </div>

                <div class="card-body bg-light text-center p-0">

                    {{-- SIAPKAN VARIABEL --}}
                    @php
                    // Pastikan path hanya di-generate jika file ada untuk menghindari error asset()
                    $path = $materi->file_path ? asset('storage/' . $materi->file_path) : '#';
                    $ext = $materi->file_path ? strtolower(pathinfo($materi->file_path, PATHINFO_EXTENSION)) : '';
                    @endphp

                    {{-- 1. LOGIKA PREVIEW FILE (Hanya jika file ada) --}}
                    @if($materi->file_path)
                    <div class="p-4">
                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                        <div class="rounded shadow-sm overflow-hidden d-inline-block border bg-white">
                            <img src="{{ $path }}" class="img-fluid" alt="{{ $materi->judul }}" style="max-height: 600px;">
                        </div>

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

                        @elseif(in_array($ext, ['mp4', 'webm', 'ogg']))
                        <div class="ratio ratio-16x9 shadow rounded overflow-hidden bg-black">
                            <video controls>
                                <source src="{{ $path }}" type="video/{{ $ext }}">
                                Browser Anda tidak mendukung elemen video.
                            </video>
                        </div>

                        @elseif($ext == 'pdf')
                        <div class="ratio ratio-1x1" style="min-height: 800px;">
                            <iframe src="{{ $path }}" class="border-0 rounded shadow-sm bg-white"></iframe>
                        </div>

                        @else
                        {{-- File Dokumen Lainnya --}}
                        <div class="py-5">
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
                    @endif

                    {{-- 2. TAMPILAN LINK EKSTERNAL (YOUTUBE EMBED / LINK BIASA) --}}
                    @if($materi->link)
                    <div class="p-4 {{ $materi->file_path ? 'border-top bg-white' : '' }}">

                        {{-- CEK APAKAH LINK YOUTUBE? --}}
                        @php
                        $isYoutube = false;
                        $embedUrl = '';

                        // Regex sederhana untuk menangkap ID video YouTube
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $materi->link, $match)) {
                            $isYoutube = true;
                            $videoId = $match[1];
                            $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                        }
                        @endphp

                        @if($isYoutube)
                        {{-- TAMPILAN KHUSUS YOUTUBE --}}
                        <div class="card border-0 shadow-sm bg-black">
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $embedUrl }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                        </div>
                        <div class="mt-3 text-start">
                            <small class="text-muted"><i class="fab fa-youtube text-danger me-1"></i> Video diputar dari YouTube</small>
                        </div>

                        @else
                        {{-- TAMPILAN LINK BIASA (NON-YOUTUBE) --}}
                        <div class="card border-primary bg-primary bg-opacity-10 py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-external-link-alt fa-3x text-primary mb-3"></i>
                                <h5 class="fw-bold">Link Materi Tersedia</h5>
                                <p class="text-muted">Materi ini memiliki referensi link eksternal yang dapat Anda buka.</p>
                                <a href="{{ $materi->link }}" target="_blank" class="btn btn-primary px-5 btn-lg rounded-pill fw-bold shadow-sm">
                                    <i class="fas fa-share-square me-2"></i> KUNJUNGI LINK
                                </a>
                                <div class="mt-3 small text-muted">
                                    URL: <span class="font-monospace">{{ Str::limit($materi->link, 50) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                    @endif

                    {{-- 3. JIKA KOSONG SAMA SEKALI --}}
                    @if(!$materi->file_path && !$materi->link && !$materi->deskripsi)
                    <div class="py-5 text-center">
                        <i class="fas fa-exclamation-circle fa-4x text-warning mb-3"></i>
                        <h5>Tidak ada konten materi untuk ditampilkan.</h5>
                    </div>
                    @endif

                    {{-- FOOTER CONTENT: DOWNLOAD & DESKRIPSI --}}
                    <div class="bg-white border-top p-4 text-start">

                        {{-- A. BAGIAN DOWNLOAD (HANYA JIKA FILE ADA) --}}
                        @if($materi->file_path)
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4">
                            <div>
                                <small class="text-muted text-uppercase fw-bold ls-1">Nama File Fisik</small>
                                <div class="fw-bold text-dark font-monospace bg-light px-2 rounded mt-1 border">
                                    {{ basename($materi->file_path) }}
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ $path }}" class="btn btn-dark px-4" download>
                                    <i class="fas fa-download me-2"></i> Download File
                                </a>
                            </div>
                        </div>
                        @endif

                        {{-- B. BAGIAN DESKRIPSI (SELALU MUNCUL JIKA ADA TEKS) --}}
                        @if($materi->deskripsi)
                        {{-- Tambahkan garis pemisah hanya jika ada file sebelumnya --}}
                        @if($materi->file_path)
                        <hr class="my-4"> @endif

                        <div>
                            <h6 class="fw-bold text-dark"><i class="fas fa-align-left me-2"></i> Deskripsi / Keterangan</h6>
                            <p class="text-muted mb-0 mt-2 text-break" style="line-height: 1.6;">
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