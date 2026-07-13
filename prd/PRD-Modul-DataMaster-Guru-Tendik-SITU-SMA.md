# PRD — Modul Data Master Guru (Advanced) & Data Master Tenaga Kependidikan

**Proyek:** Sistem Informasi Tata Usaha SMA (SITU-SMA)
**Modul:** Data Master — Guru (Advanced: Link Akun, Bulk Import/Export) & Tenaga Kependidikan
**Status Dokumen:** Final Draft — siap eksekusi
**Terakhir Diperbarui:** 13 Juli 2026

---

## 1. Latar Belakang & Tujuan

Modul Data Master Guru yang sudah ada (fase Data Master dasar) baru mencakup data inti (NIP, nama, jabatan, mapel, status). Kebutuhan sekarang adalah versi **advanced**:

- Guru bisa **terhubung dengan akun login** miliknya sendiri, bukan hanya data statis
- Input data guru bisa dilakukan **massal via Excel**, bukan satu-satu
- Data bisa **diekspor** untuk kebutuhan pelaporan eksternal (Dapodik, dinas, dll)
- Tersedia **template import** yang jelas formatnya, supaya staf TU tidak salah format saat menyiapkan file

Selain itu, dibutuhkan modul terpisah untuk **Tenaga Kependidikan** — staf non-guru seperti Staf TU, pustakawan, satpam, penjaga sekolah, dan laboran — yang secara struktur data berbeda dari guru (tidak ada mapel/jam mengajar) tapi punya kebutuhan fitur yang sama: kelola data, link akun, bulk import/export.

**Tujuan Modul:** Menyediakan pengelolaan data Guru dan Tenaga Kependidikan yang lengkap, terhubung dengan akun login sistem, mendukung input massal via template Excel yang tervalidasi, serta ekspor data kapan pun dibutuhkan.

---

## 2. Keputusan Arsitektur & Alur Bisnis

### 2.1 Relasi Data ↔ Akun Login
Setiap record Guru dan Tenaga Kependidikan punya kolom `user_id` (nullable, FK ke tabel `users`). Data personil dan akun login **dua entitas terpisah yang ditautkan**, bukan digabung — supaya data kepegawaian tetap ada meskipun akun belum/tidak pernah dibuat (mis. staf yang tidak butuh akses sistem).

### 2.2 Pembuatan Akun via Link "Set Password"
Saat import Excel, kolom **email** wajib diisi jika ingin personil tersebut punya akun. Setelah baris berhasil disimpan:
1. Sistem membuat record `users` dengan status awal `pending` (password belum ada/acak, tidak bisa dipakai login)
2. Job terkirim via Queue: kirim email berisi **signed URL** (link terenkripsi, kedaluwarsa dalam waktu tertentu) ke halaman "Set Password"
3. Personil klik link → buat password sendiri → akun aktif dan otomatis ter-assign role sesuai jenis data (guru → role `guru`, tendik → role default `staf-tu`, bisa diubah manual oleh Admin/Super Admin setelahnya)

Jika kolom email kosong saat import, baris tetap tersimpan sebagai data personil tanpa akun — Admin TU bisa buatkan akun manual belakangan dari halaman detail.

### 2.3 Penanganan Duplikat saat Import
Deteksi duplikat berdasarkan **NIP/NIK** (unique key). Jika NIP/NIK dari baris Excel sudah ada di database:
- Baris tersebut **dilewati (skip)**, proses lanjut ke baris berikutnya
- Setelah import selesai, sistem menampilkan **laporan ringkas**: jumlah berhasil, jumlah dilewati (dengan nomor baris & NIP yang bentrok), jumlah gagal validasi (dengan alasan per baris)
- Laporan ini juga bisa diunduh sebagai file log (Excel/CSV) untuk ditindaklanjuti staf TU

### 2.4 Template Import
Setiap modul (Guru & Tenaga Kependidikan) punya halaman "Import Data" dengan:
- Tombol **Download Template** — menghasilkan file Excel berisi header kolom yang benar, 1-2 baris contoh data, dan sheet kedua "Petunjuk Pengisian" (penjelasan tiap kolom, format tanggal, pilihan nilai yang valid untuk kolom enum)
- Preview struktur kolom yang diharapkan ditampilkan di layar sebelum upload
- Area upload (drag & drop / pilih file), lalu tombol "Proses Import"
- Hasil validasi ditampilkan sebagai tabel ringkas setelah proses selesai

---

## 3. Aktor & Hak Akses (RBAC)

| Role | Data Guru | Data Tenaga Kependidikan |
|---|---|---|
| Super Admin | Full CRUD, import/export, kelola akun & role | Full CRUD, import/export, kelola akun & role |
| Admin TU | Full CRUD, import/export, buat akun manual | Full CRUD, import/export, buat akun manual |
| Kepala Sekolah | View, export | View, export |
| Staf TU | View, input terbatas (tanpa hapus) | View, input terbatas (tanpa hapus) |
| Guru/Tendik (pemilik data) | View & edit data pribadi terbatas (no_hp, alamat, foto) | View & edit data pribadi terbatas |

---

## 4. Ruang Lingkup Fitur

### 4.1 Data Master Guru (Advanced)
- List guru: search nama/NIP, filter status, filter jabatan, filter mapel
- Form advanced: data pribadi lengkap, data kepegawaian, status akun (badge: belum ada akun / menunggu aktivasi / aktif)
- Detail guru: riwayat perubahan data, tombol "Buat Akun" (jika belum ada) atau "Kirim Ulang Link Aktivasi" (jika masih pending)
- Bulk import via Excel dengan template resmi
- Export data guru (semua/filtered) ke Excel

### 4.2 Data Master Tenaga Kependidikan (Baru)
- List tendik: search nama/NIP-NIK, filter jabatan, filter unit kerja, filter status
- Form: data pribadi lengkap, jabatan (Staf TU/Pustakawan/Satpam/Penjaga Sekolah/Laboran/Operator/Lainnya), unit kerja, status kepegawaian
- Detail & pengelolaan akun — pola identik dengan modul Guru
- Bulk import via Excel dengan template resmi
- Export data tendik ke Excel

### 4.3 Import & Template (Pola Sama untuk Kedua Modul)
- Halaman "Import Data" dengan tombol download template & area upload
- Validasi per baris: field wajib, format tanggal, format email, panjang NIP/NIK
- Skip baris dengan NIP/NIK duplikat + laporan
- Notifikasi WA/email ke Admin TU setelah proses import selesai (ringkasan hasil)

---

## 5. Alur Proses

### 5.1 Alur Bulk Import (Guru maupun Tendik)
```
Admin TU download template Excel
        ↓
Isi data sesuai format & petunjuk pengisian
        ↓
Upload file di halaman Import
        ↓
Sistem validasi tiap baris:
   ├─ NIP/NIK sudah ada → skip, catat di laporan
   ├─ Field wajib/format salah → skip, catat alasan di laporan
   └─ Valid → simpan ke database
        ↓
Untuk baris valid dengan email terisi:
   → buat user (status pending) → kirim email "Set Password" via Queue
        ↓
Tampilkan ringkasan hasil + opsi unduh laporan lengkap
```

### 5.2 Alur Aktivasi Akun
```
Personil terima email berisi signed URL
        ↓
Klik link → halaman "Set Password" (link kedaluwarsa jika lewat batas waktu)
        ↓
Personil buat password sendiri
        ↓
Akun aktif, role otomatis ter-assign (guru/staf-tu)
        ↓
Personil bisa login ke sistem
```

---

## 6. Skema Database (Draft)

### `master_gurus` (kolom tambahan dari versi dasar)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nip | varchar(30) unik | |
| nuptk | varchar(30) nullable | |
| nama | varchar | |
| jenis_kelamin | varchar(1) | L/P |
| tempat_lahir | varchar nullable | |
| tanggal_lahir | date nullable | |
| no_hp | varchar nullable | |
| email | varchar nullable | dipakai untuk aktivasi akun |
| alamat | text nullable | |
| jabatan | varchar | |
| mapel_id | FK nullable | ref `master_mapels` |
| pendidikan_terakhir | varchar nullable | |
| status_kepegawaian | varchar(20) | pns / gty / honorer / lainnya |
| tmt_tugas | date nullable | tanggal mulai tugas |
| foto_path | varchar nullable | |
| user_id | FK nullable, unik | ref `users` |
| status_akun | varchar(20) | belum_ada / pending / aktif |
| status | varchar(20) | aktif / nonaktif / pensiun / mutasi |
| deleted_at | timestamp null | soft delete |

### `master_tenaga_kependidikans` (tabel baru)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nip_nik | varchar(30) unik | |
| nama | varchar | |
| jenis_kelamin | varchar(1) | |
| tempat_lahir | varchar nullable | |
| tanggal_lahir | date nullable | |
| no_hp | varchar nullable | |
| email | varchar nullable | |
| alamat | text nullable | |
| jabatan | varchar(30) | staf_tu / pustakawan / satpam / penjaga_sekolah / laboran / operator / lainnya |
| unit_kerja | varchar nullable | mis. "Perpustakaan", "TU", "Keamanan" |
| status_kepegawaian | varchar(20) | pns / pttt / honorer / lainnya |
| tmt_tugas | date nullable | |
| foto_path | varchar nullable | |
| user_id | FK nullable, unik | ref `users` |
| status_akun | varchar(20) | belum_ada / pending / aktif |
| status | varchar(20) | aktif / nonaktif / pensiun / mutasi |
| deleted_at | timestamp null | soft delete |

### Enum yang perlu dibuat (`app/Enums/`)
```
StatusKepegawaianGuru::{Pns, Gty, Honorer, Lainnya}
StatusKepegawaianTendik::{Pns, Pttt, Honorer, Lainnya}
JabatanTendik::{StafTu, Pustakawan, Satpam, PenjagaSekolah, Laboran, Operator, Lainnya}
StatusAkun::{BelumAda, Pending, Aktif}
StatusPersonil::{Aktif, Nonaktif, Pensiun, Mutasi}
```

---

## 7. Notifikasi (Email & WhatsApp via Queue)

| Trigger | Kanal | Penerima | Isi |
|---|---|---|---|
| Import berhasil membuat akun baru | Email | Personil ybs | Link set password (signed URL) |
| Link set password akan kedaluwarsa | Email | Personil ybs (opsional, H-1) | Reminder aktivasi |
| Proses bulk import selesai | WA | Admin TU (pelaku import) | Ringkasan: X berhasil, Y dilewati, Z gagal |

---

## 8. Validasi & Aturan Bisnis Kunci

- NIP (guru) dan NIK/NIP (tendik) bersifat unik di masing-masing tabel
- Import tidak boleh menimpa data existing secara otomatis — duplikat selalu di-skip, perubahan data existing harus lewat edit manual atau fitur update terpisah (di luar cakupan fase ini)
- Signed URL aktivasi akun punya masa berlaku terbatas (disarankan 48 jam), setelah kedaluwarsa personil/Admin TU bisa minta kirim ulang
- Satu `user_id` hanya boleh terhubung ke satu record guru **atau** satu record tendik, tidak boleh dobel
- File Excel untuk import maksimal ukuran tertentu (disarankan 5MB) dan harus sesuai template resmi (validasi header kolom sebelum proses baris)
- Proses import baris besar (>1000 baris) menggunakan `chunk()`/queue supaya tidak timeout
- Foto profil tidak wajib diisi saat import, bisa dilengkapi belakangan lewat halaman edit
- Export mengikuti scope hak akses role yang login (mis. Guru hanya bisa export data dirinya sendiri jika diberi akses)

---

## 9. Fase Pengerjaan & Review Goals

### Fase A — Fondasi Data & Relasi Akun
**Cakupan:**
- Migration `master_gurus` (kolom tambahan) dan `master_tenaga_kependidikans` (baru)
- Model + relasi `belongsTo(User::class)` + Enum status
- Policy `MasterGuruPolicy`, `MasterTendikPolicy`
- Service `AkunPersonilService::buatAkunPending()`, `kirimLinkAktivasi()`

**Review Goals (Definition of Done):**
- [ ] Kolom baru di `master_gurus` dan tabel baru `master_tenaga_kependidikans` sesuai skema, lengkap dengan `deleted_at`
- [ ] Satu `user_id` tidak bisa terhubung ke lebih dari satu record guru/tendik (constraint unik)
- [ ] Enum status akun & status personil bisa dipanggil `::values()` untuk validasi form
- [ ] Role yang bukan Admin TU/Super Admin tidak bisa akses fitur buat akun

### Fase B — CRUD & Export Data Guru (Advanced)
**Cakupan:**
- Livewire `ListMasterGuru`, `FormMasterGuru` (advanced fields)
- Export `MasterGuruExport` (FromQuery, WithHeadings, WithStyles)
- Tombol "Buat Akun" / "Kirim Ulang Link Aktivasi" di detail guru
- Badge status akun di list & detail

**Review Goals:**
- [ ] Form guru advanced menyimpan seluruh field baru dengan validasi sesuai (format tanggal, format email, dsb)
- [ ] Export Excel menghasilkan file dengan kolom lengkap dan hanya berisi data sesuai filter/scope yang dipilih
- [ ] Tombol "Buat Akun" hanya muncul jika guru punya email terisi dan belum punya akun
- [ ] Status akun (belum ada/pending/aktif) tampil akurat dan update otomatis setelah aktivasi

### Fase C — Bulk Import Guru + Template Import
**Cakupan:**
- Class `MasterGuruTemplateExport` (generate template + sheet petunjuk)
- Class `MasterGuruImport` (validasi per baris, skip duplikat, kumpulkan laporan)
- Livewire `ImportMasterGuru` (upload, tampilkan hasil ringkasan & log)
- Job `KirimLinkAktivasiJob` terpicu untuk tiap baris valid dengan email

**Review Goals:**
- [ ] Tombol "Download Template" menghasilkan file Excel dengan header benar, contoh data, dan sheet petunjuk pengisian
- [ ] Upload file dengan header salah/tidak sesuai template ditolak dengan pesan jelas sebelum proses baris dimulai
- [ ] Baris dengan NIP duplikat di-skip dan muncul di laporan hasil, tidak menggagalkan seluruh proses
- [ ] Baris valid dengan email otomatis memicu pengiriman link aktivasi via Queue, tanpa memblokir proses import
- [ ] Laporan hasil import (berhasil/skip/gagal) bisa diunduh sebagai file terpisah
- [ ] Import 1000+ baris tidak menyebabkan timeout (pakai chunking)

### Fase D — Modul Tenaga Kependidikan Lengkap (CRUD, Import, Export)
**Cakupan:**
- Livewire `ListMasterTendik`, `FormMasterTendik`
- Export `MasterTendikExport`
- Class `MasterTendikTemplateExport`, `MasterTendikImport` — pola identik Fase C, disesuaikan field tendik
- Livewire `ImportMasterTendik`
- Halaman "Set Password" aktivasi akun (dipakai bersama oleh Guru & Tendik, generik berdasarkan tipe personil)

**Review Goals:**
- [ ] CRUD Tenaga Kependidikan berjalan lengkap dengan field jabatan & unit kerja sesuai enum
- [ ] Import & export Tenaga Kependidikan berfungsi dengan pola validasi/skip-duplikat yang sama seperti modul Guru
- [ ] Halaman "Set Password" bisa dipakai baik oleh guru maupun tendik dan mengarahkan role yang benar setelah aktivasi
- [ ] Link aktivasi yang sudah kedaluwarsa menampilkan pesan jelas dan opsi "kirim ulang" ke Admin TU

---

## 10. Di Luar Cakupan (Fase 1 Modul Ini)

- Update/overwrite data existing lewat import (saat ini duplikat selalu di-skip)
- Sinkronisasi otomatis dengan Dapodik/sistem eksternal lain
- Custom role per personil saat aktivasi (role default dulu, penyesuaian lanjutan manual oleh Admin)
- Riwayat mutasi/jabatan personil secara detail (baru sebatas kolom `status`)

---

## 11. Pertanyaan Terbuka (Belum Dikonfirmasi)

1. Masa berlaku link aktivasi "Set Password" — apakah 48 jam sudah sesuai, atau perlu durasi lain?
2. Role default untuk akun Tenaga Kependidikan — apakah semua otomatis jadi `staf-tu`, atau perlu dipetakan per jabatan (mis. satpam tidak perlu akses sistem sama sekali)?
3. Apakah field foto profil untuk Guru/Tendik nanti dipakai juga untuk kartu identitas/ID card cetak (perlu ukuran/rasio foto khusus)?

---

*Dokumen ini adalah bagian dari rangkaian PRD per modul untuk SITU-SMA. Modul terkait: Data Master (dasar), Administrasi, Absensi & Jurnal Mengajar.*
