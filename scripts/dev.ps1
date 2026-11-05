# ============================================================
# SIATU — jalankan development lokal (Windows)
#   powershell -ExecutionPolicy Bypass -File scripts\dev.ps1
#
# Menjalankan sekaligus:
#   - Server PHP   : http://localhost:8000
#   - Queue worker : proses notifikasi/job di antrian
#   - Vite         : CSS/JS dengan hot reload
# Tekan Ctrl+C untuk menghentikan semuanya.
#
# Catatan: sengaja memakai `php -S`, bukan `php artisan serve` — di Windows
# `artisan serve` mengosongkan environment proses anak sehingga APP_KEY dari
# .env tidak terbaca (error "No application encryption key").
# ============================================================
$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)

$php = (Get-Command php -ErrorAction SilentlyContinue).Source
if (-not $php) { $php = "$env:USERPROFILE\tools\php83\php.exe" }
if (-not (Test-Path $php)) { throw "PHP tidak ditemukan. Pasang PHP 8.3 atau tambahkan ke PATH." }
if (-not $env:OPENSSL_CONF) { $env:OPENSSL_CONF = Join-Path (Split-Path $php) 'extras\ssl\openssl.cnf' }
if (-not (Test-Path '.env')) { throw "File .env belum ada. Salin dari .env.example lalu isi koneksi database." }

& $php artisan config:clear | Out-Null

# server.php memakai getcwd() sebagai folder public → proses harus berjalan dari public/
$root = (Get-Location).Path
$router = Join-Path $root 'vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php'
$jobs = @(
    Start-Process -FilePath $php -ArgumentList @('-S', '127.0.0.1:8000', $router) -WorkingDirectory (Join-Path $root 'public') -NoNewWindow -PassThru
    Start-Process -FilePath $php -ArgumentList @('artisan', 'queue:listen', '--tries=1', '--timeout=0') -NoNewWindow -PassThru
    Start-Process -FilePath 'cmd.exe' -ArgumentList @('/c', 'npm', 'run', 'dev') -NoNewWindow -PassThru
)

Write-Host ''
Write-Host '  SIATU lokal berjalan di  http://localhost:8000' -ForegroundColor Yellow
Write-Host '  Ctrl+C untuk berhenti.' -ForegroundColor DarkGray
Write-Host ''

try {
    while ($true) {
        if ($jobs | Where-Object { $_.HasExited }) { Write-Host 'Salah satu proses berhenti.' -ForegroundColor Red; break }
        Start-Sleep -Seconds 1
    }
} finally {
    foreach ($j in $jobs) { if (-not $j.HasExited) { Stop-Process -Id $j.Id -Force -ErrorAction SilentlyContinue } }
    # npm run dev menjalankan node sebagai proses anak
    Get-Process node -ErrorAction SilentlyContinue | Where-Object { $_.Path -like '*node*' -and $_.StartTime -ge $jobs[2].StartTime } |
        Stop-Process -Force -ErrorAction SilentlyContinue
}
