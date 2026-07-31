<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PeminatanSiswasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('peminatan_siswas')->delete();
        
        \DB::table('peminatan_siswas')->insert(array (
            0 => 
            array (
                'id' => 15,
                'siswa_id' => 56,
                'mapel_id' => 7,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:04:51',
                'updated_at' => '2026-07-30 03:04:51',
            ),
            1 => 
            array (
                'id' => 16,
                'siswa_id' => 57,
                'mapel_id' => 7,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:04:51',
                'updated_at' => '2026-07-30 03:04:51',
            ),
            2 => 
            array (
                'id' => 17,
                'siswa_id' => 58,
                'mapel_id' => 7,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:04:51',
                'updated_at' => '2026-07-30 03:04:51',
            ),
            3 => 
            array (
                'id' => 18,
                'siswa_id' => 59,
                'mapel_id' => 7,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:04:51',
                'updated_at' => '2026-07-30 03:04:51',
            ),
            4 => 
            array (
                'id' => 19,
                'siswa_id' => 60,
                'mapel_id' => 7,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:04:51',
                'updated_at' => '2026-07-30 03:04:51',
            ),
            5 => 
            array (
                'id' => 20,
                'siswa_id' => 64,
                'mapel_id' => 7,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:04:51',
                'updated_at' => '2026-07-30 03:04:51',
            ),
            6 => 
            array (
                'id' => 21,
                'siswa_id' => 106,
                'mapel_id' => 7,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:04:51',
                'updated_at' => '2026-07-30 03:04:51',
            ),
            7 => 
            array (
                'id' => 54,
                'siswa_id' => 56,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            8 => 
            array (
                'id' => 55,
                'siswa_id' => 57,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            9 => 
            array (
                'id' => 56,
                'siswa_id' => 58,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            10 => 
            array (
                'id' => 57,
                'siswa_id' => 59,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            11 => 
            array (
                'id' => 58,
                'siswa_id' => 60,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            12 => 
            array (
                'id' => 59,
                'siswa_id' => 64,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            13 => 
            array (
                'id' => 60,
                'siswa_id' => 65,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            14 => 
            array (
                'id' => 61,
                'siswa_id' => 66,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            15 => 
            array (
                'id' => 62,
                'siswa_id' => 67,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            16 => 
            array (
                'id' => 63,
                'siswa_id' => 70,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            17 => 
            array (
                'id' => 64,
                'siswa_id' => 71,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            18 => 
            array (
                'id' => 65,
                'siswa_id' => 73,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            19 => 
            array (
                'id' => 66,
                'siswa_id' => 74,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            20 => 
            array (
                'id' => 67,
                'siswa_id' => 75,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            21 => 
            array (
                'id' => 68,
                'siswa_id' => 76,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            22 => 
            array (
                'id' => 69,
                'siswa_id' => 77,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            23 => 
            array (
                'id' => 70,
                'siswa_id' => 78,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            24 => 
            array (
                'id' => 71,
                'siswa_id' => 79,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            25 => 
            array (
                'id' => 72,
                'siswa_id' => 81,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            26 => 
            array (
                'id' => 73,
                'siswa_id' => 83,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            27 => 
            array (
                'id' => 74,
                'siswa_id' => 85,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            28 => 
            array (
                'id' => 75,
                'siswa_id' => 87,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            29 => 
            array (
                'id' => 76,
                'siswa_id' => 88,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            30 => 
            array (
                'id' => 77,
                'siswa_id' => 92,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            31 => 
            array (
                'id' => 78,
                'siswa_id' => 95,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            32 => 
            array (
                'id' => 79,
                'siswa_id' => 100,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            33 => 
            array (
                'id' => 80,
                'siswa_id' => 101,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            34 => 
            array (
                'id' => 81,
                'siswa_id' => 102,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            35 => 
            array (
                'id' => 82,
                'siswa_id' => 103,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            36 => 
            array (
                'id' => 83,
                'siswa_id' => 105,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            37 => 
            array (
                'id' => 84,
                'siswa_id' => 106,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            38 => 
            array (
                'id' => 85,
                'siswa_id' => 107,
                'mapel_id' => 5,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:17:03',
                'updated_at' => '2026-07-30 03:17:03',
            ),
            39 => 
            array (
                'id' => 86,
                'siswa_id' => 56,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            40 => 
            array (
                'id' => 87,
                'siswa_id' => 57,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            41 => 
            array (
                'id' => 88,
                'siswa_id' => 58,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            42 => 
            array (
                'id' => 89,
                'siswa_id' => 59,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            43 => 
            array (
                'id' => 90,
                'siswa_id' => 60,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            44 => 
            array (
                'id' => 91,
                'siswa_id' => 64,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            45 => 
            array (
                'id' => 92,
                'siswa_id' => 65,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            46 => 
            array (
                'id' => 93,
                'siswa_id' => 66,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            47 => 
            array (
                'id' => 94,
                'siswa_id' => 67,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            48 => 
            array (
                'id' => 95,
                'siswa_id' => 70,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            49 => 
            array (
                'id' => 96,
                'siswa_id' => 71,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            50 => 
            array (
                'id' => 97,
                'siswa_id' => 73,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            51 => 
            array (
                'id' => 98,
                'siswa_id' => 74,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            52 => 
            array (
                'id' => 99,
                'siswa_id' => 75,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            53 => 
            array (
                'id' => 100,
                'siswa_id' => 76,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            54 => 
            array (
                'id' => 101,
                'siswa_id' => 77,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            55 => 
            array (
                'id' => 102,
                'siswa_id' => 78,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            56 => 
            array (
                'id' => 103,
                'siswa_id' => 79,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            57 => 
            array (
                'id' => 104,
                'siswa_id' => 81,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            58 => 
            array (
                'id' => 105,
                'siswa_id' => 83,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            59 => 
            array (
                'id' => 106,
                'siswa_id' => 85,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            60 => 
            array (
                'id' => 107,
                'siswa_id' => 87,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            61 => 
            array (
                'id' => 108,
                'siswa_id' => 88,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            62 => 
            array (
                'id' => 109,
                'siswa_id' => 92,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            63 => 
            array (
                'id' => 110,
                'siswa_id' => 95,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            64 => 
            array (
                'id' => 111,
                'siswa_id' => 100,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            65 => 
            array (
                'id' => 112,
                'siswa_id' => 101,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            66 => 
            array (
                'id' => 113,
                'siswa_id' => 102,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            67 => 
            array (
                'id' => 114,
                'siswa_id' => 103,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            68 => 
            array (
                'id' => 115,
                'siswa_id' => 105,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            69 => 
            array (
                'id' => 116,
                'siswa_id' => 106,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
            70 => 
            array (
                'id' => 117,
                'siswa_id' => 107,
                'mapel_id' => 20,
                'tahun_ajaran_id' => 2,
                'created_at' => '2026-07-30 03:20:24',
                'updated_at' => '2026-07-30 03:20:24',
            ),
        ));
        
        
    }
}