# PRD — Modul Guru Mobile (Jurnal Mengajar & Fitur Pendukung)

**Proyek:** Sistem Informasi Tata Usaha SMA (SITU-SMA)
**Modul:** Akademik — Akses Mobile Guru (Jurnal Mengajar, Riwayat & Cetak, Notifikasi, Supervisi Realtime)
**Status Dokumen:** Final Draft — siap eksekusi
**Terakhir Diperbarui:** 14 Juli 2026

---

## 1. Latar Belakang & Tujuan

Modul Absensi & Jurnal Mengajar sebelumnya sudah mendefinisikan backend (service, tabel, alur data) untuk pencatatan jurnal. Modul ini fokus pada **pengalaman akses guru lewat HP (PWA)** — layout, navigasi, dan fitur pendukung yang membuat pengisian jurnal jadi kebiasaan harian yang mudah, bukan beban administratif.

Sebagai pembanding, aplikasi sejenis di pasar Indonesia (mis. Jurnale Pro) menyediakan pencatatan KBM realtime, jurnal mengajar, presensi siswa, dan **dashboard supervisi** bagi Kepala Sekolah/Wakil Kurikulum untuk memantau pelaksanaan KBM secara langsung. Pola ini yang diadaptasi ke SITU-SMA, disesuaikan dengan ruang lingkup yang sudah disepakati untuk fase ini (tanpa penilaian siswa, tanpa ceklok kelas terpisah — status kehadiran guru di kelas cukup terwakili dari jurnal yang diisi).

**Tujuan Modul:**
- Menyediakan tampilan mobile (PWA) yang ringkas dan cepat diakses guru untuk mengisi jurnal mengajar harian
- Menyediakan riwayat jurnal yang bisa dicetak/diekspor sebagai bukti fisik/digital
- Mengingatkan guru secara proaktif agar jurnal tidak terlewat
- Memberikan visibilitas realtime kepada Kepala Sekolah/Wakil Kurikulum atas pelaksanaan KBM hari itu, tanpa perlu menunggu laporan manual

---

## 2. Batasan Ruang Lingkup (Hasil Diskusi)

Untuk menjaga fokus modul dan menghindari duplikasi dengan PRD sebelumnya, disepakati:

| Fitur | Status |
|---|---|
| Penilaian siswa (input nilai tugas/harian) | **Di luar cakupan** — fokus modul ini hanya jurnal & presensi |
| Ceklok kehadiran guru per kelas (terpisah dari absen gerbang) | **Tidak diperlukan** — status kehadiran guru di kelas cukup terwakili dari status jurnal yang diisi pada jam tersebut |
| Dashboard supervisi realtime Kepsek/Wakakur | **Masuk cakupan** modul ini |

---

## 3. Aktor & Hak Akses (RBAC)

| Role | Dashboard Mobile Guru | Riwayat & Cetak Jurnal | Dashboard Supervisi Realtime |
|---|---|---|---|
| Guru | Akses penuh (data sendiri) | Akses penuh (jurnal sendiri) | - |
| Wali Kelas | Sama seperti Guru | Sama seperti Guru | - |
| Kepala Sekolah | - | View semua guru | Akses penuh |
| Wakil Kepala Kurikulum | - | View semua guru | Akses penuh |
| Admin TU | - | View semua guru, export | View (opsional) |

---

## 4. Ruang Lingkup Fitur

### 4.1 Dashboard Mobile Guru (Halaman Utama)
- Sapaan & ringkasan hari ini: jumlah jam mengajar, jumlah yang sudah diisi vs belum
- Daftar jam mengajar hari ini (diambil dari jadwal), tiap item menampilkan: jam ke, kelas, mapel, status (belum diisi/sudah diisi), tombol aksi langsung
- Status absensi harian guru (masuk/pulang) ditampilkan ringkas di bagian atas, dengan tombol pintas ke Absen Masuk/Pulang jika belum dilakukan
- Navigasi bawah (bottom navigation) khas mobile: Beranda, Jurnal, Riwayat, Profil — agar familiar dan cepat diakses satu jempol

### 4.2 Input Jurnal Mengajar (UX Mobile-Optimized)
- Form singkat dioptimalkan untuk layar kecil: materi, kegiatan pembelajaran, opsional foto dokumentasi kegiatan
- Diakses langsung dari item jadwal di dashboard (tap → form terbuka dengan kelas/mapel/jam sudah terisi otomatis)
- Draft otomatis tersimpan sementara di state form jika guru berpindah halaman sebelum submit final (mencegah kehilangan input)
- Validasi ringan di sisi form (field wajib, panjang minimum materi) dengan pesan jelas
- Setelah submit, status item jadwal di dashboard langsung berubah jadi "sudah diisi"

### 4.3 Riwayat & Cetak/Export Jurnal
- Daftar jurnal yang sudah diisi, filter per rentang tanggal, per kelas, per mapel
- Detail jurnal per entri (lihat kembali apa yang sudah ditulis)
- Cetak/export ke **PDF** per periode (mis. per bulan) sebagai bukti administratif, memakai template resmi (DomPDF, kop sekolah)
- Export ke Excel untuk kebutuhan rekap oleh Admin TU/Kepsek (lintas guru, lintas periode)

### 4.4 Notifikasi & Pengingat
- Reminder otomatis jam 14:00 jika ada jam mengajar hari itu yang jurnalnya belum diisi (sudah didefinisikan di PRD Absensi & Jurnal — modul ini menambahkan pengaturan preferensinya)
- Guru bisa atur preferensi: aktif/nonaktifkan reminder, pilih kanal (WA saja / WA + in-app)
- Notifikasi in-app (badge/counter) muncul di ikon "Jurnal" pada bottom navigation jika ada jam yang belum diisi

### 4.5 Dashboard Supervisi Realtime (Kepala Sekolah / Wakil Kurikulum)
- Tampilan ringkas seluruh kelas & jam pelajaran yang sedang berlangsung **saat ini** (berdasarkan waktu server vs jadwal)
- Untuk tiap jam yang sedang berjalan: tampilkan status jurnal (sudah diisi/belum), nama guru, kelas, mapel
- **Highlight/alert** untuk kelas yang jam pelajarannya sudah berjalan cukup lama tapi jurnal belum diisi — indikasi potensial kelas kosong/tidak terpantau
- Filter riwayat harian: lihat rekap KBM per hari yang sudah lewat (bukan hanya realtime)
- Ringkasan tingkat kepatuhan pengisian jurnal per guru dalam periode tertentu (persentase jurnal terisi vs total jam mengajar)

### 4.6 Profil Guru (Self-Service)
- Lihat & edit data pribadi terbatas (no. HP, alamat, foto profil) — sesuai batas akses dari PRD Data Master Guru
- Ganti password
- Lihat ringkasan performa pengisian jurnal pribadi (persentase kepatuhan bulan berjalan) sebagai bentuk kesadaran diri, bukan untuk dibandingkan dengan guru lain

---

## 5. Alur Proses

### 5.1 Alur Harian Guru (Mobile)
```
Guru buka PWA → Dashboard
        ↓
Lihat daftar jam mengajar hari ini (dari jadwal)
        ↓
Selesai mengajar jam ke-N → tap item jadwal
        ↓
Isi form jurnal (materi, kegiatan, foto opsional) → submit
        ↓
Status item berubah: sudah diisi
        ↓
(Ulangi untuk tiap jam mengajar berikutnya)
        ↓
Jam 14:00 — jika masih ada yang belum diisi → reminder WA/in-app
```

### 5.2 Alur Supervisi Realtime (Kepsek/Wakakur)
```
Kepsek/Wakakur buka Dashboard Supervisi
        ↓
Sistem hitung jam pelajaran yang sedang berlangsung (waktu now vs jadwal)
        ↓
Tampilkan status jurnal tiap kelas yang sedang berjalan
        ↓
Jika jam sudah berjalan > ambang waktu tertentu & jurnal belum diisi
        → tandai sebagai "perlu perhatian" (highlight visual)
        ↓
Kepsek/Wakakur bisa lihat detail atau tindak lanjuti langsung (mis. hubungi guru)
```

---

## 6. Skema Database (Draft — Tambahan/Penyesuaian)

Modul ini sebagian besar memakai tabel yang sudah didefinisikan di PRD Absensi & Jurnal Mengajar (`jurnal_gurus`, `absensi_gurus`). Tambahan yang diperlukan:

### `preferensi_notifikasi_gurus` (tabel baru)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| guru_id | FK, unik | ref `master_gurus` |
| reminder_jurnal_aktif | boolean default true | |
| kanal_notifikasi | varchar(20) | wa_saja / wa_dan_inapp |
| deleted_at | timestamp null | soft delete |

### Catatan Skema Terkait Jadwal
Fitur dashboard (4.1) dan supervisi realtime (4.5) bergantung pada data **jadwal mengajar** (guru, kelas, mapel, hari, jam ke, jam mulai/selesai). Jika tabel ini belum ada di Data Master, perlu dikonfirmasi dan dibuatkan (lihat Pertanyaan Terbuka Bagian 9).

### Kolom Turunan (Computed, Tidak Perlu Tabel Baru)
- Status "sedang berlangsung" untuk supervisi realtime dihitung dari `jam_mulai`/`jam_selesai` jadwal dibandingkan waktu server saat ini — tidak disimpan sebagai kolom, dihitung on-the-fly di service layer
- Persentase kepatuhan pengisian jurnal dihitung dari perbandingan jumlah `jurnal_gurus` berstatus `diisi` terhadap total jam mengajar terjadwal dalam periode tertentu

---

## 7. Notifikasi (via Queue, NotifikasiService)

| Trigger | Kanal | Penerima | Isi |
|---|---|---|---|
| Jam 14:00, ada jurnal belum diisi | WA + in-app (sesuai preferensi) | Guru bersangkutan | Reminder isi jurnal |
| Jam pelajaran berjalan > ambang waktu, jurnal belum diisi | In-app (badge di dashboard supervisi) | Kepsek/Wakakur | Info kelas perlu perhatian |

---

## 8. Validasi & Aturan Bisnis Kunci

- Status "sudah diisi"/"belum diisi" pada dashboard guru dan supervisi selalu bersumber dari tabel `jurnal_gurus`, bukan status terpisah — satu sumber kebenaran (single source of truth)
- Ambang waktu "kelas perlu perhatian" di dashboard supervisi dapat dikonfigurasi (mis. X menit setelah jam pelajaran dimulai), bukan hardcode
- Draft form jurnal yang belum disubmit **tidak** tersimpan permanen di database — hanya di state browser sementara, agar tidak ada data jurnal ganda/kosong yang mengotori laporan
- Export PDF jurnal individual hanya bisa diakses oleh guru pemilik data, Admin TU, dan Kepsek — bukan guru lain
- Preferensi notifikasi bersifat personal per guru, tidak memengaruhi guru lain
- Dashboard supervisi realtime hanya menampilkan data hari berjalan secara default; data historis diakses lewat filter terpisah agar performa query tetap ringan

---

## 9. Fase Pengerjaan & Review Goals

### Fase A — Fondasi Layout & Navigasi Mobile Guru
**Cakupan:**
- Struktur layout PWA khusus guru: bottom navigation (Beranda, Jurnal, Riwayat, Profil)
- Dashboard mobile (4.1): ringkasan hari ini, daftar jam mengajar dari jadwal
- Penyesuaian/pembuatan referensi data jadwal mengajar (lihat Pertanyaan Terbuka)

**Review Goals (Definition of Done):**
- [ ] Dashboard menampilkan seluruh jam mengajar guru hari itu sesuai jadwal, dengan status terisi/belum yang akurat
- [ ] Navigasi bawah berfungsi dan menyorot halaman aktif dengan jelas
- [ ] Tampilan dashboard tetap terbaca dan mudah dioperasikan satu tangan di layar HP standar (≥360px lebar)
- [ ] Status absensi harian (masuk/pulang) tampil ringkas di bagian atas dashboard

### Fase B — Input Jurnal Mobile-Optimized & Riwayat/Cetak
**Cakupan:**
- Form jurnal versi mobile (4.2), diakses langsung dari item jadwal
- Livewire `RiwayatJurnalMobile`, filter tanggal/kelas/mapel
- Export PDF per periode (`resources/views/pdf/jurnal-mengajar.blade.php`)
- Export Excel rekap lintas guru untuk Admin TU/Kepsek

**Review Goals:**
- [ ] Tap item jadwal langsung membuka form dengan kelas/mapel/jam terisi otomatis, guru tidak perlu input ulang
- [ ] Submit jurnal berhasil langsung mengubah status item di dashboard tanpa perlu refresh manual
- [ ] Riwayat jurnal bisa difilter dan menampilkan detail lengkap tiap entri
- [ ] Cetak PDF per periode menghasilkan dokumen rapi dengan kop sekolah dan data yang benar
- [ ] Export Excel rekap lintas guru hanya bisa diakses oleh Admin TU/Kepsek, bukan guru biasa

### Fase C — Notifikasi, Preferensi & Profil
**Cakupan:**
- Migration & model `preferensi_notifikasi_gurus`
- Livewire `PengaturanNotifikasi`, `ProfilGuru`
- Badge notifikasi in-app di ikon "Jurnal" pada bottom navigation
- Penyesuaian job reminder jam 14:00 agar menghormati preferensi kanal guru

**Review Goals:**
- [ ] Guru bisa mengaktifkan/menonaktifkan reminder dan memilih kanal notifikasi, tersimpan dan diterapkan dengan benar
- [ ] Badge di ikon "Jurnal" muncul sesuai jumlah jam yang belum diisi hari itu dan hilang setelah semua terisi
- [ ] Guru bisa update data pribadi terbatas dan ganti password dari halaman profil
- [ ] Ringkasan persentase kepatuhan pengisian jurnal pribadi tampil akurat di halaman profil

### Fase D — Dashboard Supervisi Realtime (Kepsek/Wakakur)
**Cakupan:**
- Service perhitungan "jam sedang berlangsung" berdasarkan jadwal & waktu server
- Livewire `DashboardSupervisiRealtime`, polling/realtime update
- Highlight visual untuk kelas yang perlu perhatian (jurnal belum diisi melewati ambang waktu)
- Filter riwayat harian & ringkasan kepatuhan per guru dalam periode

**Review Goals:**
- [ ] Dashboard menampilkan seluruh jam pelajaran yang sedang berlangsung saat itu secara akurat sesuai jadwal
- [ ] Kelas dengan jurnal belum diisi melewati ambang waktu yang dikonfigurasi ter-highlight jelas secara visual
- [ ] Ambang waktu bisa diubah melalui konfigurasi tanpa perlu mengubah kode
- [ ] Filter riwayat harian menampilkan data hari-hari sebelumnya dengan benar tanpa memperlambat tampilan realtime hari ini
- [ ] Ringkasan kepatuhan per guru dalam periode tertentu terhitung akurat dan hanya bisa diakses oleh Kepsek/Wakakur/Admin TU

---

## 10. Di Luar Cakupan (Fase 1 Modul Ini)

- Penilaian siswa (nilai tugas/harian) — akan dibahas di PRD terpisah jika dibutuhkan ke depan
- Ceklok/presensi guru per kelas secara terpisah dari jurnal
- Fitur "posting"/media sosial internal sekolah (ada di aplikasi pembanding, tidak relevan untuk kebutuhan saat ini)
- Pengukuran kinerja mandiri guru secara formal (self-performance scoring) di luar persentase kepatuhan jurnal
- Push notification native di luar kemampuan PWA/WA

---

## 11. Pertanyaan Terbuka (Belum Dikonfirmasi)

1. **Data jadwal mengajar** — apakah sudah ada tabel/master untuk jadwal (guru, kelas, mapel, hari, jam ke, jam mulai-selesai), atau perlu dirancang sebagai bagian dari Fase A modul ini? Ini krusial karena dashboard guru dan supervisi realtime sepenuhnya bergantung pada data ini.
2. **Ambang waktu "kelas perlu perhatian"** di dashboard supervisi — berapa menit setelah jam pelajaran dimulai dianggap wajar sebelum ditandai perlu perhatian?
3. Apakah Wakil Kepala Kurikulum adalah **role terpisah** yang perlu ditambahkan ke Spatie Permission, atau memakai role `guru`/`admin-tu` dengan permission tambahan?

---

*Dokumen ini adalah bagian dari rangkaian PRD per modul untuk SITU-SMA. Modul terkait: Absensi Guru, Absensi Siswa & Jurnal Mengajar (backend/skema dasar), Data Master Guru & Tenaga Kependidikan.*
