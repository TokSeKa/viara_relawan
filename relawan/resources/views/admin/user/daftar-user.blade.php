@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content')
<div class="container pb-5">

    {{-- HEADER --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-1">
                <i class="fas fa-users me-2"></i> Daftar Pengguna
            </h3>
            <p class="text-muted mb-0">Total {{ $users->total() }} pengguna terdaftar.</p>
        </div>

        {{-- FORM PENCARIAN --}}
        <div class="col-md-6 mt-3 mt-md-0">
            <form action="{{ route('admin.users.index_admin_user') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control border-dark"
                        placeholder="Cari nama relawan..."
                        value="{{ request('search') }}">
                    <button class="btn btn-dark" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                    <a href="{{ route('admin.users.index_admin_user') }}" class="btn btn-outline-danger">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="py-3 ps-4" width="5%">No</th>
                            <th class="py-3" width="30%">Nama Lengkap</th>
                            <th class="py-3" width="20%">Email</th>
                            <th class="py-3" width="20%">Role / Jabatan</th>
                            <th class="py-3" width="15%">Bergabung Sejak</th>
                            <th class="py-3 text-end pe-4" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $users->firstItem() + $index }}</td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <img src="{{ $user->profile_photo_url }}"
                                            class="rounded-circle object-fit-cover border shadow-sm"
                                            width="40" height="40"
                                            alt="{{ $user->name }}">
                                    </div>
                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                </div>
                            </td>

                            <td class="text-secondary small">{{ $user->email }}</td>

                            {{-- KOLOM JABATAN (Updated) --}}
                            <td>
                                @if($user->jabatan == 'relawan')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill">
                                    RELAWAN
                                </span>
                                @elseif($user->jabatan == 'blokir')
                                <span class="badge bg-danger text-white border border-danger px-3 py-1 rounded-pill">
                                    <i class="fas fa-ban me-1"></i> BLOKIR
                                </span>
                                @elseif(str_contains($user->jabatan, 'admin'))
                                {{-- Mengubah 'admin_logistik' jadi 'ADMIN LOGISTIK' --}}
                                <span class="badge bg-dark text-white border border-dark px-3 py-1 rounded-pill">
                                    <i class="fas fa-user-shield me-1"></i> {{ strtoupper(str_replace('_', ' ', $user->jabatan)) }}
                                </span>
                                @else
                                <span class="badge bg-secondary">{{ $user->jabatan }}</span>
                                @endif
                            </td>

                            <td class="small text-muted">
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            <td class="text-end pe-4">
                                <a href="{{ route('admin.users.show_admin_user', $user->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail Profil">
                                    <i class="fas fa-user-circle fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-search-minus fa-2x mb-3 opacity-25"></i>
                                <p class="mb-0">Tidak ditemukan hasil untuk "{{ request('search') }}"</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white py-3">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection