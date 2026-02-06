<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Potensi Relawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="bg-white p-5">

    {{-- KOP LAPORAN --}}
    <div class="text-center mb-4">
        <h3 class="fw-bold mb-0">LAPORAN DATA RELAWAN</h3>
        <h5 class="fw-bold">VIARA MAITREYAWIRA</h5>
        <p class="mb-0 text-muted">Diurutkan berdasarkan tingkat keaktifan (Jumlah Partisipasi)</p>

        <div class="mt-2">
            Filter Minat:
            @if(count($selectedTags) > 0)
            @foreach($selectedTags as $tagName)
            <span class="badge bg-dark border border-dark text-white">{{ $tagName }}</span>
            @endforeach
            @else
            <span class="badge bg-secondary">SEMUA KATEGORI</span>
            @endif
        </div>
    </div>

    {{-- TABEL --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th width="5%">Ranking</th> {{-- Ganti No jadi Ranking --}}
                    <th width="25%">Nama Relawan</th>
                    <th width="20%">Kontak</th>
                    <th width="35%">Minat / Tag</th>
                    <th width="15%">Total Kegiatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($relawans as $index => $relawan)
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td>
                        <span class="fw-bold d-block">{{ $relawan->name }}</span>
                        <small class="text-muted">Bergabung: {{ $relawan->created_at->format('d/m/Y') }}</small>
                    </td>
                    <td>
                        <div class="small"><i class="fas fa-envelope me-1"></i> {{ $relawan->email }}</div>
                        <div class="small"><i class="fas fa-phone me-1"></i> {{ $relawan->no_hp }}</div>
                    </td>
                    <td>
                        @if($relawan->tags->count() > 0)
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($relawan->tags as $tag)
                            {{-- Highlight tag yang sedang difilter --}}
                            @php
                            $isActive = in_array($tag->nama_tag, $selectedTags);
                            @endphp
                            <span class="badge {{ $isActive ? 'bg-primary' : 'bg-light text-dark border' }}">
                                {{ $tag->nama_tag }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <span class="text-muted fst-italic small">- Tidak ada tag -</span>
                        @endif
                    </td>

                    {{-- KOLOM PARTISIPASI (BINTANG UTAMA) --}}
                    <td class="text-center">
                        <h4 class="fw-bold mb-0">{{ $relawan->partisipasi_count }}</h4>
                        <small class="text-muted">Partisipasi</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        Tidak ditemukan relawan dengan kriteria minat tersebut.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="d-flex justify-content-end mt-5 no-print">
        <button onclick="window.print()" class="btn btn-success fw-bold">
            <i class="fas fa-print me-2"></i> Cetak Laporan
        </button>
    </div>

    <div class="mt-5 text-end d-none d-print-block">
        <p class="mb-5">Batam, {{ date('d F Y') }}<br>Mengetahui,</p>
        <p class="fw-bold text-decoration-underline">{{ Auth::user()->name }}</p>
    </div>

</body>

</html>