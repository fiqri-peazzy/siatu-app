# Database Schema & Models Documentation

Dokumen ini berisi detail schema database saat ini beserta relasi antar model/tabel pada aplikasi **SIATU (Sistem Informasi Administrasi Tata Usaha)**.

## Daftar Tabel & Model

### 1. `users` (Model: `User`)
Menyimpan data pengguna aplikasi dengan tambahan field profil untuk mendukung multi-role (Siswa, Guru, Staf).

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `name` | `varchar(255)` | Nama pengguna |
| `email` | `varchar(255)` | Email (Unik) |
| `password` | `varchar(255)` | Password |
| `nip` | `varchar(255)` | NIP untuk Guru/Staf (Nullable) |
| `nis` | `varchar(255)` | NIS untuk Siswa (Nullable) |
| `no_hp` | `varchar(255)` | Nomor HP / WhatsApp (Nullable) |
| `avatar` | `varchar(255)` | Foto profil (Nullable) |
| `jabatan` | `varchar(255)` | Jabatan struktural (Nullable) |
| `jenis_kelamin` | `varchar(255)` | L / P (Nullable) |
| `guru_id` | `bigint(20) unsigned` | FK ke `master_gurus.id` (Nullable, Set Null on Delete) |
| `siswa_id` | `bigint(20) unsigned` | FK ke `master_siswas.id` (Nullable, Set Null on Delete) |
| `is_active` | `tinyint(1)` | Status aktif akun (Default: true) |
| `last_login_at` | `timestamp` | Waktu terakhir login (Nullable) |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

---

### 2. `master_tahun_ajarans` (Model: `Master\MasterTahunAjaran`)
Menyimpan data tahun ajaran akademik.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `nama` | `varchar(255)` | Misal: "2023/2024" |
| `semester` | `varchar(255)` | "Ganjil" / "Genap" |
| `mulai` | `date` | Tanggal mulai |
| `selesai` | `date` | Tanggal selesai |
| `is_active` | `tinyint(1)` | Status aktif (Default: false) |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

---

### 3. `master_gurus` (Model: `Master\MasterGuru`)
Menyimpan data guru dan staf.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `nip` | `varchar(255)` | NIP Guru (Unik, Nullable) |
| `nama` | `varchar(255)` | Nama Guru |
| `jabatan` | `varchar(255)` | Jabatan (Nullable) |
| `mapel_utama` | `varchar(255)` | Mata pelajaran utama (Nullable) |
| `status` | `varchar(255)` | "aktif" / "nonaktif" (Default: aktif) |
| `user_id` | `bigint(20) unsigned` | FK ke `users.id` (Nullable, Set Null on Delete) |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

---

### 4. `master_kelas` (Model: `Master\MasterKelas`)
Menyimpan data rombongan belajar (kelas).

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `kode` | `varchar(20)` | Kode rombel stabil untuk import/referensi, mis. `X-SCI`, `XI-SOC` (Nullable; Unique per `tahun_ajaran_id`) |
| `nama` | `varchar(255)` | Nama kelas (mis. "X Science") |
| `tingkat` | `varchar(255)` | Tingkat kelas (X, XI, XII) |
| `jurusan` | `varchar(255)` | Program: `IPA` (Science) / `IPS` (Social) (Nullable) |
| `wali_kelas_id` | `bigint(20) unsigned` | FK ke `master_gurus.id` (Nullable, Set Null on Delete) |
| `tahun_ajaran_id` | `bigint(20) unsigned` | FK ke `master_tahun_ajarans.id` (Cascade Delete) |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

---

### 5. `master_siswas` (Model: `Master\MasterSiswa`)
Menyimpan data siswa lengkap (sesuai format Dapodik).

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `nis` | `varchar(255)` | NIS (Unik, Nullable) |
| `nisn` | `varchar(255)` | NISN (Unik, Nullable) |
| `nama` | `varchar(255)` | Nama lengkap siswa |
| `kelas_id` | `bigint(20) unsigned` | FK ke `master_kelas.id` (Nullable, Set Null on Delete) |
| `status` | `varchar(255)` | Status ("aktif", "lulus", "pindah", "keluar") - Default: aktif |
| **Data Pribadi** | | |
| `jk`, `tempat_lahir`, `tanggal_lahir`, `nik`, `agama`, `alamat`, `rt`, `rw`, `dusun`, `kelurahan`, `kecamatan`, `kode_pos`, `jenis_tinggal`, `alat_transportasi`, `telepon`, `hp`, `email`, `skhun`, `penerima_kps`, `no_kps` | `varchar/date/text` | Nullable |
| **Data Ayah** | | |
| `ayah_nama`, `ayah_tahun_lahir`, `ayah_pendidikan`, `ayah_pekerjaan`, `ayah_penghasilan`, `ayah_nik` | `varchar` | Nullable |
| **Data Ibu** | | |
| `ibu_nama`, `ibu_tahun_lahir`, `ibu_pendidikan`, `ibu_pekerjaan`, `ibu_penghasilan`, `ibu_nik` | `varchar` | Nullable |
| **Data Wali** | | |
| `wali_nama`, `wali_tahun_lahir`, `wali_pendidikan`, `wali_pekerjaan`, `wali_penghasilan`, `wali_nik` | `varchar` | Nullable |
| **Data Tambahan** | | |
| `no_peserta_un`, `no_seri_ijazah`, `penerima_kip`, `nomor_kip`, `nama_di_kip`, `nomor_kks`, `no_akta_lahir`, `bank`, `no_rekening_bank`, `rekening_atas_nama`, `layak_pip`, `alasan_layak_pip`, `kebutuhan_khusus`, `sekolah_asal`, `anak_ke`, `lintang`, `bujur`, `no_kk`, `berat_badan`, `tinggi_badan`, `lingkar_kepala`, `jml_saudara_kandung`, `jarak_rumah_ke_sekolah` | `varchar` | Nullable |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

---

### 6. `master_kategori_surats` (Model: `Master\MasterKategoriSurat`)
Menyimpan referensi kategori surat menyurat.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `kode` | `varchar(255)` | Kode Kategori (Unik) |
| `nama` | `varchar(255)` | Nama Kategori |
| `format_nomor` | `varchar(255)` | Format nomor urut surat |
| `counter` | `int(11)` | Nomor urut terakhir (Default: 0) |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

---

### 7. `master_mapels` (Model: `Master\MasterMapel`)
Menyimpan referensi mata pelajaran.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `kode` | `varchar(255)` | Kode Mata Pelajaran (Unik) |
| `nama` | `varchar(255)` | Nama Mata Pelajaran |
| `jam` | `int(11)` | Jam pelajaran (Default: 2) |
| `kurikulum` | `varchar(255)` | Kurikulum (Nullable) |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |


## Relasi Antar Tabel (Entity Relationship)

Berikut adalah daftar relasi antar tabel (Foreign Keys):

1. **`users`**
   - `guru_id` -> `master_gurus.id` (User bisa ditautkan ke profil Guru)
   - `siswa_id` -> `master_siswas.id` (User bisa ditautkan ke profil Siswa)

2. **`master_gurus`**
   - `user_id` -> `users.id` (Guru bisa ditautkan dengan akun User login)
   - *Terdapat relasi dua arah (bi-directional nullable) antara users dan master_gurus, menyesuaikan bagaimana user dibuat nantinya.*

3. **`master_kelas`**
   - `wali_kelas_id` -> `master_gurus.id` (Tiap kelas bisa memiliki satu Wali Kelas)
   - `tahun_ajaran_id` -> `master_tahun_ajarans.id` (Kelas terkait dengan Tahun Ajaran spesifik)

4. **`master_siswas`**
   - `kelas_id` -> `master_kelas.id` (Siswa tergabung dalam satu Kelas)


### 8. `master_klasifikasi_arsips` (Model: `Master\MasterKlasifikasiArsip`)
Menyimpan referensi kode klasifikasi arsip tata naskah dinas.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `kode` | `varchar(255)` | Kode Klasifikasi (Unik, misal: 421) |
| `nama` | `varchar(255)` | Nama Klasifikasi (Misal: Pendidikan) |
| `deskripsi` | `text` | Keterangan tambahan (Nullable) |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

---

### 9. `surat_masuks` (Model: `SuratMasuk`)
Menyimpan data surat masuk yang diterima instansi.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `no_agenda` | `varchar(50)` | Nomor agenda surat (Unik) |
| `kategori_surat_id` | `bigint(20) unsigned` | FK ke `master_kategori_surats.id` |
| `klasifikasi_arsip_id` | `bigint(20) unsigned` | FK ke `master_klasifikasi_arsips.id` (Nullable) |
| `tanggal_terima` | `date` | Tanggal surat diterima |
| `tanggal_surat` | `date` | Tanggal pada surat asli |
| `asal_instansi` | `varchar(255)` | Pengirim surat |
| `perihal` | `varchar(255)` | Ringkasan isi surat |
| `sifat` | `varchar(20)` | biasa/penting/segera/rahasia (Default: biasa) |
| `file_path` | `varchar(255)` | Path file scan PDF/JPG (Nullable) |
| `status` | `varchar(30)` | baru/didisposisi/diproses/selesai/diarsipkan (Default: baru) |
| `input_oleh` | `bigint(20) unsigned` | FK ke `users.id` (Nullable) |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

---

### 10. `surat_keluars` (Model: `SuratKeluar`)
Menyimpan data draft surat keluar hingga di-approve dan terkirim.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `no_surat` | `varchar(50)` | Nomor surat resmi (Nullable saat draft) |
| `kategori_surat_id` | `bigint(20) unsigned` | FK ke `master_kategori_surats.id` |
| `klasifikasi_arsip_id` | `bigint(20) unsigned` | FK ke `master_klasifikasi_arsips.id` (Nullable) |
| `surat_masuk_id` | `bigint(20) unsigned` | FK ke `surat_masuks.id` (Nullable, jika balasan) |
| `tanggal` | `date` | Tanggal surat dibuat |
| `tujuan` | `varchar(255)` | Penerima/tujuan surat |
| `perihal` | `varchar(255)` | Perihal surat |
| `isi_ringkas` | `text` | Ringkasan/draft isi surat |
| `file_draft_path` | `varchar(255)` | Path lampiran/draft sebelum TTD (Nullable) |
| `file_final_path` | `varchar(255)` | Path scan surat sesudah TTD (Nullable) |
| `status` | `varchar(30)` | draft/menunggu_approval/approved/revisi/terkirim/diarsipkan (Default: draft) |
| `dibuat_oleh` | `bigint(20) unsigned` | FK ke `users.id` pembuat draft (Nullable) |
| `catatan_revisi` | `text` | Alasan penolakan oleh Kepsek (Nullable) |
| `disetujui_oleh` | `bigint(20) unsigned` | FK ke `users.id` approver (Nullable) |
| `disetujui_at` | `timestamp` | Waktu surat di-approve (Nullable) |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

---

### 11. `disposisis` (Model: `Disposisi`)
Menyimpan riwayat disposisi surat masuk berjenjang.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `surat_masuk_id` | `bigint(20) unsigned` | FK ke `surat_masuks.id` |
| `parent_disposisi_id` | `bigint(20) unsigned` | FK ke `disposisis.id` untuk disposisi berantai (Nullable) |
| `level` | `tinyint` | Kedalaman rantai disposisi (Default: 1) |
| `dari_user_id` | `bigint(20) unsigned` | FK ke `users.id` pendisposisi |
| `ke_user_id` | `bigint(20) unsigned` | FK ke `users.id` penerima disposisi |
| `catatan` | `text` | Isi/instruksi disposisi |
| `status` | `varchar(30)` | diterima/diproses/selesai/diteruskan (Default: diterima) |
| `tgl_disposisi` | `timestamp` | Waktu disposisi dibuat |
| `deleted_at` | `timestamp` | Soft Deletes |
| `created_at`, `updated_at` | `timestamp` | Timestamps |


5. **`surat_masuks`**
   - `kategori_surat_id` -> `master_kategori_surats.id`
   - `klasifikasi_arsip_id` -> `master_klasifikasi_arsips.id`
   - `input_oleh` -> `users.id`

6. **`surat_keluars`**
   - `kategori_surat_id` -> `master_kategori_surats.id`
   - `klasifikasi_arsip_id` -> `master_klasifikasi_arsips.id`
   - `surat_masuk_id` -> `surat_masuks.id`
   - `dibuat_oleh` -> `users.id`
   - `disetujui_oleh` -> `users.id`

7. **`disposisis`**
   - `surat_masuk_id` -> `surat_masuks.id`
   - `parent_disposisi_id` -> `disposisis.id` (Self-referencing untuk disposisi berantai)
   - `dari_user_id` -> `users.id`
   - `ke_user_id` -> `users.id`


---

## Jadwal v2 (Pola Jam, Sesi, Pengampu)

Struktur jadwal fleksibel yang menggantikan `master_jadwal_pelajarans` (tabel lama tetap ada sampai semua pemakainya dimigrasi). Lihat `docs/ROADMAP_V2.md`.

```
master_tahun_ajarans 1─* master_pola_jams 1─* master_slot_jams 1─* jadwal_sesis *─* master_kelas
                                                                     │  (pivot jadwal_sesi_kelas)
                                                                     ├─ master_mapels (nullable)
                                                                     └─ master_gurus  (nullable, sinkron dari pengampu)
master_pengampus: tahun_ajaran × kelas × mapel → guru
```

### 12. `master_pola_jams` (Model: `Master\MasterPolaJam`)
Template struktur waktu sekolah per tahun ajaran. Format jadwal baru (kurikulum/jam masuk berubah) cukup dibuat sebagai pola baru.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `tahun_ajaran_id` | `bigint(20) unsigned` | FK ke `master_tahun_ajarans.id` (Cascade Delete) |
| `nama` | `varchar(255)` | Nama pola, mis. "Pola Jam Semester Ganjil 2026/2027" |
| `keterangan` | `text` | Nullable |
| `is_active` | `tinyint(1)` | Pola yang berlaku (satu aktif per tahun ajaran) |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

### 13. `master_slot_jams` (Model: `Master\MasterSlotJam`)
Satu baris waktu dalam satu hari pada sebuah pola.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `pola_jam_id` | `bigint(20) unsigned` | FK ke `master_pola_jams.id` (Cascade Delete) |
| `hari` | `varchar(10)` | `senin`..`sabtu` (Enum `Hari`) |
| `urutan` | `tinyint unsigned` | Urutan dalam hari. Unique (`pola_jam_id`, `hari`, `urutan`) |
| `jam_ke` | `tinyint unsigned` | Nomor jam pelajaran; Nullable untuk istirahat/upacara |
| `jam_mulai`, `jam_selesai` | `time` | Rentang waktu |
| `jenis` | `varchar(30)` | `pelajaran` / `istirahat` / `upacara` (Enum `JenisSlotJam`) |
| `label` | `varchar(255)` | Nullable, mis. "Istirahat / Shalat Dhuha", "ISHOMA" |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

### 14. `jadwal_sesis` (Model: `JadwalSesi`)
Isi sebuah slot untuk satu/lebih rombel.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `slot_jam_id` | `bigint(20) unsigned` | FK ke `master_slot_jams.id` (Cascade Delete) |
| `jenis` | `varchar(30)` | `pembelajaran` / `pilihan` / `kegiatan` (Enum `JenisSesi`) |
| `mapel_id` | `bigint(20) unsigned` | FK ke `master_mapels.id` (Nullable untuk kegiatan, Set Null on Delete) |
| `guru_id` | `bigint(20) unsigned` | FK ke `master_gurus.id` (Nullable, Set Null). Disinkronkan dari `master_pengampus` |
| `guru_manual` | `tinyint(1)` | `true` = guru diatur manual, tidak ditimpa sinkronisasi pengampu |
| `label` | `varchar(255)` | Nama kegiatan (Tahfidz, Pembinaan ORTOM, dll) — Nullable |
| `grup_pilihan` | `varchar(50)` | Penanda sesi paralel mapel pilihan, mis. `BIO/MATL/BINGTL` — Nullable |
| `created_at`, `updated_at` | `timestamp` | Timestamps |

### 15. `jadwal_sesi_kelas` (pivot)
Rombel peserta sebuah sesi. Lebih dari satu baris = sesi gabungan.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `jadwal_sesi_id` | `bigint(20) unsigned` | FK ke `jadwal_sesis.id` (Cascade Delete) — PK komposit |
| `kelas_id` | `bigint(20) unsigned` | FK ke `master_kelas.id` (Cascade Delete) — PK komposit |

### 16. `master_pengampus` (Model: `Master\MasterPengampu`)
Pembagian tugas mengajar: guru pengampu satu mapel di satu rombel.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint(20) unsigned` | Primary Key |
| `tahun_ajaran_id` | `bigint(20) unsigned` | FK ke `master_tahun_ajarans.id` (Cascade Delete) |
| `kelas_id` | `bigint(20) unsigned` | FK ke `master_kelas.id` (Cascade Delete) |
| `mapel_id` | `bigint(20) unsigned` | FK ke `master_mapels.id` (Cascade Delete) |
| `guru_id` | `bigint(20) unsigned` | FK ke `master_gurus.id` (Nullable = belum ditentukan, Set Null) |
| `created_at`, `updated_at` | `timestamp` | Timestamps. Unique (`tahun_ajaran_id`, `kelas_id`, `mapel_id`) |
