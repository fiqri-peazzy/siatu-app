<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterTenagaKependidikansTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('master_tenaga_kependidikans')->delete();
        
        \DB::table('master_tenaga_kependidikans')->insert(array (
            0 => 
            array (
                'id' => 1,
                'nip_nik' => '7571035501930001',
                'nama' => 'Aisa Wulandari Sutarno',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'GORONTALO',
                'tanggal_lahir' => '1993-01-15',
                'no_hp' => '082192785350',
                'email' => 'aisawulandari15@gmail.com',
                'alamat' => 'JL.BILANGATA, RT 2 / RW 4, Kel/Desa DULOMO SELATAN, Kec. Kota Utara',
                'jabatan' => 'Lainnya',
                'unit_kerja' => NULL,
                'status_kepegawaian' => 'Honorer',
                'tmt_tugas' => NULL,
                'foto_path' => NULL,
                'user_id' => 35,
                'status_akun' => 'aktif',
                'status' => 'aktif',
                'created_at' => '2026-07-29 11:10:13',
                'updated_at' => '2026-07-29 11:10:14',
            ),
            1 => 
            array (
                'id' => 2,
                'nip_nik' => '7503155901900001',
                'nama' => 'Dara Nur Asyikin',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-01-19',
                'no_hp' => '082345324765',
                'email' => 'daranurasikin803@gmail.com',
                'alamat' => 'Jl. TM Gobel, Kel/Desa Tinelo Ayula, Tinelo Ayula',
                'jabatan' => 'Lainnya',
                'unit_kerja' => NULL,
                'status_kepegawaian' => 'Honorer',
                'tmt_tugas' => NULL,
                'foto_path' => NULL,
                'user_id' => 36,
                'status_akun' => 'aktif',
                'status' => 'aktif',
                'created_at' => '2026-07-29 11:10:14',
                'updated_at' => '2026-07-29 11:10:14',
            ),
            2 => 
            array (
                'id' => 3,
                'nip_nik' => '7571024811950001',
                'nama' => 'ISMI U. DALANGO',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'GORONTALO',
                'tanggal_lahir' => '1995-11-08',
                'no_hp' => '082264325646',
                'email' => 'ismiudalanggo@gmail.com',
                'alamat' => 'JL. BOTULOIDU, Kel/Desa POHE, Kec. Hulontalangi',
                'jabatan' => 'Lainnya',
                'unit_kerja' => NULL,
                'status_kepegawaian' => 'Honorer',
                'tmt_tugas' => NULL,
                'foto_path' => NULL,
                'user_id' => 37,
                'status_akun' => 'aktif',
                'status' => 'aktif',
                'created_at' => '2026-07-29 11:10:14',
                'updated_at' => '2026-07-29 11:10:14',
            ),
            3 => 
            array (
                'id' => 4,
                'nip_nik' => '7201042009970001',
                'nama' => 'Rizal S. Lanti',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'gorontalo',
                'tanggal_lahir' => '1997-09-20',
                'no_hp' => '085399963845',
                'email' => 'rizallanti97@gmail.com',
                'alamat' => 'Jl Banteng RT/Rw 002/004, Kel/Desa siendeng, Kec. Hulontalangi',
                'jabatan' => 'Lainnya',
                'unit_kerja' => NULL,
                'status_kepegawaian' => 'Honorer',
                'tmt_tugas' => NULL,
                'foto_path' => NULL,
                'user_id' => 38,
                'status_akun' => 'aktif',
                'status' => 'aktif',
                'created_at' => '2026-07-29 11:10:14',
                'updated_at' => '2026-07-29 11:10:15',
            ),
            4 => 
            array (
                'id' => 5,
                'nip_nik' => '197303312006041004',
                'nama' => 'Syafryanto Adam',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'GORONTALO',
                'tanggal_lahir' => '1973-03-31',
                'no_hp' => '082343239500',
                'email' => 'syafryantoadam215@gmail.com',
                'alamat' => 'JL DURIAN  BLOK A.NO.126 PERUM BTN TOMSEL DUNGINGI KOTA GORONTALO, RT 2 / RW 5, Kel/Desa TOMULABUTAO SELATAN, Kec. Dungingi',
                'jabatan' => 'Lainnya',
                'unit_kerja' => NULL,
                'status_kepegawaian' => 'PNS',
                'tmt_tugas' => NULL,
                'foto_path' => NULL,
                'user_id' => 39,
                'status_akun' => 'aktif',
                'status' => 'aktif',
                'created_at' => '2026-07-29 11:10:15',
                'updated_at' => '2026-07-29 11:10:15',
            ),
        ));
        
        
    }
}