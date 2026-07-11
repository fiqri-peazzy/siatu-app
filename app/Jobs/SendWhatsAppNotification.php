<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\NotifikasiService;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    public string $nomorHp;
    public string $pesan;

    /**
     * Create a new job instance.
     */
    public function __construct(string $nomorHp, string $pesan)
    {
        $this->nomorHp = $nomorHp;
        $this->pesan = $pesan;
    }

    /**
     * Execute the job.
     */
    public function handle(NotifikasiService $service): void
    {
        $service->sendWhatsApp($this->nomorHp, $this->pesan);
    }
}
