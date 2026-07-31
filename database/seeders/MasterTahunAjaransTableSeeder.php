<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterTahunAjaransTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('master_tahun_ajarans')->delete();
        
        \DB::table('master_tahun_ajarans')->insert(array (
            0 => 
            array (
                'id' => 1,
                'nama' => '2024/2025',
                'semester' => 'Genap',
                'mulai' => '2025-01-06',
                'selesai' => '2025-06-20',
                'is_active' => 0,
                'created_at' => '2026-07-23 13:42:41',
                'updated_at' => '2026-07-31 10:26:59',
            ),
            1 => 
            array (
                'id' => 2,
                'nama' => '2026/2027',
                'semester' => 'Ganjil',
                'mulai' => '2026-08-03',
                'selesai' => '2026-12-23',
                'is_active' => 1,
                'created_at' => '2026-07-23 15:05:22',
                'updated_at' => '2026-07-31 10:26:59',
            ),
        ));
        
        
    }
}