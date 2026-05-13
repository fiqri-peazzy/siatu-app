<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = [
            'super-admin',
            'admin-tu',
            'kepala-sekolah',
            'guru',
            'staf-tu',
            'siswa',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Define basic permissions (example)
        $permissions = [
            'view dashboard',
            'manage users',
            'manage surat',
            'input absensi',
            'approve kegiatan',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles (example)
        $adminTu = Role::findByName('admin-tu');
        $adminTu->givePermissionTo(['view dashboard', 'manage surat', 'input absensi']);

        $kepalaSekolah = Role::findByName('kepala-sekolah');
        $kepalaSekolah->givePermissionTo(['view dashboard', 'approve kegiatan']);
        
        $guru = Role::findByName('guru');
        $guru->givePermissionTo(['view dashboard', 'input absensi']);

        // Super Admin gets all permissions
        // Note: Better to handle this via Gate::before in AuthServiceProvider
    }
}
