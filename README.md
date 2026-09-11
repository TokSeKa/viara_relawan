# 📖 Dokumentasi Akses & Navigasi Sistem Viara Maitreyawira

Dokumen ini berisi informasi kredensial login, alur navigasi (routing), dan daftar fitur yang tersedia untuk **Admin** dan **Relawan**.

---

## 🔐 1. Akun Login & Kredensial (Testing)

Berikut adalah daftar akun dummy yang tersedia setelah menjalankan seed database.
**Password untuk semua akun:** `12345678`

| Role | Email | Spesialisasi / Keterangan |
| :--- | :--- | :--- |
| **Super Admin** | `maitreyawira@gmail.com` | **Akses Penuh** (Yayasan Utama/Pemilik) |
| **Bot Admin** | `admin@test.com` | Akun Bot untuk Testing fitur Admin Umum |
| **Admin Logistik**| `logistik@viara.com` | Khusus Manajemen Barang & Gudang |
| **Admin Acara** | `acara@viara.com` | Khusus Manajemen Event/Kegiatan |
| **Bot Relawan** | `relawan@test.com` | Akun Bot untuk Testing fitur Relawan |
| **Relawan User** | `email_user@gmail.com` | Simulasi User Biasa (Nama: Dylan) |

> **Catatan:** Password menggunakan Hash Bcrypt standar Laravel. Jika ingin mengubah password, silakan edit file `database/seeders/UserSeeder.php` lalu jalankan `php artisan db:seed`.
---

## 🚦 2. Alur Akses Utama (Routing Logic)

Sistem menggunakan *Smart Redirect* pada halaman utama (`/`):

1. **Guest (Belum Login):**
* Otomatis diarahkan ke halaman **Login** (`/login`).
* Tersedia opsi **Register** (`/register`) untuk pendaftaran relawan baru.


2. **Sudah Login:**
* Jika **Admin**  Redirect ke `admin.dashboard`.
* Jika **Relawan**  Redirect ke `dashboard` (Navigasi Relawan).



---

## 🖥️ 3. Peta Fitur: POV ADMIN

**URL Prefix:** `/admin/*`
**Middleware:** `auth`, `admin`

Admin memiliki akses penuh ke manajemen sistem. Berikut navigasi fiturnya:

### A. Dashboard Utama (`/admin/dashboard`)

* Pusat navigasi (Menu Card) ke semua modul di bawah ini.

### B. Manajemen Kegiatan (`/admin/kegiatan`)

* **Lihat Daftar:** Menampilkan semua kegiatan (filter status).
* **Tambah Kegiatan:** Memilih jenis detail kegiatan:
* *Acara/Event* (Kuota, Lokasi)
* *Donasi Dana* (Target Rp, Bank)
* *Donasi Darah* (Target Kantong, Gol Darah)
* *Mobil/Transport* (Armada, Supir)


* **Edit & Update:** Mengubah informasi kegiatan.
* **Cek Peserta:** Melihat siapa saja relawan yang mendaftar di kegiatan tertentu.

### C. Manajemen User & Relawan (`/admin/users`)

* **Daftar User:** Melihat seluruh pengguna terdaftar.
* **Detail User:** Melihat profil lengkap.
* **Edit Minat/Tag:** Mengubah tag minat user secara manual.
* **Ubah Jabatan:** Promosi (Relawan  Admin) atau Demosi.

### D. Manajemen Master Data

* **Tags/Minat (`/admin/tags`):** Tambah/Edit/Hapus kategori minat (misal: Kesehatan, Logistik).
* **Materi/Pustaka (`/admin/materi`):** Upload dokumen PDF atau Link Video untuk bahan pembelajaran relawan.

### E. Broadcast Notifikasi (`/admin/notifikasi`)

* **Buat Notifikasi:** Mengirim pesan broadcast.
* **Target Audience:** Bisa memilih target spesifik:
* *All Users* (Semua orang)
* *By Kegiatan* (Peserta kegiatan tertentu saja)
* *By Tag* (Relawan dengan minat tertentu saja)



### F. Pusat Laporan (`/admin/laporan`)

* **Rekapitulasi:** Laporan kegiatan berdasarkan periode tanggal & status.
* **Data Peserta:** Cetak daftar hadir/relawan per kegiatan.
* **Potensi Relawan:** Analisis relawan paling aktif berdasarkan Tag Minat.
* **Statistik Tag:** Grafik distribusi minat relawan.

---

## 🙋‍♂️ 4. Peta Fitur: POV RELAWAN

**URL Prefix:** `/` (Tanpa prefix admin)
**Middleware:** `auth`

Relawan memiliki akses untuk berpartisipasi dan mengelola profil diri.

### A. Dashboard Relawan (`/dashboard`)

* Halaman utama berisi menu navigasi cepat.

### B. Kegiatan (`/kegiatan`)

* **Jelajah:** Melihat daftar kegiatan yang berstatus 'Buka'.
* *Filter Cerdas:* Kegiatan yang sesuai minat (Tag) akan diprioritaskan/ditandai.


* **Detail & Join:** Melihat detail acara dan tombol **"Daftar Jadi Relawan"**.
* **Leave:** Membatalkan partisipasi (jika belum ditutup).

### C. Riwayat & Profil

* **Riwayat (`/riwayat`):** Melihat daftar kegiatan yang pernah diikuti.
* **Profil Saya (`/profil`):** Mengubah data diri (Nama, HP, Password).
* **Minat Saya (`/minat-saya`):** Memilih Tag minat (misal: Suka Masak, Punya Mobil) agar mendapatkan rekomendasi kegiatan yang cocok.

### D. Pustaka Materi (`/materi`)

* Mengakses dan mengunduh materi panduan atau SOP yang diunggah admin.
* Filter materi berdasarkan Kategori Tag.

---

## 🛠️ Instalasi & Setup (Quick Start)

Langkah-langkah untuk menjalankan project ini di local/VPS setelah melakukan `git clone`:

```bash
# 1. Install Dependencies (Backend & Frontend)
composer install
npm install && npm run build

# 2. Setup Environment
cp .env.example .env
php artisan key:generate
# 🛑 STOP DULU: Buka file .env, atur nama database (DB_DATABASE), username, dan password.

# 3. Setup Database
php artisan migrate:fresh
# 📝 Catatan: Jika punya file dump SQL (dummy data), import manual via phpMyAdmin setelah langkah ini.

# 4. Setup Storage Link (Wajib untuk Foto/Audio)
php artisan storage:link

# 5. Jalankan Server
php artisan serve

```
