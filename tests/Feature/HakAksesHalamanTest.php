<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->akun = fn (string $role) => tap(User::factory()->create(), fn ($u) => $u->assignRole($role));
});

/** Halaman yang memuat data lintas pegawai/siswa — tidak boleh dibuka guru */
dataset('halaman pimpinan', [
    '/akademik/absensi/monitoring',
    '/akademik/absensi/tendik/monitoring',
    '/akademik/absensi-siswa/rekap',
    '/akademik/peminatan',
    '/supervisi/jurnal/realtime',
    '/supervisi/jurnal/rekapan',
    '/supervisi/jurnal/export',
    '/master/guru',
    '/master/siswa',
    '/master/jadwal',
    '/laporan',
    '/sistem/pengaturan',
]);

it('guru ditolak di halaman pimpinan/TU', function (string $url) {
    $this->actingAs(($this->akun)('guru'))->get($url)->assertForbidden();
})->with('halaman pimpinan');

it('guru tidak bisa membuka halaman absen tendik', function () {
    $this->actingAs(($this->akun)('guru'))->get('/akademik/absensi/tendik/riwayat')->assertForbidden();
});

it('kepala sekolah bisa memantau tetapi tidak mengubah data master', function () {
    $kepsek = ($this->akun)('kepala-sekolah');
    foreach (['/akademik/absensi/monitoring', '/akademik/absensi/tendik/monitoring', '/supervisi/jurnal/rekapan', '/laporan'] as $url) {
        $this->actingAs($kepsek)->get($url)->assertOk();
    }
    $this->actingAs($kepsek)->get('/master/siswa')->assertForbidden();
});

it('admin tu bisa membuka monitoring & data master', function () {
    $admin = ($this->akun)('admin-tu');
    foreach (['/akademik/absensi/monitoring', '/master/siswa', '/master/guru', '/akademik/peminatan', '/supervisi/jurnal/rekapan'] as $url) {
        $this->actingAs($admin)->get($url)->assertOk();
    }
});
