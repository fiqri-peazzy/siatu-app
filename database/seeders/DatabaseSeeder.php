<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class, // Harus pertama
            // UsersSeeder::class, // Diganti dengan data real
        ]);

        // Data real hasil export saling merujuk (users.guru_id ↔ master_gurus.user_id),
        // jadi pengecekan FK dimatikan selama data dasar dimuat.
        Schema::disableForeignKeyConstraints();
        try {
            $this->call(UsersTableSeeder::class);
            $this->call(ModelHasRolesTableSeeder::class);
            $this->call(MasterTahunAjaransTableSeeder::class);
            $this->call(MasterMapelsTableSeeder::class);
            $this->call(MasterKelasTableSeeder::class);
            $this->call(MasterGurusTableSeeder::class);
            $this->call(MasterTenagaKependidikansTableSeeder::class);
            $this->call(MasterSiswasTableSeeder::class);
            $this->call(MasterJadwalPelajaransTableSeeder::class);
            $this->call(PeminatanSiswasTableSeeder::class);
            $this->call(MasterKategoriSuratSeeder::class);
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        // Pengaturan sistem & aturan absensi (idempoten)
        $this->call(PengaturanSeeder::class);
        $this->call(AturanAbsensiSeeder::class);

        // Jadwal v2 Semester Ganjil 2026/2027
        $this->call(JadwalGanjil20262027Seeder::class);
    }
}
