# 📚 Dokumentasi SITU-SMA

Selamat datang di pusat dokumentasi resmi **SITU-SMA (Sistem Informasi Tata Usaha SMA)**. Folder ini berisi seluruh spesifikasi arsitektur, skema database, acuan desain UI/UX antarmuka guru (PWA), serta spesifikasi kebutuhan produk (PRD) per modul.

---

## 🗂️ Struktur Direktori Dokumentasi

```
docs/
├── README.md                                       # Indeks utama & panduan dokumentasi ini
├── DB_SCHEMA.md                                    # Spesifikasi lengkap tabel & skema database
├── SETUP_LOKAL.md                                  # Cara menjalankan aplikasi di laptop (Windows)
├── STORAGE_R2.md                                   # Akses foto/dokumen R2 (signed URL) & panduan subdomain storage
├── DEMO_SKRIP.md                                   # Skrip demo SIATU v2
├── ROADMAP_V2.md                                   # Rencana & progres SIATU v2
├── design.md                                       # Desain UI/UX & spesifikasi teknis PWA Mobile Guru
└── prd/                                            # Product Requirement Documents (PRD)
    ├── PRD-Modul-DataMaster-Guru-Tendik-SITU-SMA.md # Master data Guru, Tendik, Siswa, Kelas & Mapel
    ├── PRD-Modul-Administrasi-SITU-SMA.md          # Persuratan (Masuk/Keluar) & Disposisi
    ├── PRD-Modul-Absensi-Jurnal-SITU-SMA.md        # Absensi Guru/Tendik, Absensi Siswa & Jurnal
    └── PRD-Modul-Guru-Mobile-Jurnal-SITU-SMA.md    # PWA Mobile Guru & Supervisi KBM Realtime
```

---

## 📖 Ringkasan Dokumen

### 1. Database & Arsitektur Sistem
* **[DB_SCHEMA.md](DB_SCHEMA.md)**  
  Menjelaskan struktur tabel MySQL/MariaDB, relasi foreign key, tipe data, indeks, enum nilai kolom, dan model Eloquent yang mewakilinya.  
  > ⚠️ *Catatan Pengembang:* Setiap kali ada migrasi baru atau perubahan struktur tabel, pengembang wajib memperbarui dokumen ini.

* **[design.md](design.md)**  
  Spesifikasi desain antarmuka PWA Guru Mobile (*Anti-Slop Guidelines*), token palet warna (*Navy Family*), tipografi monospace untuk data resmi, serta spesifikasi layar (Dashboard, Form Jurnal, Riwayat, Supervisi Realtime).

---

### 2. Product Requirement Documents (PRD)

* **[PRD: Data Master Guru, Tendik, & Siswa](prd/PRD-Modul-DataMaster-Guru-Tendik-SITU-SMA.md)**  
  Mencakup manajemen data kepegawaian (Guru & Tenaga Kependidikan), data siswa terintegrasi, import/export Excel format resmi Dapodik/Kemdikbud, dan sinkronisasi role akun login.

* **[PRD: Administrasi & Persuratan](prd/PRD-Modul-Administrasi-SITU-SMA.md)**  
  Pencatatan surat masuk dan surat keluar, format otomatis penomoran agenda surat (Romawi bulan & kode klasifikasi), upload berkas PDF/gambar, serta alur disposisi berjenjang dari Kepala Sekolah / TU ke staf.

* **[PRD: Absensi & Jurnal Mengajar](prd/PRD-Modul-Absensi-Jurnal-SITU-SMA.md)**  
  Pencatatan absensi harian guru/tendik (dengan radius GPS dan bukti izin), absensi siswa per jam pelajaran dengan *threshold alert* (peringatan $\ge 3$ kali alfa), dan rekapitulasi laporan bulanan.

* **[PRD: Guru Mobile PWA & Supervisi Realtime](prd/PRD-Modul-Guru-Mobile-Jurnal-SITU-SMA.md)**  
  Antarmuka khusus ponsel pintar bagi guru untuk mengisi jurnal mengajar dalam hitungan detik, notifikasi pengingat via Web Push Notification, riwayat jurnal siap cetak PDF, serta dashboard supervisi langsung bagi Kepala Sekolah dan Wakil Kepala Sekolah Bidang Kurikulum.

---

## ✍️ Aturan Pemeliharaan Dokumentasi

1. **Sinkronisasi Kode & Dokumen:** Saat fitur baru diimplementasikan atau diubah, PRD terkait dan `DB_SCHEMA.md` wajib disesuaikan.
2. **Format Standar:** Gunakan GitHub Flavored Markdown (GFM) dan sertakan tabel ringkasan atau diagram alur jika mendefinisikan alur proses baru.
3. **Panduan AI Agent:** Untuk panduan aturan coding dan konvensi arsitektur Laravel, baca file `CLAUDE.md` di root repositori.

