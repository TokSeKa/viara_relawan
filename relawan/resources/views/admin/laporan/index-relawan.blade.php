@extends('layouts.app')

@section('title', 'Laporan Potensi Relawan')

@section('content')
<div class="container pb-5">

    {{-- Tombol Kembali --}}
    <div class="mb-4">
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Pusat Laporan
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="fas fa-user-tag me-2"></i> Filter Relawan Berdasarkan Minat
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('admin.laporan.cetak_relawan') }}" method="GET" target="_blank">

                        <div class="mb-3">
                            <label class="form-label fw-bold d-block">Pilih Kategori Minat / Skill</label>
                            <div class="alert alert-info small py-2">
                                <i class="fas fa-info-circle me-1"></i> Biarkan kosong jika ingin melihat <strong>Semua Relawan</strong>.
                            </div>

                            {{-- Grid Checkbox --}}
                            <div class="row g-3">
                                @foreach($tags as $tag)
                                <div class="col-md-4 col-6">
                                    <div class="form-check p-2 border rounded bg-light">
                                        <input class="form-check-input ms-1" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}">
                                        <label class="form-check-label ms-2 fw-bold small" for="tag_{{ $tag->id }}">
                                            {{ $tag->nama_tag }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-dark fw-bold">
                                <i class="fas fa-print me-2"></i> ANALISIS & CETAK DATA
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection