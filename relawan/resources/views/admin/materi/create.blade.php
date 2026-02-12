@extends('layouts.app')

@section('title', 'Upload Materi Baru')

@section('content')
<div class="container pb-5">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="fas fa-cloud-upload-alt me-2"></i> Upload Materi / Dokumen
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

    {{-- FORM UPLOAD --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2"></i> Form Data Materi</h6>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.materi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- 1. INPUT JUDUL --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Materi / Nama File</label>
                            <input type="text" name="judul" class="form-control form-control-lg"
                                placeholder="Contoh: Panduan Relawan 2024"
                                value="{{ old('judul') }}" required>
                        </div>

                        {{-- 2. INPUT DESKRIPSI --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Deskripsi Singkat (Opsional)</label>
                            <textarea name="deskripsi" class="form-control" rows="4"
                                placeholder="Jelaskan sedikit tentang isi file ini...">{{ old('deskripsi') }}</textarea>
                        </div>

                        {{-- 3. INPUT FILE (UNIVERSAL) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Konten Materi</label>
                            <div class="row g-3">
                                {{-- Opsi A: File --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-light border rounded h-100">
                                        <label class="small fw-bold mb-2"><i class="fas fa-file-upload me-1"></i> Upload File</label>
                                        <input type="file" name="file_materi" class="form-control form-control-sm">
                                        <div class="form-text mt-1" style="font-size: 0.75rem;">PDF, Office, Gambar, dll. (Max 20MB)</div>
                                    </div>
                                </div>
                                {{-- Opsi B: Link --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-light border rounded h-100">
                                        <label class="small fw-bold mb-2"><i class="fas fa-link me-1"></i> Link Eksternal</label>
                                        <input type="url" name="link" class="form-control form-control-sm" placeholder="https://youtube.com/..." value="{{ old('link') }}">
                                        <div class="form-text mt-1" style="font-size: 0.75rem;">Link YouTube, Drive, atau Website lain.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info py-2 px-3 mt-3 mb-0" style="font-size: 0.8rem;">
                                <i class="fas fa-info-circle me-1"></i> <strong>Tips:</strong> Anda bisa mengisi salah satu atau keduanya (File + Link pendukung).
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
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                <i class="fas fa-save me-2"></i> UPLOAD SEKARANG
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- CSS Tambahan untuk border putus-putus di area upload --}}
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