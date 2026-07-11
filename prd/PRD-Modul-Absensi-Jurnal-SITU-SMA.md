# PRD — Modul Absensi Guru, Absensi Siswa & Jurnal Mengajar

**Proyek:** Sistem Informasi Tata Usaha SMA (SITU-SMA)
**Modul:** Akademik — Absensi Guru, Absensi Siswa, Jurnal Mengajar
**Status Dokumen:** Final Draft — siap eksekusi
**Terakhir Diperbarui:** 11 Juli 2026

---

## 1. Latar Belakang & Tujuan

Pencatatan kehadiran guru dan siswa, serta jurnal kegiatan mengajar, adalah data operasional harian yang paling sering diakses dan paling rawan tidak tertib jika masih manual. Masalah yang ingin diselesaikan:

- Kehadiran guru sulit diverifikasi kebenarannya (rawan titip absen)
- Rekap absensi siswa per jam pelajaran tidak konsisten antar guru
- Jurnal mengajar sering terlambat/tidak diisi, sehingga tidak bisa jadi bukti kegiatan belajar mengajar
- Tidak ada mekanisme deteksi dini untuk siswa yang sering alfa

**Tujuan Modul:** Menyediakan mekanisme absensi guru berbasis mobile (PWA) dengan validasi lokasi, absensi siswa per jam pelajaran, dan jurnal mengajar yang terhubung dengan jadwal — semuanya realtime ke dashboard Admin TU dan Kepala Sekolah, dengan notifikasi otomatis untuk kasus yang butuh perhatian (telat, alfa berturut, jurnal belum diisi).

---

## 2. Keputusan Arsitektur

### 2.1 Platform Akses
**PWA (Progressive Web App)** dari aplikasi Livewire yang sudah ada — bukan native app terpisah.

**Alasan:**
- Satu codebase, satu database — tidak ada proses sinkronisasi terpisah antara "app mobile" dan "panel admin", karena keduanya mengakses sistem yang sama
- Guru cukup buka URL sekali → "Add to Home Screen" → berjalan seperti aplikasi biasa
- GPS (Geolocation API) dan kamera (input file `capture`) sudah tersedia lewat browser modern tanpa perlu install app terpisah
- Menghindari kebutuhan tim/skillset mobile terpisah (Flutter/React Native) yang di luar tech stack CLAUDE.md

**Catatan batas:** push notification native dan absensi offline penuh tidak didukung PWA di semua platform (khususnya iOS). Untuk kebutuhan saat ini (absen online saat di lokasi sekolah), ini bukan blocker.

### 2.2 Validasi Kehadiran
Validasi kehadiran guru dilakukan **di server**, bukan hanya percaya data dari device, dengan 3 lapis:

1. **Geofencing** — jarak koordinat device ke titik sekolah dihitung server (formula haversine), harus dalam radius yang dikonfigurasi (`config/sekolah.php`)
2. **Foto selfie** — wajib saat absen masuk, sebagai bukti visual untuk verifikasi manual jika ada kecurigaan
3. **Window waktu** — absen masuk hanya valid dalam rentang jam yang dikonfigurasi; di luar itu otomatis berstatus terlambat atau butuh approval manual dari Admin TU

### 2.3 Pola Event-Driven untuk Efek Samping
Pencatatan absen dan reaksi terhadapnya (notifikasi telat, deteksi alfa berturut) dipisahkan lewat Laravel Event/Listener, bukan ditumpuk dalam satu service besar:

```
AbsensiGuruDicatat (event)
   ├─ Listener: CekKeterlambatan → kirim WA jika telat
   └─ Listener: CatatAuditTrail → simpan device_info, IP

AbsensiSiswaDicatat (event)
   └─ Listener: CekAlfaBerturut → notifikasi wali kelas jika ambang batas terlampaui
```

Pendekatan ini memudahkan penambahan aturan baru di masa depan tanpa mengubah logic pencatatan utama.

---

## 3. Aktor & Hak Akses (RBAC)

| Role | Absensi Guru | Absensi Siswa | Jurnal Mengajar |
|---|---|---|---|
| Super Admin | Full | Full | Full |
| Admin TU | View semua, edit/koreksi manual, approve izin | View semua, rekap & export | View semua |
| Kepala Sekolah | View, dashboard monitoring | View, dashboard monitoring | View |
| Guru | Input absen sendiri (masuk/pulang) | Input untuk kelas yang diajar, per jam pelajaran | Input untuk kelas & jam yang diajar sendiri |
| Wali Kelas (guru dengan peran tambahan) | - | Rekap kehadiran kelas yang diampu, terima notifikasi alfa | - |
| Siswa | - | View kehadiran diri sendiri (read-only) | - |

---

## 4. Ruang Lingkup Fitur

### 4.1 Absensi Guru (via PWA di HP)
- Dashboard harian guru: tombol "Absen Masuk" / "Absen Pulang", status hari ini
- Alur absen: ambil lokasi (GPS) → ambil foto → submit → validasi server → tersimpan
- Guru bisa mengajukan izin/sakit dengan upload surat keterangan (`file_izin`), status masuk sebagai `izin`/`sakit`, tidak melalui alur GPS
- Riwayat absensi pribadi guru (kalender bulanan)
- Dashboard Admin TU: rekap harian seluruh guru realtime, filter berdasarkan status
- Export rekap bulanan ke Excel (pisah hari efektif vs hari libur kalender, sesuai CLAUDE.md 5.3)

### 4.2 Absensi Siswa (per jam pelajaran, oleh guru)
- Guru membuka kelas yang sedang diajar (sesuai jadwal jam itu) → daftar siswa muncul otomatis dari `MasterSiswa` + `MasterKelas`
- Tandai status per siswa: hadir / izin / sakit / alfa, per jam ke-berapa
- Bisa "tandai semua hadir" lalu koreksi yang tidak hadir (mempercepat input)
- Riwayat kehadiran per siswa bisa dilihat siswa sendiri (read-only) dan wali kelas
- Threshold otomatis: siswa alfa ≥ 3x dalam 1 bulan → notifikasi WA ke wali kelas
- Export rekap kehadiran per kelas/siswa ke Excel

### 4.3 Jurnal Mengajar
- Terhubung ke jadwal mengajar guru hari itu — daftar jam mengajar muncul otomatis di dashboard, masing-masing berstatus "belum diisi"/"sudah diisi"
- Form singkat per jam: materi, kegiatan pembelajaran, opsional tanda tangan digital/paraf
- **Tidak bergantung mutlak pada status absensi guru** — guru pengganti/piket yang lupa absen tetap bisa mengisi jurnal
- Reminder otomatis via WA jam 14:00 ke guru yang punya jam mengajar hari itu tapi jurnalnya belum lengkap
- Rekap jurnal per guru/kelas/periode untuk keperluan supervisi Kepala Sekolah

---

## 5. Alur Proses

### 5.1 Alur Absensi Guru (PWA)
```
Guru buka PWA di HP
        ↓
Ambil GPS & foto (browser geolocation + kamera)
        ↓
Submit absensi
        ↓
Validasi server (radius sekolah & window waktu) — AbsensiService
        ↓
Simpan ke absensi_gurus
        ↓
   ├─ Dashboard Admin TU realtime
   └─ Notifikasi WA (jika terlambat/alfa) — via Event + Queue
```

### 5.2 Alur Absensi Siswa
```
Guru buka kelas sesuai jadwal jam ke-N
        ↓
Sistem tampilkan daftar siswa kelas tsb (dari MasterSiswa + MasterKelas)
        ↓
Guru tandai status per siswa (hadir/izin/sakit/alfa)
        ↓
Simpan ke absensi_siswas
        ↓
Event AbsensiSiswaDicatat → cek akumulasi alfa bulan berjalan
        ↓
Jika ≥ 3x alfa → notifikasi WA ke wali kelas
```

### 5.3 Alur Jurnal Mengajar
```
Guru absen masuk (opsional, tidak wajib untuk alur ini)
        ↓
Dashboard tampilkan daftar jam mengajar hari ini (dari jadwal)
        ↓
Guru pilih salah satu jam → isi materi & kegiatan
        ↓
Submit → status jam tsb: sudah diisi
        ↓
Jam 14:00 — Job terjadwal cek jurnal belum lengkap
        ↓
Kirim reminder WA ke guru bersangkutan
```

---

## 6. Skema Database (Draft)

### `absensi_gurus`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| guru_id | FK | ref `master_gurus` |
| tanggal | date | |
| jenis | varchar(10) | masuk / pulang |
| status | varchar(20) | hadir / izin / sakit / alfa / dinas_luar / terlambat |
| waktu_absen | timestamp | |
| latitude | decimal(10,7) | |
| longitude | decimal(10,7) | |
| jarak_dari_sekolah_meter | int | hasil hitung server saat submit |
| foto_path | varchar nullable | wajib untuk status hadir/terlambat |
| keterangan | text nullable | |
| file_izin | varchar nullable | untuk status izin/sakit |
| device_info | varchar nullable | audit trail |
| deleted_at | timestamp null | soft delete |

### `absensi_siswas`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| siswa_id | FK | ref `master_siswas` |
| kelas_id | FK | ref `master_kelas` |
| tanggal | date | |
| jam_ke | tinyint | |
| status | varchar(20) | hadir / izin / sakit / alfa |
| dicatat_oleh | FK users | guru yang menginput |
| keterangan | text nullable | |
| deleted_at | timestamp null | soft delete |

### `jurnal_gurus`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| guru_id | FK | |
| kelas_id | FK | |
| mapel_id | FK | ref `master_mapels` |
| tanggal | date | |
| jam_ke | tinyint | |
| materi | text | |
| kegiatan | text | ringkasan aktivitas pembelajaran |
| ttd | varchar nullable | path tanda tangan/paraf digital, jika ada |
| status | varchar(20) | belum_diisi / diisi |
| deleted_at | timestamp null | soft delete |

### Enum yang perlu dibuat (`app/Enums/`)
```
StatusAbsensiGuru::{Hadir, Izin, Sakit, Alfa, DinasLuar, Terlambat}
JenisAbsenGuru::{Masuk, Pulang}
StatusAbsensiSiswa::{Hadir, Izin, Sakit, Alfa}
StatusJurnal::{BelumDiisi, Diisi}
```

---

## 7. Notifikasi WhatsApp (via Queue, NotifikasiService)

| Trigger | Penerima | Pesan Inti |
|---|---|---|
| Absen masuk guru terlambat | Admin TU | Info keterlambatan guru |
| Siswa alfa ≥ 3x dalam sebulan | Wali kelas | Info akumulasi alfa siswa |
| Jam 14:00, jurnal hari itu belum lengkap | Guru bersangkutan | Reminder isi jurnal |

---

## 8. Validasi & Aturan Bisnis Kunci

- Validasi radius geofencing dan window waktu **wajib dilakukan di server**, tidak boleh hanya mengandalkan data dari client
- Foto wajib disertakan untuk absen dengan status `hadir`/`terlambat`, tidak wajib untuk `izin`/`sakit` (diganti `file_izin`)
- Absensi siswa diinput per jam pelajaran, bukan sekali per hari
- Rekap absensi guru harus memisahkan hari efektif dan hari libur kalender (perlu referensi kalender akademik)
- Jurnal mengajar tidak bergantung pada status absensi guru — keduanya independen secara data, hanya terhubung secara tampilan (dashboard)
- Semua notifikasi dikirim via Queue (job), tidak sinkron dalam request cycle
- Soft delete di seluruh tabel transaksi modul ini
- Rate limiting pada endpoint submit absen untuk mencegah spam/percobaan manipulasi

---

## 9. Fase Pengerjaan & Review Goals

Modul ini dipecah menjadi 4 fase agar setiap bagian bisa direview dan diterima sebelum lanjut ke fase berikutnya.

### Fase A — Fondasi Data & PWA Setup
**Cakupan:**
- Migration `absensi_gurus`, `absensi_siswas`, `jurnal_gurus`
- Model + relasi + Enum status
- Setup PWA: `manifest.json`, service worker dasar, ikon aplikasi, "Add to Home Screen"
- Konfigurasi titik koordinat sekolah & radius toleransi di `config/sekolah.php`

**Review Goals (Definition of Done):**
- [ ] Semua tabel punya `deleted_at` dan foreign key sesuai skema
- [ ] Aplikasi bisa di-"Add to Home Screen" dari browser HP (Android & iOS) dan tampil dengan ikon serta nama aplikasi yang benar
- [ ] Radius dan koordinat sekolah bisa diubah lewat `.env` tanpa ubah kode
- [ ] Enum bisa dipanggil `::values()` untuk validasi form

### Fase B — Absensi Guru (GPS + Foto + Validasi Server)
**Cakupan:**
- `AbsensiService::validasiLokasi()`, `catatAbsenGuru()`
- Event `AbsensiGuruDicatat` + Listener `CekKeterlambatan`, `CatatAuditTrail`
- Livewire `AbsenMasuk`, `AbsenPulang`, `RiwayatAbsensiGuru`
- Upload foto ke `storage/app/absensi/{tahun}/{bulan}/`
- Dashboard Admin TU: rekap harian realtime

**Review Goals:**
- [ ] Absen ditolak dengan pesan jelas jika device berada di luar radius yang dikonfigurasi
- [ ] Absen di luar window waktu otomatis tercatat status `terlambat`, bukan gagal total
- [ ] Foto wajib ter-upload sebelum submit berhasil untuk status hadir/terlambat
- [ ] Notifikasi WA ke Admin TU terkirim via Queue saat ada guru terlambat, tanpa memblokir response submit
- [ ] Dashboard Admin TU menampilkan status kehadiran seluruh guru hari itu tanpa refresh manual (polling/realtime Livewire)
- [ ] Export rekap bulanan Excel memisahkan hari efektif vs hari libur kalender

### Fase C — Absensi Siswa per Jam Pelajaran
**Cakupan:**
- `AbsensiService::catatAbsenSiswa()`, `cekAmbangAlfa()`
- Event `AbsensiSiswaDicatat` + Listener `CekAlfaBerturut`
- Livewire `FormAbsensiSiswa` (per kelas per jam), `RiwayatAbsensiSiswa`
- Policy: guru hanya bisa input absen untuk kelas yang benar-benar diajarnya sesuai jadwal

**Review Goals:**
- [ ] Guru hanya melihat daftar siswa dari kelas yang sesuai jadwalnya saat itu, tidak bisa akses kelas lain
- [ ] Fitur "tandai semua hadir" berfungsi dan tetap bisa dikoreksi per siswa sebelum submit
- [ ] Notifikasi WA ke wali kelas terkirim otomatis saat siswa mencapai 3x alfa dalam sebulan, tidak terkirim ulang untuk kasus yang sama
- [ ] Siswa bisa melihat riwayat kehadiran dirinya sendiri saja (read-only, tidak bisa lihat siswa lain)
- [ ] Export rekap per kelas/siswa ke Excel berjalan untuk data > 1000 baris tanpa timeout (pakai `chunk()`/`cursor()`)

### Fase D — Jurnal Mengajar & Reminder Otomatis
**Cakupan:**
- `AbsensiService::simpanJurnal()`
- Livewire `DaftarJamMengajarHariIni`, `FormJurnalMengajar`
- Scheduled Job jam 14:00: cek jurnal belum lengkap → kirim reminder WA
- Rekap jurnal per guru/kelas/periode untuk Kepala Sekolah

**Review Goals:**
- [ ] Dashboard guru menampilkan daftar jam mengajar hari itu otomatis dari jadwal, dengan status "belum diisi"/"sudah diisi" yang akurat
- [ ] Jurnal tetap bisa diisi meskipun guru tidak melakukan absen masuk hari itu (independen dari status absensi)
- [ ] Reminder WA jam 14:00 hanya terkirim ke guru yang benar-benar punya jam mengajar hari itu dan belum lengkap mengisi jurnal
- [ ] Kepala Sekolah bisa melihat rekap jurnal per guru/kelas dalam rentang tanggal tertentu
- [ ] Data jurnal tersimpan lengkap dengan relasi kelas & mapel yang benar sesuai jadwal saat itu

---

## 10. Di Luar Cakupan (Fase 1 Modul Ini)

- Native mobile app (Flutter/React Native) — cukup PWA untuk kebutuhan saat ini
- Absensi offline penuh (tanpa koneksi internet)
- Face recognition otomatis untuk verifikasi foto absen
- Push notification native di luar kemampuan browser/PWA
- Deteksi mock location tingkat lanjut (di luar validasi radius dasar)

---

## 11. Pertanyaan Terbuka (Belum Dikonfirmasi)

1. **Radius geofencing**: berapa meter toleransi yang wajar dari titik sekolah? (mempertimbangkan akurasi GPS di area sekitar sekolah)
2. **Window waktu absen masuk**: jam berapa dianggap tepat waktu vs terlambat?
3. **Approval koreksi manual**: jika guru gagal absen karena kendala teknis (GPS/sinyal), apakah Admin TU bisa input manual dengan catatan, atau harus lewat pengajuan izin formal?
4. **Kalender akademik**: apakah sudah ada data hari libur/efektif yang bisa dijadikan referensi, atau perlu dibuatkan master baru di Data Master?

---

*Dokumen ini adalah bagian dari rangkaian PRD per modul untuk SITU-SMA. Modul sebelumnya: Administrasi (Surat Masuk, Surat Keluar, Disposisi).*
