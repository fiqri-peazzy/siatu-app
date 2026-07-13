<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\MasterMapel;
use Illuminate\Support\Str;

class MasterMapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapels = [
            // Umum
            ['nama' => 'Pendidikan Agama Islam', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Pancasila', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Indonesia', 'kelompok' => 'Umum'],
            ['nama' => 'Matematika', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Inggris', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Jasmani, Olahraga dan Kesehatan', 'kelompok' => 'Umum'],
            ['nama' => 'Informatika', 'kelompok' => 'Umum'],
            
            // IPA
            ['nama' => 'Fisika', 'kelompok' => 'IPA'],
            ['nama' => 'Kimia', 'kelompok' => 'IPA'],
            ['nama' => 'Biologi', 'kelompok' => 'IPA'],
            
            // IPS
            ['nama' => 'Sejarah', 'kelompok' => 'IPS'],
            ['nama' => 'Sosiologi', 'kelompok' => 'IPS'],
            ['nama' => 'Ekonomi', 'kelompok' => 'IPS'],
            ['nama' => 'Geografi', 'kelompok' => 'IPS'],
            
            // Seni & Prakarya
            ['nama' => 'Seni Musik', 'kelompok' => 'Seni & Prakarya'],
            ['nama' => 'Seni Rupa', 'kelompok' => 'Seni & Prakarya'],
            ['nama' => 'Seni Teater', 'kelompok' => 'Seni & Prakarya'],
            ['nama' => 'Seni Tari', 'kelompok' => 'Seni & Prakarya'],
            ['nama' => 'Prakarya dan Kewirausahaan', 'kelompok' => 'Seni & Prakarya'],
            
            // Muatan Lokal
            ['nama' => 'Bahasa Arab', 'kelompok' => 'Muatan Lokal'],
            ['nama' => 'Bahasa Jepang', 'kelompok' => 'Muatan Lokal'],
        ];

        foreach ($mapels as $index => $m) {
            MasterMapel::updateOrCreate(
                ['nama' => $m['nama']],
                [
                    'kode' => 'MPL-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'kelompok' => $m['kelompok'],
                    'jam' => 2,
                    'kurikulum' => 'Merdeka'
                ]
            );
        }
    }
}
