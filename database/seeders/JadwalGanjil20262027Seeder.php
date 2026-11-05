<?php

namespace Database\Seeders;

use App\Services\JadwalService;
use Illuminate\Database\Seeder;

/**
 * Jadwal resmi Semester Ganjil 2026/2027 (struktur v2: pola jam + sesi + pengampu).
 *
 * Aman dijalankan di production, terpisah dari DatabaseSeeder:
 *   php artisan db:seed --class=JadwalGanjil20262027Seeder --force
 *
 * Idempoten — pola jam dibangun ulang, pengampu yang sudah diatur admin dipertahankan.
 */
class JadwalGanjil20262027Seeder extends Seeder
{
    public function run(JadwalService $service): void
    {
        $data = require database_path('seeders/data/jadwal_2026_2027_ganjil.php');

        $pola = $service->importDariArray($data);
        $acak = $service->isiPengampuAcak($pola->tahun_ajaran_id);

        $this->command?->info(sprintf(
            'Pola "%s": %d slot, %d sesi. Pengampu diisi acak: %d.',
            $pola->nama,
            $pola->slots()->count(),
            \App\Models\JadwalSesi::query()->untukPola($pola->id)->count(),
            $acak,
        ));
    }
}
