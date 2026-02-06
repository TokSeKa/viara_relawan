<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik Minat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            /* Paksa print background color untuk bar chart */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        .progress {
            height: 20px;
            background-color: #e9ecef;
        }
    </style>
</head>

<body class="bg-white p-5">

    {{-- KOP LAPORAN --}}
    <div class="text-center mb-5">
        <h3 class="fw-bold mb-0">LAPORAN STATISTIK MINAT RELAWAN</h3>
        <h5 class="fw-bold">VIARA MAITREYAWIRA</h5>
        <p class="text-muted">Analisis Distribusi Ketertarikan (Tag) Relawan</p>
    </div>

    {{-- RINGKASAN --}}
    <div class="alert alert-light border mb-4">
        <strong>Total Basis Data Relawan:</strong> {{ $totalRelawan }} Orang
    </div>

    {{-- TABEL STATISTIK --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th width="5%">Ranking</th>
                    <th width="25%">Kategori Minat (Tag)</th>
                    <th width="50%">Grafik Distribusi</th>
                    <th width="20%">Jumlah Peminat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tags as $index => $tag)
                @php
                // Hitung Persentase untuk Lebar Grafik
                $persen = $totalRelawan > 0 ? ($tag->users_count / $totalRelawan) * 100 : 0;

                // Warna Bar (Top 3 beda warna)
                $color = match($index) {
                0 => 'bg-success', // Juara 1
                1 => 'bg-primary', // Juara 2
                2 => 'bg-info', // Juara 3
                default => 'bg-secondary'
                };
                @endphp
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td class="fw-bold text-uppercase">{{ $tag->nama_tag }}</td>

                    {{-- Visualisasi Bar Chart --}}
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                <div class="progress-bar {{ $color }}" role="progressbar"
                                    style="width: {{ $persen }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($persen, 1) }}%</small>
                        </div>
                    </td>

                    <td class="text-center">
                        <span class="fs-6 fw-bold">{{ $tag->users_count }}</span>
                        <small class="text-muted">Orang</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        Belum ada data tag tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="d-flex justify-content-end mt-5 no-print">
        <button onclick="window.print()" class="btn btn-dark fw-bold">
            <i class="fas fa-print me-2"></i> Cetak Statistik
        </button>
    </div>

    <div class="mt-5 text-end d-none d-print-block">
        <p class="mb-5">Batam, {{ date('d F Y') }}<br>Mengetahui,</p>
        <p class="fw-bold text-decoration-underline">{{ Auth::user()->name }}</p>
    </div>

</body>

</html>