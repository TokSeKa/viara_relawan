<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\DonasiDana;
use App\Models\DonasiDarah;
use App\Models\Kendaraan;
use App\Models\Acara;
use App\Models\DonasiBarang;
use App\Models\PinjamBarang;
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
        // 1. Mulai query dasar: harus yang berstatus 'buka'
        $query = Kegiatan::with(['detail', 'tags'])->where('status', 'buka');

        // 2. Cek apakah ini User Login atau Guest?
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Ambil ID tag milik user
            $userTagIds = $user->tags()->pluck('id')->toArray();

            // LOGIKA LOGIN: Minat Saya OR Kegiatan Tanpa Tag
            $query->where(function ($q) use ($userTagIds) {
                $q->whereHas('tags', function ($subQuery) use ($userTagIds) {
                    $subQuery->whereIn('tags.id', $userTagIds);
                })->orWhereDoesntHave('tags');
            });
        } else {
            // LOGIKA GUEST: Hanya tampilkan kegiatan yang TIDAK punya tag sama sekali
            // Ini memastikan guest tidak melihat kegiatan spesifik kategori apapun
            $query->whereDoesntHave('tags');
        }

        // 3. Eksekusi query
        $kegiatans = $query->orderBy('tanggal_mulai', 'asc')->paginate(9);

        return view('relawan.daftar-kegiatan', compact('kegiatans'));
    }

    public function index_admin(Request $request)
    {
        $query = Kegiatan::with('detail')->withCount('partisipasis')->latest();

        // 1. FILTER SEARCH (BARU)
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // 2. Filter Status
        if ($request->has('status') && in_array($request->status, ['buka', 'tutup', 'selesai'])) {
            $query->where('status', $request->status);
        }

        // 3. Filter Jenis
        if ($request->filled('jenis')) {
            $query->where('detail_type', $request->jenis);
        }

        // 4. Filter Waktu
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_mulai', '<=', $request->end_date);
        }

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
        $user = Auth::user();

        // Validasi Jenis Kegiatan
        $validTypes = ['donasi_dana', 'donasi_darah', 'mobil', 'acara', 'donasi_barang', 'peminjaman_barang'];
        if (!in_array($jenis, $validTypes)) abort(404);

        // Otorisasi Per Role
        $mapping = [
            'admin_dana'     => ['donasi_dana'],
            'admin_darah'    => ['donasi_darah'],
            'admin_mobil'    => ['mobil'],
            'admin_acara'    => ['acara'],
            'admin_logistik' => ['donasi_barang', 'peminjaman_barang'],
        ];

        if ($user->jabatan !== 'admin_super') {
            if (!isset($mapping[$user->jabatan]) || !in_array($jenis, $mapping[$user->jabatan])) {
                return redirect()->route('admin.kegiatan.pilih')->with('error', 'Anda tidak berwenang membuat jenis kegiatan ini.');
            }
        }

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
            'tanggal_mulai_acara'   => 'required|date',
            'tanggal_selesai_acara' => 'required|date|after:tanggal_mulai_acara',
            'jenis_kegiatan'  => 'required|in:donasi_dana,donasi_darah,mobil,acara,donasi_barang,peminjaman_barang',
            'tags'            => 'nullable|array',
            'tags.*'          => 'exists:tags,id',
        ];

        // 2. VALIDASI KHUSUS (Sesuai Migrasi Kamu)
        if ($request->jenis_kegiatan == 'donasi_barang') {
            $rules['target_item'] = 'required|string';
            $rules['target_jumlah'] = 'required|numeric';
            $rules['lokasi_kumpul'] = 'required|string';
        } elseif ($request->jenis_kegiatan == 'peminjaman_barang') {
            $rules['nama_barang'] = 'required|string';
            $rules['stok_tersedia'] = 'required|numeric';
        } elseif ($request->jenis_kegiatan == 'donasi_dana') {
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

                    case 'donasi_barang':
                        $detail = DonasiBarang::create([
                            'target_item' => $request->target_item,
                            'target_jumlah' => $request->target_jumlah,
                            'lokasi_kumpul' => $request->lokasi_kumpul,
                        ]);
                        break;
                    case 'peminjaman_barang':
                        $detail = PinjamBarang::create([
                            'nama_barang' => $request->nama_barang,
                            'stok_tersedia' => $request->stok_tersedia,
                            'persyaratan' => $request->persyaratan,
                        ]);
                        break;
                }

                // C. Simpan Data KEGIATAN (Induknya)
                $kegiatan = Kegiatan::create([
                    'judul' => $request->judul,
                    'slug' => Str::slug($request->judul) . '-' . time(),
                    'deskripsi' => $request->deskripsi,
                    'banner_image' => $imagePath,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'tanggal_mulai_acara'   => $request->tanggal_mulai_acara,
                    'tanggal_selesai_acara' => $request->tanggal_selesai_acara,
                    'status' => 'buka',
                    'admin_id' => Auth::id(),
                    'detail_id' => $detail->id,
                    'detail_type' => $request->jenis_kegiatan,
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
        $kegiatan->load([
            'detail',
            'admin',
            'tags' => function ($query) {
                // Ambil tag yang is_admin_only = false (0)
                $query->where('is_admin_only', false);
            }
        ]);

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
            'tanggal_mulai_acara'   => 'required|date',
            'tanggal_selesai_acara' => 'required|date|after:tanggal_mulai_acara',
            'status'          => 'required|in:buka,tutup,selesai',
            'tags'            => 'nullable|array',
            'tags.*'          => 'exists:tags,id',
        ];

        // VALIDASI DETAIL UPDATE
        if ($kegiatan->detail_type == 'donasi_dana') {
            $rules['target_rupiah'] = 'nullable|numeric';
            $rules['info_bank']     = 'required|array';
        } elseif ($kegiatan->detail_type == 'donasi_darah') {
            $rules['target_kantong']        = 'required|numeric';
            $rules['golongan_darah_needed'] = 'required|array';
            $rules['lokasi_pmi']            = 'required|string';
        } elseif ($kegiatan->detail_type == 'mobil') {
            $rules['jumlah_unit']   = 'required|numeric';
            $rules['lokasi_jemput'] = 'required|string';
        } elseif ($kegiatan->detail_type == 'acara') {
            $rules['lokasi']        = 'required|string';
            $rules['kuota_peserta'] = 'required|numeric';
        }
        // --- TAMBAHAN VALIDASI UPDATE ---
        elseif ($kegiatan->detail_type == 'donasi_barang') {
            $rules['target_item']   = 'required|string';
            $rules['target_jumlah'] = 'required|numeric';
            $rules['lokasi_kumpul'] = 'required|string';
        } elseif ($kegiatan->detail_type == 'peminjaman_barang') {
            $rules['nama_barang']   = 'required|string';
            $rules['stok_tersedia'] = 'required|numeric';
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
                'tanggal_mulai_acara'   => $request->tanggal_mulai_acara,
                'tanggal_selesai_acara' => $request->tanggal_selesai_acara,
                'status'          => $request->status,
            ]);

            // C. Update Detail
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
                // --- UPDATE BARU ---
                case 'donasi_barang':
                    $kegiatan->detail->update([
                        'target_item'   => $request->target_item,
                        'target_jumlah' => $request->target_jumlah,
                        'lokasi_kumpul' => $request->lokasi_kumpul,
                    ]);
                    break;
                case 'peminjaman_barang':
                    $kegiatan->detail->update([
                        'nama_barang'   => $request->nama_barang,
                        'stok_tersedia' => $request->stok_tersedia,
                        'persyaratan'   => $request->persyaratan,
                    ]);
                    break;
            }

            // D. Update Tags
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
