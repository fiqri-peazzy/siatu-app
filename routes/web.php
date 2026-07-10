<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // Data Master Routes
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/tahun-ajaran', \App\Livewire\Master\TahunAjaran\ListTahunAjaran::class)->name('tahun-ajaran');
        Route::get('/guru', \App\Livewire\Master\Guru\ListGuru::class)->name('guru');
        Route::get('/kelas', \App\Livewire\Master\Kelas\ListKelas::class)->name('kelas');
        
        Route::get('/siswa', \App\Livewire\Master\Siswa\ListSiswa::class)->name('siswa');
        Route::get('/siswa/create', \App\Livewire\Master\Siswa\CreateSiswa::class)->name('siswa.create');
        Route::get('/siswa/{siswa}/edit', \App\Livewire\Master\Siswa\EditSiswa::class)->name('siswa.edit');
    });

    // Administrasi Routes
    Route::prefix('administrasi')->name('administrasi.')->group(function () {
        // Surat Masuk
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
    });

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
