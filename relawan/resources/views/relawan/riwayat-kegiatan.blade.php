@extends('layouts.app')

@section('title', 'Riwayat Kegiatan Saya')

@section('content')
<div class="container pb-5">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-history me-2"></i> Riwayat Kegiatan
            </h3>
            <p class="text-muted small mb-0">
                Daftar kegiatan sosial yang pernah kamu ikuti.
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- Card Tabel --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="px-4 py-3" width="5%">No</th>
                            <th class="py-3" width="35%">Nama Kegiatan</th>
                            <th class="py-3">Waktu Mulai Pendaftaran</th>
                            <th class="py-3">Waktu Tutup Pendaftaran</th>
                            <th class="py-3">Tanggal Gabung</th>
                            <th class="py-3 text-center">Status Kegiatan</th>
                            <th class="py-3 text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayats as $index => $data)
                            <tr>
                                <td class="px-4 fw-bold">{{ $riwayats->firstItem() + $index }}</td>
                                
                                {{-- Nama Kegiatan --}}
                                <td>
                                    <div class="fw-bold text-dark">{{ Str::limit($data->kegiatan->judul, 50) }}</div>
                                    
                                    {{-- Badge Jenis Kecil --}}
                                    @php
                                        $type = $data->kegiatan->detail_type;
                                        $badgeColor = match($type) {
                                            'donasi_dana' => 'success',
                                            'donasi_darah' => 'danger',
                                            'mobil' => 'primary',
                                            'acara' => 'warning',
                                            default => 'secondary'
                                        };
                                        $label = ucwords(str_replace('_', ' ', $type));
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} rounded-1" style="font-size: 0.65rem;">
                                        {{ $label }}
                                    </span>
                                </td>

                                {{-- Waktu Pelaksanaan --}}
                                <td class="small text-secondary">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($data->kegiatan->tanggal_mulai)->format('d M Y, H:i') }}
                                </td>

                                {{-- Waktu Tutup --}}
                                <td class="small text-secondary">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($data->kegiatan->tanggal_selesai)->format('d M Y, H:i') }}
                                </td>

                                {{-- Tanggal Join (Created At Partisipasi) --}}
                                <td class="small text-muted">
                                    {{ $data->created_at->format('d M Y, H:i') }}
                                </td>

                                {{-- Status Kegiatan --}}
                                <td class="text-center">
                                    @if($data->kegiatan->status == 'buka')
                                        <span class="badge bg-success rounded-pill">Sedang Berjalan</span>
                                    @elseif($data->kegiatan->status == 'tutup')
                                        <span class="badge bg-secondary rounded-pill">Ditutup</span>
                                    @else
                                        <span class="badge bg-dark rounded-pill">Selesai</span>
                                    @endif
                                </td>

                                {{-- Tombol Lihat Detail --}}
                                <td class="text-end pe-4">
                                    <a href="{{ route('kegiatan.show', $data->kegiatan->id) }}" class="btn btn-sm btn-outline-primary fw-bold">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="mb-3 text-muted opacity-25">
                                        <i class="fas fa-clipboard-list fa-3x"></i>
                                    </div>
                                    <h6 class="fw-bold text-muted">Belum ada riwayat</h6>
                                    <p class="small text-muted mb-3">Kamu belum mendaftar di kegiatan apapun.</p>
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm px-4">
                                        Cari Kegiatan
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($riwayats->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $riwayats->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection