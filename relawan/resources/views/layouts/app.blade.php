<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul Halaman --}}
    <title>@yield('title', 'Viara Maitreyawira Batam')</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- FontAwesome (Ikon) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Trik Sticky Footer (Footer selalu di bawah) */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        main {
            flex: 1;
        }

        /* Efek hover di navbar */
        .nav-link:hover {
            color: #ffc107 !important;
            /* Kuning emas */
        }

        .active-nav {
            color: #ffc107 !important;
            font-weight: bold;
        }

        /* Utility Classes Custom */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Modal Notifikasi Styling */
        .notif-item {
            transition: background-color 0.2s;
        }

        .notif-item:hover {
            background-color: #f1f3f5 !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3 border-bottom border-secondary">
            <div class="container">
                {{-- 1. BRAND / LOGO --}}
                {{-- Tambahkan d-flex dan batasi max-width agar tidak menabrak tombol menu --}}
                <a class="navbar-brand fw-bold text-uppercase d-flex align-items-center gap-2" href="{{ url('/') }}" style="max-width: 85%;">

                    {{-- Ikon --}}
                    <i class="fas fa-hand-holding-heart text-warning fs-4 flex-shrink-0"></i>

                    {{-- Wrapper Teks & Badge --}}
                    {{-- Di HP (Default): Flex Column (Atas Bawah). Di Layar Besar (lg): Flex Row (Sampingan) --}}
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center lh-1">

                        {{-- Teks Judul (text-wrap agar kalau kepanjangan di HP dia turun baris, bukan nabrak) --}}
                        <span class="text-wrap" style="font-size: 1rem;">Viara Maitreyawira Batam</span>

                        {{-- Label Admin --}}
                        @if(Auth::check() && Auth::user()->jabatan == 'admin')
                        {{-- Badge ditaruh di bawah teks pada HP, dan di sebelah kanan teks pada Desktop --}}
                        <span class="badge bg-danger mt-1 mt-lg-0 ms-0 ms-lg-2" style="font-size: 0.6rem; width: fit-content;">
                            ADMIN PANEL
                        </span>
                        @endif
                    </div>
                </a>

                {{-- Tombol Menu Custom: Teks + Ikon --}}
                <button class="navbar-toggler border-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="outline: none; box-shadow: none;">
                    <span class="fw-bold text-white small text-uppercase" style="letter-spacing: 1px;">Menu</span>
                    <i class="fas fa-bars text-white fs-4"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto gap-2 align-items-center">

                        @auth
                        {{-- ================================================= --}}
                        {{-- LOGIKA PEMISAH NAVBAR (ADMIN vs RELAWAN) --}}
                        {{-- ================================================= --}}

                        @if(Auth::user()->jabatan == 'admin')
                        {{-- 1. MENU KHUSUS ADMIN --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active-nav' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.kegiatan.*') ? 'active-nav' : '' }}" href="{{ route('admin.kegiatan.index') }}">
                                <i class="fas fa-clipboard-list me-1"></i> Kegiatan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.notifikasi.*') ? 'active-nav' : '' }}" href="{{ route('admin.notifikasi.index') }}">
                                <i class="fas fa-bullhorn me-1"></i> Notifikasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active-nav' : '' }}" href="{{ route('admin.users.index_admin_user') }}">
                                <i class="fas fa-users me-1"></i> Pengguna
                            </a>
                        </li>

                        @else
                        {{-- 2. MENU KHUSUS RELAWAN / USER BIASA --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-search me-1"></i> Cari Kegiatan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('riwayat') ? 'active-nav' : '' }}" href="{{ route('riwayat') }}">
                                <i class="fas fa-history me-1"></i> Riwayat Saya
                            </a>
                        </li>
                        @endif

                        @endauth

                        {{-- Pemisah Vertikal --}}
                        <li class="nav-item d-none d-lg-block mx-2 border-end border-secondary" style="height: 20px;"></li>

                        {{-- DROPDOWN USER (Sama untuk Keduanya) --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn btn-outline-secondary px-3 text-white border-0" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name ?? 'Tamu' }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">

                                {{-- Menu Dropdown Admin vs Relawan beda sedikit --}}
                                @if(Auth::check() && Auth::user()->jabatan != 'admin')
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.profile.edit') }}">
                                        <i class="fas fa-id-card me-2 text-muted"></i> Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.tags.edit') }}">
                                        <i class="fas fa-tags me-2 text-muted"></i> Minat & Skill
                                    </a>
                                </li>
                                @endif

                                {{-- Notifikasi (Mobile) --}}
                                <li class="d-lg-none">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#notificationModal">
                                        <i class="fas fa-bell me-2 text-muted"></i> Notifikasi
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>

                        {{-- Tombol Notifikasi (Hanya muncul untuk RELAWAN di Desktop) --}}
                        @if(Auth::check() && Auth::user()->jabatan != 'admin')
                        <li class="nav-item ms-2 d-none d-lg-block">
                            <a class="nav-link position-relative cursor-pointer text-white" data-bs-toggle="modal" data-bs-target="#notificationModal">
                                <i class="fas fa-bell fa-lg"></i>
                                @if($globalCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-dark" style="font-size: 0.6rem;">
                                    {{ $globalCount }}
                                </span>
                                @endif
                            </a>
                        </li>
                        @endif

                    </ul>
                </div>
            </div>
        </nav>
    </header>

    {{-- KONTEN UTAMA --}}
    <main class="py-4">
        @yield('content')
    </main>

    {{-- 2. FOOTER --}}
    <footer class="bg-dark text-white pt-5 pb-3 border-top border-secondary mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h5 class="fw-bold text-uppercase text-warning mb-2">
                        <i class="fas fa-hands-helping me-2"></i> Viara Maitreyawira Batam
                    </h5>
                    <p class="mb-1 text-white-50" style="font-size: 0.95rem;">
                        Wadah kebersamaan untuk kegiatan sosial dan kemanusiaan.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="p-3 rounded border border-secondary d-inline-block bg-secondary bg-opacity-10">
                        <small class="text-uppercase text-warning fw-bold d-block mb-1">Butuh Bantuan?</small>
                        <span class="fs-6 fw-bold">
                            <i class="fas fa-envelope me-2"></i> info@viaramaitreyawira.batam
                        </span>
                    </div>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="text-center text-secondary small">
                &copy; {{ date('Y') }} Viara Maitreyawira Batam. Mari berbuat baik hari ini.
            </div>
        </div>
    </footer>

    {{-- MODAL NOTIFIKASI --}}
    @auth
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg overflow-hidden">

                {{-- Header Modal --}}
                <div class="modal-header bg-dark text-white py-3 border-bottom border-secondary">
                    <h6 class="modal-title fw-bold">
                        <i class="fas fa-bell me-2 text-warning"></i> Pusat Notifikasi
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body Modal (Scrollable Area) --}}
                <div class="modal-body p-0 bg-white">
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">

                        @forelse($globalNotif as $notif)
                        {{-- Tentukan Warna & Icon Berdasarkan Tipe --}}
                        @php
                        $iconClass = match($notif->type) {
                        'info' => 'fas fa-info-circle text-info',
                        'warning' => 'fas fa-exclamation-triangle text-warning',
                        'danger' => 'fas fa-times-circle text-danger',
                        'success' => 'fas fa-check-circle text-success',
                        default => 'fas fa-bell text-primary'
                        };

                        $bgClass = match($notif->type) {
                        'danger' => 'bg-danger bg-opacity-10',
                        default => 'bg-white'
                        };
                        @endphp

                        <div class="list-group-item p-3 border-bottom notif-item {{ $bgClass }}">
                            <div class="d-flex w-100 justify-content-between mb-1 align-items-start">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="{{ $iconClass }} fs-5"></i>

                                    <strong class="text-dark small lh-sm">
                                        @if($notif->target_audience == 'kegiatan')
                                        <span class="badge bg-primary rounded-1 me-1" style="font-size: 0.65rem;">KEGIATAN</span>
                                        @elseif($notif->target_audience == 'tag')
                                        <span class="badge bg-info text-dark rounded-1 me-1" style="font-size: 0.65rem;">MINAT</span>
                                        @endif
                                        {{ $notif->judul }}
                                    </strong>
                                </div>
                                <small class="text-muted flex-shrink-0 ms-2" style="font-size: 0.75rem;">
                                    {{ $notif->created_at->diffForHumans() }}
                                </small>
                            </div>

                            <p class="mb-1 small text-secondary text-truncate-2 ms-4 ps-1">
                                {{ $notif->pesan }}
                            </p>

                            {{-- Tombol Aksi (Looping Kegiatan - VERTIKAL) --}}
                            @if($notif->target_audience == 'kegiatan' && $notif->kegiatan_id)
                            <div class="ms-4 ps-1 mt-2">
                                @php
                                $ids = explode(',', $notif->kegiatan_id);
                                $kegiatanList = \App\Models\Kegiatan::whereIn('id', $ids)->get(['id', 'judul']);
                                @endphp

                                @foreach($kegiatanList as $keg)
                                <a href="{{ route('kegiatan.show', $keg->id) }}" class="btn btn-sm btn-outline-dark py-1 px-3 rounded-pill mb-2 d-block w-100 text-start" style="font-size: 0.75rem;">
                                    <i class="fas fa-arrow-right me-2"></i> {{ Str::limit($keg->judul, 40) }}
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        @empty
                        <div class="text-center py-5">
                            <div class="mb-3 text-muted opacity-25">
                                <i class="fas fa-bell-slash fa-4x"></i>
                            </div>
                            <h6 class="fw-bold text-muted">Tidak ada notifikasi</h6>
                            <p class="small text-muted mb-0">Semua info terbaru akan muncul di sini.</p>
                        </div>
                        @endforelse

                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="modal-footer py-2 bg-light border-top">
                    <button type="button" class="btn btn-sm btn-secondary w-100 fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>