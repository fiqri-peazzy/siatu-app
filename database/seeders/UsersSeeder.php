<?php

namespace Database\Seeders;

use App\Models\Master\MasterGuru;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterTahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Tahun Ajaran Aktif (diperlukan untuk relasi Kelas & Siswa) ──────
        $tahunAjaran = MasterTahunAjaran::firstOrCreate(
            ['nama' => '2024/2025', 'semester' => 'Genap'],
            ['mulai' => '2025-01-06', 'selesai' => '2025-06-20', 'is_active' => true]
        );

        // ─── 1. Super Admin ──────────────────────────────────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@situ-sma.test'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'jabatan'  => 'Administrator Sistem',
                'is_active'=> true,
            ]
        );
        $superAdmin->assignRole('super-admin');

        // ─── 2. Admin TU ─────────────────────────────────────────────────────
        $adminTu = User::firstOrCreate(
            ['email' => 'admin.tu@situ-sma.test'],
            [
                'name'     => 'Budi Santoso',
                'nip'      => '198501012010011001',
                'no_hp'    => '08123456789',
                'jabatan'  => 'Kepala Tata Usaha',
                'jenis_kelamin' => 'L',
                'password' => Hash::make('password'),
                'is_active'=> true,
            ]
        );
        // Buat data master guru untuk admin TU
        $guruAdminTu = MasterGuru::firstOrCreate(
            ['nip' => '198501012010011001'],
            ['nama' => 'Budi Santoso', 'jabatan' => 'Kepala TU', 'status' => 'aktif']
        );
        $guruAdminTu->update(['user_id' => $adminTu->id]);
        $adminTu->update(['guru_id' => $guruAdminTu->id]);
        $adminTu->assignRole('admin-tu');

        // ─── 3. Kepala Sekolah ────────────────────────────────────────────────
        $kepsek = User::firstOrCreate(
            ['email' => 'kepala.sekolah@situ-sma.test'],
            [
                'name'     => 'Dr. Siti Rahayu, M.Pd.',
                'nip'      => '197203152000032001',
                'no_hp'    => '08234567890',
                'jabatan'  => 'Kepala Sekolah',
                'jenis_kelamin' => 'P',
                'password' => Hash::make('password'),
                'is_active'=> true,
            ]
        );
        $guruKepsek = MasterGuru::firstOrCreate(
            ['nip' => '197203152000032001'],
            ['nama' => 'Dr. Siti Rahayu, M.Pd.', 'jabatan' => 'Kepala Sekolah', 'status' => 'aktif']
        );
        $guruKepsek->update(['user_id' => $kepsek->id]);
        $kepsek->update(['guru_id' => $guruKepsek->id]);
        $kepsek->assignRole('kepala-sekolah');

        // ─── 4. Guru ─────────────────────────────────────────────────────────
        $guruUser = User::firstOrCreate(
            ['email' => 'guru@situ-sma.test'],
            [
                'name'     => 'Ahmad Fauzi, S.Pd.',
                'nip'      => '198909202015011002',
                'no_hp'    => '08345678901',
                'jabatan'  => 'Guru Mata Pelajaran',
                'jenis_kelamin' => 'L',
                'password' => Hash::make('password'),
                'is_active'=> true,
            ]
        );
        $guruMaster = MasterGuru::firstOrCreate(
            ['nip' => '198909202015011002'],
            [
                'nama'         => 'Ahmad Fauzi, S.Pd.',
                'jabatan'      => 'Guru Mapel',
                'mapel_utama'  => 'Matematika',
                'status'       => 'aktif',
            ]
        );
        $guruMaster->update(['user_id' => $guruUser->id]);
        $guruUser->update(['guru_id' => $guruMaster->id]);
        $guruUser->assignRole('guru');

        // ─── 5. Staf TU ──────────────────────────────────────────────────────
        $stafTu = User::firstOrCreate(
            ['email' => 'staf.tu@situ-sma.test'],
            [
                'name'     => 'Dewi Lestari',
                'nip'      => '199405122019022001',
                'no_hp'    => '08456789012',
                'jabatan'  => 'Staf Tata Usaha',
                'jenis_kelamin' => 'P',
                'password' => Hash::make('password'),
                'is_active'=> true,
            ]
        );
        $guruStaf = MasterGuru::firstOrCreate(
            ['nip' => '199405122019022001'],
            ['nama' => 'Dewi Lestari', 'jabatan' => 'Staf TU', 'status' => 'aktif']
        );
        $guruStaf->update(['user_id' => $stafTu->id]);
        $stafTu->update(['guru_id' => $guruStaf->id]);
        $stafTu->assignRole('staf-tu');

        // ─── 6. Siswa ─────────────────────────────────────────────────────────
        // Buat kelas terlebih dahulu
        $kelas = MasterKelas::firstOrCreate(
            ['nama' => 'XII IPA 1', 'tahun_ajaran_id' => $tahunAjaran->id],
            [
                'tingkat'        => 'XII',
                'jurusan'        => 'IPA',
                'wali_kelas_id'  => $guruMaster->id,
            ]
        );

        $siswaMaster = MasterSiswa::firstOrCreate(
            ['nis' => '12345'],
            [
                'nisn'     => '0087654321',
                'nama'     => 'Rizky Pratama',
                'kelas_id' => $kelas->id,
                'status'   => 'aktif',
            ]
        );

        $siswaUser = User::firstOrCreate(
            ['email' => 'siswa@situ-sma.test'],
            [
                'name'          => 'Rizky Pratama',
                'nis'           => '12345',
                'no_hp'         => '08567890123',
                'jenis_kelamin' => 'L',
                'password'      => Hash::make('password'),
                'siswa_id'      => $siswaMaster->id,
                'is_active'     => true,
            ]
        );
        $siswaUser->assignRole('siswa');

        $this->command->info('✅ Seeder pengguna selesai. Akun testing:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin',    'superadmin@situ-sma.test',       'password'],
                ['Admin TU',       'admin.tu@situ-sma.test',          'password'],
                ['Kepala Sekolah', 'kepala.sekolah@situ-sma.test',    'password'],
                ['Guru',           'guru@situ-sma.test',              'password'],
                ['Staf TU',        'staf.tu@situ-sma.test',           'password'],
                ['Siswa',          'siswa@situ-sma.test',             'password'],
            ]
        );
    }
}
