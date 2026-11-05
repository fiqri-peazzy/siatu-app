<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pengingat jurnal harian (jadwal v2): hanya pertemuan yang sudah lewat/berlangsung,
// jam yang tergeser upacara/kegiatan tidak ditagih. Hari libur dilewati.
\Illuminate\Support\Facades\Schedule::call(function () {
    $hariIni = \Carbon\Carbon::today();
    if (\App\Services\HolidayService::isHoliday($hariIni)) {
        return;
    }

    $jadwal = app(\App\Services\JadwalService::class);
    $gurus = \App\Models\Master\MasterGuru::with(['preferensiNotifikasi', 'user'])->get();

    foreach ($gurus as $guru) {
        if ($guru->preferensiNotifikasi && ! $guru->preferensiNotifikasi->reminder_jurnal_aktif) {
            continue;
        }

        $harian = $jadwal->jadwalGuruHarian($guru->id, $hariIni);
        $belum = $harian['belum_jurnal'];

        if ($belum > 0 && $guru->user) {
            $pesan = "Bapak/Ibu {$guru->nama}, masih ada {$belum} jurnal mengajar hari ini yang belum diisi. Ketuk untuk melengkapinya.";
            \Illuminate\Support\Facades\Log::info("Reminder jurnal: {$guru->nama} ({$belum} belum diisi).");
            $guru->user->notify(new \App\Notifications\ReminderJurnalNotification($pesan));
        }
    }
})->name('reminder-jurnal-harian')->dailyAt('14:00');
