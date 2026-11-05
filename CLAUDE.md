# CLAUDE.md — Panduan AI Agent untuk Proyek Aplikasi Tata Usaha SMA

> File ini adalah panduan utama bagi AI agent (Antigravity) dalam membantu pengembangan aplikasi **Sistem Informasi Tata Usaha SMA**. Baca seluruh file ini sebelum mengerjakan task apapun.

---

## 1. Konteks Proyek

**Nama Proyek:** Sistem Informasi Tata Usaha SMA (SITU-SMA)
**Tujuan:** Digitalisasi proses administrasi, pengelolaan aset, keuangan BOS, kegiatan, dan dokumentasi sekolah tingkat SMA.
**Client:** Sekolah Menengah Atas (pihak Tata Usaha & Kepala Sekolah)
**Target Pengguna:** Admin TU, Kepala Sekolah, Guru/Wali Kelas, Staf TU, Siswa (terbatas)

---

## 2. Tech Stack

```
Backend  : Laravel 12 (PHP 8.2+)
Frontend : Blade + Livewire 3 (server-side reactive) + Alpine.js
Database : MySQL 8.0
CSS      : Tailwind CSS 4
Storage  : Local disk / S3-compatible (untuk upload dokumen & foto)
Auth     : Laravel Sanctum (session-based untuk web)
Queue    : Laravel Queue (database driver, bisa upgrade Redis)
Notif    : WhatsApp Gateway via Fonnte API + Laravel Mail (SMTP)
PDF      : DomPDF (via barryvdh/laravel-dompdf)
Excel    : Laravel Excel (via maatwebsite/excel)
QR Code  : simplesoftwareio/simple-qrcode
```

**Jangan** menggunakan framework/library di luar daftar di atas kecuali ada diskusi dan persetujuan eksplisit.

---

## 3. Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Controller khusus admin
│   │   ├── Auth/
│   │   └── Api/            # API endpoint jika dibutuhkan
│   ├── Livewire/           # Semua Livewire component
│   │   ├── Administrasi/
│   │   ├── Absensi/
│   │   ├── Sapras/
│   │   ├── Bos/
│   │   ├── Kegiatan/
│   │   └── Galeri/
│   ├── Middleware/
│   └── Requests/           # Form Request validation
├── Models/
├── Services/               # Business logic layer
│   ├── SuratService.php
│   ├── AbsensiService.php
│   ├── BosService.php
│   └── NotifikasiService.php
├── Exports/                # Laravel Excel export class
├── Imports/                # Laravel Excel import class
├── Policies/               # Authorization policy per model
└── Enums/                  # PHP 8.1 Enum untuk status, kategori

database/
├── migrations/
└── seeders/

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php       # Layout utama authenticated
│   │   └── guest.blade.php
│   ├── livewire/               # View untuk setiap Livewire component
│   ├── pdf/                    # Template PDF (laporan, surat)
│   └── emails/                 # Template email
└── js/

routes/
├── web.php
└── api.php

docs/
├── README.md               # Pusat indeks dokumentasi
├── DB_SCHEMA.md            # Dokumentasi skema & relasi database
├── design.md               # Spesifikasi desain UI/UX & PWA mobile
└── prd/                    # Product Requirement Documents per modul
```

---

## 4. Role & Permission

Gunakan **Spatie Laravel Permission** untuk RBAC.

### Daftar Role
| Role | Slug | Keterangan |
|---|---|---|
| Super Admin | `super-admin` | Akses penuh, kelola user & role |
| Admin TU | `admin-tu` | Kepala TU, akses hampir semua modul |
| Kepala Sekolah | `kepala-sekolah` | Approve, monitoring, dashboard |
| Guru | `guru` | Input jurnal, absensi kelas, pengajuan kegiatan |
| Staf TU | `staf-tu` | Input surat, absensi guru, sapras |
| Siswa | `siswa` | Read-only: absensi pribadi, galeri |

### Pola Penggunaan Permission di Kode

```php
// Di Policy
public function create(User $user): bool
{
    return $user->hasAnyRole(['admin-tu', 'staf-tu']);
}

// Di Blade
@can('create', SuratMasuk::class)
    <button>Tambah Surat</button>
@endcan

// Di Livewire component
public function mount(): void
{
    $this->authorize('viewAny', SuratMasuk::class);
}
```

---

## 5. Modul & Model Utama

### 5.1 Data Master

```php
// Model-model master — selalu gunakan soft delete
// App\Models\Master\*

MasterSiswa          // NIS, nama, kelas_id, jurusan, status
MasterGuru           // NIP, nama, jabatan, mapel, status
MasterKelas          // nama, tingkat, jurusan, wali_kelas_id, tahun_ajaran_id
MasterTahunAjaran    // nama, semester, mulai, selesai, is_active
MasterKategoriSurat  // kode, nama, format_nomor, counter
MasterJenisAset      // kode_barang, nama, satuan, kategori
MasterMapel          // kode, nama, jam, kurikulum
MasterJenisKegiatan  // nama, kategori, template_surat
MasterRekeningBos    // no_rekening, bank, nama, komponen
```

### 5.2 Modul Administrasi

**Model:**
```php
SuratMasuk     // no_agenda, tanggal, pengirim, perihal, file_path, status
SuratKeluar    // no_surat, tanggal, tujuan, perihal, file_path, ditandatangani
Disposisi      // surat_masuk_id, penerima_id, isi, status, tgl_disposisi
```

**Aturan bisnis:**
- Nomor agenda surat masuk: `{KODE_KATEGORI}/{ROMAWI_BULAN}/{TAHUN}` — counter auto-increment per kategori per tahun
- Nomor surat keluar: `{NO_URUT}/{KODE}/{ROMAWI_BULAN}/{TAHUN}`
- Upload file: max 5MB, format PDF/JPG/PNG, simpan di `storage/app/surat/{tahun}/{bulan}/`
- Disposisi bersifat multi-level, satu surat bisa didisposisi ke beberapa pejabat

### 5.3 Modul Absensi

**Model:**
```php
AbsensiGuru    // guru_id, tanggal, status (hadir|izin|sakit|alfa|dinas_luar), keterangan, file_izin
AbsensiSiswa   // siswa_id, kelas_id, tanggal, jam_ke, status, keterangan
JurnalGuru     // guru_id, kelas_id, mapel_id, tanggal, jam_ke, materi, kegiatan, ttd
```

**Aturan bisnis:**
- Absensi siswa diinput per jam pelajaran (jam ke-1 s/d ke-N)
- Threshold alert: siswa alfa ≥ 3x dalam 1 bulan → notifikasi ke wali kelas
- Jurnal guru harus diisi setiap hari mengajar; reminder otomatis jam 14.00
- Rekap absensi guru: pisahkan antara hari efektif dan hari libur kalender

### 5.4 Modul Sapras (Sarana & Prasarana)

**Model:**
```php
Aset           // kode_aset, jenis_aset_id, nama, spesifikasi, nilai_perolehan,
               // tgl_perolehan, kondisi (baik|rusak_ringan|rusak_berat|hilang),
               // ruangan, penanggungjawab_id, qr_code_path
RiwayatAset    // aset_id, tanggal, jenis (perbaikan|mutasi|penghapusan), keterangan, user_id
```

**Aturan bisnis:**
- Kode aset: `{KODE_JENIS}-{TAHUN}-{NO_URUT}` — generate otomatis
- QR code di-generate saat aset pertama kali disimpan, simpan di `storage/app/qrcode/`
- Penghapusan aset wajib lampirkan Berita Acara (upload PDF)
- Tidak boleh hard delete — gunakan status `nonaktif`

### 5.5 Modul BOS

**Model:**
```php
TahunBos       // tahun, total_dana, jumlah_siswa, status (draft|aktif|selesai)
Rkas           // tahun_bos_id, komponen, sub_komponen, uraian, volume, satuan,
               // harga_satuan, total_rencana
RealisasiBos   // rkas_id, tanggal, uraian_belanja, nilai, no_bukti,
               // file_bukti, input_oleh
```

**Aturan bisnis:**
- Total realisasi per komponen tidak boleh melebihi plafon RKAS → validasi di service layer
- Upload bukti: max 10MB, PDF/JPG, wajib ada
- Laporan triwulan: Q1 (Jan–Mar), Q2 (Apr–Jun), Q3 (Jul–Sep), Q4 (Okt–Des)
- Format export harus kompatibel dengan format ARKAS Kemdikbud

### 5.6 Modul Permohonan Kegiatan

**Model:**
```php
PermohonanKegiatan  // judul, jenis_kegiatan_id, tanggal_mulai, tanggal_selesai,
                    // lokasi, peserta, estimasi_biaya, file_proposal, file_rab,
                    // pengaju_id, status (draft|review_wakasek|review_kepsek|approved|rejected)
ApprovalKegiatan    // permohonan_id, approver_id, role_approver, status, catatan, tgl_approval
```

**Aturan bisnis:**
- Alur approve wajib berurutan: Guru → Wakasek → Kepsek
- Jika Wakasek reject → kembali ke draft, bisa direvisi dan resubmit
- Jika Kepsek approve → otomatis generate surat tugas (PDF via DomPDF)
- Notifikasi WhatsApp dikirim ke approver berikutnya setiap perubahan status

### 5.7 Modul Galeri

**Model:**
```php
Album          // judul, kegiatan_id (nullable), deskripsi, cover_path,
               // is_publik, created_by
FotoGaleri     // album_id, file_path, thumbnail_path, caption, urutan
```

**Aturan bisnis:**
- Resize otomatis: thumbnail 300×200px, preview 800×600px, simpan original
- Watermark logo sekolah di pojok kanan bawah (via Intervention Image)
- Album `is_publik = true` bisa diakses tanpa login di halaman publik
- Upload batch: max 20 foto sekaligus, max 8MB per foto, format JPG/PNG/WEBP

---

## 6. Konvensi Penulisan Kode

### Penamaan
```
Model          : PascalCase singular         → SuratMasuk, AbsensiGuru
Tabel DB       : snake_case            → surat_masuk, absensi_guru
Controller     : PascalCase + Controller     → SuratMasukController
Livewire       : PascalCase per fitur        → ListSuratMasuk, FormSuratMasuk
Service        : PascalCase + Service        → SuratService
Request        : PascalCase + Request        → StoreSuratMasukRequest
Policy         : PascalCase + Policy         → SuratMasukPolicy
Enum           : PascalCase                  → StatusSurat, KondisiAset
Migration      : snake_case timestamp prefix → 2024_01_01_create_surat_masuks_table
```

### Struktur Livewire Component
```php
<?php

namespace App\Livewire\Administrasi;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ListSuratMasuk extends Component
{
    use WithPagination, WithFileUploads;

    // Public properties (state)
    public string $search = '';
    public string $filterStatus = '';
    public string $filterBulan = '';

    // Computed properties (pakai #[Computed] attribute)
    #[Computed]
    public function suratMasuks()
    {
        return SuratMasuk::query()
            ->with(['kategori', 'disposisis'])
            ->when($this->search, fn($q) => $q->where('perihal', 'like', "%{$this->search}%"))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest('tanggal')
            ->paginate(15);
    }

    // Listeners dari event
    protected $listeners = ['suratDisimpan' => '$refresh'];

    public function render(): \Illuminate\View\View
    {
        return view('livewire.administrasi.list-surat-masuk');
    }
}
```

### Struktur Service Layer
```php
<?php

namespace App\Services;

class SuratService
{
    public function generateNomorAgenda(int $kategoriId): string
    {
        // Logic nomor agenda di sini, bukan di Controller/Livewire
    }

    public function simpanSuratMasuk(array $data, ?UploadedFile $file): SuratMasuk
    {
        // Wrap dalam DB::transaction jika multi-tabel
        return DB::transaction(function () use ($data, $file) {
            // ...
        });
    }
}
```

### Aturan Query
- **Selalu** gunakan Eloquent atau Query Builder, **tidak pernah** raw SQL kecuali untuk laporan agregasi kompleks
- **Selalu** eager load relasi yang dibutuhkan di view (`with([...])`) untuk hindari N+1
- Semua query dengan filter user (per kelas, per guru) **wajib** di-scope via Policy atau local scope
- Gunakan `chunk()` atau `cursor()` untuk export data besar (> 1000 rows)

---

## 7. Skema Database Utama

### Konvensi Tabel
```sql
-- Semua tabel wajib memiliki kolom ini:
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
created_at      TIMESTAMP
updated_at      TIMESTAMP
deleted_at      TIMESTAMP NULL  -- soft delete wajib di semua tabel transaksi

-- Foreign key naming:
{nama_tabel_referensi}_id    -- contoh: guru_id, kelas_id, tahun_ajaran_id

-- Enum disimpan sebagai VARCHAR, bukan tipe ENUM MySQL
-- agar mudah migrasi nilai baru
status          VARCHAR(30) DEFAULT 'draft'
```

### Relasi Penting
```
master_siswa        → master_kelas (many-to-one)
master_kelas        → master_tahun_ajaran (many-to-one)
master_kelas        → master_guru (wali_kelas, many-to-one)
surat_masuks        → master_kategori_surat (many-to-one)
disposisis          → surat_masuks (many-to-one)
disposisis          → users (penerima, many-to-one)
absensi_gurus       → master_guru (many-to-one)
absensi_siswas      → master_siswa (many-to-one)
absensi_siswas      → master_kelas (many-to-one)
jurnal_gurus        → master_guru, master_kelas, master_mapel
asets               → master_jenis_aset (many-to-one)
rkas                → tahun_bos (many-to-one)
realisasi_bos       → rkas (many-to-one)
permohonan_kegiatan → master_jenis_kegiatan (many-to-one)
approval_kegiatan   → permohonan_kegiatan (many-to-one)
foto_galeris        → albums (many-to-one)
albums              → permohonan_kegiatan (nullable, many-to-one)
```

---

## 8. Panduan untuk AI Agent

### Sebelum Mengerjakan Task Apapun

1. **Baca konteks modul** — identifikasi modul mana yang terlibat (lihat Bagian 5)
2. **Cek role yang relevan** — siapa yang bisa akses fitur ini (lihat Bagian 4)
3. **Ikuti konvensi penamaan** — tanpa pengecualian (lihat Bagian 6)
4. **Terapkan aturan bisnis** — cek Bagian 5.x untuk modul yang dikerjakan
5. **Jangan buat file/tabel baru** tanpa menyebutkan dan menjelaskan ke developer terlebih dahulu

### Urutan Pengerjaan Fitur Baru

```
1. Migration (database/migrations/)
2. Model + relasi + casts (app/Models/)
3. Enum jika ada status/kategori (app/Enums/)
4. Form Request untuk validasi (app/Http/Requests/)
5. Policy untuk otorisasi (app/Policies/)
6. Service layer untuk business logic (app/Services/)
7. Livewire component (app/Livewire/ + resources/views/livewire/)
8. Route (routes/web.php)
9. Seeder jika dibutuhkan (database/seeders/)
```

### Perubahan Database

- **Wajib Update `docs/DB_SCHEMA.md`:** Jika ada perubahan pada struktur database (menambah tabel, menambah/mengubah kolom, mengubah relasi, dsb), Anda **wajib secara otomatis memperbarui file `docs/DB_SCHEMA.md`**. Hal ini untuk memastikan aplikasi selalu memiliki dokumentasi skema database yang lengkap dan *up-to-date* seiring pengembangan fitur.

### Validasi Wajib di Form Request

```php
// Contoh pola validasi yang konsisten
public function rules(): array
{
    return [
        'tanggal'    => ['required', 'date', 'before_or_equal:today'],
        'file_surat' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        'status'     => ['required', Rule::in(StatusSurat::values())],
        'guru_id'    => ['required', 'exists:master_gurus,id'],
    ];
}

public function messages(): array
{
    return [
        'tanggal.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
        'file_surat.max'          => 'Ukuran file maksimal 5MB.',
    ];
}
```

### Penanganan File Upload

```php
// Selalu gunakan pola ini untuk upload
$path = $file->store(
    "surat/{$tahun}/{$bulan}",  // path relatif dari storage/app/
    'local'                      // disk
);

// Untuk akses publik (foto galeri), gunakan disk 'public'
$path = $file->store('galeri/albums', 'public');

// Jangan pernah simpan full path, simpan path relatif dari disk
// Model menyimpan: "surat/2024/01/namafile.pdf"
// Untuk URL: Storage::url($model->file_path)
```

### Notifikasi WhatsApp

```php
// Gunakan NotifikasiService, jangan panggil Fonnte API langsung dari controller
app(NotifikasiService::class)->kirimKeGuru(
    guru: $guru,
    pesan: "Permohonan kegiatan '{$kegiatan->judul}' telah disetujui.",
    konteks: 'approval_kegiatan'
);

// Notifikasi selalu dikirmkan via Queue (job), bukan synchronous
// agar tidak memblokir response
```

### Generate PDF Laporan

```php
// Gunakan pola ini untuk semua PDF
$pdf = Pdf::loadView('pdf.surat-tugas', [
    'kegiatan'  => $kegiatan,
    'sekolah'   => config('sekolah'),
    'dicetak_at' => now(),
])
->setPaper('a4', 'portrait')
->setOption(['defaultFont' => 'sans-serif', 'dpi' => 150]);

return $pdf->download("surat-tugas-{$kegiatan->id}.pdf");
```

### Export Excel

```php
// Buat class Export di app/Exports/, pakai FromQuery + WithHeadings
class AbsensiGuruExport implements FromQuery, WithHeadings, WithStyles
{
    public function __construct(private string $bulan, private string $tahun) {}

    public function query(): Builder
    {
        return AbsensiGuru::query()
            ->with('guru')
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun);
    }

    public function headings(): array
    {
        return ['No', 'NIP', 'Nama Guru', 'Tanggal', 'Status', 'Keterangan'];
    }
}
```

---

## 9. Konfigurasi Aplikasi

### Config Sekolah (config/sekolah.php)

```php
return [
    'nama'        => env('SEKOLAH_NAMA', 'SMA Negeri 1 Contoh'),
    'npsn'        => env('SEKOLAH_NPSN', ''),
    'alamat'      => env('SEKOLAH_ALAMAT', ''),
    'kota'        => env('SEKOLAH_KOTA', ''),
    'provinsi'    => env('SEKOLAH_PROVINSI', ''),
    'telepon'     => env('SEKOLAH_TELEPON', ''),
    'email'       => env('SEKOLAH_EMAIL', ''),
    'logo_path'   => env('SEKOLAH_LOGO', 'logo/logo-sekolah.png'),
    'kepala'      => env('SEKOLAH_KEPALA_NAMA', ''),
    'nip_kepala'  => env('SEKOLAH_KEPALA_NIP', ''),
];
```

### Variabel .env yang Diperlukan

```env
# Aplikasi
APP_NAME="Sistem TU SMA"
APP_TIMEZONE=Asia/Makassar

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=situ_sma
DB_USERNAME=
DB_PASSWORD=

# Storage
FILESYSTEM_DISK=local

# Queue
QUEUE_CONNECTION=database

# Notifikasi WhatsApp (Fonnte)
FONNTE_TOKEN=
FONNTE_ENDPOINT=https://api.fonnte.com/send

# Mail
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="Sistem TU SMA"

# Sekolah
SEKOLAH_NAMA=
SEKOLAH_NPSN=
SEKOLAH_KEPALA_NAMA=
SEKOLAH_KEPALA_NIP=
```

---

## 10. Urutan Prioritas Pengembangan

### Fase 1 — Core (Wajib selesai lebih dulu)
```
[ ] Setup project, autentikasi, RBAC (Spatie Permission)
[ ] Data Master (Siswa, Guru, Kelas, Tahun Ajaran)
[ ] Modul Administrasi Surat (Masuk & Keluar + Disposisi)
[ ] Modul Absensi Guru
[ ] Modul Absensi Siswa + Jurnal Guru
[ ] Dashboard & laporan dasar
```

### Fase 2 — Operasional
```
[ ] Modul BOS (RKAS + Realisasi + Laporan SPJ)
[ ] Modul Sapras (Aset + QR Code + Inventaris)
[ ] Permohonan Kegiatan + Alur Approval
[ ] Notifikasi WhatsApp & Email
```

### Fase 3 — Enhancement
```
[ ] Modul Galeri Kegiatan
[ ] Halaman publik sekolah (galeri publik)
[ ] Export semua laporan (PDF & Excel)
[ ] Import data dari Excel (siswa, guru, aset)
[ ] Dashboard analytics (grafik absensi, BOS, aset)
[ ] Backup otomatis
```

---

## 11. Hal yang Tidak Boleh Dilakukan Agent

- ❌ Hapus atau modifikasi migration yang sudah di-commit
- ❌ Ubah struktur tabel tanpa membuat migration baru
- ❌ Hard delete record dari tabel yang menggunakan soft delete
- ❌ Simpan logika bisnis langsung di Controller atau Blade view
- ❌ Query database langsung dari Blade view
- ❌ Expose data sensitif (NIP, nilai BOS, data pribadi siswa) di response tanpa otorisasi
- ❌ Panggil API eksternal (Fonnte, dll) secara synchronous dari request cycle
- ❌ Gunakan `dd()`, `dump()`, atau `var_dump()` di production code
- ❌ Commit file `.env` atau credential apapun ke repository
- ❌ Gunakan `DB::statement('DROP ...')` atau DDL query di luar migration

---

## 12. Referensi Cepat

| Kebutuhan | Solusi |
|---|---|
| Generate nomor surat | `app(SuratService::class)->generateNomor(...)` |
| Cek permission | `$user->can('create', SuratMasuk::class)` |
| Upload file | `$file->store('folder', 'local')` |
| Generate PDF | `Pdf::loadView('pdf.template', $data)->download(...)` |
| Export Excel | `Excel::download(new ExportClass(), 'nama.xlsx')` |
| Generate QR Code | `QrCode::format('png')->size(200)->generate($kodeAset)` |
| Kirim notif WA | `app(NotifikasiService::class)->kirim(...)` via Queue |
| Rekap absensi | `app(AbsensiService::class)->rekap($bulan, $tahun)` |
| Validasi BOS | `app(BosService::class)->cekPlafonRkas($rkasId, $nilai)` |

## 13. Source Control
setelah selesai membuat satu modul, lakukan commit dengan pesan sebagai berikut:

```bash
feat: init
feat: auth, rbac, data master
feat: administrasi surat
feat: absensi guru dan siswa
feat: sapras (sarana & prasarana)
feat: bos (bantuan operasional sekolah)
feat: permohonan kegiatan
feat: galeri
feat: halaman publik sekolah
feat: export semua laporan (pdf & excel)
feat: import data dari excel (siswa, guru, aset)
feat: dashboard analytics (grafik absensi, bos, aset)
feat: backup otomatis
```

---

*Dokumen ini harus selalu diperbarui setiap ada keputusan arsitektur baru, perubahan tech stack, atau penambahan aturan bisnis. Update CLAUDE.md sebelum memulai sprint baru.*