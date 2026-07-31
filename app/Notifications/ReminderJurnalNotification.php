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
    protected $kanal;

    /**
     * Create a new notification instance.
     */
    public function __construct($pesan, $kanal)
    {
        $this->pesan = $pesan;
        $this->kanal = $kanal; // 'wa_saja' atau 'wa_dan_inapp'
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        // We handle WA manually outside of Laravel's built-in channels for simplicity,
        // but we can also trigger it from `toDatabase` or via a custom channel.
        // For 'wa_dan_inapp', we use database and WebPush
        
        if ($this->kanal === 'wa_dan_inapp') {
            $channels[] = 'database';
            $channels[] = WebPushChannel::class;
        }

        return $channels;
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
            'url' => '/guru/notifikasi'
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Reminder Jurnal Harian')
            ->icon('/images/logo-removebg-preview.png')
            ->body($this->pesan)
            ->data(['url' => '/guru/notifikasi']);
    }
}
