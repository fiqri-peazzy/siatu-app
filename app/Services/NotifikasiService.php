<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifikasiService
{
    /**
     * Kirim notifikasi WhatsApp via Fonnte Gateway.
     */
    public function kirimWhatsApp(string $nomor, string $pesan): bool
    {
        $token = config('services.fonnte.token');
        $endpoint = config('services.fonnte.endpoint', 'https://api.fonnte.com/send');

        if (!$token) {
            Log::warning('WhatsApp Notification skipped: FONNTE_TOKEN not set.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post($endpoint, [
                'target' => $nomor,
                'message' => $pesan,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp Notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi ke Guru.
     */
    public function kirimKeGuru(User $user, string $pesan, string $konteks = ''): void
    {
        // Logika pengiriman (bisa via Job/Queue)
        // placeholder for now
        Log::info("Notifikasi Guru ({$user->name}): {$pesan} [Konteks: {$konteks}]");
        
        if ($user->phone) {
            $this->kirimWhatsApp($user->phone, $pesan);
        }
    }
}
