# design.md — Spesifikasi Desain PWA Guru Mobile (SITU-SMA)

**Proyek:** Sistem Informasi Tata Usaha SMA (SITU-SMA)
**Cakupan:** Layout & UI seluruh screen PWA yang diakses Guru dari HP — Absensi (Face Recognition + Selfie), Jadwal Mengajar, Input Jurnal, Riwayat, Notifikasi, Profil, serta Dashboard Supervisi (Kepsek/Wakakur) di lebar tablet
**Referensi visual:** Struktur layout terinspirasi dari referensi *AbsenEase* (header status, tombol absen besar, grid menu, kartu notifikasi) — **palet warna diganti total**, tidak memakai pink
**Terakhir Diperbarui:** 15 Juli 2026

---

## 0. Prinsip Desain (Anti-Slop)

Sebelum masuk ke token dan screen, ini batasan sadar supaya hasil akhir tidak terasa seperti "template dashboard absensi generik nomor 4.281" yang bertebaran di Play Store:

- **Jangan** pakai kombinasi ungu/pink-ke-magenta gradient seperti kebanyakan app HR/absensi generik (termasuk referensi AbsenEase) — kita ganti total ke keluarga navy/biru tua yang terasa institusional, tenang, dan cocok untuk konteks sekolah, bukan startup fintech.
- **Jangan** pakai kartu putih-melayang-dengan-shadow-lembut di semua tempat tanpa alasan — dorong satu elemen signature yang benar-benar dipakai konsisten (lihat Bagian 2.4), sisanya dibuat tenang/disiplin.
- **Jangan** pakai ikon generik "flat line icon pack" tanpa kepribadian untuk elemen kunci (khususnya di jam pelajaran & status jurnal) — gunakan pendekatan strip garis waktu yang dijelaskan di Bagian 2.4, bukan sekadar ikon jam biasa.
- Angka & data waktu (jam absen, NIP, jam pelajaran) ditulis dengan tipografi monospace agar terasa seperti "catatan tercatat/log resmi" — bukan sekadar teks biasa. Ini pilihan yang disengaja, bukan dekorasi.
- State kosong dan error ditulis dengan suara sistem yang jelas dan actionable (lihat Bagian 6), bukan pesan generik "Data tidak ditemukan."

---

## 1. Ground Truth Subjek

Aplikasi ini dipakai oleh **guru SMA Muhammadiyah Gorontalo**, kemungkinan besar di sela waktu mengajar — dibuka cepat, dipakai < 30 detik per sesi, lalu ditutup. Prioritas desain: **kecepatan baca status** (sudah/belum) dan **kecepatan aksi** (1–2 tap ke tujuan), bukan eksplorasi visual yang berlama-lama. Nada visual: **tepercaya, formal-tapi-hangat**, mencerminkan institusi pendidikan — bukan nada "gamified" seperti app HR startup.

---

## 2. Design Tokens

### 2.1 Palet Warna — Keluarga Navy

| Token | Hex | Penggunaan |
|---|---|---|
| `navy-ink` | `#0B1730` | Header/app bar, bottom navigation aktif, teks tergelap di atas permukaan terang |
| `navy-deep` | `#122A52` | Kartu status utama (mis. kartu absen di dashboard), permukaan gelap sekunder |
| `navy-mid` | `#1E3A6B` | Elemen interaktif sekunder, border aktif, ikon terpilih |
| `signal-blue` | `#3D6BFF` | Warna aksi utama — tombol primer, link, indikator "sedang berlangsung" |
| `sky-mist` | `#DCE7FB` | Latar badge/chip ringan, latar highlight lembut |
| `paper` | `#F5F7FC` | Latar belakang aplikasi (bukan putih polos — sedikit dingin ke arah biru) |
| `ink-900` | `#0F172A` | Teks utama |
| `slate-500` | `#5B6B85` | Teks sekunder, caption, placeholder |
| `line-200` | `#E1E7F2` | Border tipis, divider |
| `success-teal` | `#12866F` | Status "hadir", "sudah diisi", konfirmasi berhasil |
| `warning-amber` | `#C98A1B` | Status "belum diisi", "perlu perhatian", terlambat |
| `danger-coral` | `#C6493B` | Status "alfa", gagal validasi, error |

Catatan: warna fungsional (success/warning/danger) sengaja **diredupkan** (bukan hijau/kuning/merah terang) supaya tetap masuk ke keluarga palet navy yang tenang, bukan terasa seperti alert sistem generik.

### 2.2 Tipografi

| Peran | Font | Penggunaan |
|---|---|---|
| Display | **Sora** (600/700) | Judul halaman, sapaan dashboard ("Selamat Pagi, Bu Sarah"), angka besar status |
| Body | **Plus Jakarta Sans** (400/500) | Isi teks, label form, deskripsi |
| Utility/Data | **JetBrains Mono** (400/500) | Jam (`07:42`), NIP, nomor agenda, timestamp absen, kode kelas — memberi kesan "tercatat resmi", bukan estetika semata |

Skala tipe (mobile): Display XL 28px/34 · Display L 22px/28 · Title 18px/24 · Body 15px/22 · Caption 13px/18 · Mono Data 15px/20 (tabular-nums).

### 2.3 Radius, Spasi, Elevasi

- Radius: kartu utama `20px`, tombol `14px`, chip/badge `999px` (pill), input field `12px` — konsisten membulat sedang, bukan tajam 0px ala broadsheet, bukan bulat penuh ala bubble chat.
- Spasi dasar 4px grid: 4/8/12/16/24/32/48.
- Elevasi dibatasi 2 tingkat saja: `shadow-flat` (nyaris tanpa bayangan, untuk kartu list) dan `shadow-raised` (bayangan lembut navy-tinted `rgba(11,23,48,0.12)`, hanya untuk kartu status utama & tombol primer mengambang). Jangan pakai shadow di semua kartu — disiplinkan.

### 2.4 Elemen Signature: "Rel Jam Pelajaran"

Satu elemen visual yang dipakai berulang dan jadi identitas aplikasi ini: **rel vertikal tipis berwarna navy** di sisi kiri daftar jam mengajar/jurnal, dengan titik-titik penanda tiap jam pelajaran —

- Titik terisi penuh `signal-blue` = jurnal sudah diisi
- Titik outline `warning-amber` = jurnal belum diisi
- Titik berdenyut (pulse animation halus, sekali per beberapa detik, bukan looping cepat) = jam pelajaran yang **sedang berlangsung saat ini**
- Garis rel menghubungkan titik-titik ini secara vertikal, menyerupai garis waktu di buku agenda guru fisik — bukan kartu terpisah-pisah tanpa hubungan visual

Elemen ini dipakai konsisten di: Dashboard, Jadwal Mengajar, Riwayat Jurnal, dan Dashboard Supervisi (versi tablet). Ini pengganti "ikon jam generik" yang dipakai kebanyakan app sejenis.

---

## 3. Prinsip Responsif & PWA

- **Mobile-first**, breakpoint utama: `<768px` (HP, layout 1 kolom, bottom navigation), `768–1023px` (tablet portrait, 2 kolom untuk list+detail), `≥1024px` (tablet landscape/desktop kecil, bottom navigation berubah jadi **side rail** kiri, dashboard supervisi memakai grid multi-kolom).
- Bottom navigation di HP: `Beranda · Jadwal · Jurnal · Riwayat · Profil` — ikon + label, tinggi 64px + safe-area-inset-bottom untuk device dengan gesture bar.
- Semua target tap minimal `44×44px`.
- `theme-color` meta & splash screen PWA memakai `navy-ink` (`#0B1730`), ikon app memakai lockup navy dengan aksen `signal-blue`.
- Kamera (face capture) dan geolocation diakses lewat permission prompt native browser — desain state "izin ditolak" wajib ada (lihat 5.4).
- Skeleton loading (bentuk kartu abu-abu berdenyut halus) dipakai saat data jadwal/jurnal dimuat — bukan spinner polos di tengah layar kosong.
- Karena PWA tidak selalu punya push notification andal (khususnya iOS), badge notifikasi in-app di ikon "Jurnal" pada bottom nav adalah mekanisme utama, WA sebagai pelengkap.

---

## 4. Komponen Global

| Komponen | Deskripsi Singkat |
|---|---|
| **App Bar** | Latar `navy-ink`, judul halaman kiri, ikon notifikasi kanan (badge merah kecil jika ada unread) |
| **Kartu Status Utama** | Latar `navy-deep` dengan teks putih, dipakai di Dashboard untuk ringkasan hari ini — satu-satunya kartu gelap di antara kartu-kartu terang lain, jadi fokus utama halaman |
| **Chip Status** | Pill kecil: `sudah diisi` (latar `sky-mist`, teks `signal-blue`), `belum diisi` (latar amber muda, teks `warning-amber`), `sedang berlangsung` (latar `signal-blue`, teks putih, dengan titik berdenyut) |
| **Tombol Primer** | Latar `signal-blue`, teks putih, radius 14px, penuh lebar di mobile untuk aksi utama per halaman |
| **Tombol Sekunder** | Outline `navy-mid`, teks `navy-ink`, latar transparan |
| **Input Field** | Border `line-200`, radius 12px, label di atas field (bukan placeholder-only, supaya konteks tidak hilang saat field terisi) |

---

## 5. Spesifikasi Screen

Setiap screen berisi: tujuan, deskripsi layout, elemen kunci, edge case, dan **prompt desain anti-slop** yang bisa langsung dipakai sebagai instruksi ke tim/tool desain.

### 5.1 Login

**Tujuan:** Masuk memakai akun yang sudah diaktivasi (lihat PRD Data Master Guru — link "Set Password").

**Layout (mobile):**
```
┌─────────────────────────┐
│  [Logo Sekolah]          │
│  SITU-SMA                │
│                          │
│  "Satu aplikasi,         │
│   seluruh administrasi   │
│   mengajar Anda."        │
│                          │
│  [ Email/NIP        ]   │
│  [ Password          ]   │
│  Lupa password?          │
│                          │
│  [   Masuk (primer)  ]   │
└─────────────────────────┘
```

**Elemen kunci:** logo sekolah asli (bukan ilustrasi stok tangan generik seperti referensi), latar `paper`, aksen garis navy tipis di bawah logo sebagai signature kecil.

**Prompt desain:** *Halaman login institusional, bukan promosional — hilangkan foto hero/ilustrasi besar ala consumer app. Fokus ke logo sekolah, satu kalimat pengantar singkat, dan form. Latar `paper` polos dengan satu garis dekoratif tipis warna `signal-blue` di bawah logo sebagai satu-satunya aksen bentuk.*

---

### 5.2 Dashboard / Beranda

**Tujuan:** Ringkasan hari ini dalam < 3 detik baca: status absen, jam mengajar hari ini beserta status jurnal.

**Layout (mobile):**
```
┌─────────────────────────┐
│ App Bar: "Beranda"   🔔  │
├─────────────────────────┤
│ ┌─ Kartu Status Utama ─┐ │
│ │ Selamat Pagi, Bu Sri │ │
│ │ Jumat, 17 Jul 2026    │ │
│ │ Absen masuk: 06:52 ✓  │ │  (mono font utk jam)
│ │ [ Absen Pulang ]      │ │
│ └───────────────────────┘ │
│                          │
│ Jadwal Mengajar Hari Ini │
│ │ ● 07:00 X IPA 1 Fisika │ │  ← rel jam pelajaran
│ │ ● 08:30 XI IPA 2 ✓diisi│ │
│ │ ○ 10:00 XII IPA 1 belum│ │
│ │                        │ │
├─────────────────────────┤
│ [🏠][📅][📝][🕐][👤]    │ ← bottom nav
└─────────────────────────┘
```

**Elemen kunci:** Kartu Status Utama satu-satunya elemen gelap di halaman (fokus visual utama). Rel jam pelajaran di bawahnya memakai elemen signature (2.4). Tap satu baris jadwal langsung membuka Form Jurnal (5.6) dengan data terisi otomatis.

**Edge case:** Belum absen masuk → tombol di kartu status berubah jadi "Absen Masuk" dengan warna `signal-blue` menyala, bukan abu-abu pasif — dorong aksi. Semua jurnal hari itu sudah terisi → rel jam pelajaran menampilkan pesan kecil di bawah rel: "Semua jurnal hari ini sudah lengkap, bagus!" (nada suara sistem yang membangun, bukan generik).

**Prompt desain:** *Satu kartu gelap `navy-deep` yang menonjol sebagai satu-satunya elemen "berat" di halaman terang; sisanya datar dan tenang. Jadwal mengajar memakai rel vertikal navy dengan titik-titik status, bukan kartu kotak terpisah-pisah untuk tiap jam. Jam dan angka pakai font monospace tabular.*

---

### 5.3 Absen — Face Recognition + Selfie (Multi-step)

**Tujuan:** Merekam bukti kehadiran guru dengan validasi visual (foto wajah) dan lokasi (GPS, divalidasi server — lihat PRD Absensi & Jurnal). Catatan penting: fase ini adalah **capture + liveness check sederhana** (bukan face-matching AI penuh — sesuai batas cakupan di PRD Absensi Guru: verifikasi lanjutan dilakukan manual oleh Admin TU jika perlu). Desain harus tetap terasa "diverifikasi", bukan asal jepret.

**Step 1 — Persiapan & Izin**
```
┌─────────────────────────┐
│  ← Absen Masuk           │
│                          │
│  📍 Memeriksa lokasi...  │
│  Jarak dari sekolah: 42m │  (mono)
│  ✓ Dalam radius sekolah  │
│                          │
│  [  Lanjut ke Kamera  ]  │
└─────────────────────────┘
```

**Step 2 — Bingkai Wajah (Face Capture)**
```
┌─────────────────────────┐
│  ← Ambil Foto Absen      │
│                          │
│   ╭─────────────╮        │
│   │   (oval      │        │
│   │    panduan   │        │  ← overlay oval navy translucent
│   │    wajah)    │        │
│   ╰─────────────╯        │
│                          │
│  Posisikan wajah di      │
│  dalam bingkai            │
│                          │
│      ( ● ambil foto )     │
└─────────────────────────┘
```

**Step 3 — Konfirmasi**
```
┌─────────────────────────┐
│  Tinjau Foto              │
│  [preview foto guru]     │
│                          │
│  [Ambil Ulang] [Gunakan] │
└─────────────────────────┘
```

**Elemen kunci:**
- Overlay oval panduan wajah warna `signal-blue` translucent (bukan kotak kamera generik) — memberi kesan proses verifikasi yang jelas
- Chip lokasi (`📍 42m dari sekolah`) tetap tampil kecil di pojok atas selama proses capture sebagai reassurance
- Indikator progres 3 titik kecil di atas (step 1/2/3), memakai gaya rel yang sama seperti signature element, bukan progress bar generik

**Edge case:**
- Di luar radius sekolah → Step 1 tidak lanjut, tampilkan pesan tegas: "Anda berada 850m dari sekolah. Absen hanya bisa dilakukan di area sekolah." + tombol "Coba lagi" — tanpa opsi memaksa lanjut.
- Izin kamera/lokasi ditolak browser → layar penuh dengan ilustrasi sederhana (bukan foto stok) + instruksi konkret cara mengaktifkan izin di pengaturan browser, bukan pesan error generik.

**Prompt desain:** *Overlay oval panduan wajah dengan warna aksen `signal-blue` translucent di atas live camera feed — bukan kotak/frame kotak generik. Chip info lokasi kecil dan tenang di pojok, bukan banner besar. Step indicator memakai gaya titik-rel yang sama dengan elemen signature aplikasi, menyatukan bahasa visual absen dengan jadwal.*

---

### 5.4 Absen Berhasil (Konfirmasi)

**Layout:**
```
┌─────────────────────────┐
│        ✓ (lingkaran      │  warna success-teal,
│      navy dgn centang)   │  bukan pink/hijau terang
│                          │
│   Absen Berhasil          │
│   Tercatat pukul 06:52    │  (mono)
│                          │
│ ┌─ Detail ──────────────┐│
│ │ Jumat, 17 Jul 2026     ││
│ │ 06:52 WITA · 42m       ││
│ │ dari sekolah           ││
│ └────────────────────────┘│
│                          │
│  [    Kembali ke Beranda  ]│
└─────────────────────────┘
```

**Prompt desain:** *Ikon konfirmasi lingkaran solid `navy-deep` dengan centang `success-teal`, bukan lingkaran pink besar seperti referensi. Detail absen ditampilkan sebagai kartu ringkas dengan tipografi mono untuk jam & jarak — kesan "struk/bukti tercatat", bukan pop-up perayaan.*

---

### 5.5 Jadwal Mengajar

**Tujuan:** Lihat jadwal mengajar per hari/minggu, akses cepat ke input jurnal.

**Layout (mobile — tab Harian/Mingguan di atas):**
```
┌─────────────────────────┐
│  Jadwal Mengajar          │
│  [Harian] [Mingguan]     │
│                          │
│  Senin, 20 Jul 2026   ›  │  (swipe hari)
│                          │
│  ● 07:00–07:45           │
│    X IPA 1 · Fisika       │
│    ✓ Jurnal sudah diisi   │
│                          │
│  ○ 08:30–09:15           │
│    XI IPA 2 · Fisika      │
│    Belum diisi  [Isi >]  │
└─────────────────────────┘
```

**Layout (tablet ≥768px — 2 kolom: kalender ringkas kiri, daftar jam kanan):**
```
┌───────────┬──────────────────────┐
│  Kalender  │  Jadwal — Senin 20/7  │
│  bulan ini │  ● 07:00 X IPA 1  ✓   │
│  (grid)    │  ○ 08:30 XI IPA 2      │
│            │  ○ 10:00 XII IPA 1     │
└───────────┴──────────────────────┘
```

**Prompt desain:** *Rel jam pelajaran vertikal jadi elemen dominan halaman ini — bukan tabel jadwal generik dengan garis grid. Di tablet, kalender bulan ringkas ditempatkan sebagai kolom kiri yang tenang (warna netral, tanpa dekorasi berlebih), rel jadwal tetap jadi fokus di kanan.*

---

### 5.6 Input Jurnal Mengajar (Form)

**Tujuan:** Input cepat, minim gesekan, terasa seperti mengisi buku agenda — bukan form birokratis panjang.

**Layout:**
```
┌─────────────────────────┐
│  ← Isi Jurnal              │
│  X IPA 1 · Fisika · 07:00  │  (chip info, terisi otomatis)
│                          │
│  Materi Pembelajaran       │
│  [___________________]    │
│                          │
│  Kegiatan Pembelajaran     │
│  [___________________]    │
│  [___________________]    │
│                          │
│  Dokumentasi (opsional)    │
│  [+ Tambah Foto]           │
│                          │
│  [   Simpan Jurnal    ]   │
└─────────────────────────┘
```

**Elemen kunci:** Chip info kelas/mapel/jam di bagian atas (mono font untuk jam) — dikunci, tidak bisa diedit, supaya guru tahu jurnal ini untuk sesi yang benar. Field materi & kegiatan memakai textarea auto-grow, bukan field kaku dengan tinggi tetap.

**Edge case:** Guru berpindah halaman sebelum submit → draft tersimpan sementara di state lokal (bukan ke database, sesuai aturan bisnis PRD), saat kembali form terisi ulang otomatis dengan catatan kecil "Draf tersimpan sementara."

**Prompt desain:** *Form terasa ringan dan cepat diisi — bukan wizard multi-halaman. Chip konteks kelas/jam di atas berwarna `sky-mist` dengan teks mono, berfungsi sebagai "label arsip" yang menegaskan data ini terhubung ke jam pelajaran spesifik. Tombol simpan penuh lebar, warna `signal-blue`, selalu terlihat tanpa perlu scroll jauh (sticky di bagian bawah pada layar kecil).*

---

### 5.7 Riwayat & Cetak Jurnal

**Layout (mobile):**
```
┌─────────────────────────┐
│  Riwayat Jurnal            │
│  [Filter: Bulan ▾][Kelas▾]│
│                          │
│  ● Senin, 20 Jul          │
│    X IPA 1 · Fisika        │
│    "Hukum Newton II..."   │
│                          │
│  ● Jumat, 17 Jul           │
│    XI IPA 2 · Fisika       │
│                          │
│  [  Unduh PDF Bulan Ini  ] │
└─────────────────────────┘
```

**Elemen kunci:** Tiap entri riwayat memakai potongan rel yang sama seperti Dashboard/Jadwal, bukan kartu List generik terpisah — menjaga konsistensi bahasa visual di seluruh app. Tombol unduh PDF di bagian bawah, ikon dokumen kecil dengan sudut terlipat (detail kecil yang memberi kesan "surat/berkas resmi").

**Prompt desain:** *Riwayat memakai potongan rel signature yang sama dari Dashboard — jangan reset ke gaya list generik hanya karena ini halaman berbeda. Tombol unduh PDF ditempatkan tenang di bagian bawah, tidak bersaing secara visual dengan daftar riwayat di atasnya.*

---

### 5.8 Notifikasi

**Layout:**
```
┌─────────────────────────┐
│  Notifikasi                │
│                          │
│  ⏰ Reminder Jurnal          │
│  2 jam pelajaran belum     │
│  diisi hari ini            │
│  17 Jul · 14:00            │  (mono)
│                          │
│  ✓ Surat Kegiatan Disetujui│
│  15 Jul · 09:12             │
└─────────────────────────┘
```

**Prompt desain:** *Tiap notifikasi punya ikon kecil berwarna sesuai jenis (amber untuk reminder, teal untuk konfirmasi/approval) di lingkaran latar `sky-mist` — bukan ikon lonceng generik berulang untuk semua jenis notifikasi. Timestamp memakai font mono, rata kanan, tenang.*

---

### 5.9 Profil & Pengaturan

**Layout:**
```
┌─────────────────────────┐
│  [foto]  Bu Sri Wahyuni    │
│  NIP 19850312...  (mono)   │
│                          │
│  Kepatuhan Jurnal Bulan Ini│
│  ▓▓▓▓▓▓▓▓░░  82%           │  (bar progress navy)
│                          │
│  Data Pribadi        ›     │
│  Notifikasi           ›    │
│  Ganti Password       ›    │
│  Keluar                    │
└─────────────────────────┘
```

**Prompt desain:** *Bar kepatuhan jurnal memakai warna `signal-blue` solid tanpa gradient pelangi — angka persentase besar di sisi kanan pakai font mono. Daftar menu memakai divider tipis `line-200`, bukan kartu terpisah-pisah dengan shadow untuk tiap item menu.*

---

### 5.10 Dashboard Supervisi Realtime (Kepsek/Wakakur — Tablet-First)

**Tujuan:** Pantau KBM yang sedang berjalan sekaligus, di layar lebih lebar.

**Layout (tablet ≥1024px, grid multi-kolom):**
```
┌──────────────────────────────────────────────┐
│ Supervisi KBM — Sedang Berlangsung   Jumat 17/7 │
├───────────────┬───────────────┬───────────────┤
│ X IPA 1        │ XI IPA 2       │ XII IPA 1      │
│ Fisika · Bu Sri │ Kimia · Pak Andi│ Biologi · Bu Rin│
│ ● sedang        │ ● sedang        │ ⚠ belum diisi  │
│   berlangsung   │   berlangsung   │   (28 menit)   │
│ ✓ jurnal diisi  │ ○ belum diisi   │                │
└───────────────┴───────────────┴───────────────┘
│ Kepatuhan Jurnal per Guru (minggu ini)          │
│ [tabel ringkas / bar chart horizontal navy]      │
└──────────────────────────────────────────────┘
```

**Elemen kunci:** Kartu kelas yang "perlu perhatian" (jurnal belum diisi melewati ambang waktu) mendapat border `warning-amber` yang jelas namun tidak norak (tidak berkedip/animasi mencolok — cukup warna solid + ikon peringatan kecil). Titik berdenyut halus (dari elemen signature) menandakan kelas yang aktif sesi-nya saat ini.

**Prompt desain:** *Grid kartu kelas tenang dan seragam, dengan satu-satunya sinyal mencolok adalah border amber pada kartu yang perlu perhatian — hindari dashboard yang penuh warna-warni status di semua kartu sekaligus, supaya mata langsung tertarik ke yang benar-benar butuh tindakan. Ringkasan kepatuhan di bawah pakai bar horizontal monokrom navy, bukan pie chart warna-warni.*

---

## 6. Suara Sistem (Microcopy)

- Aksi selalu pakai kata kerja aktif & spesifik: "Simpan Jurnal" bukan "Submit", "Absen Masuk" bukan "Check In".
- Error lokasi/kamera: jelaskan apa yang terjadi + langkah konkret. Contoh: *"Lokasi tidak terdeteksi. Aktifkan GPS di pengaturan HP Anda, lalu coba lagi."* — bukan *"Terjadi kesalahan."*
- Empty state jadwal (mis. hari libur): *"Tidak ada jadwal mengajar hari ini. Selamat beristirahat!"* — nada hangat tapi tidak berlebihan, sesuai konteks institusi pendidikan.

---

## 7. Catatan untuk Tim Pengembang (Antigravity)

- Elemen "Rel Jam Pelajaran" sebaiknya dibangun sebagai satu komponen reusable (`<JadwalRail>`), dipakai ulang di Dashboard, Jadwal Mengajar, Riwayat Jurnal, dan Dashboard Supervisi — bukan diimplementasi ulang tiap halaman.
- Palet warna disarankan didefinisikan sebagai CSS variables/Tailwind theme extension di satu tempat (`tailwind.config` atau `resources/css/tokens.css`), mengikuti nama token di Bagian 2.1, supaya konsisten dan mudah diubah di kemudian hari.
- Font Sora, Plus Jakarta Sans, dan JetBrains Mono tersedia via Google Fonts — pastikan hanya memuat weight yang benar-benar dipakai (600/700 Sora, 400/500 Plus Jakarta Sans, 400/500 JetBrains Mono) untuk menjaga performa loading di PWA.
- Validasi face capture & geolocation tetap mengikuti aturan bisnis di PRD Absensi Guru — desain ini hanya mendefinisikan UX capture & feedback visual, bukan menambah cakupan backend (face-matching AI otomatis tetap di luar cakupan fase ini).

---

*Dokumen ini adalah bagian dari rangkaian dokumen SITU-SMA. Terkait langsung dengan PRD Modul Absensi & Jurnal Mengajar, dan PRD Modul Guru Mobile (Jurnal Mengajar & Fitur Pendukung).*
