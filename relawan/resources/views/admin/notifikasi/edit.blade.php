@extends('layouts.app')

@section('title', 'Edit Notifikasi')

@section('content')
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold"><i class="fas fa-edit me-2"></i> Edit Notifikasi</h3>
                <a href="{{ route('admin.notifikasi.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card shadow border-0">
                <div class="card-body p-4">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.notifikasi.update', $notifikasi->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- PENTING UNTUK UPDATE --}}

                        {{-- 1. JUDUL & PESAN --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Notifikasi</label>
                            <input type="text" name="judul" class="form-control" required value="{{ old('judul', $notifikasi->judul) }}">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Isi Pesan</label>
                            <textarea name="pesan" class="form-control" rows="3" required>{{ old('pesan', $notifikasi->pesan) }}</textarea>
                        </div>

                        {{-- 2. TIPE NOTIFIKASI --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold d-block">Tipe / Level Penting</label>
                            <div class="btn-group w-100" role="group">
                                @foreach(['info', 'success', 'warning', 'danger'] as $type)
                                    <input type="radio" class="btn-check" name="type" id="type_{{ $type }}" value="{{ $type }}" 
                                        {{ old('type', $notifikasi->type) == $type ? 'checked' : '' }}>
                                    
                                    @php
                                        $btnClass = match($type) {
                                            'info' => 'btn-outline-primary',
                                            'success' => 'btn-outline-success',
                                            'warning' => 'btn-outline-warning',
                                            'danger' => 'btn-outline-danger',
                                        };
                                        $icon = match($type) {
                                            'info' => 'fa-info-circle',
                                            'success' => 'fa-check-circle',
                                            'warning' => 'fa-exclamation-triangle',
                                            'danger' => 'fa-times-circle',
                                        };
                                    @endphp
                                    <label class="btn {{ $btnClass }}" for="type_{{ $type }}">
                                        <i class="fas {{ $icon }} me-1"></i> {{ ucfirst($type) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- 3. TARGET AUDIENCE --}}
                        <div class="mb-4 bg-light p-4 rounded border">
                            <label class="form-label fw-bold mb-3">Target Penerima</label>
                            
                            <div class="d-flex gap-4 mb-4 border-bottom pb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_audience" id="target_all" value="all" 
                                        {{ old('target_audience', $notifikasi->target_audience) == 'all' ? 'checked' : '' }} onchange="toggleTarget()">
                                    <label class="form-check-label fw-bold cursor-pointer" for="target_all">Semua User (Broadcast)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_audience" id="target_kegiatan" value="kegiatan" 
                                        {{ old('target_audience', $notifikasi->target_audience) == 'kegiatan' ? 'checked' : '' }} onchange="toggleTarget()">
                                    <label class="form-check-label fw-bold cursor-pointer" for="target_kegiatan">Peserta Kegiatan Tertentu</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_audience" id="target_tag" value="tag" 
                                        {{ old('target_audience', $notifikasi->target_audience) == 'tag' ? 'checked' : '' }} onchange="toggleTarget()">
                                    <label class="form-check-label fw-bold cursor-pointer" for="target_tag">Peminat Tag Tertentu</label>
                                </div>
                            </div>

                            {{-- WRAPPER KEGIATAN --}}
                            <div id="wrapper_kegiatan" class="d-none">
                                <label class="small text-muted mb-2 fw-bold d-block">Pilih Kegiatan:</label>
                                <div style="max-height: 300px; overflow-y: auto; overflow-x: hidden; padding: 2px;">
                                    <div class="row g-2">
                                        @foreach($kegiatans as $keg)
                                            <div class="col-md-6">
                                                <label class="cursor-pointer d-block h-100">
                                                    {{-- LOGIK CHECKED: Cek apakah ID kegiatan ini ada di array $selectedKegiatanIds --}}
                                                    <input type="checkbox" name="kegiatan_ids[]" value="{{ $keg->id }}" 
                                                        class="item-checkbox position-absolute opacity-0"
                                                        {{ in_array($keg->id, $selectedKegiatanIds) ? 'checked' : '' }}>
                                                    
                                                    <div class="card h-100 py-3 px-2 shadow-sm border item-content position-relative d-flex flex-column align-items-center justify-content-center text-center">
                                                        <div class="position-absolute top-0 end-0 mt-2 me-2 opacity-0 check-icon transition-all">
                                                            <i class="fas fa-check-circle text-primary fa-lg"></i>
                                                        </div>
                                                        <span class="fw-bold text-dark lh-sm mb-2" style="font-size: 0.9rem;">{{ $keg->judul }}</span>
                                                        <span class="badge bg-secondary rounded-pill px-3" style="font-size: 0.65rem;">{{ strtoupper($keg->status) }}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- WRAPPER TAG --}}
                            <div id="wrapper_tag" class="d-none">
                                <label class="small text-muted mb-2 fw-bold d-block">Pilih Tag Minat:</label>
                                <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                                    <div class="row g-2">
                                        @foreach($tags as $tag)
                                            <div class="col-md-4 col-sm-6">
                                                <label class="cursor-pointer d-block h-100">
                                                    {{-- LOGIK CHECKED: Cek apakah ID tag ini ada di array $selectedTagIds --}}
                                                    <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" 
                                                        class="item-checkbox position-absolute opacity-0"
                                                        {{ in_array($tag->id, $selectedTagIds) ? 'checked' : '' }}>
                                                    
                                                    <div class="card h-100 px-3 py-2 shadow-sm border item-content d-flex align-items-center justify-content-between">
                                                        <span class="fw-bold text-dark text-truncate small">{{ $tag->nama_tag }}</span>
                                                        <i class="fas fa-check-circle text-primary opacity-0 check-icon"></i>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 4. EXPIRED DATE --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Waktu Kadaluarsa</label>
                            {{-- Format Value untuk datetime-local harus: Y-m-d\TH:i --}}
                            <input type="datetime-local" name="expires_at" class="form-control" 
                                value="{{ old('expires_at', $notifikasi->expires_at ? $notifikasi->expires_at->format('Y-m-d\TH:i') : '') }}">
                            <small class="text-muted">Biarkan kosong jika notifikasi berlaku selamanya.</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success fw-bold py-2">
                                <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleTarget() {
        const target = document.querySelector('input[name="target_audience"]:checked').value;
        const wrapperKegiatan = document.getElementById('wrapper_kegiatan');
        const wrapperTag = document.getElementById('wrapper_tag');

        // Reset
        wrapperKegiatan.classList.add('d-none');
        wrapperTag.classList.add('d-none');

        if (target === 'kegiatan') {
            wrapperKegiatan.classList.remove('d-none');
        } else if (target === 'tag') {
            wrapperTag.classList.remove('d-none');
        }
    }

    // Jalankan saat halaman pertama kali dimuat untuk menampilkan wrapper yang sesuai (Edit Mode)
    document.addEventListener("DOMContentLoaded", function() {
        toggleTarget();
    });
</script>
@endpush

@push('styles')
<style>
    /* ... CSS SAMA PERSIS SEPERTI CREATE ... */
    .cursor-pointer { cursor: pointer; }
    .item-content { background-color: #fff; border: 1px solid #dee2e6 !important; transition: all 0.2s ease-in-out; min-height: 100px; }
    .cursor-pointer:hover .item-content { background-color: #f8f9fa; border-color: #adb5bd !important; transform: translateY(-2px); }
    .item-checkbox:checked + .item-content { border: 2px solid #0d6efd !important; background-color: #f0f7ff !important; box-shadow: 0 4px 6px rgba(13, 110, 253, 0.15) !important; }
    .item-checkbox:checked + .item-content .text-dark { color: #0d6efd !important; }
    .item-checkbox:checked + .item-content .check-icon { opacity: 1 !important; transform: scale(1.1); }
    .transition-all { transition: all 0.2s ease; }
</style>
@endpush

@endsection