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
        $token = env('FONNTE_TOKEN');
        $endpoint = env('FONNTE_ENDPOINT', 'https://api.fonnte.com/send');

        if (empty($token) || empty($nomorHp)) {
            Log::info('--- SIMULASI WHATSAPP TERKIRIM ---');
            Log::info('Ke: ' . $nomorHp);
            Log::info('Pesan: ' . $pesan);
            Log::info('----------------------------------');
            return true; // Simulate success
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $token,
            ])->post($endpoint, [
                'target' => $nomorHp,
                'message' => $pesan,
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Fonnte Error: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Fonnte Exception: ' . $e->getMessage());
            return false;
        }
    }
}
