# Skrip Demo SIATU v2 — ± 15 menit

> Demo dijalankan dari **laptop (localhost)**: tampilan baru ada di branch `v2` dan **belum** di production.

## A. Persiapan (30 menit sebelum demo)

1. **Jalankan aplikasi**: `powershell -ExecutionPolicy Bypass -File scripts\dev.ps1` → buka `http://localhost:8000`.
2. **Tampilan HP di laptop**: Chrome → `F12` → ikon *Toggle device* (`Ctrl+Shift+M`) → pilih **iPhone 12 Pro** → zoom 100%.
3. **Lokasi absen agar demo tidak ditolak**: login admin → *Pengaturan Lokasi* → isi koordinat ruangan demo (Google Maps → tekan lama → salin) & radius **200 m**. (Hanya di database lokal.)
4. **Jurnal butuh hari mengajar.** Hari Jumat jadwalnya hanya kegiatan ORTOM, jadi form jurnal tidak bisa dibuka. Pilih salah satu:
   - Ubah jam Windows sementara ke **Senin 9 Nov 2026 pukul 10.20** (*Settings → Time → matikan "Set time automatically"*), lalu kembalikan setelah demo; **atau**
   - Tunjukkan jadwal hari Senin lewat strip hari, dan jelaskan alur jurnalnya secara lisan.
5. **Onboarding tampil lagi**: DevTools → *Application* → *Local Storage* → hapus kunci `siatu:onboarding:*` (atau di Profil → *Panduan aplikasi*).
6. Siapkan 2 jendela Chrome (Incognito untuk akun kedua).

| Peran | Email | Password (lokal) |
|---|---|---|
| Guru | `Abdullatifbuhari1991@gmail.com` | `lokal123` |
| Tendik | `aisawulandari15@gmail.com` | `lokal123` |
| Admin | `superadmin@situ-sma.test` | (password lama Anda) |

## B. Alur demo

**1. Pembuka (1 menit)**
> "SIATU membawa urusan tata usaha ke HP: guru absen dan isi jurnal dari kelas, tendik absen, pimpinan memantau. Tampilannya mengikuti identitas sekolah: navy, emas, gading."

**2. Login (1 menit)** — tunjukkan tampilan HP & desktop (`Ctrl+Shift+M` bolak-balik).
- Tombol *Lihat* password, peringatan Caps Lock. Salah 5× → dikunci 1 menit (keamanan).

**3. Onboarding guru (2 menit)** — login sebagai Guru.
> "Saat pertama dibuka, aplikasi minta izin satu per satu dengan penjelasan — bukan pop-up mendadak."
- Izinkan lokasi & kamera → status berubah *Diizinkan*. Notifikasi → panduan pasang ke layar utama (ada versi iPhone).

**4. Beranda guru (2 menit)**
- Sapaan & tanggal, kartu **Absen masuk/pulang**, akses cepat geser, kartu **kelas berlangsung**, ringkasan kehadiran, daftar *Mengajar hari ini*.

**5. Absen (2 menit)** — tombol emas **Absen** di tengah.
- Bingkai wajah, jarak ke sekolah, jam WITA → ambil swafoto → *tercatat*.
- Ceritakan aturannya: jam buka/tutup per hari, toleransi terlambat, **pulang cepat wajib alasan** (pilihan cepat: Dinas luar, Sakit, …), tidak bisa absen ganda, libur nasional otomatis.

**6. Jadwal mengajar (2 menit)** — tab **Jadwal**, pilih **Senin**.
> "Jadwal mengikuti dokumen resmi semester ganjil, lengkap dengan istirahat, ISHOMA, dan upacara."
- Jam ke-1 **dicoret**: tergeser upacara → tidak ditagih jurnal. Label *Gabungan*, *Mapel pilihan*, *Lanjutan setelah istirahat*. Ketuk kartu → detail.

**7. Jurnal mengajar (3 menit)** — dari detail → *Isi jurnal & absensi*.
- Langkah 1: ketuk H/I/S/A, *Semua hadir*, cari siswa, lampirkan surat izin.
- Langkah 2: *Salin dari pertemuan sebelumnya*, chip metode (Ceramah, Diskusi…).
- Langkah 3: foto & ringkasan → **Simpan**. Kembali ke Jadwal → status *Jurnal terisi*.
> "Pelajaran yang terpotong istirahat cukup diisi satu kali."
- Tab **Jurnal** → riwayat per tanggal, unduh PDF.

**8. Profil & pengingat (1 menit)**
- Kepatuhan jurnal bulan ini; *Notifikasi & pengingat* → pengingat otomatis pukul 14.00 WITA lewat notifikasi aplikasi (bukan WhatsApp).

**9. Tendik (1 menit)** — jendela Incognito, login Tendik → buka `/tendik`.
- Beranda tendik: kartu absen, garis waktu **jam absen hari ini**.

**10. Penutup & rencana berikutnya (1 menit)**
> "Berikutnya: dashboard Kepala Sekolah untuk memantau KBM & kehadiran real-time, pengaturan sistem, redesign panel admin, dan format rekap resmi siap cetak."

## C. Kalau ada kendala

| Masalah | Solusi cepat |
|---|---|
| "Di luar area absen" | Naikkan radius di *Pengaturan Lokasi* (langkah A.3) |
| Kamera tidak muncul | Izinkan kamera di ikon gembok address bar; pastikan memakai `localhost` |
| Form jurnal: "tidak sesuai dengan hari ini" | Hari ini bukan hari mengajar → lihat langkah A.4 |
| Rombel *Social* tanpa siswa | Data siswa Social belum dipindahkan; gunakan kelas *Science* |
| Halaman error / kosong | Pastikan `scripts\dev.ps1` masih berjalan; muat ulang halaman |
