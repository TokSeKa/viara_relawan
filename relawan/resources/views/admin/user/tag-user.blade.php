@extends('layouts.app')

@section('title', 'Kelola Tag User')

@section('content')
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">
                    Kelola Tag: <span class="text-primary">{{ $user->name }}</span>
                </h4>
                <a href="{{ route('admin.users.show_admin_user', $user->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-1"></i> Batal
                </a>
            </div>

            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Tag bertanda <span class="text-danger fw-bold"><i class="fas fa-lock"></i></span> adalah tag internal/khusus admin.
                    </p>
                </div>

                <form action="{{ route('admin.users.tags.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-0">
                        {{-- Wrapper Scrollable --}}
                        <div class="p-4" style="max-height: 65vh; overflow-y: auto;">

                            <div class="row g-3">
                                @foreach($tags as $tag)
                                <div class="col-md-4 col-sm-6">
                                    <label class="tag-card d-block cursor-pointer position-relative h-100">
                                        <input type="checkbox"
                                            name="tags[]"
                                            value="{{ $tag->id }}"
                                            class="tag-checkbox position-absolute opacity-0"
                                            {{ in_array($tag->id, $userTagIds) ? 'checked' : '' }}>

                                        {{-- Style Card Berbeda jika Admin Only --}}
                                        <div class="card h-100 p-3 shadow-sm transition-all tag-content 
                                                {{ $tag->is_admin_only ? 'border-danger border-opacity-25 bg-danger bg-opacity-10' : 'border' }}">

                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="text-truncate">
                                                    @if($tag->is_admin_only)
                                                    <i class="fas fa-lock text-danger me-1" title="Khusus Admin"></i>
                                                    @endif
                                                    <span class="fw-bold d-block text-dark small text-truncate">
                                                        {{ $tag->nama_tag }}
                                                    </span>
                                                </div>

                                                {{-- Icon Check --}}
                                                <div class="check-icon opacity-0 ms-2 {{ $tag->is_admin_only ? 'text-danger' : 'text-primary' }}">
                                                    <i class="fas fa-check-circle fa-lg"></i>
                                                </div>
                                            </div>

                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <div class="card-footer bg-light py-3 border-top d-flex justify-content-between">
                        <span class="text-muted small align-self-center">Perubahan akan langsung diterapkan ke user.</span>
                        <button type="submit" class="btn btn-dark fw-bold px-4">
                            <i class="fas fa-save me-2"></i> SIMPAN TAG
                        </button>
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

    /* Scrollbar */
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

    /* Tag Style Normal */
    .tag-content {
        background-color: #fff;
        border: 1px solid #e9ecef;
    }

    /* Hover Effect */
    .tag-card:hover .tag-content {
        transform: translateY(-2px);
        border-color: #dee2e6;
    }

    /* Checked State (General) */
    .tag-checkbox:checked+.tag-content {
        border-color: #0d6efd !important;
        background-color: #f0f7ff !important;
        box-shadow: 0 4px 6px rgba(13, 110, 253, 0.15) !important;
    }

    /* Checked State (ADMIN Only Override) */
    .tag-checkbox:checked+.tag-content.bg-danger {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
        /* Merah sangat muda */
        box-shadow: 0 4px 6px rgba(220, 53, 69, 0.15) !important;
    }

    .tag-checkbox:checked+.tag-content .check-icon {
        opacity: 1 !important;
    }
</style>
@endpush

@endsection