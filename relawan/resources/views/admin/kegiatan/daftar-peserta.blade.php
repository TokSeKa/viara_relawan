@extends('layouts.app')

@section('title', 'Daftar Peserta')

@section('content')
<div class="container pb-5">
    
    {{-- Header & Tombol Kembali --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-users me-2"></i> Daftar Peserta
            </h3>
            <p class="text-muted small mb-0">
                Kegiatan: <span class="fw-bold text-dark">{{ $kegiatan->judul }}</span>
            </p>
        </div>
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Card Tabel --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark">Total Peserta: {{ $kegiatan->partisipasis->count() }} Orang</span>
            
            {{-- Badge Status Kegiatan --}}
            @if($kegiatan->status == 'buka') 
                <span class="badge bg-success">Pendaftaran Buka</span>
            @else 
                <span class="badge bg-secondary">Pendaftaran Tutup</span>
            @endif
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="px-4 py-3" width="5%">No</th>
                            <th class="py-3">Nama Lengkap</th>
                            <th class="py-3">Jenis Kelamin</th>
                            <th class="py-3">Usia</th>
                            <th class="py-3">No. Handphone</th>
                            <th class="py-3">Alamat Domisili</th>
                            <th class="py-3">Tanggal Join</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatan->partisipasis as $index => $partisipasi)
                            <tr>
                                <td class="px-4 fw-bold">{{ $index + 1 }}</td>
                                
                                {{-- Nama --}}
                                <td class="fw-bold text-dark">
                                    {{ $partisipasi->user->name }}
                                </td>

                                {{-- Gender --}}
                                <td>
                                    @if($partisipasi->user->jenis_kelamin == 'laki-laki')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill">Laki-laki</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill">Perempuan</span>
                                    @endif
                                </td>

                                {{-- Usia (Hitung dari Tanggal Lahir) --}}
                                <td>
                                    {{ \Carbon\Carbon::parse($partisipasi->user->tanggal_lahir)->age }} Tahun
                                </td>

                                {{-- No HP --}}
                                <td>
                                    {{ $partisipasi->user->no_hp }}
                                </td>

                                {{-- Alamat --}}
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $partisipasi->user->alamat }}">
                                        {{ $partisipasi->user->alamat }}
                                    </span>
                                </td>

                                {{-- Tanggal Join --}}
                                <td class="text-muted small">
                                    {{ $partisipasi->created_at->format('d M Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="mb-2 text-muted opacity-25">
                                        <i class="fas fa-user-slash fa-3x"></i>
                                    </div>
                                    <h6 class="fw-bold text-muted">Belum ada peserta</h6>
                                    <p class="small text-muted mb-0">Peserta yang bergabung akan muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection