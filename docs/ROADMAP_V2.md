# Roadmap SIATU v2

Branch kerja: `v2`. Merge ke `main` **per tahap**, setelah dites — setiap push ke `main` langsung ter-deploy ke production (`siatu.smamuhkotagorontalo.my.id`).

## Keputusan yang sudah disepakati (6 Nov 2026)

| Topik | Keputusan |
|---|---|
| Rombel | 1 rombel per program: X/XI/XII × Science (IPA) / Social (IPS) = 6 rombel |
| Sesi gabungan | 1 jurnal per sesi; absensi siswa tetap dicatat per rombel |
| Guru pengampu | Diatur per **mapel × rombel** (bukan per jam); awalnya diisi acak, lalu disetel admin |
| Palet warna | Mengikuti website sekolah (smamg-platform.vercel.app): navy `#0e2a55`, gold `#d4af37`, ivory `#f6f4ee`; font Poppins + Plus Jakarta Sans — diolah jadi gaya "console" yang lebih kaya |
| Upacara Senin | 07.00–07.45, menimpa jam ke-I; fleksibel per tanggal via `jadwal_pengecualians` |

## Tahap

### Tahap 1 — Fondasi jadwal fleksibel
- [x] Skema: `master_pola_jams`, `master_slot_jams`, `master_pengampus`, `jadwal_sesis`, `jadwal_sesi_kelas`, kolom `master_kelas.kode`
- [x] Data jadwal Semester Ganjil 2026/2027 (transkripsi foto, diverifikasi developer)
- [x] Pengecualian jadwal per tanggal (`jadwal_pengecualians`): upacara ditiadakan/molor, kegiatan sisipan → `JadwalService::jadwalEfektif()`
- [ ] Modul admin: Pengecualian jadwal (kalender harian)
- [x] Seeder: 6 rombel, mapel baru, pola jam, sesi, pengampu acak
- [ ] Modul admin: Guru Pengampu (matriks mapel × rombel)
- [ ] Modul admin: Jadwal (grid per hari, edit sesi, deteksi bentrok guru)
- [ ] Migrasi pemakai tabel lama `master_jadwal_pelajarans` (portal guru, jurnal, supervisi) → pensiunkan tabel lama
- [ ] Jurnal per sesi (multi-rombel) + absensi per rombel

### Tahap 1b — Aturan absensi & Pengaturan Sistem
- [x] Tabel `aturan_absensis` + `AturanAbsensiService::evaluasi()` (jendela check-in/out per kategori per hari, toleransi, pulang cepat + alasan, cegah absen ganda, hormati hari libur)
- [x] Integrasi ke form absen guru & tendik (cek sebelum kamera dibuka)
- [ ] Halaman Pengaturan Sistem (tab: Lokasi & Radius, Jam Absensi, Notifikasi, Identitas Sekolah) — dibangun dengan design system Tahap 2

### Tahap 2 — Design system
- [ ] Token warna (light/dark), tipografi, spacing, radius, elevation
- [ ] Komponen: button, input, select, tabel, badge status, card, tabs, modal, drawer
- [ ] Custom alert/toast/confirm (ganti SweetAlert default), tooltip informatif, empty state, skeleton

### Tahap 3 — Dashboard Kepala Sekolah
- [ ] KBM berlangsung sekarang per slot × rombel (guru hadir / jurnal terisi / kosong)
- [ ] Kehadiran hari ini: siswa, guru, tendik
- [ ] Rekap mingguan/bulanan + tren; drill-down per guru/rombel/siswa

### Tahap 4 — Redesign panel admin (per modul)

### Tahap 5 — Portal mobile guru & tendik
- [ ] Audit alur absen: GPS mati/akurasi rendah, di luar radius, terlambat, lupa absen pulang, offline/sinyal lemah, izin kamera ditolak, izin/sakit + bukti
- [ ] Redesign layout mobile (bottom nav, gesture-friendly, state lengkap)

### Tahap 6 — Dokumen resmi siap cetak
- [ ] Format rekap resmi (absensi siswa/guru/tendik, jurnal, rekap bulanan) — kop sekolah, tanda tangan, PDF & Excel
- [ ] Template & alur cetak surat masuk/keluar + lembar disposisi resmi
- [ ] Template import: jadwal, siswa, guru, tendik (format Dapodik + template Excel sendiri), dengan pratinjau & validasi sebelum simpan

## Catatan teknis
- Tidak ada PHP lokal; pengujian migrasi lewat staging/CI (lihat diskusi).
- Tabel lama `master_jadwal_pelajarans` tetap ada sampai semua pemakainya dipindah.
