<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        /* CSS Khusus Cetak */
        @media print {
            .no-print { display: none !important; }
            .table { border: 1px solid #000 !important; }
            th, td { border: 1px solid #000 !important; }
        }
    </style>
</head>
<body class="bg-white p-5">

    {{-- KOP LAPORAN --}}
    <div class="text-center mb-4">
        <h3 class="fw-bold mb-0">LAPORAN REKAPITULASI KEGIATAN</h3>
        <h5 class="fw-bold">VIARA MAITREYAWIRA</h5>
        <p class="mb-0">
            Periode: {{ \Carbon\Carbon::parse($request->tgl_awal)->format('d M Y') }} 
            s/d {{ \Carbon\Carbon::parse($request->tgl_akhir)->format('d M Y') }}
        </p>
        <small class="text-muted">
            Filter: Status ({{ ucfirst($request->status ?? 'Semua') }}) | 
            Jenis ({{ ucfirst(str_replace('_', ' ', $request->jenis ?? 'Semua')) }})
        </small>
    </div>

    {{-- TABEL DATA --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Tgl Mulai</th>
                    <th width="20%">Nama Kegiatan</th>
                    <th width="10%">Jenis</th>
                    <th width="10%">Status</th>
                    <th width="35%">Detail Khusus (Target/Lokasi)</th>
                    <th width="10%">Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $index => $kegiatan)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d/m/Y') }}
                    </td>
                    <td class="fw-bold">{{ $kegiatan->judul }}</td>
                    
                    {{-- Jenis Kegiatan --}}
                    <td class="text-center">
                        <span class="badge bg-secondary">
                            {{ ucwords(str_replace('_', ' ', $kegiatan->detail_type)) }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="text-center text-uppercase fw-bold small">
                        {{ $kegiatan->status }}
                    </td>

                    {{-- LOGIKA DETAIL KHUSUS (POLYMORPHIC) --}}
                    <td>
                        <ul class="list-unstyled mb-0 small">
                            @if($kegiatan->detail_type == 'donasi_dana')
                                <li><strong>Target:</strong> Rp {{ number_format($kegiatan->detail->target_rupiah, 0, ',', '.') }}</li>
                                <li><strong>Bank:</strong> {{ count($kegiatan->detail->info_bank ?? []) }} Rekening</li>

                            @elseif($kegiatan->detail_type == 'donasi_darah')
                                <li><strong>Target:</strong> {{ $kegiatan->detail->target_kantong }} Kantong</li>
                                <li><strong>Gol. Darah:</strong> {{ implode(', ', $kegiatan->detail->golongan_darah_needed ?? []) }}</li>
                                <li><strong>PMI:</strong> {{ $kegiatan->detail->lokasi_pmi }}</li>

                            @elseif($kegiatan->detail_type == 'mobil')
                                <li><strong>Armada:</strong> {{ $kegiatan->detail->jumlah_unit }} Unit</li>
                                <li><strong>Supir:</strong> {{ $kegiatan->detail->butuh_supir ? 'Disediakan' : 'Mandiri' }}</li>
                                <li><strong>Jemput:</strong> {{ Str::limit($kegiatan->detail->lokasi_jemput, 50) }}</li>

                            @elseif($kegiatan->detail_type == 'acara')
                                <li><strong>Lokasi:</strong> {{ Str::limit($kegiatan->detail->lokasi, 50) }}</li>
                                <li><strong>Kuota:</strong> {{ $kegiatan->detail->kuota_peserta }} Org</li>
                            @endif
                        </ul>
                    </td>

                    <td class="text-center small">{{ $kegiatan->admin->name ?? 'System' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted fst-italic">
                        Tidak ada data kegiatan pada periode dan filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FOOTER TANDA TANGAN --}}
    <div class="d-flex justify-content-end mt-5 no-print">
        <button onclick="window.print()" class="btn btn-dark fw-bold">
            <i class="fas fa-print me-2"></i> Cetak PDF
        </button>
    </div>

    <div class="mt-5 text-end d-none d-print-block" style="page-break-inside: avoid;">
        <p class="mb-5">Batam, {{ date('d F Y') }}<br>Mengetahui,</p>
        <br><br>
        <p class="fw-bold text-decoration-underline">{{ Auth::user()->name }}</p>
        <p>Admin Pengelola</p>
    </div>

</body>
</html>