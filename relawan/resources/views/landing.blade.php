@extends('layouts.app')

@section('title', 'Selamat Datang - Mari Berbagi Cahaya')

@push('styles')
<style>
    /* Menghilangkan navbar default jika ingin fokus ke landing, atau biarkan saja */
    .question-section {
        min-height: 80vh;
        display: none;
        /* Sembunyikan semua dulu */
        align-items: center;
        justify-content: center;
        text-align: center;
        animation: fadeIn 0.8s ease-in-out;
    }

    .question-section.active {
        display: flex;
    }

    .q-card {
        max-width: 600px;
        padding: 40px;
        border: none;
        background: transparent;
    }

    .q-text {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 30px;
        line-height: 1.4;
    }

    .btn-choice {
        padding: 15px 40px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 50px;
        transition: all 0.3s;
        margin: 10px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .progress-thin {
        height: 5px;
        position: fixed;
        top: 0;
        left: 0;
        background: #ffc107;
        transition: width 0.4s;
        z-index: 9999;
    }
</style>
@endpush

@section('content')
<div class="progress-thin" id="progressBar" style="width: 0%"></div>

<div class="container">
    {{-- STEP 1: HERO PEMBUKA --}}
    <div class="question-section active" id="step-1">
        <div class="q-card">
            <i class="fas fa-heart text-danger fa-4x mb-4"></i>
            <h1 class="display-4 fw-bold mb-3">Pernahkah kamu merasa ingin melakukan sesuatu yang berarti?</h1>
            <p class="lead text-muted mb-4">Mungkin hari ini adalah jawaban dari pencarianmu.</p>
            <button class="btn btn-primary btn-choice" onclick="nextStep(2)">MULAI</button>
        </div>
    </div>

    {{-- STEP 2: PERTANYAAN PSIKOLOGIS 1 --}}
    <div class="question-section" id="step-2">
        <div class="q-card">
            <div class="q-text">Apakah kamu sedang merasa lelah dengan rutinitas yang itu-itu saja?</div>
            <button class="btn btn-outline-primary btn-choice" onclick="nextStep(3)">IYA</button>
            <button class="btn btn-outline-secondary btn-choice" onclick="nextStep(3)">TIDAK JUGA</button>
        </div>
    </div>

    {{-- STEP 3: PERTANYAAN PSIKOLOGIS 2 --}}
    <div class="question-section" id="step-3">
        <div class="q-card">
            <div class="q-text">Kadang, membantu orang lain adalah cara terbaik untuk menyembuhkan diri sendiri. Setuju?</div>
            <button class="btn btn-outline-primary btn-choice" onclick="nextStep(4)">SANGAT SETUJU</button>
            <button class="btn btn-outline-secondary btn-choice" onclick="nextStep(4)">MUNGKIN</button>
        </div>
    </div>

    {{-- STEP 4: PERTANYAAN PSIKOLOGIS 3 --}}
    <div class="question-section" id="step-4">
        <div class="q-card">
            <div class="q-text">Jika kamu diberikan kesempatan untuk menjadi alasan seseorang tersenyum hari ini, maukah kamu mengambilnya?</div>
            <button class="btn btn-warning btn-choice text-dark" onclick="nextStep(5)">TENTU SAJA</button>
            <button class="btn btn-outline-secondary btn-choice" onclick="nextStep(5)">SAYA RAGU</button>
        </div>
    </div>

    {{-- STEP 5: PENUTUP / CALL TO ACTION --}}
    <div class="question-section" id="step-5">
        <div class="q-card">
            <i class="fas fa-dove text-warning fa-4x mb-4"></i>
            <h2 class="fw-bold mb-3">Kami Menunggumu di Keluarga Relawan.</h2>
            <p class="text-muted mb-4">Ada banyak tangan yang membutuhkan bantuanmu. Mari bergabung bersama Vihara Maitreya.</p>

            <div class="d-grid gap-2">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg fw-bold rounded-pill">DAFTAR JADI RELAWAN SEKARANG</a>
                <a href="{{ route('kegiatan.index') }}" class="btn btn-link text-decoration-none text-muted">Lihat daftar kegiatan dulu</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 5;

    function nextStep(step) {
        // Sembunyikan step sekarang
        document.getElementById(`step-${currentStep}`).classList.remove('active');

        // Tampilkan step baru
        document.getElementById(`step-${step}`).classList.add('active');

        // Update progress bar
        currentStep = step;
        let progressWidth = ((currentStep - 1) / (totalSteps - 1)) * 100;
        document.getElementById('progressBar').style.width = progressWidth + '%';

        // Scroll ke atas otomatis
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
</script>
@endpush
@endsection