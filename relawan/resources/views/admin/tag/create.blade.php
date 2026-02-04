@extends('layouts.app')

@section('title', 'Tambah Tag Baru')

@section('content')
<div class="container pb-5">

    {{-- HEADER: Judul & Tombol Kembali --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-tags me-2"></i> Buat Tag Baru
        </h3>
        {{-- Asumsi kamu punya route index tag, jika belum ada bisa dikosongkan dulu href-nya --}}
        <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- ALERT ERROR (Jika Validasi Gagal) --}}
    @if ($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- FORM SECTION --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">Formulir Tag</h6>
                </div>

                <div class="card-body p-4">
                    {{-- Pastikan route 'admin.tags.store' sudah dibuat di web.php --}}
                    <form action="{{ route('admin.tags.store') }}" method="POST">
                        @csrf

                        {{-- 1. NAMA TAG --}}
                        <div class="mb-4">
                            <label for="nama_tag" class="form-label fw-bold">Nama Tag <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control @error('nama_tag') is-invalid @enderror"
                                id="nama_tag"
                                name="nama_tag"
                                value="{{ old('nama_tag') }}"
                                placeholder="Contoh: Mendesak, Bencana Alam, Edukasi"
                                required autofocus>
                            @error('nama_tag')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. DESKRIPSI --}}
                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-bold">Deskripsi (Opsional)</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                id="deskripsi"
                                name="deskripsi"
                                rows="3"
                                placeholder="Penjelasan singkat tentang kegunaan tag ini...">{{ old('deskripsi') }}</textarea>
                        </div>

                        {{-- 3. VISIBILITAS ADMIN (CHECKBOX/SWITCH) --}}
                        <div class="mb-4">
                            <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-check-label fw-bold mb-0 text-dark" for="is_admin_only">
                                        Khusus Admin?
                                    </label>
                                    <small class="d-block text-muted">
                                        Jika aktif, tag ini tidak akan terlihat oleh relawan biasa.
                                    </small>
                                </div>
                                <div class="form-check form-switch">
                                    {{-- Value 1 dikirim jika dicentang --}}
                                    <input class="form-check-input fs-4"
                                        type="checkbox"
                                        name="is_admin_only"
                                        value="1"
                                        id="is_admin_only"
                                        {{ old('is_admin_only') ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL SIMPAN --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary fw-bold py-2">
                                <i class="fas fa-save me-2"></i> SIMPAN TAG
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection