@php
// 1. Ambil Musik Latar Aktif
$activeMusic = \App\Models\Music::where('is_active', true)->first();

// 2. Logika Notifikasi (Hanya untuk User Login & Bukan Admin)
$globalCount = 0;
$globalNotif = collect();

if(Auth::check() && !str_contains(Auth::user()->jabatan, 'admin')) {
$user = Auth::user();
$userTagIds = $user->tags ? $user->tags->pluck('id')->toArray() : [];

// Query Notifikasi
$query = \App\Models\Notifikasi::query()
->where(function($q) {
$q->where('expires_at', '>', now())
->orWhereNull('expires_at');
})
->where(function($q) use ($userTagIds) {
// A. Umum
$q->where('target_audience', 'all')
// B. Berdasarkan Minat (Tag)
->orWhere(function($sub) use ($userTagIds) {
if(!empty($userTagIds)) {
$sub->where('target_audience', 'tag')->whereIn('tag_id', $userTagIds);
}
})
// C. Kegiatan Spesifik
->orWhere(function($sub) {
$sub->where('target_audience', 'kegiatan')->whereNotNull('kegiatan_id');
});
});

// Ambil 5 terbaru
$globalNotif = $query->latest()->take(5)->get();
$globalCount = $query->count();
}
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $webProfile->name)</title>

    {{-- CSS Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;

            /* [BARU] BACKGROUND IMAGE SETUP */
            /* Pastikan file ada di public/images/bg.jpeg */
            background-image: url("{{ asset('images/bg.jpeg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            /* Agar background tetap diam saat scroll */
        }

        /* Overlay transparan agar konten tetap terbaca di atas gambar */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.55);
            /* Putih transparan 85% */
            z-index: -1;
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

        /* Animasi Lonceng saat ada notif baru */
        @keyframes bellShake {
            0% {
                transform: rotate(0);
            }

            15% {
                transform: rotate(5deg);
            }

            30% {
                transform: rotate(-5deg);
            }

            45% {
                transform: rotate(4deg);
            }

            60% {
                transform: rotate(-4deg);
            }

            75% {
                transform: rotate(2deg);
            }

            85% {
                transform: rotate(-2deg);
            }

            100% {
                transform: rotate(0);
            }
        }

        .bell-shake {
            animation: bellShake 0.5s cubic-bezier(.36, .07, .19, .97) both;
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

                        {{-- 2. TOMBOL MUSIK LATAR --}}
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
                        {{-- 3. MENU LOGIN --}}
                        @if(str_contains(Auth::user()->jabatan, 'admin'))
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active-nav' : '' }}" href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                        @else
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('riwayat') ? 'active-nav' : '' }}" href="{{ route('riwayat') }}">Riwayat Saya</a></li>
                        @endif
                        @endauth

                        <li class="nav-item d-none d-lg-block mx-2 border-end border-secondary" style="height: 20px;"></li>

                        @guest
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-bold px-3 me-lg-1">LOGIN</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-warning btn-sm fw-bold px-3 text-dark">DAFTAR</a>
                        </li>
                        @else
                        {{-- 4. DROPDOWN USER --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white px-3" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li>
                                    <h6 class="dropdown-header text-uppercase small text-muted">Akun Saya</h6>
                                </li>
                                <li><a class="dropdown-item py-2" href="{{ route('user.profile.edit') }}"><i class="fas fa-id-card me-2 text-secondary w-25"></i> Profil</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('user.tags.edit') }}"><i class="fas fa-tags me-2 text-secondary w-25"></i> Minat</a></li>
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

                        {{-- 5. IKON NOTIFIKASI (Hanya Relawan) --}}
                        @if(!str_contains(Auth::user()->jabatan, 'admin'))
                        <li class="nav-item ms-2 d-none d-lg-block">
                            <a class="nav-link position-relative cursor-pointer text-white" data-bs-toggle="modal" data-bs-target="#notificationModal">
                                <i id="bellIcon" class="fas fa-bell fa-lg"></i>
                                @if($globalCount > 0)
                                <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-dark" style="font-size: 0.6rem;">
                                    {{ $globalCount }}
                                </span>
                                @endif
                            </a>
                        </li>
                        {{-- Versi Mobile --}}
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

    {{-- MODAL NOTIFIKASI --}}
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

                            @if($notif->target_audience == 'kegiatan' && $notif->kegiatan_id)
                            @php
                            $ids = explode(',', $notif->kegiatan_id);
                            $kegiatanList = \App\Models\Kegiatan::whereIn('id', $ids)->take(3)->get(['id', 'judul']);
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

    {{-- SCRIPT 1: BACKGROUND MUSIC PLAYER --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const audio = document.getElementById('bgAudio');
            const btn = document.getElementById('musicToggle');
            const icon = document.getElementById('musicIcon');
            const text = document.getElementById('musicText');

            if (!audio || !btn) return;

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
                audio.play().catch(() => updateUI(false));
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

    {{-- SCRIPT 2: NOTIFIKASI BERSUARA (BASE64 VERSION) --}}
    @auth @if(!str_contains(Auth::user()->jabatan, 'admin'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // [1] TEMPAT ISI BASE64 AUDIO
            const soundBase64 = "data:audio/mp3;base64,//PkZAAcWdsyD6fgAB6KDiQDTHgAgAQm9ZJU+o504WxoG+AEAJhJF2W8uajngK9/Z4rFYrIjfDklWHYO0QCENCAwgXKfvHqAcRn6AdOuTxi9DEOWJZzPv/+8/zzz/Cnz1SRiMWLsbchnDXIpH2Hqne9OQxjAw1L5U1hrkOWMPwpKSkscr08Ylljmeeef7zz////3nn2pGH8nImuxrlFK43T6w3SUmH16enz/Onp7fcKSx9SkpKTD889Z5/rDDD88MMMKlJSUnKSx+fdYYbqWMOf//hnnn9SkpLG6Sksc///8P/PPPPPPDDD869P2pG5f0QOid85BAECTt5YMDCIlgAAcLEJ2fv5Re/+UpTm3kFPs64G4GoJIMMAAADwfjWaajs808eU1/////77/9KUprN3jAyRMsavnPwTchaF2eMjylL/FKa+b0pT/////////FHjzUNWMmob8+cBCs/8Qf//DEH3//BAEHAg74Pn/rB98PgoDWYBQRP//mVGTeYIZTZi3+xmX6LcWOoeGDuGsYLAIRhEhEGBsBt/mECDoYGgEJgBgflgM8sDdlgM7/8x2ARDCgAeNdVkE7NipvLBP5k/PaledX+boToRW6EKhOGD2EWPB6mA4CEYNAExhuAbG//PkZG4t1ecgAM94ACTzfjATlagABuBt5YCILARH//+YaQLhEAGYEgFhhWgsAYMkwUwh////////zAQAQMGAFcxsiRjauYMMbYNkwEwDDDdAC/zJ/J/N7VRn///83QnQv/zA8ARL+mAUASmaqs+xaB2BGA7///////////+RArGE+HKY4ZNpiigGmDeDOYiIbpgUgFGAYAAEAGiQBxgAgEhwAXlYZxjdFTFYZ/mGeGd///////////+PAAGAMAEYEwCQJAPSTU0pQKACCgTTAhAHnEF8ve1f3////+WAzv//////////////9o6g+WSY/7Y4xowBgBDAFAETFan/qDmBSAPrv////////lgDf//ywBsVgbAAQAQMFiM/AxGIwBRaEbfCNvhYkLk8MTDpGX/BEKBAgGf0WBwgUYRt4Mt3BluFDBiMaAnQZD/wwoQMgAcsKoY//8fAskDCQeA00hABg6PoNK//xQKJcFKFQgxBS2X///yfCxsDDIpCgSDVYAQPJo0Jkd50i////58qDQL5fPniJkRJeSLThz/////X6Qz/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qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq//PkZAAAAAGkAOAAAAAAA0gBwAAATEFNRTMuMTAwqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqTEFNRTMuMTAwqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq";

            let notifAudio = null;
            if (soundBase64 && soundBase64.length > 50) {
                notifAudio = new Audio(soundBase64);
            } else {
                console.warn("⚠️ Audio Base64 Masih Kosong! Notifikasi tidak akan bunyi.");
            }

            let audioUnlocked = false;

            // [2] UNLOCK AUDIO
            function unlockAudio() {
                if (!audioUnlocked && notifAudio) {
                    notifAudio.play().then(() => {
                        notifAudio.pause();
                        notifAudio.currentTime = 0;
                        audioUnlocked = true;
                        document.removeEventListener('click', unlockAudio);
                        document.removeEventListener('keydown', unlockAudio);
                    }).catch(e => {
                        // Silent fail
                    });
                }
            }
            document.addEventListener('click', unlockAudio);
            document.addEventListener('keydown', unlockAudio);

            // [3] LOGIKA POLLING
            let lastCount = parseInt("{{ $globalCount }}") || 0;

            setInterval(function() {
                fetch("{{ route('notifikasi.check') }}", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (typeof data.count !== 'undefined') {

                            if (data.count > lastCount) {

                                // Update Badge
                                const badge = document.getElementById('notifBadge');
                                if (badge) badge.innerText = data.count;

                                // Animasi Lonceng
                                const bell = document.getElementById('bellIcon');
                                if (bell) {
                                    bell.classList.add('bell-shake');
                                    setTimeout(() => bell.classList.remove('bell-shake'), 1000);
                                }

                                // Mainkan Suara
                                if (audioUnlocked && notifAudio) {
                                    notifAudio.currentTime = 0;
                                    notifAudio.play().catch(e => {});
                                }
                            }

                            lastCount = data.count;
                        }
                    })
                    .catch(err => {
                        // Silent error
                    });
            }, 10000); // 10 Detik
        });
    </script>
    @endif @endauth

    @stack('scripts')
</body>

</html>