@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content')
<div class="container pb-5">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-users me-2"></i> Daftar Pengguna
            </h3>
            <p class="text-muted mb-0">Total {{ $users->total() }} pengguna terdaftar.</p>
        </div>

        {{-- Opsional: Tombol untuk export atau add user manual jika perlu --}}
        {{-- <a href="#" class="btn btn-outline-dark btn-sm"><i class="fas fa-download me-1"></i> Export Data</a> --}}
    </div>

    {{-- TABEL --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="py-3 ps-4" width="5%">No</th>
                            <th class="py-3" width="25%">Nama Lengkap</th>
                            <th class="py-3" width="25%">Email</th>
                            <th class="py-3" width="15%">Role / Jabatan</th>
                            <th class="py-3" width="15%">Bergabung Sejak</th>
                            <th class="py-3 text-end pe-4" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            {{-- Nomor Urut --}}
                            <td class="ps-4 fw-bold">{{ $users->firstItem() + $index }}</td>

                            {{-- Nama & Avatar Kecil --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-secondary me-2" style="width: 35px; height: 35px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="text-secondary small">{{ $user->email }}</td>

                            {{-- Role Badge --}}
                            <td>
                                @if($user->jabatan == 'blokir')
                                <span class="badge bg-danger text-white border border-danger px-3 py-1 rounded-pill">
                                    <i class="fas fa-user-shield me-1"></i> BLOKIR
                                </span>
                                @elseif($user->jabatan == 'admin')
                                <span class="badge bg-dark text-white border border-dark px-3 py-1 rounded-pill">
                                    <i class="fas fa-user-shield me-1"></i> ADMIN
                                </span>
                                @else
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill">
                                    <i class="fas fa-hands-helping me-1"></i> RELAWAN
                                </span>
                                @endif
                            </td>

                            {{-- Tanggal Join --}}
                            <td class="small text-muted">
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            {{-- Tombol Aksi --}}
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.users.show_admin_user', $user->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail Profil">
                                    <i class="fas fa-user-circle fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-2x mb-3 opacity-25"></i>
                                <p class="mb-0">Belum ada user yang terdaftar.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="card-footer bg-white py-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection