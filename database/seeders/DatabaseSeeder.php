<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class, // Harus pertama
            // UsersSeeder::class, // Diganti dengan data real
        ]);
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
        $this->call(UsersTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
    }
}
