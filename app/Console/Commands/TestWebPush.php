<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ReminderJurnalNotification;
use Illuminate\Console\Command;

class TestWebPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:webpush {email? : Email user yang akan dikirimi notifikasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test pengiriman Web Push Notification ke user tertentu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            $user = User::where('email', $email)->first();
        } else {
            // Ambil user pertama yang memiliki push subscription
            $user = User::whereHas('pushSubscriptions')->first();
            
            if (!$user) {
                // Fallback ke user pertama jika tidak ada yang subscribe
                $user = User::first();
            }
        }

        if (!$user) {
            $this->error('User tidak ditemukan!');
            return;
        }

        $this->info("Mengirim notifikasi ke user: {$user->name} ({$user->email})");

        // Cek apakah user sudah punya subscription
        if ($user->pushSubscriptions()->count() === 0) {
            $this->warn("User {$user->name} belum mengaktifkan izin (subscribe) Web Push dari browsernya.");
            $this->warn("Silakan login dengan user ini di PWA, lalu izinkan notifikasi dari browser terlebih dahulu.");
        }

        try {
            $pesanTest = "Ini adalah pesan percobaan Push Notification dari server (Realtime Test!).";
            $kanal = 'wa_dan_inapp';
            
            $user->notify(new ReminderJurnalNotification($pesanTest, $kanal));
            
            $this->info('Notifikasi berhasil dikirim (di-queue/diproses)! Cek layar HP/Browser Anda.');
        } catch (\Exception $e) {
            $this->error('Gagal mengirim: ' . $e->getMessage());
        }
    }
}
