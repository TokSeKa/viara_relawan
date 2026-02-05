@extends('layouts.app')

@section('title', 'Edit Materi')

@section('content')
<div class="container pb-5">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="fas fa-edit me-2"></i> Edit Materi
        </h3>
        <a href="{{ route('admin.materi.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- ALERT ERROR --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
    @endif

    {{-- FORM EDIT --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning bg-opacity-10 py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-pencil-alt me-2"></i> Form Perubahan Data</h6>
                </div>

                <div class="card-body p-4">
                    {{-- Perhatikan route update membutuhkan ID --}}
                    <form action="{{ route('admin.materi.update', $materi->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- Wajib untuk Update di Laravel --}}

                        {{-- 1. INPUT JUDUL (Pre-filled) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Materi</label>
                            {{-- old('judul', $materi->judul) artinya: Kalau ada error validasi pake old, kalau baru buka pake data db --}}
                            <input type="text" name="judul" class="form-control form-control-lg"
                                value="{{ old('judul', $materi->judul) }}" required>
                        </div>

                        {{-- 2. INPUT DESKRIPSI (Pre-filled) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                        </div>

                        {{-- 3. INFO FILE SAAT INI --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">File Saat Ini</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded border">
                                <i class="fas fa-file-check text-success fa-2x me-3"></i>
                                <div class="flex-grow-1 text-truncate">
                                    {{-- Menampilkan nama file asli (basename mengambil nama file dari path panjang) --}}
                                    <span class="fw-bold text-dark">{{ basename($materi->file_path) }}</span>
                                    <br>
                                    <small class="text-muted">
                                        {{ strtoupper(pathinfo($materi->file_path, PATHINFO_EXTENSION)) }} File
                                    </small>
                                </div>
                                {{-- Gunakan route 'materi.show' agar masuk ke halaman Smart Preview --}}
                                <a href="{{ route('materi.show', $materi->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Lihat Preview">
                                    <i class="fas fa-eye me-1"></i> Preview
                                </a>
                            </div>
                        </div>

                        {{-- 4. INPUT FILE BARU (OPSIONAL) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Ganti File (Opsional)</label>
                            <div class="p-4 bg-white border border-dashed rounded text-center">
                                <i class="fas fa-exchange-alt fa-2x text-muted mb-3"></i>

                                {{-- Tidak perlu required, karena kalau kosong berarti gak ganti file --}}
                                <input type="file" name="file_materi" class="form-control">

                                <div class="form-text mt-2 text-muted">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        Biarkan kosong jika tidak ingin mengubah file materi.
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- --- PILIH TAG --- --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fas fa-tags me-1"></i> Label / Tag Materi</label>
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <div style="max-height: 150px; overflow-y: auto;">
                                        <div class="row g-2">
                                            @foreach($tags as $tag)
                                            <div class="col-md-4 col-sm-6">
                                                <label class="cursor-pointer d-block h-100">
                                                    {{-- Logic Checkbox: 
                                 - Create: Cek old('tags')
                                 - Edit: Cek connectedTagIds (jika ada) ATAU old('tags') 
                            --}}
                                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                                        class="tag-checkbox position-absolute opacity-0"
                                                        {{ (is_array(old('tags')) && in_array($tag->id, old('tags'))) ? 'checked' : '' }}
                                                        {{ (isset($connectedTagIds) && in_array($tag->id, $connectedTagIds)) ? 'checked' : '' }}>

                                                    <div class="card h-100 px-2 py-2 shadow-sm border tag-content d-flex align-items-center justify-content-between">
                                                        <span class="small fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ $tag->nama_tag }}</span>
                                                        <i class="fas fa-check-circle text-primary opacity-0 check-icon"></i>
                                                    </div>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-text text-muted small mt-2">
                                        <i class="fas fa-info-circle me-1"></i> Pilih tag agar materi mudah ditemukan relawan.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL SIMPAN --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold">
                                <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

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

    /* State Checked */
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

    .border-dashed {
        border-style: dashed !important;
        border-width: 2px !important;
    }
</style>
@endpush

@endsection