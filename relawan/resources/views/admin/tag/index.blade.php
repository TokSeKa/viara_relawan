@extends('layouts.app')

@section('title', 'Daftar Tag')

@section('content')
<div class="container pb-5">

    {{-- HEADER: Judul & Tombol Tambah --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-dark">
            <i class="fas fa-tags me-2"></i> Manajemen Tag
        </h3>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-dark fw-bold">
            <i class="fas fa-plus me-1"></i> Tambah Tag
        </a>
    </div>

    {{-- ALERT SUKSES (Jika ada session success dari Create/Delete) --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- ALERT ERROR (Untuk Gagal Hapus) --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- TABEL DATA --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light text-dark border-bottom">
                        <tr>
                            <th class="py-3 ps-4" width="5%">No</th>
                            <th class="py-3" width="25%">Nama Tag</th>
                            <th class="py-3" width="35%">Deskripsi</th>
                            <th class="py-3 text-center" width="15%">Visibilitas</th>
                            <th class="py-3 text-end pe-4" width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tags as $index => $tag)
                        <tr>
                            {{-- Nomor Urut (sesuai pagination) --}}
                            <td class="ps-4">{{ $tags->firstItem() + $index }}</td>

                            {{-- Nama Tag --}}
                            <td>
                                <span class="fw-bold text-dark">{{ $tag->nama_tag }}</span>
                                <br>
                                <small class="text-muted fst-italic">Slug: {{ $tag->slug }}</small>
                            </td>

                            {{-- Deskripsi --}}
                            <td class="text-secondary">
                                {{ Str::limit($tag->deskripsi, 60, '...') ?: '-' }}
                            </td>

                            {{-- Visibilitas Badge --}}
                            <td class="text-center">
                                @if($tag->is_admin_only)
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2">
                                    <i class="fas fa-lock me-1"></i> Admin Only
                                </span>
                                @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2">
                                    <i class="fas fa-globe me-1"></i> Publik
                                </span>
                                @endif
                            </td>

                            {{-- Tombol Aksi --}}
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    {{-- Edit (Pastikan route edit ada) --}}
                                    <a href="{{ route('admin.tags.edit', $tag->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    {{-- Delete Form --}}
                                    <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tag ini?');" class="d-inline">
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
                        {{-- Tampilan Kosong --}}
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-bold">Belum ada tag tersedia.</p>
                                    <small>Silakan buat tag baru untuk memulai.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="card-footer bg-white py-3">
                {{ $tags->links() }}
            </div>
        </div>
    </div>
</div>
@endsection