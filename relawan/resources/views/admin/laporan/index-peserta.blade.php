@extends('layouts.app')

@section('title', 'Laporan Peserta Kegiatan')

@section('content')
<div class="container pb-5">
    <div class="mb-4">
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Pusat Laporan
        </a>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-users me-2"></i> Laporan Partisipasi Relawan
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('admin.laporan.cetak_peserta') }}" method="GET" target="_blank">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Kegiatan</label>
                            <select name="kegiatan_id" class="form-select form-select-lg" required>
                                <option value="" selected disabled>-- Cari Judul Kegiatan --</option>
                                @foreach($kegiatans as $keg)
                                <option value="{{ $keg->id }}">
                                    {{ \Carbon\Carbon::parse($keg->tanggal_mulai)->format('d/m/Y') }} - {{ Str::limit($keg->judul, 50) }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih kegiatan untuk melihat siapa saja yang mendaftar.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark fw-bold">
                                <i class="fas fa-print me-2"></i> LIHAT DATA PESERTA
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection