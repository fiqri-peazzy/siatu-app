<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

/**
 * Pengaturan sistem default. Idempoten: hanya mengisi key yang belum ada,
 * nilai yang sudah diubah admin tidak ditimpa.
 *   php artisan db:seed --class=PengaturanSeeder --force
 */
class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $default = [
            ['key' => 'radius_absen', 'value' => '50', 'type' => 'number', 'description' => 'Radius maksimal absen (dalam meter)'],
            ['key' => 'lat_sekolah', 'value' => '0.540182', 'type' => 'location', 'description' => 'Latitude koordinat sekolah'],
            ['key' => 'lng_sekolah', 'value' => '123.059293', 'type' => 'location', 'description' => 'Longitude koordinat sekolah'],
        ];

        foreach ($default as $row) {
            Pengaturan::firstOrCreate(['key' => $row['key']], $row);
        }
    }
}
