@php
    // 1. Ambil Musik Aktif (Tabel Music punya kolom is_active)
    $activeMusic = \App\Models\Music::where('is_active', true)->first();

    // 2. Hitung Notifikasi (Hanya jika login & bukan admin)
    $globalCount = 0;
    $globalNotif = collect();

    if(Auth::check() && !str_contains(Auth::user()->jabatan, 'admin')) {
        // Ambil User
        $user = Auth::user();
        
        // Ambil Tag ID user (pastikan di model User ada relasi tags())
        // Kita pakai optional() atau check dulu biar aman kalau user gak punya tag
        $userTagIds = $user->tags ? $user->tags->pluck('id')->toArray() : [];

        // Query Notifikasi
        // PERBAIKAN: Ganti 'is_active' dengan cek tanggal kadaluarsa (expires_at)
        $query = \App\Models\Notifikasi::query()
            ->where(function($q) {
                $q->where('expires_at', '>', now())
                  ->orWhereNull('expires_at');
            })
            ->where(function($q) use ($userTagIds) {
                // A. Notifikasi Umum
                $q->where('target_audience', 'all')
                
                // B. Notifikasi Berdasarkan Minat (Tag)
                  ->orWhere(function($sub) use ($userTagIds) {
                      if(!empty($userTagIds)) {
                          $sub->where('target_audience', 'tag')
                              ->whereIn('tag_id', $userTagIds);
                      }
                  })
                  
                // C. Notifikasi Kegiatan Spesifik
                  ->orWhere(function($sub) {
                      $sub->where('target_audience', 'kegiatan')
                          ->whereNotNull('kegiatan_id');
                  });
            });
            
        // Ambil 5 notifikasi terbaru
        $globalNotif = $query->latest()->take(5)->get();
        $globalCount = $globalNotif->count();
    }
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $webProfile->name)</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        main {
            flex: 1;
        }

        .nav-link:hover {
            color: #ffc107 !important;
        }

        .active-nav {
            color: #ffc107 !important;
            font-weight: bold;
        }

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
                {{-- BRAND / LOGO --}}
                <a class="navbar-brand fw-bold text-uppercase d-flex align-items-center gap-2" href="{{ url('/') }}">
                    <i class="fas fa-hand-holding-heart text-warning fs-4 flex-shrink-0"></i>
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center lh-1">
                        @auth
                        @if(str_contains(Auth::user()->jabatan, 'admin'))
                        <span class="badge bg-danger mb-1 mb-lg-0 me-0 me-lg-2" style="font-size: 0.6rem; width: fit-content;">ADMIN PANEL</span>
                        @endif
                        @endauth
                        <span class="text-wrap" style="font-size: 1rem;">{{ $webProfile->name }}</span>
                    </div>
                </a>

                <button class="navbar-toggler border-0 d-flex d-lg-none align-items-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="fw-bold text-white small text-uppercase">Menu</span>
                    <i class="fas fa-bars text-white fs-4"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto gap-2 align-items-center">

                        {{-- 1. MENU PUBLIK --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('kegiatan.index') ? 'active-nav' : '' }}" href="{{ route('kegiatan.index') }}">
                                <i class="fas fa-search me-1"></i> Cari Kegiatan
                            </a>
                        </li>

                        {{-- 2. TOMBOL MUSIK LATAR (Muncul untuk SEMUA: Guest & Auth) --}}
                        @if($activeMusic)
                        <li class="nav-item me-2">
                            <button id="musicToggle" class="btn btn-outline-warning btn-sm rounded-pill px-3 d-flex align-items-center gap-2">
                                <i id="musicIcon" class="fas fa-play"></i>
                                <span id="musicText" class="d-none d-md-inline small fw-bold text-uppercase">Musik</span>
                            </button>
                            <audio id="bgAudio" loop>
                                <source src="{{ asset('storage/' . $activeMusic->file_path) }}" type="audio/mpeg">
                            </audio>
                        </li>
                        @endif

                        @auth
                        {{-- 3. MENU LOGIN (ADMIN & RELAWAN) --}}
                        @if(str_contains(Auth::user()->jabatan, 'admin'))
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active-nav' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        @else
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('riwayat') ? 'active-nav' : '' }}" href="{{ route('riwayat') }}">Riwayat Saya</a></li>
                        @endif
                        @endauth

                        <li class="nav-item d-none d-lg-block mx-2 border-end border-secondary" style="height: 20px;"></li>

                        {{-- 4. KONDISI GUEST VS AUTH --}}
                        @guest
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-bold px-3 me-lg-1">LOGIN</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-warning btn-sm fw-bold px-3 text-dark">DAFTAR</a>
                        </li>
                        @else
                        {{-- 5. DROPDOWN USER --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white px-3" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li>
                                    <h6 class="dropdown-header text-uppercase small text-muted">Akun Saya</h6>
                                </li>
                                <li><a class="dropdown-item py-2" href="{{ route('user.profile.edit') }}"><i class="fas fa-id-card me-2 text-secondary w-25"></i> Profil</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('user.tags.edit') }}"><i class="fas fa-tags me-2 text-secondary w-25"></i> Minat & Skill</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger fw-bold">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>

                        {{-- 6. IKON NOTIFIKASI (Hanya Relawan) --}}
                        @if(!str_contains(Auth::user()->jabatan, 'admin'))
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
                        {{-- Menu Notifikasi Mobile (d-lg-none) --}}
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#notificationModal">
                                <i class="fas fa-bell me-2"></i> Notifikasi
                                @if($globalCount > 0) <span class="badge bg-danger ms-1">{{ $globalCount }}</span> @endif
                            </a>
                        </li>
                        @endif
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="py-4">
        @yield('content')
    </main>

    <footer class="bg-dark text-white pt-5 pb-3 border-top border-secondary mt-auto">
        <div class="container text-center text-secondary small">
            &copy; {{ date('Y') }} {{ $webProfile->name }}. Mari berbuat baik hari ini.
        </div>
    </footer>

    {{-- MODAL NOTIFIKASI (WAJIB ADA) --}}
    @auth
    @if(!str_contains(Auth::user()->jabatan, 'admin'))
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <div class="modal-header bg-dark text-white py-3 border-bottom border-secondary">
                    <h6 class="modal-title fw-bold"><i class="fas fa-bell me-2 text-warning"></i> Pusat Notifikasi</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 bg-white">
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        @forelse($globalNotif as $notif)
                        @php
                        $iconClass = match($notif->type) {
                        'info' => 'fas fa-info-circle text-info',
                        'warning' => 'fas fa-exclamation-triangle text-warning',
                        'danger' => 'fas fa-times-circle text-danger',
                        'success' => 'fas fa-check-circle text-success',
                        default => 'fas fa-bell text-primary'
                        };
                        $bgClass = ($notif->type == 'danger') ? 'bg-danger bg-opacity-10' : 'bg-white';
                        @endphp

                        <div class="list-group-item p-3 border-bottom notif-item {{ $bgClass }}">
                            <div class="d-flex w-100 justify-content-between mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="{{ $iconClass }} fs-5"></i>
                                    <strong class="text-dark small lh-sm">{{ $notif->judul }}</strong>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $notif->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 small text-secondary text-truncate-2 ms-4 ps-1">{{ $notif->pesan }}</p>

                            {{-- Link ke Kegiatan --}}
                            @if($notif->target_audience == 'kegiatan' && $notif->kegiatan_id)
                            @php
                            $ids = explode(',', $notif->kegiatan_id);
                            $kegiatanList = \App\Models\Kegiatan::whereIn('id', $ids)->get(['id', 'judul']);
                            @endphp
                            <div class="ms-4 ps-1 mt-2">
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
                            <div class="mb-3 text-muted opacity-25"><i class="fas fa-bell-slash fa-4x"></i></div>
                            <h6 class="fw-bold text-muted">Tidak ada notifikasi</h6>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light border-top">
                    <button type="button" class="btn btn-sm btn-secondary w-100 fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- LOGIKA JS AUDIO PLAYER --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const audio = document.getElementById('bgAudio');
            const btn = document.getElementById('musicToggle');
            const icon = document.getElementById('musicIcon');
            const text = document.getElementById('musicText');

            if (!audio || !btn) return;

            // Cek preferensi user di localStorage
            const userPref = localStorage.getItem('music_playing');
            let shouldPlay = userPref !== 'false';

            function updateUI(isPlaying) {
                if (isPlaying) {
                    icon.className = 'fas fa-pause';
                    btn.classList.remove('btn-outline-warning');
                    btn.classList.add('btn-warning');
                    if (text) text.innerText = "MUSIK ON";
                } else {
                    icon.className = 'fas fa-play';
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-outline-warning');
                    if (text) text.innerText = "MUSIK OFF";
                }
            }

            updateUI(shouldPlay);

            if (shouldPlay) {
                audio.play().then(() => {
                    console.log("Autoplay Success");
                }).catch(error => {
                    console.log("Autoplay blocked, waiting interaction.");
                    updateUI(false); // Matikan UI jika diblokir
                });
            }

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (audio.paused) {
                    audio.play();
                    localStorage.setItem('music_playing', 'true');
                    updateUI(true);
                } else {
                    audio.pause();
                    localStorage.setItem('music_playing', 'false');
                    updateUI(false);
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>