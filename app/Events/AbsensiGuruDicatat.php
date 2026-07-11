<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbsensiGuruDicatat
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public \App\Models\AbsensiGuru $absen;

    /**
     * Create a new event instance.
     */
    public function __construct(\App\Models\AbsensiGuru $absen)
    {
        $this->absen = $absen;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
