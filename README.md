# SITU-SMA (Sistem Informasi Tata Usaha SMA)

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-blue.svg?style=flat-square&logo=php)](https://php.net)
[![Tailwind CSS Version](https://img.shields.io/badge/Tailwind_CSS-v4.0-38bdf8.svg?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![Livewire Version](https://img.shields.io/badge/Livewire-v3.x-fb7185.svg?style=flat-square)](https://livewire.laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](https://opensource.org/licenses/MIT)

**SITU-SMA** adalah Sistem Informasi Tata Usaha Sekolah Menengah Atas yang dirancang untuk mendigitalisasi seluruh proses administrasi, pengelolaan aset (sapras), keuangan dana BOS, permohonan kegiatan, absensi, serta galeri dokumentasi sekolah. Aplikasi ini membantu pihak Tata Usaha, Kepala Sekolah, Guru, dan Staf dalam meningkatkan efisiensi dan transparansi manajemen sekolah.

---

## 🚀 Fitur Utama

Aplikasi ini memiliki 7 modul utama yang saling terintegrasi:

1. **Data Master Sekolah**
   * Pengelolaan data Siswa, Guru, Kelas, dan Tahun Ajaran secara terpusat.
   * Master data pendukung seperti Kategori Surat, Jenis Aset, Mata Pelajaran, Rekening BOS, dan Jenis Kegiatan.

2. **Administrasi & Persuratan**
   * **Surat Masuk:** Pencatatan surat masuk, upload dokumen (max 5MB, PDF/JPG/PNG), dan penomoran agenda otomatis (`{KODE_KATEGORI}/{ROMAWI_BULAN}/{TAHUN}`).
   * **Surat Keluar:** Pembuatan surat keluar dengan format nomor otomatis (`{NO_URUT}/{KODE}/{ROMAWI_BULAN}/{TAHUN}`).
   * **Disposisi Multi-Level:** Alur disposisi dari Kepala Sekolah / Kepala TU ke staf pelaksana secara digital.

3. **Absensi & Jurnal Harian**
   * **Absensi Guru & Staf:** Pencatatan kehadiran harian beserta unggah surat izin jika berhalangan.
   * **Absensi Siswa:** Input absensi per jam pelajaran dengan fitur threshold alert (notifikasi jika siswa alfa $\ge$ 3 kali dalam sebulan).
   * **Jurnal Mengajar Guru:** Pengisian materi, kegiatan kelas, dan tanda tangan digital guru per jam pelajaran.

4. **Sarana & Prasarana (Sapras)**
   * **Inventarisasi Aset:** Pencatatan aset dengan kode unik otomatis (`{KODE_JENIS}-{TAHUN}-{NO_URUT}`).
   * **Integrasi QR Code:** Generate QR Code otomatis saat aset disimpan untuk mempermudah inventarisasi fisik.
   * **Riwayat Aset:** Pencatatan riwayat mutasi, perbaikan, atau berita acara penghapusan aset.

5. **Manajemen Keuangan BOS**
   * **RKAS:** Penyusunan Rencana Kegiatan dan Anggaran Sekolah per tahun anggaran.
   * **Realisasi Anggaran:** Pencatatan realisasi dana BOS dilengkapi dengan upload bukti kuitansi (max 10MB).
   * **Export ARKAS:** Laporan triwulan yang kompatibel dan siap diunggah ke sistem ARKAS Kemdikbud.

6. **Permohonan & Approval Kegiatan**
   * **Pengajuan Proposal:** Guru/Staf dapat mengajukan rencana kegiatan lengkap dengan estimasi biaya (RAB) dan proposal.
   * **Alur Approval Berjenjang:** Alur persetujuan otomatis (Guru $\rightarrow$ Wakasek $\rightarrow$ Kepsek).
   * **Surat Tugas Otomatis:** Setelah disetujui Kepsek, sistem otomatis men-generate Surat Tugas dalam format PDF.

7. **Galeri Dokumentasi**
   * Dokumentasi kegiatan sekolah dalam bentuk album publik dan internal.
   * Resize otomatis gambar (Thumbnail 300x200px, Preview 800x600px) & watermark otomatis logo sekolah.

---

## 🛠️ Tech Stack

* **Backend:** Laravel 12 (PHP 8.2+)
* **Frontend:** Blade + Livewire 3 (Server-side Reactive) + Alpine.js
* **Database:** MySQL 8.0 / MariaDB
* **Styling (CSS):** Tailwind CSS 4
* **Library Eksternal:**
  * **PDF Generation:** DomPDF (via `barryvdh/laravel-dompdf`)
  * **Excel Export/Import:** Laravel Excel (via `maatwebsite/excel`)
  * **QR Code:** Simple QR Code (via `simplesoftwareio/simple-qrcode`)
  * **Notifikasi:** WhatsApp Gateway (via Fonnte API) & SMTP Email

---

## 🔑 Role & Hak Akses (RBAC)

Menggunakan **Spatie Laravel Permission** untuk mengelola hak akses berdasarkan role berikut:

| Role | Kode Role | Cakupan Hak Akses |
|---|---|---|
| **Super Admin** | `super-admin` | Kontrol penuh sistem, manajemen user, & konfigurasi database |
| **Admin TU** | `admin-tu` | Kelola data master, administrasi surat, sapras, dan keuangan BOS |
| **Kepala Sekolah**| `kepala-sekolah` | Monitoring dashboard, persetujuan (approval) kegiatan, disposisi surat |
| **Guru** | `guru` | Input jurnal harian, absensi kelas, dan pengajuan kegiatan |
| **Staf TU** | `staf-tu` | Input surat masuk/keluar, absensi guru, dan inventarisasi aset |
| **Siswa** | `siswa` | Akses terbatas untuk melihat absensi pribadi & album galeri publik |

---

## ⚙️ Petunjuk Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan project di lingkungan lokal:

### 1. Clone Repositori
```bash
git clone https://github.com/username/siatu-app.git
cd siatu-app
```

### 2. Instalasi Dependency PHP & JavaScript
```bash
# Install PHP packages
composer install

# Install npm packages
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan kredensial database Anda:
```bash
cp .env.example .env
```
Buka `.env` dan edit bagian koneksi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siatu_app
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key & Link Storage
```bash
php artisan key:generate
php artisan storage:link
```

### 5. Jalankan Migrasi & Database Seeding
Jalankan perintah berikut untuk membuat tabel database dan mengisi data awal (roles, permissions, dan akun uji coba):
```bash
php artisan migrate --seed
```

### 6. Jalankan Server Dev & Asset Build
Jalankan server lokal Laravel dan compiler aset frontend secara bersamaan:
```bash
composer run dev
```

---

## 🧪 Akun Uji Coba (Akses Demo)

Setelah menjalankan seeder, gunakan akun-akun berikut untuk masuk dan menguji sistem:

| Role | Email | Password |
|---|---|---|
| **Super Admin** | `superadmin@situ-sma.test` | `password` |
| **Admin TU** | `admin.tu@situ-sma.test` | `password` |
| **Kepala Sekolah**| `kepala.sekolah@situ-sma.test` | `password` |
| **Guru** | `guru@situ-sma.test` | `password` |
| **Staf TU** | `staf.tu@situ-sma.test` | `password` |
| **Siswa** | `siswa@situ-sma.test` | `password` |

---

## 📄 Lisensi

Project ini dilisensikan di bawah **[MIT License](LICENSE)**.
