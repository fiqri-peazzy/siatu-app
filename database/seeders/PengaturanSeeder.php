<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Pengaturan::insert([
            ['key' => 'radius_absen', 'value' => '50', 'type' => 'number', 'description' => 'Radius maksimal absen (dalam meter)'],
            ['key' => 'batas_telat', 'value' => '07:45', 'type' => 'time', 'description' => 'Batas jam masuk (telat jika lebih dari ini)'],
            ['key' => 'lat_sekolah', 'value' => '0.540182', 'type' => 'location', 'description' => 'Latitude koordinat sekolah'],
            ['key' => 'lng_sekolah', 'value' => '123.059293', 'type' => 'location', 'description' => 'Longitude koordinat sekolah'],
        ]);
    }
}
