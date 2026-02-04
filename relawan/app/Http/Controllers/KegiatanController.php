<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\DonasiDana;
use App\Models\DonasiDarah;
use App\Models\Kendaraan;
use App\Models\Acara;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Ambil data kegiatan
        // - Filter: Hanya yang statusnya 'buka'
        // - Sort: Tanggal mulai paling dekat ditaruh di atas
        // - Eager Loading: with('detail') biar query ke tabel anak (donasi_dana, dll) efisien
        // - Pagination: Tampilkan 9 kartu per halaman

        $kegiatans = Kegiatan::with('detail')
            ->where('status', 'buka')
            ->orderBy('tanggal_mulai', 'asc')
            ->paginate(9);

        // 2. Kirim ke View
        return view('relawan.daftar-kegiatan', compact('kegiatans'));
    }

    public function index_admin(Request $request)
    {
        // 1. Inisialisasi Query + Hitung Partisipasi
        // withCount('partisipasis') akan menambahkan atribut 'partisipasis_count'
        $query = Kegiatan::with('detail')->withCount('partisipasis')->latest();

        // 2. Filter Status
        if ($request->has('status') && in_array($request->status, ['buka', 'tutup', 'selesai'])) {
            $query->where('status', $request->status);
        }

        // 3. Filter Waktu
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_mulai', '<=', $request->end_date);
        }

        // 4. Eksekusi
        $kegiatans = $query->paginate(10)->withQueryString();

        return view('admin.kegiatan.index', compact('kegiatans'));
    }

    public function pilihJenis()
    {
        return view('admin.kegiatan.pilih-jenis');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($jenis)
    {
        $validTypes = ['donasi_dana', 'donasi_darah', 'mobil', 'acara'];
        if (!in_array($jenis, $validTypes)) abort(404);

        // AMBIL SEMUA TAG UNTUK DITAMPILKAN DI CHECKBOX
        $tags = Tag::orderBy('nama_tag', 'asc')->get();

        return view('admin.kegiatan.tambah-kegiatan', compact('jenis', 'tags'));
    }

    public function store(Request $request)
    {
        // 1. VALIDASI DATA UMUM (Wajib untuk semua jenis)
        $rules = [
            'judul'           => 'required|string|max:255',
            'deskripsi'       => 'required|string',
            'banner_image'    => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'jenis_kegiatan'  => 'required|in:donasi_dana,donasi_darah,mobil,acara',
            'tags'            => 'nullable|array',
            'tags.*'          => 'exists:tags,id',
        ];

        // 2. VALIDASI KHUSUS (Sesuai Migrasi Kamu)
        if ($request->jenis_kegiatan == 'donasi_dana') {
            // Migrasi: decimal nullable, json
            $rules['target_rupiah'] = 'nullable|numeric';
            $rules['info_bank']     = 'required|array';
        } elseif ($request->jenis_kegiatan == 'donasi_darah') {
            // Migrasi: integer, json, string (lokasi_pmi)
            $rules['target_kantong']      = 'required|numeric';
            $rules['golongan_darah_needed'] = 'required|array';
            $rules['lokasi_pmi']          = 'required|string'; // <--- Baru
        } elseif ($request->jenis_kegiatan == 'mobil') {
            // Migrasi: integer, boolean, string (lokasi_jemput)
            $rules['jumlah_unit']   = 'required|numeric';
            $rules['lokasi_jemput'] = 'required|string'; // <--- Baru
            // butuh_supir tidak perlu validasi required karena checkbox
        } elseif ($request->jenis_kegiatan == 'acara') {
            // Migrasi: string (lokasi), integer
            $rules['lokasi']        = 'required|string'; // <--- Baru
            $rules['kuota_peserta'] = 'required|numeric';
        }

        // Jalankan Validasi
        $validated = $request->validate($rules);

        // 3. MULAI PENYIMPANAN
        try {
            DB::transaction(function () use ($request) {

                // A. Upload Gambar
                $imagePath = null;
                if ($request->hasFile('banner_image')) {
                    $imagePath = $request->file('banner_image')->store('banners', 'public');
                }

                // B. Simpan Data DETAIL (Anaknya Dulu)
                $detail = null;

                switch ($request->jenis_kegiatan) {
                    case 'donasi_dana':
                        $detail = DonasiDana::create([
                            'target_rupiah' => $request->target_rupiah,
                            'info_bank'     => $request->info_bank,
                        ]);
                        break;

                    case 'donasi_darah':
                        $detail = DonasiDarah::create([
                            'target_kantong' => $request->target_kantong,
                            'golongan_darah_needed' => $request->golongan_darah_needed, // Perbaikan nama field
                            'lokasi_pmi'     => $request->lokasi_pmi, // Sesuai migrasi
                        ]);
                        break;

                    case 'mobil':
                        $detail = Kendaraan::create([
                            'jumlah_unit'   => $request->jumlah_unit,
                            'lokasi_jemput' => $request->lokasi_jemput, // Sesuai migrasi
                            'butuh_supir'   => $request->has('butuh_supir'), // Checkbox logic
                        ]);
                        break;

                    case 'acara':
                        $detail = Acara::create([
                            'lokasi'        => $request->lokasi, // Sesuai migrasi
                            'kuota_peserta' => $request->kuota_peserta,
                        ]);
                        break;
                }

                // C. Simpan Data KEGIATAN (Induknya)
                $kegiatan = Kegiatan::create([
                    'judul'           => $request->judul,
                    'slug'            => Str::slug($request->judul) . '-' . time(),
                    'deskripsi'       => $request->deskripsi,
                    'banner_image'    => $imagePath,
                    'tanggal_mulai'   => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'status'          => 'buka',
                    'admin_id'        => Auth::id() ?? 1, // Pakai Auth::id() biar VS Code aman

                    // KUNCI POLYMORPHIC
                    'detail_id'       => $detail->id,
                    'detail_type'     => $request->jenis_kegiatan,
                ]);

                // attach() digunakan untuk menambah data ke tabel pivot (kegiatan_tags)
                if ($request->has('tags')) {
                    $kegiatan->tags()->attach($request->tags);
                }
            }); // End Transaction


            return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan!');
            // return json
            // return response()->json(['success' => 'Kegiatan berhasil ditambahkan!']);
        } catch (\Exception $e) {
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return back()->withInput()->withErrors(['msg' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Kegiatan $kegiatan)
    {
        // 1. Eager Load Relasi
        // Kita butuh data detail (anak), tags, dan siapa admin pembuatnya
        $kegiatan->load(['detail', 'tags', 'admin']);

        // 2. Kirim ke View Detail Relawan
        return view('relawan.detail-kegiatan', compact('kegiatan'));
    }

    public function edit($id)
    {
        // Load Tags juga di sini
        $kegiatan = Kegiatan::with(['detail', 'tags'])->findOrFail($id);
        $jenis = $kegiatan->detail_type;

        // Ambil semua tag
        $tags = Tag::orderBy('nama_tag', 'asc')->get();

        // Ambil ID tag yang sudah dipilih kegiatan ini (biar auto-check)
        $selectedTags = $kegiatan->tags->pluck('id')->toArray();

        return view('admin.kegiatan.edit', compact('kegiatan', 'jenis', 'tags', 'selectedTags'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::with('detail')->findOrFail($id);

        $rules = [
            'judul'           => 'required|string|max:255',
            'deskripsi'       => 'required|string',
            'banner_image'    => 'nullable|image|max:4096',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status'          => 'required|in:buka,tutup,selesai',
            // VALIDASI TAG UPDATE
            'tags'            => 'nullable|array',
            'tags.*'          => 'exists:tags,id',
        ];

        // ... (Validasi Detail TETAP SAMA) ...
        if ($kegiatan->detail_type == 'donasi_dana') {
            $rules['target_rupiah'] = 'nullable|numeric';
            $rules['info_bank']     = 'required|array';
        } elseif ($kegiatan->detail_type == 'donasi_darah') {
            $rules['target_kantong']      = 'required|numeric';
            $rules['golongan_darah_needed'] = 'required|array';
            $rules['lokasi_pmi']          = 'required|string';
        } elseif ($kegiatan->detail_type == 'mobil') {
            $rules['jumlah_unit']   = 'required|numeric';
            $rules['lokasi_jemput'] = 'required|string';
        } elseif ($kegiatan->detail_type == 'acara') {
            $rules['lokasi']        = 'required|string';
            $rules['kuota_peserta'] = 'required|numeric';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $kegiatan) {
            // A. Update Gambar
            if ($request->hasFile('banner_image')) {
                if ($kegiatan->banner_image) {
                    Storage::disk('public')->delete($kegiatan->banner_image);
                }
                $imagePath = $request->file('banner_image')->store('banners', 'public');
                $kegiatan->banner_image = $imagePath;
            }

            // B. Update Kegiatan
            $kegiatan->update([
                'judul'           => $request->judul,
                'deskripsi'       => $request->deskripsi,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status'          => $request->status,
            ]);

            // C. Update Detail (Switch case TETAP SAMA seperti kodemu)
            switch ($kegiatan->detail_type) {
                case 'donasi_dana':
                    $kegiatan->detail->update([
                        'target_rupiah' => $request->target_rupiah,
                        'info_bank'     => $request->info_bank,
                    ]);
                    break;
                case 'donasi_darah':
                    $kegiatan->detail->update([
                        'target_kantong' => $request->target_kantong,
                        'golongan_darah_needed' => $request->golongan_darah_needed,
                        'lokasi_pmi'     => $request->lokasi_pmi,
                    ]);
                    break;
                case 'mobil':
                    $kegiatan->detail->update([
                        'jumlah_unit'   => $request->jumlah_unit,
                        'lokasi_jemput' => $request->lokasi_jemput,
                        'butuh_supir'   => $request->has('butuh_supir'),
                    ]);
                    break;
                case 'acara':
                    $kegiatan->detail->update([
                        'lokasi'        => $request->lokasi,
                        'kuota_peserta' => $request->kuota_peserta,
                    ]);
                    break;
            }

            // D. UPDATE TAGS (BARU)
            // sync() otomatis hapus tag lama yang tidak dipilih, dan tambah yang baru
            $kegiatan->tags()->sync($request->tags ?? []);
        });

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui!');
    }

    /**
     * Menampilkan daftar peserta suatu kegiatan.
     */
    public function peserta($id)
    {
        // Ambil kegiatan + data partisipasi + data user-nya sekalian
        $kegiatan = Kegiatan::with('partisipasis.user')->findOrFail($id);

        return view('admin.kegiatan.daftar-peserta', compact('kegiatan'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kegiatan $kegiatan)
    {
        //
    }
}
