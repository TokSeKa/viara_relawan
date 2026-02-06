<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Peserta - {{ $kegiatan->judul }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .bg-light {
                background-color: #fff !important;
            }

            /* Hemat tinta */
        }
    </style>
</head>

<body class="bg-white p-5">

    {{-- BAGIAN 1: HEADER INFORMASI KEGIATAN --}}
    <div class="border-bottom pb-4 mb-4">
        <h5 class="text-uppercase text-muted ls-1 mb-1">Laporan Partisipasi</h5>
        <h2 class="fw-bold mb-3">{{ $kegiatan->judul }}</h2>

        <div class="row">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="120" class="fw-bold">Jenis Kegiatan</td>
                        <td>: {{ ucwords(str_replace('_', ' ', $kegiatan->detail_type)) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status</td>
                        <td>: {{ ucfirst($kegiatan->status) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tanggal</td>
                        <td>: {{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d F Y, H:i') }}</td>
                    </tr>
                </table>
            </div>

            {{-- Detail Khusus (Polymorphic) --}}
            <div class="col-6">
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold border-bottom pb-2 mb-2">Detail Teknis</h6>
                    <ul class="list-unstyled mb-0 small">
                        @if($kegiatan->detail_type == 'donasi_dana')
                        <li>Target: Rp {{ number_format($kegiatan->detail->target_rupiah, 0, ',', '.') }}</li>
                        @elseif($kegiatan->detail_type == 'donasi_darah')
                        <li>Target: {{ $kegiatan->detail->target_kantong }} Kantong</li>
                        <li>Lokasi: {{ $kegiatan->detail->lokasi_pmi }}</li>
                        @elseif($kegiatan->detail_type == 'mobil')
                        <li>Lokasi Jemput: {{ $kegiatan->detail->lokasi_jemput }}</li>
                        <li>Armada: {{ $kegiatan->detail->jumlah_unit }} Unit</li>
                        @elseif($kegiatan->detail_type == 'acara')
                        <li>Lokasi: {{ $kegiatan->detail->lokasi }}</li>
                        <li>Kuota: {{ $kegiatan->detail->kuota_peserta }} Orang</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: TABEL PESERTA --}}
    <h5 class="fw-bold mb-3">Daftar Relawan / Peserta ({{ $kegiatan->partisipasis->count() }} Orang)</h5>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="25%">Nama Relawan</th>
                    <th width="20%">Kontak (Email/HP)</th>
                    <th width="15%">Waktu Bergabung</th>
                    <!-- <th width="35%">Data Tambahan (JSON)</th> -->
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatan->partisipasis as $index => $partisipasi)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="fw-bold d-block">{{ $partisipasi->user->name }}</span>
                        <small class="text-muted">{{ ucfirst($partisipasi->user->jenis_kelamin) }}</small>
                    </td>
                    <td>
                        <div class="small">{{ $partisipasi->user->email }}</div>
                        <div class="small">{{ $partisipasi->user->no_hp }}</div>
                    </td>
                    <td>
                        {{ $partisipasi->created_at->format('d/m/Y H:i') }}
                    </td>
                    <!-- <td>
                        {{-- LOGIKA JSON DATA TAMBAHAN --}}
                        @if($partisipasi->data_tambahan)
                            <ul class="mb-0 small ps-3">
                                @foreach(json_decode($partisipasi->data_tambahan, true) ?? [] as $key => $value)
                                    <li>
                                        <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> 
                                        {{ is_string($value) ? $value : json_encode($value) }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted fst-italic">- Tidak ada data tambahan -</span>
                        @endif
                    </td> -->
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        Belum ada relawan yang bergabung di kegiatan ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="d-flex justify-content-end mt-5 no-print">
        <button onclick="window.print()" class="btn btn-primary fw-bold">
            <i class="fas fa-print me-2"></i> Cetak Dokumen
        </button>
    </div>

    <div class="mt-5 text-end d-none d-print-block">
        <p class="mb-5">Batam, {{ date('d F Y') }}<br>Mengetahui,</p>
        <p class="fw-bold text-decoration-underline">{{ Auth::user()->name }}</p>
    </div>

</body>

</html>