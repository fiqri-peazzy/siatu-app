# Setup Development Lokal (Windows)

Panduan menjalankan SIATU di laptop developer. Production ada di cPanel (`siatu.smamuhkotagorontalo.my.id`) dan ter-deploy otomatis dari branch `main` — lihat `.github/workflows/deploy.yml`.

## Prasyarat

| Kebutuhan | Versi | Catatan |
|---|---|---|
| PHP | **8.3** (sama dengan production) | Ekstensi: `bcmath curl exif fileinfo gd intl mbstring openssl pdo_mysql pdo_sqlite sodium zip` |
| Composer | 2.x | |
| Node.js | 20+ | Untuk Vite / Tailwind 4 |
| MySQL | 8.0 | Service `MySQL80` |

PHP portabel cukup diekstrak ke `%USERPROFILE%\tools\php83` lalu tambahkan folder itu ke PATH user.
Agar OpenSSL berfungsi (dipakai `webpush:vapid`), set variabel environment user:

```
OPENSSL_CONF=%USERPROFILE%\tools\php83\extras\ssl\openssl.cnf
```

> Setelah mengubah PATH / environment, **tutup lalu buka ulang VS Code** (bukan hanya tab terminal) agar terminal mengenali `php` dan `composer`.

## Langkah pertama kali

```bash
composer install
npm ci

cp .env.example .env          # lalu isi DB_* (MySQL lokal) dan set CLOUDFLARE_R2_LOCAL=true
php artisan key:generate
php artisan webpush:vapid      # VAPID khusus lokal — jangan pakai key production

# buat database kosong, mis. lewat MySQL: CREATE DATABASE siatu_app;
php artisan migrate --seed
php artisan storage:link
```

`CLOUDFLARE_R2_LOCAL=true` membuat upload (foto absen, lampiran surat) disimpan di `storage/app/public/r2`, sehingga uji coba lokal **tidak masuk ke bucket R2 production**.

## Menjalankan aplikasi

```powershell
powershell -ExecutionPolicy Bypass -File scripts\dev.ps1
```

Membuka server `http://localhost:8000`, queue worker, dan Vite (hot reload) sekaligus. `Ctrl+C` untuk berhenti.

> ⚠️ Jangan pakai `composer run dev` / `php artisan serve` di Windows: `artisan serve` mengosongkan environment proses anak sehingga `APP_KEY` dari `.env` tidak terbaca (error *"No application encryption key has been specified"*). `scripts/dev.ps1` memakai `php -S` langsung dengan router yang sama.

## Test

```bash
php artisan test
```

Test memakai SQLite in-memory (lihat `phpunit.xml`), tidak menyentuh database lokal. CI di GitHub Actions menjalankan test yang sama untuk setiap branch selain `main`.

## Reset database lokal

```bash
php artisan migrate:fresh --seed
```

Hanya untuk database **lokal**. Di production jangan jalankan `db:seed` tanpa `--class` — `UsersTableSeeder` menghapus tabel users.
