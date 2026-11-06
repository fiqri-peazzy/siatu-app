<?php

use App\Livewire\Mobile\Guru\DashboardGuru;
use App\Models\AbsensiGuru;
use App\Models\Master\MasterGuru;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\MasterGurusTableSeeder;
use Database\Seeders\MasterTahunAjaransTableSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    $this->seed([RolesAndPermissionsSeeder::class, MasterTahunAjaransTableSeeder::class, MasterGurusTableSeeder::class]);
    Schema::enableForeignKeyConstraints();
    Carbon::setTestNow('2026-11-13 10:00'); // Jumat

    [$this->guru, $this->lain] = MasterGuru::where('status', 'aktif')->orderBy('id')->take(2)->get();
    $this->user = User::factory()->create(['guru_id' => $this->guru->id]);
    $this->user->assignRole('guru');
    $this->absen = fn ($guru, $tgl, $status = 'hadir') => AbsensiGuru::create(['guru_id' => $guru->id, 'tanggal' => $tgl, 'jenis' => 'masuk', 'status' => $status, 'waktu_absen' => "$tgl 07:00:00"]);
});

afterEach(fn () => Carbon::setTestNow());

it('hari kerja tanpa catatan absen dihitung tidak hadir (bukan 100%)', function () {
    // Sistem mulai dipakai Senin 9 Nov (absen pertama siapa pun)
    ($this->absen)($this->lain, '2026-11-09');
    ($this->absen)($this->guru, '2026-11-09');
    ($this->absen)($this->guru, '2026-11-10', 'terlambat');
    // Rabu 11 & Kamis 12 tidak absen; Jumat 13 (hari ini) belum absen → tidak dihitung

    Livewire::actingAs($this->user)->test(DashboardGuru::class)
        ->assertSet('hariEfektif', 4)
        ->assertSet('hariHadir', 2)
        ->assertSet('persentaseKehadiran', 50)
        ->assertSee('2 dari 4 hari kerja');
});

it('hari ini ikut dihitung setelah absen masuk', function () {
    ($this->absen)($this->guru, '2026-11-12');
    ($this->absen)($this->guru, '2026-11-13');

    Livewire::actingAs($this->user)->test(DashboardGuru::class)
        ->assertSet('hariEfektif', 2)
        ->assertSet('persentaseKehadiran', 100);
});

it('belum ada data absensi sama sekali → tampil tanda strip, bukan 100%', function () {
    Livewire::actingAs($this->user)->test(DashboardGuru::class)
        ->assertSet('persentaseKehadiran', null)
        ->assertSee('Belum ada hari kerja dinilai');
});
