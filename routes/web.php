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
    });

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
