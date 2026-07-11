<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class NotifikasiService
{
    /**
     * Mengirim pesan WhatsApp.
     * @param string $nomorHp
     * @param string $pesan
     */
    public function sendWhatsApp(string $nomorHp, string $pesan): bool
    {
        // TODO: Implementasi CURL ke API Fonnte / Wablas di sini.
        // Karena API KEY belum tersedia, maka dicatat ke dalam log untuk sementara.
        
        Log::info('--- SIMULASI WHATSAPP TERKIRIM ---');
        Log::info('Ke: ' . $nomorHp);
        Log::info('Pesan: ' . $pesan);
        Log::info('----------------------------------');

        return true;
    }
}
