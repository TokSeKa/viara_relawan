@extends('layouts.app')

@section('title', 'Daftar Notifikasi')

@section('content')
<div class="container pb-5">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fas fa-bell me-2"></i> Kelola Notifikasi</h3>
            <p class="text-muted small mb-0">Atur pengumuman dan pesan untuk relawan.</p>
        </div>
        <a href="{{ route('admin.notifikasi.create') }}" class="btn btn-dark fw-bold">
            <i class="fas fa-plus-circle me-2"></i> Buat Baru
        </a>
    </div>

    {{-- CARD UTAMA --}}
    <div class="card shadow border-0">

        {{-- FILTER SECTION --}}
        <div class="card-header bg-white py-3 border-bottom">
            <form action="{{ route('admin.notifikasi.index') }}" method="GET" class="row g-2 align-items-center">

                {{-- Filter Tanggal --}}
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Tanggal Dibuat</label>
                    <input type="date" name="filter_date" class="form-control form-control-sm"
                        value="{{ request('filter_date') }}">
                </div>

                {{-- Filter Tipe --}}
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Tipe Notifikasi</label>
                    <select name="filter_type" class="form-select form-select-sm">
                        <option value="">-- Semua Tipe --</option>
                        <option value="info" {{ request('filter_type') == 'info' ? 'selected' : '' }}>Info (Biru)</option>
                        <option value="success" {{ request('filter_type') == 'success' ? 'selected' : '' }}>Sukses (Hijau)</option>
                        <option value="warning" {{ request('filter_type') == 'warning' ? 'selected' : '' }}>Peringatan (Kuning)</option>
                        <option value="danger" {{ request('filter_type') == 'danger' ? 'selected' : '' }}>Darurat (Merah)</option>
                    </select>
                </div>

                {{-- Tombol Filter --}}
                <div class="col-md-3 d-flex align-items-end pt-4">
                    <button type="submit" class="btn btn-sm btn-primary me-2 fw-bold">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->has('filter_date') || request()->has('filter_type'))
                    <a href="{{ route('admin.notifikasi.index') }}" class="btn btn-sm btn-outline-secondary">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABEL --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-secondary small text-uppercase fw-bold" width="5%">No</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold" width="35%">Judul Notifikasi</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold" width="15%">Tipe / Level</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold" width="20%">Target</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold" width="20%">Status & Expired</th>
                            <th class="py-3 text-end px-4" width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifikasis as $index => $notif)
                        {{-- Cek apakah sudah expired --}}
                        @php
                        $isExpired = $notif->expires_at && $notif->expires_at->isPast();
                        $rowClass = $isExpired ? 'bg-light text-muted opacity-75' : '';
                        @endphp

                        <tr class="{{ $rowClass }}">
                            <td class="px-4 fw-bold">{{ $notifikasis->firstItem() + $index }}</td>

                            {{-- Judul & Pesan Singkat --}}
                            <td>
                                <span class="fw-bold d-block {{ $isExpired ? 'text-decoration-line-through' : 'text-dark' }}">
                                    {{ $notif->judul }}
                                </span>
                                <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                    {{ $notif->pesan }}
                                </small>
                            </td>

                            {{-- Badge Tipe --}}
                            <td>
                                @php
                                $badgeColor = match($notif->type) {
                                'info' => 'bg-info text-dark',
                                'warning' => 'bg-warning text-dark',
                                'danger' => 'bg-danger',
                                'success' => 'bg-success',
                                default => 'bg-secondary'
                                };
                                $label = ucfirst($notif->type);
                                @endphp
                                <span class="badge {{ $badgeColor }} bg-opacity-75 rounded-pill px-3">
                                    {{ $label }}
                                </span>
                            </td>

                            {{-- Target Audience --}}
                            <td>
                                @if($notif->target_audience == 'all')
                                <span class="badge bg-dark">Broadcast (All)</span>
                                @elseif($notif->target_audience == 'kegiatan')
                                <span class="badge bg-primary">Peserta Kegiatan</span>
                                @elseif($notif->target_audience == 'tag')
                                <span class="badge bg-secondary">Peminat Tag</span>
                                @endif
                            </td>

                            {{-- Tanggal Expired & Status --}}
                            <td>
                                @if($notif->expires_at)
                                <div class="d-flex flex-column">
                                    <span class="fw-bold small {{ $isExpired ? 'text-danger' : 'text-success' }}">
                                        {{ $notif->expires_at->format('d M Y, H:i') }}
                                    </span>
                                    <small style="font-size: 0.7rem;">
                                        @if($isExpired)
                                        <i class="fas fa-clock me-1"></i> Kadaluarsa
                                        @else
                                        <i class="fas fa-hourglass-half me-1"></i> Aktif
                                        @endif
                                    </small>
                                </div>
                                @else
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-infinity me-1"></i> Selamanya
                                </span>
                                @endif
                            </td>

                            {{-- Tombol Edit & Delete --}}
                            <td class="text-end px-4 text-nowrap">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('admin.notifikasi.edit', $notif->id) }}" class="btn btn-sm btn-link text-primary p-0 me-3" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Tombol Delete (Tetap Sama, cuma saya rapikan dikit layoutnya) --}}
                                <form action="{{ route('admin.notifikasi.destroy', $notif->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus notifikasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="fas fa-bell-slash fa-2x opacity-25"></i></div>
                                <p class="small text-muted mb-0">Belum ada notifikasi yang dibuat.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="card-footer bg-white py-3">
                {{ $notifikasis->links() }}
            </div>
        </div>
    </div>
</div>
@endsection