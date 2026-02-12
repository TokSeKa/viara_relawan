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
            <form action="{{ route('admin.kegiatan.index') }}" method="GET" class="row g-2 align-items-end">

                {{-- 1. SEARCH JUDUL (BARU) --}}
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Cari Judul</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control"
                            placeholder="Nama kegiatan..."
                            value="{{ request('search') }}">
                    </div>
                </div>

                {{-- 2. Filter Jenis --}}
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Jenis</label>
                    <select name="jenis" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">- Semua -</option>
                        <option value="donasi_dana" {{ request('jenis') == 'donasi_dana' ? 'selected' : '' }}>Donasi Dana</option>
                        <option value="donasi_darah" {{ request('jenis') == 'donasi_darah' ? 'selected' : '' }}>Donor Darah</option>
                        <option value="mobil" {{ request('jenis') == 'mobil' ? 'selected' : '' }}>Transportasi</option>
                        <option value="acara" {{ request('jenis') == 'acara' ? 'selected' : '' }}>Acara</option>
                        <option value="donasi_barang" {{ request('jenis') == 'donasi_barang' ? 'selected' : '' }}>Donasi Barang</option>
                        <option value="peminjaman_barang" {{ request('jenis') == 'peminjaman_barang' ? 'selected' : '' }}>Pinjam Barang</option>
                    </select>
                </div>

                {{-- 3. Filter Status --}}
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Status</label>
                    <div class="btn-group w-100 btn-group-sm" role="group">
                        <a href="{{ route('admin.kegiatan.index', array_merge(request()->except(['page', 'status']), ['status' => null])) }}"
                            class="btn btn-outline-secondary {{ request('status') == null ? 'active fw-bold' : '' }}">
                            Semua
                        </a>
                        <a href="{{ route('admin.kegiatan.index', array_merge(request()->except(['page', 'status']), ['status' => 'buka'])) }}"
                            class="btn btn-outline-success {{ request('status') == 'buka' ? 'active fw-bold' : '' }}">
                            Buka
                        </a>
                        <a href="{{ route('admin.kegiatan.index', array_merge(request()->except(['page', 'status']), ['status' => 'tutup'])) }}"
                            class="btn btn-outline-warning {{ request('status') == 'tutup' ? 'active fw-bold' : '' }}">
                            Tutup
                        </a>
                        <a href="{{ route('admin.kegiatan.index', array_merge(request()->except(['page', 'status']), ['status' => 'selesai'])) }}"
                            class="btn btn-outline-dark {{ request('status') == 'selesai' ? 'active fw-bold' : '' }}">
                            Selesai
                        </a>
                    </div>
                </div>

                {{-- 4. Filter Waktu & Submit --}}
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Rentang Waktu</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="start_date" class="form-control"
                            value="{{ request('start_date') }}" title="Dari Tanggal">
                        <span class="input-group-text bg-white">-</span>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ request('end_date') }}" title="Sampai Tanggal">
                        <button type="submit" class="btn btn-primary" title="Terapkan Filter">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>

                {{-- Reset Button (Full Width baris bawah jika ada filter aktif) --}}
                @if(request()->anyFilled(['search', 'jenis', 'status', 'start_date', 'end_date']))
                <div class="col-12 mt-2">
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-link text-danger text-decoration-none btn-sm p-0 small">
                        <i class="fas fa-times me-1"></i> Hapus Semua Filter
                    </a>
                </div>
                @endif

                {{-- Input Hidden Status (Agar tidak hilang saat tekan enter di search) --}}
                @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

            </form>
        </div>
    </div>

    {{-- === TABEL DATA === --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="py-3 ps-4" width="5%">No</th>
                            <th class="py-3" width="25%">Judul Kegiatan</th>
                            <th class="py-3" width="15%">Jenis</th>
                            <th class="py-3" width="20%">Target / Info Utama</th>
                            <th class="py-3" width="15%">Waktu Pelaksanaan</th>
                            <th class="text-center py-3" width="10%">Status</th>
                            <th class="text-center py-3" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatans as $index => $kegiatan)
                        <tr>
                            <td class="ps-4">{{ $kegiatans->firstItem() + $index }}</td>

                            {{-- Judul --}}
                            <td>
                                <div class="fw-bold text-dark text-truncate-2" style="max-width: 250px;">
                                    {{ $kegiatan->judul }}
                                </div>
                                <small class="text-muted d-block mt-1">ID: #{{ $kegiatan->id }}</small>
                            </td>

                            {{-- Jenis (Badge Warna-Warni) --}}
                            <td>
                                @php
                                $badges = [
                                'donasi_dana' => ['success', 'Donasi Dana', 'fas fa-hand-holding-usd'],
                                'donasi_darah' => ['danger', 'Donor Darah', 'fas fa-heartbeat'],
                                'mobil' => ['primary', 'Transportasi', 'fas fa-truck-pickup'],
                                'acara' => ['warning', 'Acara / Event', 'fas fa-calendar-alt'],
                                'donasi_barang' => ['info', 'Donasi Barang', 'fas fa-box-open'],
                                'peminjaman_barang' => ['secondary', 'Pinjam Barang', 'fas fa-tools'],
                                ];
                                $type = $kegiatan->detail_type;
                                $config = $badges[$type] ?? ['secondary', 'Lainnya', 'fas fa-question'];
                                @endphp
                                <span class="badge bg-{{ $config[0] }} bg-opacity-10 text-{{ $config[0] }} border border-{{ $config[0] }} px-2 py-1">
                                    <i class="{{ $config[2] }} me-1"></i> {{ $config[1] }}
                                </span>
                            </td>

                            {{-- Target/Info (Dinamis sesuai jenis) --}}
                            <td class="small">
                                @if($type == 'donasi_dana')
                                Target: <span class="fw-bold">Rp {{ number_format($kegiatan->detail->target_rupiah ?? 0, 0, ',', '.') }}</span>
                                @elseif($type == 'donasi_darah')
                                Target: <span class="fw-bold">{{ $kegiatan->detail->target_kantong }} Kantong</span>
                                @elseif($type == 'mobil')
                                Unit: <span class="fw-bold">{{ $kegiatan->detail->jumlah_unit }} Mobil</span>
                                @elseif($type == 'acara')
                                Kuota: <span class="fw-bold">{{ $kegiatan->detail->kuota_peserta }} Peserta</span>
                                @elseif($type == 'donasi_barang')
                                <span class="fw-bold">{{ $kegiatan->detail->target_item }}</span> ({{ $kegiatan->detail->target_jumlah }} Pcs)
                                @elseif($type == 'peminjaman_barang')
                                <span class="fw-bold">{{ $kegiatan->detail->nama_barang }}</span> (Stok: {{ $kegiatan->detail->stok_tersedia }})
                                @endif
                            </td>

                            {{-- Waktu --}}
                            <td>
                                <div class="small fw-bold">{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d M Y') }}</div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('H:i') }} WIB</div>
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($kegiatan->status == 'buka')
                                <span class="badge bg-success rounded-pill px-3">Buka</span>
                                @elseif($kegiatan->status == 'tutup')
                                <span class="badge bg-warning text-dark rounded-pill px-3">Tutup</span>
                                @else
                                <span class="badge bg-secondary rounded-pill px-3">Selesai</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-end pe-4 text-nowrap">
                                <a href="{{ route('admin.kegiatan.peserta', $kegiatan->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Lihat Peserta">
                                    <i class="fas fa-users"></i>
                                </a>
                                <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-outline-dark" title="Edit Kegiatan">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0 fw-bold">Belum ada kegiatan yang dibuat.</p>
                                <p class="small">Silakan tambah kegiatan baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="card-footer bg-white py-3">
                {{ $kegiatans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection