<?php

namespace App\Listeners;

use App\Events\AbsensiGuruDicatat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class KirimNotifikasiKeterlambatan
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AbsensiGuruDicatat $event): void
    {
        $absen = $event->absen;
        
        if ($absen->status === \App\Enums\StatusAbsensiGuru::TERLAMBAT->value) {
            $adminTu = \App\Models\User::role('admin-tu')->first();
            if ($adminTu && $adminTu->no_hp) {
                $guru = $absen->guru;
                $jam = \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i');
                $pesan = "Info: Guru {$guru->nama} absen masuk pada jam {$jam} (Terlambat).";
                
                \App\Jobs\SendWhatsAppNotification::dispatch($adminTu->no_hp, $pesan);
            }
        }
    }
}
