<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Models\Kegiatan;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Base Query
        $query = Notifikasi::query();

        // 2. Filter: Tipe (Info, Warning, dll)
        if ($request->filled('filter_type')) {
            $query->where('type', $request->filter_type);
        }

        // 3. Filter: Tanggal Dibuat
        if ($request->filled('filter_date')) {
            $query->whereDate('created_at', $request->filter_date);
        }

        // 4. SORTING UTAMA (Sesuai Request)
        // Logika:
        // - Jika expires_at > SEKARANG (masih aktif) ATAU NULL (selamanya) -> Prioritas 0 (Atas)
        // - Jika expires_at < SEKARANG (basi) -> Prioritas 1 (Bawah)
        // - Setelah itu urutkan berdasarkan created_at terbaru
        $query->orderByRaw("CASE WHEN expires_at >= NOW() OR expires_at IS NULL THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');

        // 5. Pagination
        $notifikasis = $query->paginate(10)->withQueryString();

        return view('admin.notifikasi.index', compact('notifikasis'));
    }
    // Tampilkan Form Create
    public function create()
    {
        // Ambil kegiatan yang masih aktif saja biar relevan
        $kegiatans = Kegiatan::where('status', 'buka')->orderBy('created_at', 'desc')->get();

        // Ambil semua tag
        $tags = Tag::orderBy('nama_tag', 'asc')->get();

        return view('admin.notifikasi.create', compact('kegiatans', 'tags'));
    }

    // Proses Simpan
    public function store(Request $request)
    {
        $request->validate([
            'judul'           => 'required|string|max:255',
            'pesan'           => 'required|string',
            'type'            => 'required|in:info,warning,danger,success',
            'target_audience' => 'required|in:all,kegiatan,tag',
            'expires_at'      => 'nullable|date|after:now',

            // Validasi Kondisional (Array karena kita mau support multi-select)
            'kegiatan_ids'    => 'required_if:target_audience,kegiatan|array',
            'kegiatan_ids.*'  => 'exists:kegiatans,id',

            'tag_ids'         => 'required_if:target_audience,tag|array',
            'tag_ids.*'       => 'exists:tags,id',
        ]);

        try {
            DB::transaction(function () use ($request) {

                // Siapkan data dasar
                $data = [
                    'judul'           => $request->judul,
                    'pesan'           => $request->pesan,
                    'type'            => $request->type,
                    'target_audience' => $request->target_audience,
                    'expires_at'      => $request->expires_at,
                    'created_by'      => Auth::id(),
                    'kegiatan_id'     => null,
                    'tag_id'          => null,
                ];

                // LOGIKA BARU: IMPLODE (Array to String)
                if ($request->target_audience == 'kegiatan') {
                    // Ubah [1, 5, 9] menjadi "1,5,9"
                    $data['kegiatan_id'] = implode(',', $request->kegiatan_ids);
                } elseif ($request->target_audience == 'tag') {
                    // Ubah [2, 4] menjadi "2,4"
                    $data['tag_id'] = implode(',', $request->tag_ids);
                }

                // Simpan HANYA SATU KALI
                Notifikasi::create($data);
            });

            return redirect()->route('admin.notifikasi.index')->with('success', 'Notifikasi berhasil disiarkan!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['msg' => 'Gagal mengirim notifikasi: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        Notifikasi::findOrFail($id)->delete();
        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    // Tampilkan Form Edit
    public function edit($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);

        // Ambil data pendukung (sama seperti create)
        $kegiatans = Kegiatan::where('status', 'buka')->orderBy('created_at', 'desc')->get();
        $tags = Tag::orderBy('nama_tag', 'asc')->get();

        // LOGIKA PENTING: Ubah String CSV di database kembali jadi Array untuk View
        // Contoh: "1,5,9" -> [1, 5, 9]
        $selectedKegiatanIds = $notifikasi->kegiatan_id ? explode(',', $notifikasi->kegiatan_id) : [];
        $selectedTagIds      = $notifikasi->tag_id ? explode(',', $notifikasi->tag_id) : [];

        return view('admin.notifikasi.edit', compact(
            'notifikasi',
            'kegiatans',
            'tags',
            'selectedKegiatanIds',
            'selectedTagIds'
        ));
    }

    // Proses Update
    public function update(Request $request, $id)
    {
        $notifikasi = Notifikasi::findOrFail($id);

        $request->validate([
            'judul'           => 'required|string|max:255',
            'pesan'           => 'required|string',
            'type'            => 'required|in:info,warning,danger,success',
            'target_audience' => 'required|in:all,kegiatan,tag',
            'expires_at'      => 'nullable|date',

            // Validasi Kondisional
            'kegiatan_ids'    => 'required_if:target_audience,kegiatan|array',
            'tag_ids'         => 'required_if:target_audience,tag|array',
        ]);

        try {
            DB::transaction(function () use ($request, $notifikasi) {

                $data = [
                    'judul'           => $request->judul,
                    'pesan'           => $request->pesan,
                    'type'            => $request->type,
                    'target_audience' => $request->target_audience,
                    'expires_at'      => $request->expires_at,
                    // Reset dulu jadi null, nanti diisi ulang di bawah
                    'kegiatan_id'     => null,
                    'tag_id'          => null,
                ];

                // LOGIKA IMPLODE (Array to String)
                if ($request->target_audience == 'kegiatan') {
                    $data['kegiatan_id'] = implode(',', $request->kegiatan_ids);
                } elseif ($request->target_audience == 'tag') {
                    $data['tag_id'] = implode(',', $request->tag_ids);
                }

                $notifikasi->update($data);
            });

            return redirect()->route('admin.notifikasi.index')->with('success', 'Notifikasi berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['msg' => 'Gagal update: ' . $e->getMessage()]);
        }
    }
}
