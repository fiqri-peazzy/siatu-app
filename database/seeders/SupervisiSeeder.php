<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SupervisiSeeder extends Seeder
{
    public function run(): void
    {
        // Add Permission
        $permission = Permission::firstOrCreate(['name' => 'view_dashboard_supervisi']);
        
        // Add to existing roles if they exist
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) $superAdmin->givePermissionTo($permission);
        
        $adminTu = Role::where('name', 'admin-tu')->first();
        if ($adminTu) $adminTu->givePermissionTo($permission);

        // Add Configuration Setting
        DB::table('pengaturans')->updateOrInsert(
            ['key' => 'ambang_waktu_jurnal'],
            [
                'value' => '30',
                'type' => 'number',
                'description' => 'Ambang waktu (dalam menit) sejak jam mulai sebelum KBM ditandai belum diisi jurnalnya.',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
