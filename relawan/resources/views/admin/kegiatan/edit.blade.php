@extends('layouts.app')

@section('title', 'Edit Kegiatan')

@section('content')
<div class="container pb-5">
    {{-- HEADER SAMA --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="fas fa-edit me-2"></i> 
            Edit: {{ ucwords(str_replace('_', ' ', $jenis)) }}
        </h3>
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.kegiatan.update', $kegiatan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') 
        <input type="hidden" name="jenis_kegiatan" value="{{ $jenis }}">

        <div class="row">
            <div class="col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold py-3">Data Umum</div>
                    <div class="card-body">
                        {{-- INPUT DATA UMUM SAMA --}}
                        <div class="mb-3"><label class="form-label">Judul Kegiatan</label><input type="text" name="judul" class="form-control" required value="{{ old('judul', $kegiatan->judul) }}"></div>
                        <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="buka" {{ $kegiatan->status == 'buka' ? 'selected' : '' }}>Buka</option><option value="tutup" {{ $kegiatan->status == 'tutup' ? 'selected' : '' }}>Tutup</option><option value="selesai" {{ $kegiatan->status == 'selesai' ? 'selected' : '' }}>Selesai</option></select></div>
                        <div class="mb-3"><label class="form-label">Deskripsi Lengkap</label><textarea name="deskripsi" class="form-control" rows="5" required>{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai</label><input type="datetime-local" name="tanggal_mulai" class="form-control" required value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Selesai</label><input type="datetime-local" name="tanggal_selesai" class="form-control" required value="{{ old('tanggal_selesai', $kegiatan->tanggal_selesai) }}"></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Ganti Banner (Opsional)</label>
                            @if($kegiatan->banner_image) <div class="mb-2"><img src="{{ asset('storage/' . $kegiatan->banner_image) }}" class="img-thumbnail" style="height: 100px;"></div> @endif
                            <input type="file" name="banner_image" class="form-control" accept="image/*">
                        </div>

                        {{-- --- BARU: PILIH TAG (EDIT) --- --}}
                        <div class="mt-4 pt-3 border-top">
                            <label class="form-label fw-bold"><i class="fas fa-tags me-1"></i> Kategori / Tag</label>
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <div style="max-height: 200px; overflow-y: auto;">
                                        <div class="row g-2">
                                            @foreach($tags as $tag)
                                                <div class="col-md-4 col-sm-6">
                                                    <label class="cursor-pointer d-block h-100">
                                                        {{-- Cek if $selectedTags contain ID ini --}}
                                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                                                            class="tag-checkbox position-absolute opacity-0"
                                                            {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}>
                                                        
                                                        <div class="card h-100 px-2 py-2 shadow-sm border tag-content d-flex align-items-center justify-content-between">
                                                            <span class="small fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ $tag->nama_tag }}</span>
                                                            <i class="fas fa-check-circle text-primary opacity-0 check-icon"></i>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- --- END TAG --- --}}
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                {{-- CARD KANAN (DETAIL KHUSUS) TETAP SAMA KEK KODEMU --}}
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning bg-opacity-10 fw-bold py-3 text-warning-emphasis">
                        Detail Khusus: {{ ucwords(str_replace('_', ' ', $jenis)) }}
                    </div>
                    <div class="card-body">
                        {{-- ... PASTE DETAIL KHUSUS DARI FILE ASLIMU ... --}}
                        {{-- Untuk hemat tempat di chat, saya asumsikan kodemu yg donasi_dana, darah, mobil, dll disini sama persis dengan yang diatas --}}
                        @if($jenis == 'donasi_dana')
                             <div class="mb-3"><label class="form-label">Target Rupiah</label><input type="number" name="target_rupiah" class="form-control" value="{{ old('target_rupiah', $kegiatan->detail->target_rupiah) }}"></div>
                             {{-- ... bank logic ... --}}
                             <label class="form-label fw-bold">Informasi Rekening Bank</label>
                            <div id="bank-container">
                                @foreach($kegiatan->detail->info_bank as $index => $bank)
                                    <div class="card bg-light p-2 mb-2 bank-row position-relative">
                                        @if($index > 0) <button type="button" class="btn-close position-absolute top-0 end-0 m-1" onclick="this.parentElement.remove()"></button> @endif
                                        <input type="text" name="info_bank[{{$index}}][nama_bank]" class="form-control mb-1 form-control-sm" placeholder="Nama Bank" value="{{ $bank['nama_bank'] }}" required>
                                        <input type="text" name="info_bank[{{$index}}][no_rekening]" class="form-control mb-1 form-control-sm" placeholder="No. Rekening" value="{{ $bank['no_rekening'] }}" required>
                                        <input type="text" name="info_bank[{{$index}}][atas_nama]" class="form-control form-control-sm" placeholder="Atas Nama" value="{{ $bank['atas_nama'] }}" required>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="tambahBank()">Tambah Rekening</button>

                        @elseif($jenis == 'donasi_darah')
                             <div class="mb-3"><label class="form-label">Target Kantong</label><input type="number" name="target_kantong" class="form-control" required value="{{ old('target_kantong', $kegiatan->detail->target_kantong) }}"></div>
                             <div class="mb-3"><label class="form-label">Lokasi PMI</label><input type="text" name="lokasi_pmi" class="form-control" required value="{{ old('lokasi_pmi', $kegiatan->detail->lokasi_pmi) }}"></div>
                             <div class="mb-3"><label class="form-label fw-bold">Golongan Darah</label><div class="row g-2">
                                @php $daftarDarah = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']; $savedBlood = $kegiatan->detail->golongan_darah_needed ?? []; @endphp
                                @foreach($daftarDarah as $goldar)
                                <div class="col-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="golongan_darah_needed[]" value="{{ $goldar }}" id="gd_{{ $goldar }}" {{ in_array($goldar, $savedBlood) ? 'checked' : '' }}><label class="form-check-label" for="gd_{{ $goldar }}">{{ $goldar }}</label></div></div>
                                @endforeach
                             </div></div>

                        @elseif($jenis == 'mobil')
                             <div class="mb-3"><label class="form-label">Jumlah Unit</label><input type="number" name="jumlah_unit" class="form-control" required value="{{ old('jumlah_unit', $kegiatan->detail->jumlah_unit) }}"></div>
                             <div class="mb-3"><label class="form-label">Lokasi Jemput</label><textarea name="lokasi_jemput" class="form-control" rows="2" required>{{ old('lokasi_jemput', $kegiatan->detail->lokasi_jemput) }}</textarea></div>
                             <div class="form-check form-switch p-3 bg-light rounded border"><input class="form-check-input" type="checkbox" name="butuh_supir" id="butuh_supir" value="1" {{ old('butuh_supir', $kegiatan->detail->butuh_supir) ? 'checked' : '' }}><label class="form-check-label fw-bold" for="butuh_supir">Butuh Supir?</label></div>

                        @elseif($jenis == 'acara')
                             <div class="mb-3"><label class="form-label">Lokasi</label><textarea name="lokasi" class="form-control" rows="2" required>{{ old('lokasi', $kegiatan->detail->lokasi) }}</textarea></div>
                             <div class="mb-3"><label class="form-label">Kuota Peserta</label><input type="number" name="kuota_peserta" class="form-control" required value="{{ old('kuota_peserta', $kegiatan->detail->kuota_peserta) }}"></div>
                        @endif
                    </div>
                </div>
                
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success btn-lg fw-bold">
                        <i class="fas fa-save me-2"></i> UPDATE KEGIATAN
                    </button>
                </div>
            </div>
        </div>
    </form> 
</div>

{{-- SCRIPT BANK (Sama seperti sebelumnya) --}}
@if($jenis == 'donasi_dana')
<script>
    let bankCount = {{ count($kegiatan->detail->info_bank ?? []) }};
    function tambahBank() {
        const container = document.getElementById('bank-container');
        const html = `<div class="card bg-light p-2 mb-2 bank-row position-relative"><button type="button" class="btn-close position-absolute top-0 end-0 m-1" onclick="this.parentElement.remove()"></button><input type="text" name="info_bank[${bankCount}][nama_bank]" class="form-control mb-1 form-control-sm" placeholder="Nama Bank" required><input type="text" name="info_bank[${bankCount}][no_rekening]" class="form-control mb-1 form-control-sm" placeholder="No. Rekening" required><input type="text" name="info_bank[${bankCount}][atas_nama]" class="form-control form-control-sm" placeholder="Atas Nama" required></div>`;
        container.insertAdjacentHTML('beforeend', html);
        bankCount++;
    }
</script>
@endif

{{-- CSS KHUSUS TAG (Sama seperti Create) --}}
@push('styles')
<style>
    .cursor-pointer { cursor: pointer; }
    .tag-content { background-color: #fff; border: 1px solid #dee2e6 !important; transition: all 0.2s; }
    .cursor-pointer:hover .tag-content { background-color: #f8f9fa; border-color: #adb5bd !important; }
    .tag-checkbox:checked + .tag-content { border-color: #0d6efd !important; background-color: #f0f7ff !important; }
    .tag-checkbox:checked + .tag-content .text-dark { color: #0d6efd !important; }
    .tag-checkbox:checked + .tag-content .check-icon { opacity: 1 !important; }
</style>
@endpush

@endsection