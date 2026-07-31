<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterMapelsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('master_mapels')->delete();
        
        \DB::table('master_mapels')->insert(array (
            0 => 
            array (
                'id' => 1,
                'kode' => 'MPL-004',
                'nama' => 'Matematika',
                'kelompok' => 'Umum',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 13:42:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            1 => 
            array (
                'id' => 2,
                'kode' => 'MPL-001',
                'nama' => 'Pendidikan Agama Islam',
                'kelompok' => 'Umum',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            2 => 
            array (
                'id' => 3,
                'kode' => 'MPL-002',
                'nama' => 'Pendidikan Pancasila',
                'kelompok' => 'Umum',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            3 => 
            array (
                'id' => 4,
                'kode' => 'MPL-003',
                'nama' => 'Bahasa Indonesia',
                'kelompok' => 'Umum',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            4 => 
            array (
                'id' => 5,
                'kode' => 'MPL-005',
                'nama' => 'Bahasa Inggris',
                'kelompok' => 'Umum',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            5 => 
            array (
                'id' => 6,
                'kode' => 'MPL-006',
                'nama' => 'Pendidikan Jasmani, Olahraga dan Kesehatan',
                'kelompok' => 'Umum',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            6 => 
            array (
                'id' => 7,
                'kode' => 'MPL-007',
                'nama' => 'Informatika',
                'kelompok' => 'Umum',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            7 => 
            array (
                'id' => 8,
                'kode' => 'MPL-008',
                'nama' => 'Fisika',
                'kelompok' => 'IPA',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            8 => 
            array (
                'id' => 9,
                'kode' => 'MPL-009',
                'nama' => 'Kimia',
                'kelompok' => 'IPA',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            9 => 
            array (
                'id' => 10,
                'kode' => 'MPL-010',
                'nama' => 'Biologi',
                'kelompok' => 'IPA',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            10 => 
            array (
                'id' => 11,
                'kode' => 'MPL-011',
                'nama' => 'Sejarah',
                'kelompok' => 'IPS',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            11 => 
            array (
                'id' => 12,
                'kode' => 'MPL-012',
                'nama' => 'Sosiologi',
                'kelompok' => 'IPS',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            12 => 
            array (
                'id' => 13,
                'kode' => 'MPL-013',
                'nama' => 'Ekonomi',
                'kelompok' => 'IPS',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:43',
                'updated_at' => '2026-07-23 14:10:43',
            ),
            13 => 
            array (
                'id' => 14,
                'kode' => 'MPL-014',
                'nama' => 'Geografi',
                'kelompok' => 'IPS',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:44',
                'updated_at' => '2026-07-23 14:10:44',
            ),
            14 => 
            array (
                'id' => 15,
                'kode' => 'MPL-015',
                'nama' => 'Seni Musik',
                'kelompok' => 'Seni & Prakarya',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:44',
                'updated_at' => '2026-07-23 14:10:44',
            ),
            15 => 
            array (
                'id' => 16,
                'kode' => 'MPL-016',
                'nama' => 'Seni Rupa',
                'kelompok' => 'Seni & Prakarya',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:44',
                'updated_at' => '2026-07-23 14:10:44',
            ),
            16 => 
            array (
                'id' => 17,
                'kode' => 'MPL-017',
                'nama' => 'Seni Teater',
                'kelompok' => 'Seni & Prakarya',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:44',
                'updated_at' => '2026-07-23 14:10:44',
            ),
            17 => 
            array (
                'id' => 18,
                'kode' => 'MPL-018',
                'nama' => 'Seni Tari',
                'kelompok' => 'Seni & Prakarya',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:44',
                'updated_at' => '2026-07-23 14:10:44',
            ),
            18 => 
            array (
                'id' => 19,
                'kode' => 'MPL-019',
                'nama' => 'Prakarya dan Kewirausahaan',
                'kelompok' => 'Seni & Prakarya',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:44',
                'updated_at' => '2026-07-23 14:10:44',
            ),
            19 => 
            array (
                'id' => 20,
                'kode' => 'MPL-020',
                'nama' => 'Bahasa Arab',
                'kelompok' => 'Muatan Lokal',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:44',
                'updated_at' => '2026-07-23 14:10:44',
            ),
            20 => 
            array (
                'id' => 21,
                'kode' => 'MPL-021',
                'nama' => 'Bahasa Jepang',
                'kelompok' => 'Muatan Lokal',
                'jam' => 2,
                'kurikulum' => 'Merdeka',
                'created_at' => '2026-07-23 14:10:44',
                'updated_at' => '2026-07-23 14:10:44',
            ),
            21 => 
            array (
                'id' => 22,
                'kode' => 'BING-TKA',
                'nama' => 'BING TKA',
                'kelompok' => NULL,
                'jam' => 2,
                'kurikulum' => NULL,
                'created_at' => '2026-07-29 15:19:26',
                'updated_at' => '2026-07-29 15:19:26',
            ),
            22 => 
            array (
                'id' => 23,
                'kode' => 'TKA-PILIHAN',
                'nama' => 'TKA PILIHAN',
                'kelompok' => NULL,
                'jam' => 2,
                'kurikulum' => NULL,
                'created_at' => '2026-07-29 15:19:26',
                'updated_at' => '2026-07-29 15:19:26',
            ),
            23 => 
            array (
                'id' => 24,
                'kode' => 'BIND-TKA',
                'nama' => 'BIND TKA',
                'kelompok' => NULL,
                'jam' => 2,
                'kurikulum' => NULL,
                'created_at' => '2026-07-29 15:19:26',
                'updated_at' => '2026-07-29 15:19:26',
            ),
            24 => 
            array (
                'id' => 25,
                'kode' => 'TAHFIDZ',
                'nama' => 'Tahfidz',
                'kelompok' => NULL,
                'jam' => 2,
                'kurikulum' => NULL,
                'created_at' => '2026-07-29 15:19:27',
                'updated_at' => '2026-07-29 15:19:27',
            ),
            25 => 
            array (
                'id' => 26,
                'kode' => 'BIND',
                'nama' => 'BIND',
                'kelompok' => NULL,
                'jam' => 2,
                'kurikulum' => NULL,
                'created_at' => '2026-07-29 15:19:27',
                'updated_at' => '2026-07-29 15:19:27',
            ),
            26 => 
            array (
                'id' => 28,
                'kode' => 'ANTROPOLOGI',
                'nama' => 'Antropologi',
                'kelompok' => NULL,
                'jam' => 2,
                'kurikulum' => NULL,
                'created_at' => '2026-07-29 15:20:05',
                'updated_at' => '2026-07-29 15:20:05',
            ),
            27 => 
            array (
                'id' => 29,
                'kode' => 'OPSI',
                'nama' => 'OPSI',
                'kelompok' => NULL,
                'jam' => 2,
                'kurikulum' => NULL,
                'created_at' => '2026-07-29 15:20:05',
                'updated_at' => '2026-07-29 15:20:05',
            ),
            28 => 
            array (
                'id' => 30,
                'kode' => 'MAT-TKA',
                'nama' => 'MAT TKA',
                'kelompok' => NULL,
                'jam' => 2,
                'kurikulum' => NULL,
                'created_at' => '2026-07-29 15:20:06',
                'updated_at' => '2026-07-29 15:20:06',
            ),
            29 => 
            array (
                'id' => 31,
                'kode' => 'PKN',
                'nama' => 'PKn',
                'kelompok' => NULL,
                'jam' => 2,
                'kurikulum' => NULL,
                'created_at' => '2026-07-29 15:20:06',
                'updated_at' => '2026-07-29 15:20:06',
            ),
        ));
        
        
    }
}