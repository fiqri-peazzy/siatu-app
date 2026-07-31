<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterKelasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('master_kelas')->delete();
        
        \DB::table('master_kelas')->insert(array (
            0 => 
            array (
                'id' => 3,
                'nama' => 'Kelas XI',
                'tingkat' => 'XI',
                'jurusan' => '',
                'wali_kelas_id' => 37,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-23 15:06:18',
                'updated_at' => '2026-07-23 15:06:18',
            ),
            1 => 
            array (
                'id' => 5,
                'nama' => 'Kelas XII',
                'tingkat' => 'XII',
                'jurusan' => '',
                'wali_kelas_id' => 42,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-23 15:06:55',
                'updated_at' => '2026-07-23 15:06:55',
            ),
            2 => 
            array (
                'id' => 8,
                'nama' => 'Kelas X',
                'tingkat' => 'X',
                'jurusan' => '',
                'wali_kelas_id' => NULL,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-29 15:56:31',
                'updated_at' => '2026-07-29 15:56:31',
            ),
        ));
        
        
    }
}