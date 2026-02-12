@extends('layouts.app')

@section('title', 'Daftar Kegiatan Relawan')

@section('content')
<div class="container pb-5">

    {{-- 1. Header Hero Section --}}
    <div class="bg-primary bg-gradient text-white rounded-3 p-4 mb-5 shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="mb-3 mb-md-0">
            <h2 class="fw-bold mb-1">Ayo Beraksi!</h2>
            <p class="mb-0 opacity-75">Temukan kegiatan sosial yang cocok dengan minat dan keahlianmu.</p>
        </div>

        <div class="d-flex align-items-center gap-3">
            {{-- Input Pencarian Frontend --}}
            <div class="input-group">
                <span class="input-group-text border-0 bg-white text-muted ps-3">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-0 py-2" placeholder="Cari kegiatan..." style="min-width: 250px;">
            </div>

            <i class="fas fa-hands-helping fa-3x opacity-50 d-none d-lg-block ms-2"></i>
        </div>
    </div>

    {{-- 2. Grid Kegiatan --}}
    <div class="row g-4" id="kegiatanGrid">
        @forelse($kegiatans as $kegiatan)
        <div class="col-md-6 col-lg-4 kegiatan-item">
            <div class="card h-100 shadow-sm border-0 hover-lift overflow-hidden">

                {{-- Gambar Banner & Badge Jenis --}}
                <div class="position-relative">
                    @if($kegiatan->banner_image)
                    <img src="{{ asset('storage/' . $kegiatan->banner_image) }}" class="card-img-top" alt="{{ $kegiatan->judul }}" style="height: 200px; object-fit: cover;">
                    @else
                    <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-image fa-3x text-secondary opacity-25"></i>
                    </div>
                    @endif

                    {{-- Badge Jenis Kegiatan --}}
                    <span class="position-absolute top-0 end-0 m-3 badge rounded-pill shadow-sm
                        {{ $kegiatan->detail_type == 'donasi_dana' ? 'bg-success' : '' }}
                        {{ $kegiatan->detail_type == 'donasi_darah' ? 'bg-danger' : '' }}
                        {{ $kegiatan->detail_type == 'mobil' ? 'bg-primary' : '' }}
                        {{ $kegiatan->detail_type == 'acara' ? 'bg-warning text-dark' : '' }}
                        {{ $kegiatan->detail_type == 'donasi_barang' ? 'bg-info' : '' }}
                        {{ $kegiatan->detail_type == 'peminjaman_barang' ? 'bg-secondary' : '' }}">

                        @if($kegiatan->detail_type == 'donasi_dana') <i class="fas fa-hand-holding-usd me-1"></i> Dana
                        @elseif($kegiatan->detail_type == 'donasi_darah') <i class="fas fa-tint me-1"></i> Darah
                        @elseif($kegiatan->detail_type == 'mobil') <i class="fas fa-truck me-1"></i> Transport
                        @elseif($kegiatan->detail_type == 'acara') <i class="fas fa-calendar-alt me-1"></i> Event
                        @elseif($kegiatan->detail_type == 'donasi_barang') <i class="fas fa-box-open me-1"></i> Barang
                        @elseif($kegiatan->detail_type == 'peminjaman_barang') <i class="fas fa-tools me-1"></i> Pinjam
                        @endif
                    </span>
                </div>

                <div class="card-body d-flex flex-column">
                    {{-- Judul --}}
                    <h5 class="card-title fw-bold text-dark mb-2 search-title">
                        <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="text-decoration-none text-dark stretched-link">
                            {{ Str::limit($kegiatan->judul, 50) }}
                        </a>
                    </h5>

                    <small class="text-muted mb-3 d-block">
                        <i class="far fa-clock me-1 text-primary"></i>
                        {{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d M Y, H:i') }}
                    </small>

                    {{-- Deskripsi --}}
                    <p class="card-text text-secondary small flex-grow-1 search-desc">
                        {{ Str::limit($kegiatan->deskripsi, 90) }}
                    </p>

                    {{-- Informasi Target Khusus --}}
                    <div class="bg-light rounded p-2 mb-3 small">
                        @if($kegiatan->detail_type == 'donasi_dana')
                        <div class="d-flex justify-content-between fw-bold text-success">
                            <span>Target:</span>
                            <span>Rp {{ number_format($kegiatan->detail->target_rupiah ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @elseif($kegiatan->detail_type == 'donasi_darah')
                        <div class="d-flex justify-content-between fw-bold text-danger">
                            <span>Butuh:</span>
                            <span>{{ $kegiatan->detail->target_kantong }} Kantong</span>
                        </div>
                        @elseif($kegiatan->detail_type == 'mobil')
                        <div class="d-flex justify-content-between fw-bold text-primary">
                            <span>Unit:</span>
                            <span>{{ $kegiatan->detail->jumlah_unit }} Kendaraan</span>
                        </div>
                        @elseif($kegiatan->detail_type == 'acara')
                        <div class="d-flex justify-content-between fw-bold text-warning-emphasis">
                            <span>Kuota:</span>
                            <span>{{ $kegiatan->detail->kuota_peserta }} Orang</span>
                        </div>
                        {{-- INFO BARANG & PINJAM --}}
                        @elseif($kegiatan->detail_type == 'donasi_barang')
                        <div class="d-flex justify-content-between fw-bold text-info">
                            <span>Butuh:</span>
                            <span>{{ $kegiatan->detail->target_item }} ({{ $kegiatan->detail->target_jumlah }})</span>
                        </div>
                        @elseif($kegiatan->detail_type == 'peminjaman_barang')
                        <div class="d-flex justify-content-between fw-bold text-secondary">
                            <span>Tersedia:</span>
                            <span>{{ $kegiatan->detail->nama_barang }} ({{ $kegiatan->detail->stok_tersedia }} Unit)</span>
                        </div>
                        @endif
                    </div>

                    <div class="d-grid">
                        <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="btn btn-outline-dark fw-bold btn-sm">
                            LIHAT DETAIL <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="mb-3">
                <i class="fas fa-box-open fa-4x text-muted opacity-25"></i>
            </div>
            <h4 class="text-muted">Belum ada kegiatan aktif saat ini.</h4>
            <p class="text-muted small">Cek lagi nanti ya!</p>
        </div>
        @endforelse
    </div>

    {{-- Pesan "Tidak Ditemukan" untuk Search --}}
    <div id="noResults" class="text-center py-5 d-none">
        <div class="mb-3 text-muted opacity-25">
            <i class="fas fa-search fa-4x"></i>
        </div>
        <h5 class="fw-bold text-muted">Tidak ditemukan</h5>
        <p class="text-muted small">Coba kata kunci lain.</p>
    </div>

    {{-- 3. Pagination --}}
    <div class="mt-5 d-flex justify-content-center">
        {{ $kegiatans->links() }}
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const items = document.querySelectorAll('.kegiatan-item');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            let hasResult = false;

            items.forEach(item => {
                const title = item.querySelector('.search-title').textContent.toLowerCase();
                const desc = item.querySelector('.search-desc').textContent.toLowerCase();

                if (title.includes(term) || desc.includes(term)) {
                    item.classList.remove('d-none');
                    hasResult = true;
                } else {
                    item.classList.add('d-none');
                }
            });

            if (hasResult) {
                noResults.classList.add('d-none');
            } else {
                noResults.classList.remove('d-none');
            }
        });
    });
</script>
@endpush
@endsection