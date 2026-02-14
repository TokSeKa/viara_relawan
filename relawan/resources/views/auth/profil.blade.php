@extends('layouts.app')

@section('title', 'Profil Saya')

{{-- 1. Tambahkan CSS Cropper.js & Kustomisasi Lingkaran --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<style>
    /* Agar gambar di dalam modal tidak melebihi batas */
    .image-container {
        max-height: 400px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #333;
    }

    #image-to-crop {
        display: block;
        max-width: 100%;
    }

    /* KUSTOMISASI CSS AGAR CROPPER TERLIHAT BULAT */
    .cropper-view-box,
    .cropper-face {
        border-radius: 50%;
        /* Membuat area seleksi jadi bulat */
    }

    /* Opsi tambahan: Menghilangkan garis putus-putus agar lebih bersih */
    .cropper-dashed,
    .cropper-point,
    .cropper-line {
        opacity: 0.5;
        /* Bisa diset 0 jika ingin benar-benar bersih */
    }
</style>
@endpush

@section('content')
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-user-circle me-2"></i> Profil Saya</h4>
                </div>

                <div class="card-body p-4 bg-white">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('user.profile.update') }}" id="profileForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6 text-center">
                                <div class="mb-3 position-relative d-inline-block">
                                    {{-- Preview Foto Utama --}}
                                    {{-- Tambahkan border-radius 50% di sini juga --}}
                                    <img src="{{ $user->profile_photo_url }}"
                                        id="main-preview"
                                        class="rounded-circle object-fit-cover shadow-sm border"
                                        width="150" height="150" alt="Foto Profil">

                                    <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-warning text-dark shadow-sm">
                                        {{ strtoupper(str_replace('_', ' ', $user->jabatan)) }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label class="btn btn-outline-primary btn-sm fw-bold">
                                        <i class="fas fa-camera me-1"></i> Ganti Foto / Ambil Gambar
                                        {{-- Input file dengan capture untuk HP --}}
                                        <input type="file" id="inputImage" name="photo" accept="image/*" capture="user" style="display:none">
                                    </label>
                                    <div class="form-text small mt-2">Format: JPG, PNG (Max 2MB).</div>

                                    {{-- Hidden input untuk menyimpan data base64 hasil crop --}}
                                    <input type="hidden" name="cropped_image" id="cropped_image">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Alamat Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor HP</label>
                                <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin" required>
                                    <option value="laki-laki" {{ $user->jenis_kelamin == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="perempuan" {{ $user->jenis_kelamin == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Rentang Usia</label>
                                <select class="form-select" name="usia_range" required>
                                    <option value="17-25" {{ $user->usia_range == '17-25' ? 'selected' : '' }}>17 - 25 Tahun</option>
                                    <option value="26-35" {{ $user->usia_range == '26-35' ? 'selected' : '' }}>26 - 35 Tahun</option>
                                    <option value="36-50" {{ $user->usia_range == '36-50' ? 'selected' : '' }}>36 - 50 Tahun</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Alamat Domisili</label>
                                <textarea class="form-control" name="alamat" rows="3" required>{{ old('alamat', $user->alamat) }}</textarea>
                            </div>

                            <div class="col-12 mt-4 d-flex justify-content-between">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4 fw-bold">Kembali</a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">
                                    <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. MODAL UNTUK CROP --}}
<div class="modal fade" id="cropModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold text-center w-100">Sesuaikan Foto Profil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-black d-flex justify-content-center align-items-center" style="min-height: 300px;">
                <div class="image-container w-100">
                    <img id="image-to-crop" src="" style="max-width: 100%; display: block;">
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary fw-bold px-4" id="btnCrop">
                    <i class="fas fa-check me-1"></i> SIMPAN FOTO
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 3. JAVASCRIPT LOGIC --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    let cropper;
    const inputImage = document.getElementById('inputImage');
    const imageToCrop = document.getElementById('image-to-crop');
    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
    const btnCrop = document.getElementById('btnCrop');
    const croppedImageInput = document.getElementById('cropped_image');
    const mainPreview = document.getElementById('main-preview');

    // Saat file dipilih atau kamera mengambil gambar
    inputImage.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function(event) {
                imageToCrop.src = event.target.result;
                cropModal.show();
            };
            reader.readAsDataURL(files[0]);
        }
    });

    // Inisialisasi Cropper saat modal muncul
    document.getElementById('cropModal').addEventListener('shown.bs.modal', function() {
        cropper = new Cropper(imageToCrop, {
            aspectRatio: 1, // Wajib 1:1 agar lingkaran sempurna
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.8, // Ukuran awal crop area
            restore: false,
            guides: false, // Hilangkan garis panduan agar lebih bersih
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            // Opsional: Kunci rasio agar tidak bisa ditarik jadi lonjong
            zoomable: true,
            background: false, // Hilangkan background grid
        });
    });

    // Hancurkan cropper saat modal ditutup agar tidak berat/buggy
    document.getElementById('cropModal').addEventListener('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        // Reset input file agar bisa pilih file yang sama lagi jika user membatalkan
        inputImage.value = '';
    });

    // Aksi tombol POTONG
    btnCrop.addEventListener('click', function() {
        if (!cropper) return;

        // Ambil canvas hasil crop
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingQuality: 'high',
        });

        // Ubah canvas ke format Base64 (JPG kualitas 90%)
        const base64data = canvas.toDataURL('image/jpeg', 0.9);

        // Masukkan ke hidden input untuk dikirim ke controller
        croppedImageInput.value = base64data;

        // Update preview di halaman utama biar user langsung lihat hasilnya
        mainPreview.src = base64data;

        // Tutup modal
        cropModal.hide();
    });
</script>
@endpush