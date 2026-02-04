@extends('layouts.app')

@section('title', 'Buat Notifikasi Baru')

@section('content')
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-9">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold"><i class="fas fa-bullhorn me-2"></i> Buat Notifikasi Baru</h3>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card shadow border-0">
                <div class="card-body p-4">

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.notifikasi.store') }}" method="POST">
                        @csrf

                        {{-- 1. JUDUL & PESAN --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Notifikasi</label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Perubahan Jadwal..." required value="{{ old('judul') }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Isi Pesan</label>
                            <textarea name="pesan" class="form-control" rows="3" placeholder="Deskripsi singkat..." required>{{ old('pesan') }}</textarea>
                        </div>

                        {{-- 2. TIPE NOTIFIKASI (RADIO WARNA) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold d-block">Tipe / Level Penting</label>
                            <div class="btn-group w-100" role="group">

                                <input type="radio" class="btn-check" name="type" id="type_info" value="info" checked>
                                <label class="btn btn-outline-primary" for="type_info">
                                    <i class="fas fa-info-circle me-1"></i> Info
                                </label>

                                <input type="radio" class="btn-check" name="type" id="type_success" value="success">
                                <label class="btn btn-outline-success" for="type_success">
                                    <i class="fas fa-check-circle me-1"></i> Sukses
                                </label>

                                <input type="radio" class="btn-check" name="type" id="type_warning" value="warning">
                                <label class="btn btn-outline-warning" for="type_warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Peringatan
                                </label>

                                <input type="radio" class="btn-check" name="type" id="type_danger" value="danger">
                                <label class="btn btn-outline-danger" for="type_danger">
                                    <i class="fas fa-times-circle me-1"></i> Darurat
                                </label>
                            </div>
                        </div>

                        {{-- 3. TARGET AUDIENCE (RADIO & CHECKBOX GRID) --}}
                        <div class="mb-4 bg-light p-4 rounded border">
                            <label class="form-label fw-bold mb-3">Target Penerima</label>

                            {{-- Pilihan Target Utama --}}
                            <div class="d-flex gap-4 mb-4 border-bottom pb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_audience" id="target_all" value="all" checked onchange="toggleTarget()">
                                    <label class="form-check-label fw-bold cursor-pointer" for="target_all">Semua User (Broadcast)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_audience" id="target_kegiatan" value="kegiatan" onchange="toggleTarget()">
                                    <label class="form-check-label fw-bold cursor-pointer" for="target_kegiatan">Peserta Kegiatan Tertentu</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_audience" id="target_tag" value="tag" onchange="toggleTarget()">
                                    <label class="form-check-label fw-bold cursor-pointer" for="target_tag">Peminat Tag Tertentu</label>
                                </div>
                            </div>

                            {{-- WRAPPER KEGIATAN (GRID CHECKBOX - CENTERED STYLE) --}}
                            <div id="wrapper_kegiatan" class="d-none">
                                <label class="small text-muted mb-2 fw-bold d-block">Pilih Kegiatan:</label>

                                <div style="max-height: 300px; overflow-y: auto; overflow-x: hidden; padding: 2px;">
                                    <div class="row g-2">
                                        @foreach($kegiatans as $keg)
                                        <div class="col-md-6">
                                            <label class="cursor-pointer d-block h-100">
                                                <input type="checkbox" name="kegiatan_ids[]" value="{{ $keg->id }}"
                                                    class="item-checkbox position-absolute opacity-0">

                                                {{--
                            PERUBAHAN STYLE: 
                            1. text-center: Biar tulisan di tengah
                            2. justify-content-center: Konten vertikal di tengah
                            3. position-relative: Supaya icon check bisa ditaruh di pojok (absolute)
                        --}}
                                                <div class="card h-100 py-3 px-2 shadow-sm border item-content position-relative d-flex flex-column align-items-center justify-content-center text-center">

                                                    {{-- Icon Check (Melayang di Pojok Kanan Atas) --}}
                                                    <div class="position-absolute top-0 end-0 mt-2 me-2 opacity-0 check-icon transition-all">
                                                        <i class="fas fa-check-circle text-primary fa-lg"></i>
                                                    </div>

                                                    {{-- Judul Kegiatan --}}
                                                    <span class="fw-bold text-dark lh-sm mb-2" style="font-size: 0.9rem;">
                                                        {{ $keg->judul }}
                                                    </span>

                                                    {{-- Badge Status (Langsung dibawah nama) --}}
                                                    @php
                                                    $statusColor = match($keg->status) {
                                                    'buka' => 'bg-success',
                                                    'tutup' => 'bg-secondary',
                                                    'selesai' => 'bg-dark',
                                                    default => 'bg-secondary'
                                                    };
                                                    @endphp
                                                    <span class="badge {{ $statusColor }} rounded-pill px-3" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                                        {{ strtoupper($keg->status) }}
                                                    </span>

                                                </div>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            {{-- WRAPPER TAG (GRID CHECKBOX) --}}
                            <div id="wrapper_tag" class="d-none">
                                <label class="small text-muted mb-2 fw-bold d-block">Pilih Tag Minat (Bisa pilih lebih dari satu):</label>

                                <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                                    <div class="row g-2">
                                        @foreach($tags as $tag)
                                        <div class="col-md-4 col-sm-6">
                                            <label class="cursor-pointer d-block h-100">
                                                <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                                                    class="item-checkbox position-absolute opacity-0">

                                                <div class="card h-100 px-3 py-2 shadow-sm border item-content d-flex align-items-center justify-content-between">
                                                    <span class="fw-bold text-dark text-truncate small">{{ $tag->nama_tag }}</span>
                                                    <i class="fas fa-check-circle text-primary opacity-0 check-icon"></i>
                                                </div>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 4. EXPIRED DATE --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Waktu Kadaluarsa</label>
                            <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                            <small class="text-muted">Biarkan kosong jika notifikasi berlaku selamanya.</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark fw-bold py-2">
                                <i class="fas fa-paper-plane me-2"></i> KIRIM NOTIFIKASI
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleTarget() {
        const target = document.querySelector('input[name="target_audience"]:checked').value;
        const wrapperKegiatan = document.getElementById('wrapper_kegiatan');
        const wrapperTag = document.getElementById('wrapper_tag');

        // Reset display
        wrapperKegiatan.classList.add('d-none');
        wrapperTag.classList.add('d-none');

        // Reset selections (opsional, biar bersih kalau ganti target)
        // document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);

        // Show based on selection
        if (target === 'kegiatan') {
            wrapperKegiatan.classList.remove('d-none');
        } else if (target === 'tag') {
            wrapperTag.classList.remove('d-none');
        }
    }
</script>
@endpush

@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }

    /* Style untuk Card Checkbox */
    .item-content {
        background-color: #fff;
        border: 1px solid #dee2e6 !important;
        transition: all 0.15s ease-in-out;
    }

    .cursor-pointer:hover .item-content {
        background-color: #f8f9fa;
        border-color: #adb5bd !important;
        transform: translateY(-1px);
    }

    /* State Checked */
    .item-checkbox:checked+.item-content {
        border-color: #0d6efd !important;
        background-color: #f0f7ff !important;
        /* Biru sangat muda */
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.1) !important;
    }

    .item-checkbox:checked+.item-content .text-dark {
        color: #0d6efd !important;
    }

    .item-checkbox:checked+.item-content .check-icon {
        opacity: 1 !important;
    }
</style>
@endpush

@endsection