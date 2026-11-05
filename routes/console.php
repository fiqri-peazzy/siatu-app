<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::call(function () {
    $hariIniStr = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
    $tanggalSekarang = \Carbon\Carbon::today()->format('Y-m-d');

    $gurus = \App\Models\Master\MasterGuru::with(['preferensiNotifikasi', 'jurnals' => function($q) use ($tanggalSekarang) {
        $q->where('tanggal', $tanggalSekarang)->where('status', 'diisi');
    }])->get();

    foreach ($gurus as $guru) {
        // Skip if reminder is disabled
        if ($guru->preferensiNotifikasi && !$guru->preferensiNotifikasi->reminder_jurnal_aktif) {
            continue;
        }

        $totalJadwal = \App\Models\Master\MasterJadwalPelajaran::where('guru_id', $guru->id)
            ->where('hari', $hariIniStr)
            ->count();

        $totalTerisi = $guru->jurnals->count();

        if ($totalJadwal > $totalTerisi) {
            $pesan = "Halo Bpk/Ibu {$guru->nama}, Anda memiliki {$totalJadwal} jadwal hari ini namun baru mengisi {$totalTerisi} jurnal. Mohon segera lengkapi jurnal Anda di aplikasi.";
            // WhatsApp (Fonnte) sengaja dinonaktifkan — reminder hanya via in-app + Web Push.
            if ($guru->user) {
                \Illuminate\Support\Facades\Log::info("Reminder jurnal dikirim ke {$guru->nama} (in-app + web push).");
                $guru->user->notify(new \App\Notifications\ReminderJurnalNotification($pesan));
            }
        }
    }
})->dailyAt('14:00');
