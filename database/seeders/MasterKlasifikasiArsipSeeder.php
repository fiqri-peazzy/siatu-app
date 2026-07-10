<?php

namespace Database\Seeders;

use App\Models\Master\MasterKlasifikasiArsip;
use Illuminate\Database\Seeder;

class MasterKlasifikasiArsipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klasifikasi = [
            ['kode' => '000', 'nama' => 'Umum', 'deskripsi' => 'Klasifikasi Umum'],
            ['kode' => '421', 'nama' => 'Pendidikan', 'deskripsi' => 'Sekolah Dasar, Menengah, dan Tinggi'],
            ['kode' => '800', 'nama' => 'Kepegawaian', 'deskripsi' => 'Administrasi Kepegawaian'],
            ['kode' => '900', 'nama' => 'Keuangan', 'deskripsi' => 'Administrasi Keuangan'],
        ];

        foreach ($klasifikasi as $k) {
            MasterKlasifikasiArsip::firstOrCreate(['kode' => $k['kode']], $k);
        }
    }
}
