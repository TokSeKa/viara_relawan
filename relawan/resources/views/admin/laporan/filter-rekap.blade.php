@extends('layouts.app')

@section('title', 'Laporan Kegiatan')

@section('content')
<div class="container pb-5">
    <div class="mb-4">
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Pusat Laporan
        </a>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fas fa-print me-2"></i> Filter Laporan Kegiatan
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('admin.laporan.cetak_kegiatan') }}" method="GET" target="_blank">

                        <div class="row g-3">
                            {{-- Pilih Periode --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Dari Tanggal</label>
                                <input type="date" name="tgl_awal" class="form-control" required value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sampai Tanggal</label>
                                <input type="date" name="tgl_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>

                            {{-- Filter Status --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status Kegiatan</label>
                                <select name="status" class="form-select">
                                    <option value="semua">-- Semua Status --</option>
                                    <option value="buka">Buka (Sedang Jalan)</option>
                                    <option value="tutup">Tutup (Pendaftaran Tutup)</option>
                                    <option value="selesai">Selesai (Sudah Berakhir)</option>
                                </select>
                            </div>

                            {{-- Filter Jenis (Detail Type) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Kegiatan</label>
                                <select name="jenis" class="form-select">
                                    <option value="semua">-- Semua Jenis --</option>
                                    <option value="acara">Acara / Event</option>
                                    <option value="donasi_dana">Penggalangan Dana</option>
                                    <option value="donasi_darah">Donor Darah</option>
                                    <option value="mobil">Layanan Mobil</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold">
                                <i class="fas fa-file-pdf me-2"></i> CETAK / PREVIEW LAPORAN
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection