<?php

use App\Models\AbsensiGuru;
use App\Models\Master\MasterGuru;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Database\Seeders\MasterGurusTableSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    $this->seed([RolesAndPermissionsSeeder::class, MasterGurusTableSeeder::class]);
    Schema::enableForeignKeyConstraints();
    Carbon::setTestNow('2026-11-12 09:00'); // Kamis, jam sekolah
});

afterEach(fn () => Carbon::setTestNow());

it('menampilkan dashboard kepala sekolah untuk role kepala-sekolah', function () {
    $user = User::factory()->create(['name' => 'Bapak Kepala']);
    $user->assignRole('kepala-sekolah');

    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertSee('Pemantauan sekolah hari ini')
        ->assertSee('Tren kehadiran guru')
        ->assertDontSee('Ruang kerja Tata Usaha');
});

it('menampilkan dashboard tata usaha untuk admin-tu', function () {
    $user = User::factory()->create(['name' => 'Ibu Admin']);
    $user->assignRole('admin-tu');

    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertSee('Ruang kerja Tata Usaha')
        ->assertSee('Kesiapan sistem')
        ->assertDontSee('Pemantauan sekolah hari ini');
});

it('guru tetap diarahkan ke dashboard guru', function () {
    $user = User::factory()->create();
    $user->assignRole('guru');

    $this->actingAs($user)->get('/')->assertRedirect(route('guru.dashboard'));
});

it('menghitung kehadiran guru hari ini dengan benar', function () {
    $guru = MasterGuru::where('status', 'aktif')->orderBy('id')->take(3)->get();
    AbsensiGuru::create(['guru_id' => $guru[0]->id, 'tanggal' => '2026-11-12', 'jenis' => 'masuk', 'status' => 'hadir', 'waktu_absen' => '2026-11-12 07:00:00']);
    AbsensiGuru::create(['guru_id' => $guru[1]->id, 'tanggal' => '2026-11-12', 'jenis' => 'masuk', 'status' => 'terlambat', 'waktu_absen' => '2026-11-12 07:40:00']);
    AbsensiGuru::create(['guru_id' => $guru[2]->id, 'tanggal' => '2026-11-12', 'jenis' => 'masuk', 'status' => 'sakit', 'waktu_absen' => '2026-11-12 06:30:00']);

    $k = app(DashboardService::class)->kehadiranPegawai('guru');
    $total = MasterGuru::where('status', 'aktif')->count();

    expect($k['hadir'])->toBe(1)
        ->and($k['terlambat'])->toBe(1)
        ->and($k['izin'])->toBe(1)
        ->and($k['belum'])->toBe($total - 3)
        ->and(app(DashboardService::class)->guruBelumAbsen(100))->not->toContain($guru[0]->nama);
});
