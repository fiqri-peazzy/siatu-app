# Storage Foto & Dokumen (Cloudflare R2)

Semua unggahan SIATU disimpan di bucket R2 **`smamgo-absen`**: swafoto absen guru/tendik, foto profil,
dokumentasi jurnal, bukti izin siswa, dan lampiran surat. Isinya **data pribadi**, jadi cara aksesnya penting.

Kode: `App\Support\Berkas::url($path)` — satu-satunya tempat yang membuat URL berkas.

---

## Ringkasan keputusan

| Mode | Cara kerja | Perlu subdomain/DNS? | Keamanan | Status |
|---|---|---|---|---|
| **Privat** (bawaan) | Aplikasi membuat *signed URL* R2 yang berlaku 2 jam, hanya untuk pengguna yang sedang login & berhak membuka halaman | Tidak | File tidak bisa dibuka tanpa tautan bertanda tangan yang masih berlaku | **Aktif otomatis** setelah deploy |
| Publik | URL tetap `https://storage.smamuhkotagorontalo.my.id/<path>` | Ya (domain harus di Cloudflare) | Siapa pun yang memegang tautan bisa membuka file selamanya | Opsional — Bagian C |
| `pub-….r2.dev` | URL pengembangan bawaan R2 | Tidak | Publik **dan** dibatasi laju oleh Cloudflare — *bukan untuk production* | **Harus dimatikan** — Bagian A |

**Rekomendasi: tetap di mode Privat.** Foto sudah tampil di production tanpa subdomain. Pakai subdomain publik
hanya untuk konten yang memang publik (mis. galeri sekolah nanti).

---

## A. Wajib — matikan akses publik `r2.dev` (± 2 menit)

Saat ini bucket bisa dibuka lewat `https://pub-f1bc4bb6771a41bd897cc1131d231bbf.r2.dev/...` oleh siapa saja.
Setelah deploy mode Privat, URL itu tidak dipakai aplikasi lagi, jadi aman ditutup.

1. Buka <https://dash.cloudflare.com> → menu kiri **R2 Object Storage** → klik bucket **smamgo-absen**.
2. Tab **Settings** → bagian **Public access** → **R2.dev subdomain** → klik **Disallow** → ketik `allow`/konfirmasi.
3. Uji: buka lagi URL `pub-….r2.dev/...` salah satu foto → harus muncul **401 / tidak bisa dilihat**.
4. Uji aplikasi: login admin → **Monitoring Absen Guru** → foto guru tetap tampil (lewat signed URL).

> `.env` production **tidak perlu diubah** untuk mode Privat. `CLOUDFLARE_R2_URL` boleh dibiarkan atau dikosongkan.
> Kunci `CLOUDFLARE_R2_ACCESS_KEY_ID/SECRET/ENDPOINT/BUCKET` yang sudah ada dipakai untuk menandatangani URL.

---

## B. Cek cepat bila foto tidak tampil

| Gejala | Penyebab umum | Perbaikan |
|---|---|---|
| Gambar rusak, URL berawalan `https://<akun>.r2.cloudflarestorage.com/...` dan berisi `X-Amz-Signature` → 403 | Jam server meleset / kunci API salah | Cek `CLOUDFLARE_R2_*` di `.env` production, lalu `php artisan config:cache` |
| Gambar rusak, URL tanpa `X-Amz-Signature` | Masih mode publik tanpa domain aktif | Pastikan `CLOUDFLARE_R2_PUBLIK=false` (atau hapus barisnya), `php artisan config:cache` |
| Foto lama tampil, foto baru tidak | Unggahan gagal | Lihat `storage/logs/laravel.log` di server |

---

## C. Opsional — subdomain `storage.smamuhkotagorontalo.my.id` (mode Publik)

### C.0 Syarat & hal yang perlu dipahami

- Custom domain R2 **hanya bisa dipasang jika DNS domain dikelola Cloudflare**. Saat ini nameserver domain
  ada di **Dewaweb** (`ns1/ns2/ns3.dewaweb.com`), jadi nameserver harus dipindah ke Cloudflare (gratis).
  Hosting cPanel, email, dan aplikasi **tetap di Dewaweb** — yang pindah hanya pengelola DNS.
- Dengan custom domain, bucket menjadi **publik**: siapa pun yang memegang tautan dapat membuka file tanpa login.
  Nama file baru sudah acak 42 karakter (tidak bisa ditebak), tetapi file lama (dibuat sebelum November 2026)
  bernama `uniqid()` berbasis waktu sehingga **masih bisa ditebak**.

### C.1 Catat record DNS yang ada sekarang

Data per 7 Nov 2026 (cek ulang di cPanel → **Zone Editor** sebelum pindah):

| Nama | Tipe | Nilai |
|---|---|---|
| `smamuhkotagorontalo.my.id` | A | `103.185.53.46` |
| `www` | CNAME | `smamuhkotagorontalo.my.id` |
| `siatu` | A | `103.185.53.46` |
| `mail` | CNAME/A | `smamuhkotagorontalo.my.id` / `103.185.53.46` |
| `webmail` | A | `103.185.53.46` |
| `smamuhkotagorontalo.my.id` | MX (prioritas 0) | `smamuhkotagorontalo.my.id` |
| `smamuhkotagorontalo.my.id` | TXT (SPF) | `v=spf1 +a +mx +ip4:103.185.53.46 include:relay.mailchannels.net ~all` |
| `_dmarc` | TXT | `v=DMARC1; p=none;` |
| `default._domainkey` | TXT (DKIM) | salin **lengkap** dari Zone Editor cPanel |

Juga salin record lain yang muncul di Zone Editor (mis. `cpanel`, `webdisk`, `autodiscover`, `_cpanel-dcv-test-record`).

### C.2 Tambahkan domain ke Cloudflare

1. <https://dash.cloudflare.com> → **Add a domain** → ketik `smamuhkotagorontalo.my.id` → pilih paket **Free**.
2. Cloudflare memindai & mengimpor record. **Bandingkan dengan tabel C.1** — tambahkan yang belum ada,
   terutama **DKIM** (`default._domainkey`), karena sering terpotong.
3. Atur ikon awan (**Proxy status**):
   - `siatu`, `@`, `www`: **DNS only (abu-abu)** dulu, agar SSL AutoSSL cPanel tetap berjalan seperti sekarang.
   - `mail`, `webmail`, MX, semua TXT: **wajib DNS only (abu-abu)**.
4. Klik **Continue** — Cloudflare memberi **2 nameserver** (contoh: `ada.ns.cloudflare.com`, `bob.ns.cloudflare.com`). Catat.

### C.3 Ganti nameserver di Dewaweb

1. Login **Client Area Dewaweb** → **Domains** → `smamuhkotagorontalo.my.id` → **Nameservers**.
2. Pilih **Use custom nameservers**, isi 2 nameserver dari Cloudflare, hapus `ns1/ns2/ns3.dewaweb.com` → **Save**.
3. Tunggu propagasi (biasanya 15 menit – beberapa jam, maksimal 24 jam). Cloudflare akan mengirim email
   *"… is now active"*. Selama menunggu, situs tetap jalan karena record-nya sama.
4. Uji setelah aktif: buka `https://siatu.smamuhkotagorontalo.my.id` (harus normal) dan kirim email uji ke/dari akun sekolah.

### C.4 Hubungkan subdomain ke bucket R2

1. Cloudflare → **R2 Object Storage** → bucket **smamgo-absen** → **Settings** → **Custom Domains** → **Connect Domain**.
2. Isi `storage.smamuhkotagorontalo.my.id` → **Continue** → **Connect Domain**.
   Cloudflare otomatis membuat record DNS & sertifikat SSL. Status menjadi **Active** dalam beberapa menit.
3. Uji: `https://storage.smamuhkotagorontalo.my.id/<path-salah-satu-foto>` → gambar tampil.
4. Pastikan **R2.dev subdomain tetap Disallow** (Bagian A).

### C.5 (Disarankan) batasi hotlink dengan WAF — gratis

Cloudflare → domain → **Security** → **WAF** → **Custom rules** → **Create rule**:

- Nama: `Storage hanya dari SIATU`
- *If incoming requests match* → **Edit expression**:
  ```
  (http.host eq "storage.smamuhkotagorontalo.my.id" and not http.referer contains "siatu.smamuhkotagorontalo.my.id")
  ```
- Action: **Block** → **Deploy**.

Efek: tautan foto yang disalin & dibuka di luar aplikasi ditolak. (Tidak 100% — header Referer bisa dipalsukan —
tetapi menahan penyebaran tautan biasa.)

### C.6 Aktifkan mode Publik di aplikasi

Edit `.env` production (cPanel → **File Manager** → `/home/smamuhko/siatu-app/.env`, atau lewat SSH):

```env
CLOUDFLARE_R2_URL=https://storage.smamuhkotagorontalo.my.id
CLOUDFLARE_R2_PUBLIK=true
```

Lalu lewat SSH:

```bash
cd ~/siatu-app && /usr/local/bin/php artisan config:cache
```

Untuk kembali ke mode Privat: set `CLOUDFLARE_R2_PUBLIK=false` lalu `config:cache` lagi.

---

## D. Catatan teknis

- Signed URL berlaku **120 menit** dan di-*cache* 50 menit per file, sehingga URL stabil dan gambar tetap
  di-*cache* browser/PWA. Halaman yang dibiarkan terbuka > 2 jam perlu di-refresh agar foto kembali tampil.
- Penandatanganan URL dilakukan di server tanpa memanggil API R2 (tidak menambah kuota/biaya operasi).
- Nama file baru: `Berkas::namaAcak()` — ULID + 16 karakter acak.
- Development lokal: `CLOUDFLARE_R2_LOCAL=true` → file di `storage/app/public/r2`, URL `/storage/r2/...`.
