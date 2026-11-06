<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        if (auth()->user()->hasRole('guru')) {
            return redirect()->route('guru.dashboard');
        }
        $komponen = auth()->user()->hasRole('kepala-sekolah')
            ? \App\Livewire\Dashboard\DashboardKepsek::class
            : \App\Livewire\Dashboard\DashboardAdmin::class;

        return app()->call([app($komponen), '__invoke']);
    })->name('dashboard');

    // Data Master Routes
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/tahun-ajaran', \App\Livewire\Master\TahunAjaran\ListTahunAjaran::class)->name('tahun-ajaran');
        Route::get('/guru', \App\Livewire\Master\Guru\ListGuru::class)->name('guru');
        Route::get('/tendik', \App\Livewire\Master\Tendik\ListTendik::class)->name('tendik');
        Route::get('/mapel', \App\Livewire\Master\Mapel\ListMapel::class)->name('mapel');
        Route::get('/kelas', \App\Livewire\Master\Kelas\ListKelas::class)->name('kelas');
        Route::get('/jadwal', \App\Livewire\Master\Jadwal\ListJadwal::class)->name('jadwal.index');
        
        Route::get('/siswa', \App\Livewire\Master\Siswa\ListSiswa::class)->name('siswa');
        Route::get('/siswa/create', \App\Livewire\Master\Siswa\CreateSiswa::class)->name('siswa.create');
        Route::get('/siswa/pindah-rombel', \App\Livewire\Master\Siswa\PindahRombel::class)->name('siswa.pindah-rombel');
        Route::get('/siswa/{siswa}/edit', \App\Livewire\Master\Siswa\EditSiswa::class)->name('siswa.edit');
    });

    // Administrasi
    Route::prefix('administrasi')->name('administrasi.')->group(function () {
        // Surat Masuk
        // Dipindah ke Pengaturan Sistem; nama route dipertahankan untuk tautan lama
        Route::redirect('/pengaturan-lokasi', '/sistem/pengaturan?tab=lokasi')->name('pengaturan-lokasi');
        Route::get('/surat-masuk', \App\Livewire\Administrasi\SuratMasuk\ListSuratMasuk::class)->name('surat-masuk.index');
        Route::get('/surat-masuk/create', \App\Livewire\Administrasi\SuratMasuk\FormSuratMasuk::class)->name('surat-masuk.create');
        Route::get('/surat-masuk/{surat}/edit', \App\Livewire\Administrasi\SuratMasuk\FormSuratMasuk::class)->name('surat-masuk.edit');
        Route::get('/surat-masuk/{surat}', \App\Livewire\Administrasi\SuratMasuk\DetailSuratMasuk::class)->name('surat-masuk.show');

        // Surat Keluar
        Route::get('/surat-keluar', \App\Livewire\Administrasi\SuratKeluar\ListSuratKeluar::class)->name('surat-keluar.index');
        Route::get('/surat-keluar/create', \App\Livewire\Administrasi\SuratKeluar\FormSuratKeluar::class)->name('surat-keluar.create');
        Route::get('/surat-keluar/{surat}/edit', \App\Livewire\Administrasi\SuratKeluar\FormSuratKeluar::class)->name('surat-keluar.edit');
        Route::get('/surat-keluar/{surat}', \App\Livewire\Administrasi\SuratKeluar\DetailSuratKeluar::class)->name('surat-keluar.show');
        Route::get('/surat-keluar/{surat}/pdf', function (\App\Models\SuratKeluar $surat) {
            if (!in_array($surat->status->value, ['approved', 'terkirim'])) {
                abort(403, 'Dokumen resmi belum tersedia.');
            }
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.surat-keluar', ['surat' => $surat]);
            return $pdf->stream('Surat_Keluar_' . str_replace('/', '_', $surat->no_surat) . '.pdf');
        })->name('surat-keluar.pdf');

        Route::get('/surat-keluar/{surat}/word', function (\App\Models\SuratKeluar $surat) {
            if (!in_array($surat->status->value, ['approved', 'terkirim'])) {
                abort(403, 'Dokumen resmi belum tersedia.');
            }

            if (!\Illuminate\Support\Facades\Storage::exists('temp')) {
                \Illuminate\Support\Facades\Storage::makeDirectory('temp');
            }
            \PhpOffice\PhpWord\Settings::setTempDir(storage_path('app/temp'));

            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();

            // Header (Kop Surat)
            $header = $section->addHeader();
            $table = $header->addTable();
            $table->addRow();
            
            $logoPath = public_path('images/logo-removebg-preview.png');
            if (file_exists($logoPath)) {
                $table->addCell(2000)->addImage($logoPath, ['width' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            } else {
                $table->addCell(2000);
            }
            
            $cell = $table->addCell(8000);
            $cell->addText('SMA MUHAMMADIYAH GORONTALO', ['bold' => true, 'size' => 18, 'color' => '002060'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $cell->addText('ISLAMIC GLOBAL SCHOOL', ['bold' => true, 'size' => 12, 'color' => 'B8860B'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            
            // Simulating the contact info in one line
            $cell->addText('● Jln. Taman Bunga, Kota Timur, Kota Gorontalo, Gorontalo 96135  |  ✉ Email. smasmuhammadiyahkotagorontalo@gmail.com', ['size' => 9], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

            $section->addLine(['weight' => 4, 'width' => 600, 'height' => 0, 'color' => '002060']);
            $section->addLine(['weight' => 2, 'width' => 600, 'height' => 0, 'color' => 'B8860B']);
            $section->addTextBreak(1);

            // Meta Surat
            $metaTable = $section->addTable();
            $metaTable->addRow();
            $metaTable->addCell(1500)->addText('Nomor');
            $metaTable->addCell(200)->addText(':');
            $metaTable->addCell(4000)->addText($surat->no_surat);
            $metaTable->addCell(4000)->addText('Gorontalo, ' . $surat->tanggal->translatedFormat('d F Y'), [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
            
            $metaTable->addRow();
            $metaTable->addCell(1500)->addText('Lampiran');
            $metaTable->addCell(200)->addText(':');
            $metaTable->addCell(4000)->addText('-');
            
            $metaTable->addRow();
            $metaTable->addCell(1500)->addText('Hal');
            $metaTable->addCell(200)->addText(':');
            $metaTable->addCell(4000)->addText($surat->perihal);

            $section->addTextBreak(2);

            // Tujuan
            $section->addText('Kepada Yth.');
            $section->addText($surat->tujuan);
            $section->addText('di');
            $section->addText('       Tempat');

            $section->addTextBreak(1);

            // Isi
            // Render HTML to Word
            $html = str_replace(['<br>', '<br/>', '<br />'], '<br/>', $surat->isi_ringkas);
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

            $section->addTextBreak(2);

            // Tanda Tangan
            $ttdTable = $section->addTable(['alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::END]);
            $ttdTable->addRow();
            $ttdTable->addCell(4000)->addText('Mengetahui,', [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $ttdTable->addRow();
            $ttdTable->addCell(4000)->addText('Kepala SMAMGo', [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $ttdTable->addRow();
            $ttdTable->addCell(4000)->addTextBreak(3);
            $ttdTable->addRow();
            $ttdTable->addCell(4000)->addText($surat->disetujuiOleh->name ?? 'NAMA KEPALA SEKOLAH', ['bold' => true, 'underline' => 'single'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $ttdTable->addRow();
            $ttdTable->addCell(4000)->addText('NBM. 1478021', [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

            $fileName = 'Surat_Keluar_' . str_replace('/', '_', $surat->no_surat) . '.docx';
            
            // Save to Laravel's storage directory to avoid permissions issue in C:\Windows\Temp
            if (!\Illuminate\Support\Facades\Storage::exists('temp')) {
                \Illuminate\Support\Facades\Storage::makeDirectory('temp');
            }
            $tempFile = storage_path('app/temp/' . uniqid('word_') . '.docx');
            
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);
            
            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        })->name('surat-keluar.word');
    });

    // Akademik / Absensi
    Route::prefix('akademik')->name('akademik.')->group(function () {
        // Peminatan Admin
        Route::get('/peminatan', \App\Livewire\Akademik\Peminatan\AssignPeminatan::class)->name('peminatan.index');

        // Absensi Siswa (Rekap)
        Route::get('/absensi-siswa/rekap', \App\Livewire\Akademik\Absensi\RekapSiswa::class)->name('absensi-siswa.rekap');
        Route::get('/absensi-siswa/export', [\App\Http\Controllers\Akademik\ExportRekapSiswaController::class, 'export'])->name('absensi-siswa.export');

        // Guru
        Route::get('/absensi/absen-sekarang', \App\Livewire\Absensi\Guru\FormAbsensi::class)->name('absensi.guru.form');
        Route::get('/absensi/riwayat', \App\Livewire\Absensi\Guru\RiwayatAbsensi::class)->name('absensi.guru.riwayat');
        
        // Tendik (Staf/Admin)
        Route::get('/absensi/tendik/absen-sekarang', \App\Livewire\Absensi\Tendik\FormAbsensi::class)->name('absensi.tendik.form');
        Route::get('/absensi/tendik/riwayat', \App\Livewire\Absensi\Tendik\RiwayatAbsensi::class)->name('absensi.tendik.riwayat');
        
        // Monitoring (Web Dashboard)
        Route::get('/absensi/monitoring', \App\Livewire\Absensi\DashboardAbsensiHariIni::class)->name('absensi.monitoring');
        Route::get('/absensi/tendik/monitoring', \App\Livewire\Absensi\Tendik\DashboardAbsensiTendik::class)->name('absensi.tendik.monitoring');

        // Rekap kehadiran bulanan (Excel 2 sheet, kop resmi) — pimpinan & TU
        Route::get('/absensi/rekap-bulanan', \App\Http\Controllers\Akademik\ExportRekapKehadiranController::class)
            ->middleware('role:admin-tu|kepala-sekolah|super-admin')->name('absensi.rekap-bulanan');
    });

    // Supervisi
    Route::prefix('supervisi')->name('supervisi.')->group(function () {
        Route::get('/jurnal/realtime', \App\Livewire\Supervisi\DashboardSupervisiRealtime::class)->name('jurnal.realtime');
        Route::get('/jurnal/rekapan', \App\Livewire\Supervisi\RekapanJurnal::class)->name('jurnal.rekapan');
        Route::get('/jurnal/export', [\App\Http\Controllers\ExportJurnalController::class, 'cetakExcel'])->name('jurnal.export');
    });

    // Portal mobile tenaga kependidikan
    Route::get('/tendik', \App\Livewire\Mobile\Tendik\BerandaTendik::class)->name('tendik.beranda');

    // PWA Mobile Guru
    Route::prefix('guru')->name('guru.')->middleware(['role:guru'])->group(function () {
        Route::get('/dashboard', \App\Livewire\Mobile\Guru\DashboardGuru::class)->name('dashboard');
        Route::get('/jadwal', \App\Livewire\Mobile\Guru\JadwalGuru::class)->name('jadwal');
        // {sesi} = id jadwal_sesis (jadwal v2) dipisah koma, satu blok mengajar
        Route::get('/jurnal/isi/{sesi}', \App\Livewire\Mobile\Guru\FormJurnal::class)->name('jurnal.form');
        Route::get('/jurnal/riwayat', \App\Livewire\Mobile\Guru\RiwayatJurnal::class)->name('jurnal.riwayat');
        Route::get('/jurnal/cetak', [\App\Http\Controllers\ExportJurnalController::class, 'cetakPdfGuru'])->name('jurnal.cetak');
        Route::get('/profil', \App\Livewire\Mobile\Guru\ProfilGuru::class)->name('profil');
        Route::get('/pengaturan-notifikasi', \App\Livewire\Mobile\Guru\PengaturanNotifikasi::class)->name('pengaturan-notifikasi');
        Route::get('/notifikasi', \App\Livewire\Mobile\Guru\DaftarNotifikasi::class)->name('notifikasi');
    });

    // Pusat Laporan bulanan (pimpinan & TU)
    Route::prefix('laporan')->name('laporan.')->middleware(['role:admin-tu|kepala-sekolah|super-admin'])->group(function () {
        Route::get('/', \App\Livewire\Laporan\PusatLaporan::class)->name('index');
        Route::get('/unduh/{jenis}', \App\Http\Controllers\Laporan\UnduhLaporanController::class)->name('unduh');
    });

    // Sistem / Pengaturan (Hanya Super Admin & Admin TU)
    Route::prefix('sistem')->name('sistem.')->middleware(['role:super-admin|admin-tu'])->group(function () {
        Route::get('/pengguna', \App\Livewire\Sistem\User\ListUser::class)->name('user');
        Route::get('/pengaturan', \App\Livewire\Sistem\PengaturanSistem::class)->name('pengaturan');
    });

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    Route::post('/webpush/subscribe', [\App\Http\Controllers\WebPushController::class, 'subscribe']);
});
