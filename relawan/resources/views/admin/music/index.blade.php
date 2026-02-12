@extends('layouts.app')

@section('title', 'Kelola Musik Latar')

@section('content')
<div class="container-fluid px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center my-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h3 class="fw-bold text-dark mb-0">
                <i class="fas fa-music me-2"></i> Musik Latar Web
            </h3>
        </div>

        {{-- Tombol Tambah (Memicu Collapse Form) --}}
        <button class="btn btn-dark fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUpload">
            <i class="fas fa-plus me-1"></i> Tambah Musik
        </button>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- 1. FORM UPLOAD (Hidden by Default) --}}
    <div class="collapse mb-4 {{ $errors->any() ? 'show' : '' }}" id="collapseUpload">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold">Upload File Musik Baru</div>
            <div class="card-body p-4">
                <form action="{{ route('admin.music.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Judul Musik</label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Instrumen Kedamaian" required value="{{ old('judul') }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">File Audio (MP3/WAV)</label>
                            <input type="file" name="file_musik" class="form-control" accept="audio/*" required>
                            <div class="form-text small">Maksimal 10MB.</div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="fas fa-cloud-upload-alt me-1"></i> UPLOAD & AKTIFKAN
                            </button>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi / Catatan (Opsional)</label>
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Musik untuk suasana tenang...">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. TABEL DAFTAR MUSIK --}}
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4" width="5%">No</th>
                            <th width="30%">Informasi Musik</th>
                            <th width="25%">Preview</th>
                            <th class="text-center" width="15%">Status</th>
                            <th class="text-end pe-4" width="25%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($musics as $index => $music)
                        <tr class="{{ $music->is_active ? 'table-warning border-warning' : '' }}">
                            <td class="ps-4 fw-bold">{{ $index + 1 }}</td>

                            <td>
                                <div class="fw-bold text-dark">{{ $music->judul }}</div>
                                <small class="text-muted text-truncate d-block" style="max-width: 250px;">
                                    {{ $music->deskripsi ?? 'Tidak ada deskripsi' }}
                                </small>
                            </td>

                            <td>
                                <audio controls class="w-100" style="height: 30px;">
                                    <source src="{{ asset('storage/' . $music->file_path) }}" type="audio/mpeg">
                                </audio>
                            </td>

                            <td class="text-center">
                                @if($music->is_active)
                                <span class="badge bg-success rounded-pill px-3">
                                    <i class="fas fa-play-circle me-1"></i> AKTIF
                                </span>
                                @else
                                <span class="badge bg-secondary rounded-pill px-3 opacity-50">OFF</span>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm">
                                    {{-- Tombol Aktifkan --}}
                                    @if(!$music->is_active)
                                    <form action="{{ route('admin.music.activate', $music->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-primary fw-bold" title="Putar Musik Ini">
                                            <i class="fas fa-check me-1"></i> Aktifkan
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-sm btn-success fw-bold" disabled>
                                        <i class="fas fa-volume-up me-1"></i> Sedang Diputar
                                    </button>
                                    @endif

                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.music.destroy', $music->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus file musik ini?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-music fa-3x mb-3 opacity-25"></i>
                                <p>Belum ada koleksi musik.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- INFO LIMIT --}}
    <div class="mt-3 small text-muted">
        <i class="fas fa-info-circle me-1"></i>
        Saran: Batasi maksimal 12 musik agar server tidak penuh. Hanya 1 musik yang bisa aktif dalam satu waktu.
    </div>
</div>
@endsection