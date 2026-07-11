<?php

namespace App\Listeners;

use App\Events\AbsensiGuruDicatat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CatatAuditTrail
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
        $ip = request()->ip();
        $userAgent = request()->userAgent();
        
        $absen->update([
            'device_info' => "IP: {$ip} | Browser: {$userAgent}"
        ]);
    }
}
