@extends('layouts.app')

@section('title', 'Kelola Materi & Dokumen')

@section('content')
<div class="container-fluid px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center my-4">

        {{-- Kiri: Judul & Tombol Kembali --}}
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm" title="Kembali ke Dashboard">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h3 class="fw-bold text-dark mb-0">
                <i class="fas fa-folder-open me-2"></i> Daftar Materi
            </h3>
        </div>

        {{-- Kanan: Tombol Upload --}}
        <a href="{{ route('admin.materi.create') }}" class="btn btn-dark fw-bold">
            <i class="fas fa-cloud-upload-alt me-1"></i> Upload Baru
        </a>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- TABEL DATA --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-dark">Data Dokumen Tersimpan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    {{-- HEADER TABEL --}}
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="py-3 ps-4" width="5%">No</th>
                            <th width="10%">Tipe File</th>
                            <th width="40%">Informasi Materi</th>
                            <th width="20%">Tanggal Upload</th>
                            <th class="text-end pe-4" width="15%">Aksi</th>
                        </tr>
                    </thead>

                    {{-- BODY TABEL --}}
                    <tbody>
                        @forelse($materis as $index => $materi)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $index + 1 }}</td>

                            {{-- KOLOM TIPE FILE (IKON) --}}
                            <td class="text-center">
                                @php
                                $ext = strtolower(pathinfo($materi->file_path, PATHINFO_EXTENSION));
                                $icon = 'fa-file';
                                $color = 'text-secondary';

                                if(in_array($ext, ['pdf'])) { $icon = 'fa-file-pdf'; $color = 'text-danger'; }
                                elseif(in_array($ext, ['doc', 'docx'])) { $icon = 'fa-file-word'; $color = 'text-primary'; }
                                elseif(in_array($ext, ['xls', 'xlsx'])) { $icon = 'fa-file-excel'; $color = 'text-success'; }
                                elseif(in_array($ext, ['ppt', 'pptx'])) { $icon = 'fa-file-powerpoint'; $color = 'text-warning'; }
                                elseif(in_array($ext, ['jpg', 'jpeg', 'png'])) { $icon = 'fa-image'; $color = 'text-info'; }
                                @endphp
                                {{-- Arahkan ke SHOW (Smart Preview) --}}
                                <a href="{{ route('materi.show', $materi->id) }}" class="text-decoration-none" title="Lihat Preview">
                                    <i class="fas {{ $icon }} fa-2x {{ $color }}"></i>
                                    <div class="small text-muted mt-1 text-uppercase" style="font-size: 0.65rem;">{{ $ext }}</div>
                                </a>
                            </td>

                            {{-- KOLOM JUDUL & DESKRIPSI --}}
                            <td>
                                {{-- Judul bisa diklik ke preview --}}
                                <a href="{{ route('materi.show', $materi->id) }}" class="fw-bold text-dark text-decoration-none mb-1 d-block hover-underline">
                                    {{ $materi->judul }}
                                </a>
                                @if($materi->deskripsi)
                                <small class="text-muted d-block text-truncate-2" style="line-height: 1.2;">
                                    {{ Str::limit($materi->deskripsi, 80) }}
                                </small>
                                @else
                                <small class="text-muted fst-italic">- Tidak ada deskripsi -</small>
                                @endif
                            </td>

                            {{-- KOLOM TANGGAL --}}
                            <td>
                                <div class="small">
                                    <i class="fas fa-calendar-alt me-1 text-secondary"></i>
                                    {{ $materi->created_at->format('d M Y') }}
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-clock me-1 text-secondary"></i>
                                    {{ $materi->created_at->format('H:i') }} WIB
                                </div>
                            </td>

                            {{-- KOLOM AKSI --}}
                            <td class="text-end pe-4">
                                <div class="btn-group" role="group">
                                    {{-- Tombol Preview (Mata) --}}
                                    <a href="{{ route('materi.show', $materi->id) }}" class="btn btn-sm btn-outline-primary" title="Preview">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('admin.materi.edit', $materi->id) }}" class="btn btn-sm btn-outline-dark" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    {{-- Tombol Delete (Pakai Form) --}}
                                    <form action="{{ route('admin.materi.destroy', $materi->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus file ini? File fisik juga akan dihapus dari server.');"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="opacity-50 mb-3">
                                    <i class="fas fa-folder-open fa-3x"></i>
                                </div>
                                <h6 class="text-muted fw-bold">Belum ada materi yang diupload.</h6>
                                <a href="{{ route('admin.materi.create') }}" class="btn btn-sm btn-primary mt-2">
                                    Upload Sekarang
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-underline:hover {
        text-decoration: underline !important;
    }
</style>
@endpush

@endsection