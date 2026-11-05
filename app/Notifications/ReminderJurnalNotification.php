<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class ReminderJurnalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $pesan;

    public function __construct($pesan)
    {
        $this->pesan = $pesan;
    }

    /**
     * In-app (database) selalu; Web Push terkirim ke semua perangkat yang
     * sudah mengizinkan notifikasi. WhatsApp dinonaktifkan.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Reminder Jurnal Harian',
            'body' => $this->pesan,
            'icon' => 'bell',
            'url' => '/guru/jadwal'
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Reminder Jurnal Harian')
            ->icon('/images/logo-removebg-preview.png')
            ->body($this->pesan)
            ->data(['url' => '/guru/jadwal']);
    }
}
