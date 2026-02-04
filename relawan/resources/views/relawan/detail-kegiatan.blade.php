@extends('layouts.app')

@section('title', $kegiatan->judul)

@section('content')
<div class="container pb-5">

    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kegiatan.index') }}" class="text-decoration-none">Cari Kegiatan</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($kegiatan->judul, 20) }}</li>
        </ol>
    </nav>

    <div class="row">
        {{-- KOLOM KIRI: INFO UTAMA --}}
        <div class="col-lg-8">

            {{-- Banner Image --}}
            <div class="card shadow-sm border-0 mb-4 overflow-hidden rounded-3">
                @if($kegiatan->banner_image)
                <img src="{{ asset('storage/' . $kegiatan->banner_image) }}" class="w-100 object-fit-cover" style="height: 400px;" alt="{{ $kegiatan->judul }}">
                @else
                <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 400px;">
                    <i class="fas fa-image fa-4x text-muted opacity-50"></i>
                </div>
                @endif
            </div>

            {{-- Judul & Deskripsi --}}
            <h1 class="fw-bold mb-3">{{ $kegiatan->judul }}</h1>

            {{-- Tags / Kategori --}}
            <div class="mb-4">
                @foreach($kegiatan->tags as $tag)
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary me-1 mb-1 px-3 py-2 rounded-pill">
                    #{{ $tag->nama_tag }}
                </span>
                @endforeach
            </div>

            {{-- Info Admin & Tanggal --}}
            <div class="d-flex align-items-center mb-4 text-muted small">
                <div class="me-4">
                    <i class="fas fa-user-circle me-1"></i> {{ $kegiatan->admin->name ?? 'Admin' }}
                </div>
                <div class="me-4">
                    <i class="fas fa-calendar-alt me-1"></i> {{ $kegiatan->created_at->format('d M Y') }}
                </div>
            </div>

            <hr>

            {{-- Deskripsi Konten --}}
            <div class="content-body mt-4 text-secondary" style="line-height: 1.8;">
                {!! nl2br(e($kegiatan->deskripsi)) !!}
            </div>
        </div>

        {{-- KOLOM KANAN: SIDEBAR DETAIL KHUSUS --}}
        <div class="col-lg-4">
            <div class="card shadow border-0 sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-body p-4">

                    {{-- Status Badge --}}
                    <div class="mb-3">
                        @if($kegiatan->status == 'buka')
                        <span class="badge bg-success w-100 py-2">SEDANG DIBUKA</span>
                        @elseif($kegiatan->status == 'tutup')
                        <span class="badge bg-secondary w-100 py-2">SEMENTARA DITUTUP</span>
                        @else
                        <span class="badge bg-dark w-100 py-2">SELESAI</span>
                        @endif
                    </div>

                    {{-- Info Waktu Pelaksanaan --}}
                    <h6 class="fw-bold text-dark"><i class="far fa-clock me-2 text-warning"></i> Waktu Pelaksanaan</h6>
                    <ul class="list-unstyled small mb-4 text-secondary">
                        <li class="mb-1 d-flex justify-content-between">
                            <span>Mulai:</span>
                            <span class="fw-bold text-dark">{{ $kegiatan->tanggal_mulai->format('d M Y, H:i') }}</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Selesai:</span>
                            <span class="fw-bold text-dark">{{ $kegiatan->tanggal_selesai->format('d M Y, H:i') }}</span>
                        </li>
                    </ul>

                    <hr class="border-secondary border-opacity-25">

                    {{-- --- LOGIKA POLYMORPHIC: TAMPILKAN DETAIL SESUAI JENIS --- --}}

                    {{-- 1. JIKA DONASI DANA --}}
                    @if($kegiatan->detail_type == 'donasi_dana')
                    <h6 class="fw-bold mb-3"><i class="fas fa-hand-holding-usd me-2 text-success"></i> Informasi Donasi</h6>

                    {{-- Progress Bar (Opsional logic) --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Target Dana</small>
                        <h4 class="fw-bold text-success">Rp {{ number_format($kegiatan->detail->target_rupiah, 0, ',', '.') }}</h4>
                    </div>

                    <div class="alert alert-light border">
                        <small class="fw-bold d-block mb-2 text-muted">Rekening Transfer:</small>
                        @foreach($kegiatan->detail->info_bank as $bank)
                        <div class="mb-2 pb-2 border-bottom last-no-border">
                            <div class="fw-bold text-dark">{{ $bank['nama_bank'] }}</div>
                            <div class="font-monospace fs-5 text-dark">{{ $bank['no_rekening'] }}</div>
                            <small class="text-muted">a.n {{ $bank['atas_nama'] }}</small>
                        </div>
                        @endforeach
                    </div>

                    {{-- 2. JIKA DONASI DARAH --}}
                    @elseif($kegiatan->detail_type == 'donasi_darah')
                    <h6 class="fw-bold mb-3"><i class="fas fa-heartbeat me-2 text-danger"></i> Informasi Donor</h6>

                    <div class="mb-3">
                        <label class="small text-muted">Target Kantong</label>
                        <div class="fs-4 fw-bold">{{ $kegiatan->detail->target_kantong }} Kantong</div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted">Golongan Darah Dibutuhkan</label>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach($kegiatan->detail->golongan_darah_needed as $darah)
                            <span class="badge bg-danger rounded-circle p-2" style="width: 35px; height: 35px; display:flex; align-items:center; justify-content:center;">{{ $darah }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i> Lokasi PMI</label>
                        <p class="fw-bold mb-0">{{ $kegiatan->detail->lokasi_pmi }}</p>
                    </div>

                    {{-- 3. JIKA MOBIL --}}
                    @elseif($kegiatan->detail_type == 'mobil')
                    <h6 class="fw-bold mb-3"><i class="fas fa-truck-pickup me-2 text-info"></i> Peminjaman Mobil</h6>

                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Unit</small>
                                <span class="fw-bold fs-5">{{ $kegiatan->detail->jumlah_unit }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Supir</small>
                                <span class="fw-bold fs-5">
                                    {{ $kegiatan->detail->butuh_supir ? 'Ya' : 'Tidak' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted"><i class="fas fa-map-pin me-1"></i> Titik Penjemputan</label>
                        <p class="fw-bold mb-0">{{ $kegiatan->detail->lokasi_jemput }}</p>
                    </div>

                    {{-- 4. JIKA ACARA --}}
                    @elseif($kegiatan->detail_type == 'acara')
                    <h6 class="fw-bold mb-3"><i class="fas fa-calendar-check me-2 text-primary"></i> Detail Acara</h6>

                    <div class="mb-3">
                        <label class="small text-muted">Kuota Peserta</label>
                        <div class="fs-4 fw-bold text-dark">
                            {{ $kegiatan->detail->kuota_peserta > 0 ? $kegiatan->detail->kuota_peserta . ' Orang' : 'Tanpa Batas' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted"><i class="fas fa-map-marked-alt me-1"></i> Lokasi Acara</label>
                        <p class="fw-bold mb-0">{{ $kegiatan->detail->lokasi }}</p>
                    </div>

                    @endif

                    <hr>

                    {{-- TOMBOL AKSI --}}
                    {{-- Cek apakah user sudah join atau belum --}}
                    @php
                    // Cek di database apakah user auth sudah ada di tabel partisipasi kegiatan ini
                    $isJoined = \App\Models\Partisipasi::where('user_id', Auth::id())
                    ->where('kegiatan_id', $kegiatan->id)
                    ->exists();
                    @endphp

                    @auth
                    @if($isJoined)
                    {{-- JIKA SUDAH JOIN: Tampilkan Tombol Batal --}}
                    <div class="alert alert-success text-center py-2 mb-2 small">
                        <i class="fas fa-check-circle"></i> Anda terdaftar sebagai relawan.
                    </div>

                    {{-- HAPUS --}}
                    <form action="{{ route('partisipasi.leave') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">

                        <button type="submit" class="btn btn-outline-danger w-100 fw-bold py-2">
                            <i class="fas fa-times me-2"></i> BATALKAN PARTISIPASI
                        </button>
                    </form>

                    @else
                    {{-- JIKA BELUM JOIN: Tampilkan Tombol Daftar --}}
                    <form action="{{ route('partisipasi.join') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">

                        {{-- Disable tombol jika status bukan 'buka' --}}
                        @if($kegiatan->status == 'buka')
                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">
                            <i class="fas fa-hand-paper me-2"></i> GABUNG JADI RELAWAN
                        </button>
                        @else
                        <button type="button" class="btn btn-secondary w-100 fw-bold py-2" disabled>
                            PENDAFTARAN DITUTUP
                        </button>
                        @endif
                    </form>
                    @endif

                    @else
                    {{-- Kalau belum login --}}
                    <a href="{{ route('login') }}" class="btn btn-outline-dark w-100 fw-bold">
                        LOGIN UNTUK BERGABUNG
                    </a>
                    @endauth

                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS Tambahan Kecil --}}
@push('styles')
<style>
    .last-no-border:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
@endpush

@endsection