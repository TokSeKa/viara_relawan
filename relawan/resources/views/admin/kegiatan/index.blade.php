@extends('layouts.app')

@section('title', 'Kelola Kegiatan')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center my-4">
        <h3 class="fw-bold text-dark mb-0">
            <i class="fas fa-clipboard-list me-2"></i> Daftar Kegiatan
        </h3>
        <a href="{{ route('admin.kegiatan.pilih') }}" class="btn btn-dark fw-bold">
            <i class="fas fa-plus me-1"></i> Tambah Baru
        </a>
    </div>

    {{-- === FILTER SECTION === --}}
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body py-3">
            <form action="{{ route('admin.kegiatan.index') }}" method="GET" class="row g-3 align-items-center">

                {{-- 1. Filter Status (Tombol Group) --}}
                <div class="col-md-5">
                    <label class="small fw-bold text-muted mb-1 d-block">Status Kegiatan</label>
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('admin.kegiatan.index', array_merge(request()->all(), ['status' => null, 'page' => 1])) }}"
                            class="btn btn-outline-secondary {{ request('status') == null ? 'active fw-bold' : '' }}">
                            Semua
                        </a>
                        <a href="{{ route('admin.kegiatan.index', array_merge(request()->all(), ['status' => 'buka', 'page' => 1])) }}"
                            class="btn btn-outline-success {{ request('status') == 'buka' ? 'active fw-bold' : '' }}">
                            <i class="fas fa-door-open me-1"></i> Buka
                        </a>
                        <a href="{{ route('admin.kegiatan.index', array_merge(request()->all(), ['status' => 'tutup', 'page' => 1])) }}"
                            class="btn btn-outline-warning {{ request('status') == 'tutup' ? 'active fw-bold' : '' }}">
                            <i class="fas fa-door-closed me-1"></i> Tutup
                        </a>
                        <a href="{{ route('admin.kegiatan.index', array_merge(request()->all(), ['status' => 'selesai', 'page' => 1])) }}"
                            class="btn btn-outline-dark {{ request('status') == 'selesai' ? 'active fw-bold' : '' }}">
                            <i class="fas fa-check-circle me-1"></i> Selesai
                        </a>
                    </div>
                </div>

                {{-- 2. Filter Waktu (Date Range) --}}
                <div class="col-md-5">
                    <label class="small fw-bold text-muted mb-1 d-block">Rentang Waktu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="fas fa-calendar"></i></span>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ request('start_date') }}" placeholder="Dari">
                        <span class="input-group-text bg-white">-</span>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ request('end_date') }}" placeholder="Sampai">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                {{-- 3. Reset Filter --}}
                <div class="col-md-2 text-end">
                    <label class="small fw-bold text-muted mb-1 d-block">&nbsp;</label>
                    @if(request()->has('status') || request()->has('start_date'))
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-link text-danger text-decoration-none btn-sm">
                        <i class="fas fa-times me-1"></i> Reset Filter
                    </a>
                    @endif
                </div>

                {{-- Input Hidden Status (Supaya pas submit tanggal, status gak hilang) --}}
                @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
            </form>
        </div>
    </div>

    {{-- === TABEL DATA (Sama seperti sebelumnya) === --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    {{-- HEADER TABEL --}}
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="py-3 ps-4">No</th>
                            <th>Judul Kegiatan</th>
                            <th>Jenis</th>
                            <th>Target/Info</th>
                            <th>Waktu Pelaksanaan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Total Peserta</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>

                    {{-- BODY TABEL --}}
                    <tbody>
                        @forelse($kegiatans as $index => $kegiatan)
                        <tr>
                            <td class="ps-4">{{ $kegiatans->firstItem() + $index }}</td>

                            {{-- Judul --}}
                            <td>
                                <div class="fw-bold text-dark">{{ Str::limit($kegiatan->judul, 40) }}</div>
                                <small class="text-muted">ID: #{{ $kegiatan->id }}</small>
                            </td>

                            {{-- Jenis (Badge) --}}
                            <td>
                                @php
                                $badges = [
                                'donasi_dana' => ['success', 'Dana'],
                                'donasi_darah' => ['danger', 'Darah'],
                                'mobil' => ['primary', 'Mobil'],
                                'acara' => ['warning', 'Acara']
                                ];
                                $type = $kegiatan->detail_type;
                                @endphp
                                <span class="badge bg-{{ $badges[$type][0] ?? 'secondary' }} bg-opacity-10 text-{{ $badges[$type][0] ?? 'secondary' }} border border-{{ $badges[$type][0] ?? 'secondary' }} px-2">
                                    {{ $badges[$type][1] ?? 'Lainnya' }}
                                </span>
                            </td>

                            {{-- Target/Info --}}
                            <td class="small fw-bold">
                                @if($type == 'donasi_dana') Rp {{ number_format($kegiatan->detail->target_rupiah ?? 0, 0, ',', '.') }}
                                @elseif($type == 'donasi_darah') {{ $kegiatan->detail->target_kantong }} Kantong
                                @elseif($type == 'mobil') {{ $kegiatan->detail->jumlah_unit }} Unit
                                @elseif($type == 'acara') {{ $kegiatan->detail->kuota_peserta }} Pax
                                @endif
                            </td>

                            {{-- Waktu --}}
                            <td>
                                <small>{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d M Y H:i') }}</small>
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($kegiatan->status == 'buka') <span class="badge bg-success rounded-pill">Buka</span>
                                @elseif($kegiatan->status == 'tutup') <span class="badge bg-warning text-dark rounded-pill">Tutup</span>
                                @else <span class="badge bg-secondary rounded-pill">Selesai</span>
                                @endif
                            </td>

                            {{-- KOLOM TOTAL PESERTA --}}
                            <td class="text-center">
                                <span class="fw-bold fs-6 text-dark">
                                    {{ $kegiatan->partisipasis_count ?? 0 }}
                                </span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Orang</small>
                            </td>


                            {{-- Aksi --}}
                            <td class="text-end pe-4 text-nowrap">
                                {{-- Tombol Lihat Peserta --}}
                                <a href="{{ route('admin.kegiatan.peserta', $kegiatan->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Lihat Peserta">
                                    <i class="fas fa-users"></i>
                                </a>

                                {{-- Tombol Edit (Yang lama) --}}
                                <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-outline-dark" title="Edit Kegiatan">
                                    <i class="fas fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted"> {{-- Colspan jadi 8 karena nambah 1 kolom --}}
                                Tidak ada kegiatan yang sesuai filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white py-3">
                {{ $kegiatans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection