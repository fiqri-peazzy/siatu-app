# PRD — Modul Administrasi (Surat Masuk, Surat Keluar, Disposisi)

**Proyek:** Sistem Informasi Tata Usaha SMA (SITU-SMA)
**Modul:** Administrasi
**Status Dokumen:** Final Draft — siap eksekusi
**Terakhir Diperbarui:** 10 Juli 2026

---

## 1. Latar Belakang & Tujuan

Saat ini pencatatan surat masuk, surat keluar, dan pendistribusian (disposisi) surat di sekolah masih dilakukan manual menggunakan buku agenda. Hal ini menyebabkan:

- Sulit melacak status tindak lanjut surat
- Risiko kehilangan/kerusakan dokumen fisik
- Penomoran surat rawan duplikat atau tidak konsisten
- Tidak ada notifikasi otomatis ke pihak yang harus menindaklanjuti

**Tujuan Modul:** Mendigitalisasi seluruh siklus hidup surat — dari penerimaan, penomoran otomatis, pengarsipan file digital, disposisi berjenjang, hingga proses persetujuan surat keluar — sehingga seluruh proses tercatat, terlacak, dan terarsip rapi.

---

## 2. Aktor & Hak Akses (RBAC)

| Role | Surat Masuk | Surat Keluar | Disposisi |
|---|---|---|---|
| Super Admin | Full CRUD | Full CRUD | Full |
| Admin TU | Full CRUD | Full CRUD, tidak bisa approve | Buat & terima disposisi |
| Kepala Sekolah | View, disposisi | View, **approve/reject** | Terima & disposisi ulang |
| Staf TU | Input, edit surat sendiri | Buat draft, tidak bisa approve | Terima & disposisi ulang |
| Guru | View (jika ditujukan ke dirinya) | - | Terima & bisa disposisi ulang |

Otorisasi diimplementasikan via **Policy** (`SuratMasukPolicy`, `SuratKeluarPolicy`, `DisposisiPolicy`) sesuai pola di CLAUDE.md Bagian 4.

---

## 3. Ruang Lingkup Fitur

### 3.1 Surat Masuk
- List surat masuk: search perihal, filter kategori, filter status, filter bulan/tahun ajaran
- Form input: tanggal terima, tanggal surat asli, kategori, asal instansi/pengirim, perihal, sifat surat, upload file (PDF/JPG/PNG, max 5MB)
- Nomor agenda **auto-generate** saat disimpan: `{KODE_KATEGORI}/{ROMAWI_BULAN}/{TAHUN}`
- Detail surat menampilkan riwayat disposisi (rantai lengkap)
- Export rekap ke Excel per periode

### 3.2 Surat Keluar
- List surat keluar: search, filter kategori, filter status
- Form draft: kategori, tanggal, tujuan, perihal, isi ringkas, lampiran opsional, relasi opsional ke surat masuk (jika balasan)
- Alur **draft → menunggu approval → approve/reject oleh Kepsek**
- Nomor surat resmi **hanya digenerate saat Kepsek approve** (menghindari nomor bolong akibat draft batal/revisi)
- Setelah approve: sistem generate PDF surat resmi (DomPDF) siap cetak/TTD; Admin TU upload hasil scan setelah ditandatangani
- Jika reject: surat kembali ke status revisi dengan catatan alasan dari Kepsek, bisa diedit dan diajukan ulang

### 3.3 Disposisi (Berantai/Multi-level)
- Dibuat dari detail Surat Masuk, bisa ke satu atau lebih penerima
- Instruksi/catatan disposisi berupa **teks bebas**
- Penerima bisa: menindaklanjuti langsung (selesai) **atau** meneruskan disposisi ke pihak lain (disposisi berantai, tidak dibatasi jumlah level)
- Setiap disposisi baru mengirim notifikasi WhatsApp ke penerima
- Riwayat rantai disposisi tervisualisasi di detail surat masuk (siapa → siapa → siapa)

### 3.4 Master Kategori Surat (relasi ke Data Master)
- `MasterKategoriSurat` dipakai bersama untuk Surat Masuk & Surat Keluar
- Tambahan kolom `tipe` (`masuk` / `keluar` / `keduanya`) untuk membedakan konteks penggunaan format nomor
- **[Perlu konfirmasi]:** apakah pendekatan satu master dengan kolom `tipe` ini disetujui, atau tetap dipisah jadi dua master berbeda

---

## 4. Alur Proses

### 4.1 Alur Surat Masuk → Disposisi
```
Staf TU input surat masuk
        ↓
Sistem generate no_agenda otomatis
        ↓
Admin TU / Kepsek buka detail → "Disposisikan"
        ↓
Pilih penerima (1 atau lebih) + catatan bebas
        ↓
Notifikasi WA terkirim ke penerima (via Queue)
        ↓
Penerima pilih:
   ├─ Tindak lanjuti langsung → status: selesai
   └─ Disposisi ulang ke pihak lain → level bertambah, ulangi proses
```

### 4.2 Alur Surat Keluar
```
Staf TU / Admin TU buat draft (status: draft)
        ↓
Submit → status: menunggu_approval
        ↓
Kepsek review
   ├─ Approve → generate no_surat otomatis
   │             → generate PDF resmi (DomPDF)
   │             → status: approved
   │             → setelah TTD fisik, upload scan final → status: terkirim
   └─ Reject → status: revisi (+catatan) → kembali ke pembuat → edit & resubmit
```

---

## 5. Skema Database (Draft)

### `surat_masuks`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| no_agenda | varchar(50) | unik, auto-generate |
| kategori_surat_id | FK | ref `master_kategori_surats` |
| tanggal_terima | date | |
| tanggal_surat | date | tanggal pada surat asli |
| asal_instansi | varchar | free text |
| perihal | varchar | |
| sifat | varchar(20) | biasa / penting / segera / rahasia |
| file_path | varchar | |
| status | varchar(30) | baru / didisposisi / diproses / selesai / diarsipkan |
| input_oleh | FK users | |
| deleted_at | timestamp null | soft delete |

### `surat_keluars`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| no_surat | varchar(50) nullable | terisi saat approved |
| kategori_surat_id | FK | |
| surat_masuk_id | FK nullable | jika merupakan balasan |
| tanggal | date | |
| tujuan | varchar | |
| perihal | varchar | |
| isi_ringkas | text | |
| file_draft_path | varchar nullable | |
| file_final_path | varchar nullable | hasil scan setelah TTD |
| status | varchar(30) | draft / menunggu_approval / approved / revisi / terkirim |
| dibuat_oleh | FK users | |
| catatan_revisi | text nullable | diisi Kepsek saat reject |
| disetujui_oleh | FK users nullable | |
| disetujui_at | timestamp nullable | |
| deleted_at | timestamp null | soft delete |

### `disposisis`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| surat_masuk_id | FK | |
| parent_disposisi_id | FK nullable, self-ref | null = disposisi awal |
| level | tinyint | kedalaman rantai (1, 2, 3, ...) |
| dari_user_id | FK users | |
| ke_user_id | FK users | |
| catatan | text | free text |
| status | varchar(30) | diterima / diproses / selesai / diteruskan |
| tgl_disposisi | timestamp | |
| deleted_at | timestamp null | soft delete |

### Enum yang perlu dibuat (`app/Enums/`)
```
StatusSuratMasuk::{Baru, Didisposisi, Diproses, Selesai, Diarsipkan}
StatusSuratKeluar::{Draft, MenungguApproval, Approved, Revisi, Terkirim}
StatusDisposisi::{Diterima, Diproses, Selesai, Diteruskan}
SifatSurat::{Biasa, Penting, Segera, Rahasia}
```

---

## 6. Notifikasi WhatsApp (via Queue, NotifikasiService)

| Trigger | Penerima | Pesan Inti |
|---|---|---|
| Surat keluar disubmit | Kepsek | Ada surat menunggu approval |
| Surat keluar approved/revisi | Pembuat draft | Status surat berubah |
| Disposisi baru (level berapa pun) | `ke_user_id` | Ada surat baru untuk ditindaklanjuti |

---

## 7. Validasi & Aturan Bisnis Kunci

- Upload file surat masuk/keluar: max 5MB, format PDF/JPG/PNG
- Nomor agenda & nomor surat generate otomatis via `SuratService`, tidak boleh diinput manual
- Nomor surat keluar **hanya** digenerate pada saat status berubah menjadi `approved`
- Disposisi bisa berantai tanpa batas level, dilacak via `parent_disposisi_id` dan `level`
- Semua proses simpan multi-tabel (mis. approve surat keluar yang sekaligus generate nomor & PDF) wajib dibungkus `DB::transaction()`
- Soft delete di seluruh tabel transaksi modul ini
- Data sensitif (isi surat rahasia) hanya tampil untuk role yang berwenang sesuai Policy

---

## 8. Fase Pengerjaan & Review Goals

Modul ini dipecah menjadi 4 fase kecil agar setiap bagian bisa direview dan diterima secara independen sebelum lanjut ke fase berikutnya.

### Fase A — Fondasi Data (Migration, Model, Enum)
**Cakupan:**
- Migration `surat_masuks`, `surat_keluars`, `disposisis`
- Model + relasi (`belongsTo` kategori, `belongsTo` user, self-relation disposisi)
- Enum status & sifat surat
- Seeder data dummy untuk testing

**Review Goals (Definition of Done):**
- [ ] Semua tabel punya `deleted_at` dan foreign key sesuai skema di atas
- [ ] Relasi Eloquent bisa diakses tanpa N+1 (`with([...])` sudah disiapkan di model scope)
- [ ] Enum bisa dipanggil `::values()` untuk validasi
- [ ] Seeder menghasilkan minimal 1 rantai disposisi 3 level untuk uji coba tampilan

### Fase B — Surat Masuk & Penomoran Otomatis
**Cakupan:**
- `SuratService::generateNomorAgenda()`
- Form Request `StoreSuratMasukRequest`, `UpdateSuratMasukRequest`
- Policy `SuratMasukPolicy`
- Livewire `ListSuratMasuk`, `FormSuratMasuk`
- Upload file ke `storage/app/surat/{tahun}/{bulan}/`

**Review Goals:**
- [ ] Input surat masuk baru menghasilkan `no_agenda` sesuai format `{KODE_KATEGORI}/{ROMAWI_BULAN}/{TAHUN}` tanpa duplikat
- [ ] Counter nomor reset otomatis per tahun per kategori
- [ ] Upload file gagal dengan pesan jelas jika lebih dari 5MB atau format salah
- [ ] Role selain Admin TU/Staf TU tidak bisa akses tombol "Tambah Data"
- [ ] List bisa di-search dan difilter sesuai kolom yang ditentukan

### Fase C — Surat Keluar & Alur Approval Kepsek
**Cakupan:**
- `SuratService::generateNomorSurat()`, `approveSuratKeluar()`, `rejectSuratKeluar()`
- Form Request, Policy `SuratKeluarPolicy`
- Livewire `ListSuratKeluar`, `FormSuratKeluar`, `ApprovalSuratKeluar` (khusus Kepsek)
- Template PDF surat resmi (`resources/views/pdf/surat-keluar.blade.php`)
- Notifikasi WA ke Kepsek & pembuat draft

**Review Goals:**
- [ ] Draft surat keluar tidak punya nomor sampai di-approve
- [ ] Approve oleh Kepsek menghasilkan nomor otomatis + PDF terunduh dengan data benar (kop surat, tanda tangan placeholder, nomor)
- [ ] Reject mengharuskan pengisian catatan alasan, status kembali ke `revisi`, surat bisa diedit ulang oleh pembuat
- [ ] Hanya user dengan role `kepala-sekolah` yang bisa mengakses aksi approve/reject
- [ ] Notifikasi WA terkirim lewat Queue, tidak memblokir response saat submit

### Fase D — Disposisi Berantai & Riwayat
**Cakupan:**
- `SuratService::buatDisposisi()`
- Livewire `FormDisposisi`, komponen tampilan rantai disposisi (timeline di detail surat masuk)
- Policy `DisposisiPolicy`
- Notifikasi WA berjenjang ke setiap penerima baru

**Review Goals:**
- [ ] Disposisi awal (`parent_disposisi_id = null`) bisa dibuat dari detail surat masuk oleh Admin TU/Kepsek
- [ ] Penerima disposisi bisa meneruskan ke pihak lain, `level` bertambah otomatis, `parent_disposisi_id` tersimpan benar
- [ ] Timeline rantai disposisi tampil urut dan menunjukkan siapa mendisposisikan ke siapa beserta catatan
- [ ] Update status disposisi (`diproses`/`selesai`) hanya bisa dilakukan oleh `ke_user_id` yang bersangkutan
- [ ] Notifikasi WA terkirim ke setiap penerima baru di setiap level, tanpa duplikat kirim

---

## 9. Di Luar Cakupan (Fase 1 Modul Ini)

- Tanda tangan digital (e-signature) — proses TTD tetap manual/scan
- OCR otomatis untuk membaca isi surat hasil scan
- Approval surat keluar berjenjang lebih dari satu orang (saat ini hanya Kepsek)
- Integrasi dengan sistem persuratan eksternal (SIMPATIKA, dsb.)

---

## 10. Pertanyaan Terbuka (Belum Dikonfirmasi)

1. **Master Kategori Surat**: apakah pendekatan satu master dengan kolom `tipe` (masuk/keluar/keduanya) disetujui, atau perlu dipisah jadi dua master berbeda?
2. Apakah dibutuhkan kolom **klasifikasi arsip** (kode tata naskah dinas) terpisah dari kategori surat, untuk kebutuhan pengarsipan formal?
3. Retensi arsip: apakah surat yang sudah `diarsipkan`/`terkirim` perlu ditampilkan terpisah dari daftar aktif (mis. tab "Arsip"), atau cukup filter status di list yang sama?

---

*Dokumen ini adalah bagian dari rangkaian PRD per modul untuk SITU-SMA. Modul berikutnya sesuai roadmap CLAUDE.md: Absensi Guru & Siswa.*
