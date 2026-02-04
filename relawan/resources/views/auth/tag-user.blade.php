@extends('layouts.app')

@section('title', 'Atur Minat Saya')

@section('content')
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold text-dark mb-1">Apa yang Anda minati?</h5>
                    <p class="text-muted small mb-0">Pilih topik agar kami bisa merekomendasikan kegiatan yang tepat.</p>
                </div>


                <form action="{{ route('user.tags.update') }}" method="POST">
                    @csrf

                    <div class="card-body p-0">
                        {{--
                             WRAPPER SCROLLABLE 
                             Ini kuncinya: max-height membuat dia tidak memanjang ke bawah,
                             overflow-y-auto memunculkan scrollbar jika kontennya banyak.
                        --}}
                        <div class="p-4" style="max-height: 50vh; overflow-y: auto;">

                            @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif

                            {{-- Grid jadi 3 kolom (col-md-4) biar lebih padat --}}
                            <div class="row g-3">
                                @forelse($tags as $tag)
                                <div class="col-md-4 col-sm-6">
                                    <label class="tag-card d-block cursor-pointer position-relative h-100">
                                        <input type="checkbox"
                                            name="tags[]"
                                            value="{{ $tag->id }}"
                                            class="tag-checkbox position-absolute opacity-0"
                                            {{ in_array($tag->id, $userTagIds) ? 'checked' : '' }}>

                                        <div class="card h-100 p-3 shadow-sm border transition-all tag-content">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="text-truncate">
                                                    <span class="fw-bold d-block text-dark small text-truncate">{{ $tag->nama_tag }}</span>
                                                </div>
                                                <div class="check-icon text-primary opacity-0 ms-2">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @empty
                                <div class="col-12 text-center py-5">
                                    <p class="text-muted small">Belum ada topik minat tersedia.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER YANG STICKY / TETAP --}}
                    <div class="card-footer bg-light py-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">

                            {{-- Tombol Kembali --}}
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary fw-bold px-4">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>

                            {{-- Tombol Simpan --}}
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                            </button>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .transition-all {
        transition: all 0.2s ease-in-out;
    }

    /* Custom Scrollbar biar cantik */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }

    /* Tag Style */
    .tag-content {
        background-color: #fff;
        border: 1px solid #e9ecef !important;
        /* Border lebih tipis */
    }

    .tag-card:hover .tag-content {
        border-color: #dee2e6 !important;
        background-color: #f8f9fa;
        transform: translateY(-2px);
    }

    .tag-checkbox:checked+.tag-content {
        border-color: #0d6efd !important;
        background-color: #f0f7ff;
        box-shadow: 0 4px 6px rgba(13, 110, 253, 0.15) !important;
    }

    .tag-checkbox:checked+.tag-content .check-icon {
        opacity: 1 !important;
    }
</style>
@endpush

@endsection