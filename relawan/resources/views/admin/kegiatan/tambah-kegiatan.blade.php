@extends('layouts.app')

@section('title', 'Tambah Kegiatan Baru')

@section('content')
<div class="container pb-5">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="fas fa-plus-circle me-2"></i>
            Tambah: {{ ucwords(str_replace('_', ' ', $jenis)) }}
        </h3>
        <a href="{{ route('admin.kegiatan.pilih') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Ganti Jenis
        </a>
    </div>

    {{-- ERROR VALIDATION --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="jenis_kegiatan" value="{{ $jenis }}">

        <div class="row">
            {{-- KOLOM KIRI (DATA UMUM) --}}
            <div class="col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold py-3">Data Umum Kegiatan</div>
                    <div class="card-body">
                        {{-- INPUT JUDUL, DESKRIPSI, TANGGAL, BANNER --}}
                        <div class="mb-3">
                            <label class="form-label">Judul Kegiatan</label>
                            <input type="text" name="judul" class="form-control" required value="{{ old('judul') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Lengkap</label>
                            <textarea name="deskripsi" class="form-control" rows="5" required>{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" name="tanggal_mulai" class="form-control" required value="{{ old('tanggal_mulai') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="datetime-local" name="tanggal_selesai" class="form-control" required value="{{ old('tanggal_selesai') }}">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <hr class="my-2 text-muted opacity-25">
                                <label class="form-label fw-bold text-primary small text-uppercase">Waktu Pelaksanaan Acara (Info)</label>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Acara Mulai</label>
                                <input type="datetime-local" name="tanggal_mulai_acara" class="form-control" required value="{{ old('tanggal_mulai_acara') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Acara Selesai</label>
                                <input type="datetime-local" name="tanggal_selesai_acara" class="form-control" required value="{{ old('tanggal_selesai_acara') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Banner Gambar (Opsional)</label>
                            <input type="file" name="banner_image" class="form-control" accept="image/*">
                        </div>

                        {{-- PILIH TAG --}}
                        <div class="mt-4 pt-3 border-top">
                            <label class="form-label fw-bold"><i class="fas fa-tags me-1"></i> Kategori / Tag</label>
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <div style="max-height: 200px; overflow-y: auto;">
                                        <div class="row g-2">
                                            @foreach($tags as $tag)
                                            <div class="col-md-4 col-sm-6">
                                                <label class="cursor-pointer d-block h-100">
                                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                                        class="tag-checkbox position-absolute opacity-0"
                                                        {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>

                                                    <div class="card h-100 px-2 py-2 shadow-sm border tag-content d-flex align-items-center justify-content-between">
                                                        <span class="small fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ $tag->nama_tag }}</span>
                                                        <i class="fas fa-check-circle text-primary opacity-0 check-icon"></i>
                                                    </div>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <small class="text-muted mt-2 d-block fst-italic">* Pilih tag yang sesuai agar mudah ditemukan relawan.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (DETAIL KHUSUS) --}}
            <div class="col-md-5">
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning bg-opacity-10 fw-bold py-3 text-warning-emphasis">
                        Detail Khusus: {{ ucwords(str_replace('_', ' ', $jenis)) }}
                    </div>
                    <div class="card-body">

                        {{-- 1. DONASI DANA --}}
                        @if($jenis == 'donasi_dana')
                        <div class="mb-3">
                            <label class="form-label">Target Rupiah (Rp)</label>
                            <input type="number" name="target_rupiah" class="form-control" value="{{ old('target_rupiah') }}">
                        </div>
                        <label class="form-label fw-bold">Informasi Rekening Bank</label>
                        <div id="bank-container">
                            <div class="card bg-light p-2 mb-2 bank-row">
                                <input type="text" name="info_bank[0][nama_bank]" class="form-control mb-1 form-control-sm" placeholder="Nama Bank" required>
                                <input type="text" name="info_bank[0][no_rekening]" class="form-control mb-1 form-control-sm" placeholder="No. Rekening" required>
                                <input type="text" name="info_bank[0][atas_nama]" class="form-control form-control-sm" placeholder="Atas Nama" required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="tambahBank()">Tambah Rekening Lain</button>

                        {{-- 2. DONOR DARAH --}}
                        @elseif($jenis == 'donasi_darah')
                        <div class="mb-3">
                            <label class="form-label">Target Kantong</label>
                            <input type="number" name="target_kantong" class="form-control" required value="{{ old('target_kantong') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi PMI</label>
                            <input type="text" name="lokasi_pmi" class="form-control" required value="{{ old('lokasi_pmi') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Golongan Darah Dibutuhkan</label>
                            @php $daftarDarah = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']; @endphp
                            <div class="row g-2">
                                @foreach($daftarDarah as $goldar)
                                <div class="col-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="golongan_darah_needed[]" value="{{ $goldar }}" id="gd_{{ $goldar }}">
                                        <label class="form-check-label" for="gd_{{ $goldar }}">{{ $goldar }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 3. MOBIL --}}
                        @elseif($jenis == 'mobil')
                        <div class="mb-3">
                            <label class="form-label">Jumlah Unit</label>
                            <input type="number" name="jumlah_unit" class="form-control" required value="{{ old('jumlah_unit') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi Jemput</label>
                            <textarea name="lokasi_jemput" class="form-control" rows="2" required>{{ old('lokasi_jemput') }}</textarea>
                        </div>
                        <div class="form-check form-switch p-3 bg-light rounded border">
                            <input class="form-check-input" type="checkbox" name="butuh_supir" id="butuh_supir" value="1">
                            <label class="form-check-label fw-bold" for="butuh_supir">Butuh Supir?</label>
                        </div>

                        {{-- 4. ACARA --}}
                        @elseif($jenis == 'acara')
                        <div class="mb-3">
                            <label class="form-label">Lokasi Acara</label>
                            <textarea name="lokasi" class="form-control" rows="2" required>{{ old('lokasi') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kuota Peserta</label>
                            <input type="number" name="kuota_peserta" class="form-control" required value="{{ old('kuota_peserta') }}">
                        </div>

                        {{-- 5. DONASI BARANG (BARU) --}}
                        @elseif($jenis == 'donasi_barang')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Item / Barang</label>
                            <input type="text" name="target_item" class="form-control" placeholder="Contoh: Beras, Selimut, Pakaian" required value="{{ old('target_item') }}">
                            <div class="form-text small">Barang utama yang ingin dikumpulkan.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Jumlah</label>
                            <div class="input-group">
                                <input type="number" name="target_jumlah" class="form-control" placeholder="0" required value="{{ old('target_jumlah') }}">
                                <span class="input-group-text bg-light">Pcs / Kg / Paket</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi Pengumpulan</label>
                            <textarea name="lokasi_kumpul" class="form-control" rows="3" placeholder="Alamat posko pengumpulan..." required>{{ old('lokasi_kumpul') }}</textarea>
                        </div>

                        {{-- 6. PEMINJAMAN BARANG (BARU) --}}
                        @elseif($jenis == 'peminjaman_barang')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Tenda, Kursi, Sound System" required value="{{ old('nama_barang') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Stok Tersedia</label>
                            <input type="number" name="stok_tersedia" class="form-control" placeholder="0" required value="{{ old('stok_tersedia') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Persyaratan Peminjaman</label>
                            <textarea name="persyaratan" class="form-control" rows="3" placeholder="Contoh: Wajib KTP, Deposit uang, dll.">{{ old('persyaratan') }}</textarea>
                        </div>

                        @endif
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-dark btn-lg fw-bold">
                        <i class="fas fa-save me-2"></i> SIMPAN KEGIATAN
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- SCRIPT BANK --}}
@if($jenis == 'donasi_dana')
<script>
    let bankCount = 1;

    function tambahBank() {
        const container = document.getElementById('bank-container');
        const html = `<div class="card bg-light p-2 mb-2 bank-row position-relative"><button type="button" class="btn-close position-absolute top-0 end-0 m-1" onclick="this.parentElement.remove()"></button><input type="text" name="info_bank[${bankCount}][nama_bank]" class="form-control mb-1 form-control-sm" placeholder="Nama Bank" required><input type="text" name="info_bank[${bankCount}][no_rekening]" class="form-control mb-1 form-control-sm" placeholder="No. Rekening" required><input type="text" name="info_bank[${bankCount}][atas_nama]" class="form-control form-control-sm" placeholder="Atas Nama" required></div>`;
        container.insertAdjacentHTML('beforeend', html);
        bankCount++;
    }
</script>
@endif

{{-- CSS KHUSUS TAG --}}
@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .tag-content {
        background-color: #fff;
        border: 1px solid #dee2e6 !important;
        transition: all 0.2s;
    }

    .cursor-pointer:hover .tag-content {
        background-color: #f8f9fa;
        border-color: #adb5bd !important;
    }

    .tag-checkbox:checked+.tag-content {
        border-color: #0d6efd !important;
        background-color: #f0f7ff !important;
    }

    .tag-checkbox:checked+.tag-content .text-dark {
        color: #0d6efd !important;
    }

    .tag-checkbox:checked+.tag-content .check-icon {
        opacity: 1 !important;
    }
</style>
@endpush

@endsection