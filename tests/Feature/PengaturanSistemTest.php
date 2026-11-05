<?php

use App\Enums\JenisAbsenGuru;
use App\Enums\KategoriAbsensi;
use App\Livewire\Sistem\PengaturanSistem;
use App\Models\AturanAbsensi;
use App\Models\Pengaturan;
use App\Models\User;
use App\Services\AturanAbsensiService;
use App\Services\PengaturanService;
use Carbon\Carbon;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin-tu');
});

it('hanya admin tu / super admin yang bisa membuka pengaturan', function () {
    $kepsek = User::factory()->create();
    $kepsek->assignRole('kepala-sekolah');

    $this->actingAs($kepsek)->get(route('sistem.pengaturan'))->assertForbidden();
    foreach (['identitas', 'absensi', 'lokasi', 'notifikasi'] as $tab) {
        $this->actingAs($this->admin)->get(route('sistem.pengaturan', ['tab' => $tab]))->assertOk();
    }
});

it('tautan lama pengaturan lokasi diarahkan ke tab lokasi', function () {
    $this->actingAs($this->admin)->get('/administrasi/pengaturan-lokasi')
        ->assertRedirect('/sistem/pengaturan?tab=lokasi');
});

it('identitas sekolah tersimpan dan menimpa config kop', function () {
    Livewire::actingAs($this->admin)->test(PengaturanSistem::class)
        ->set('identitas.nama', 'SMA Uji Coba')
        ->set('identitas.npsn', '40500123')
        ->set('identitas.kepala', 'Dra. Kepala Uji')
        ->call('simpanIdentitas')
        ->assertHasNoErrors();

    expect(Pengaturan::where('key', 'sekolah.nama')->value('value'))->toBe('SMA Uji Coba')
        ->and(config('sekolah.nama'))->toBe('SMA Uji Coba')
        ->and(config('sekolah.kepala'))->toBe('Dra. Kepala Uji');
});

it('NPSN harus 8 digit', function () {
    Livewire::actingAs($this->admin)->test(PengaturanSistem::class)
        ->set('identitas.npsn', '12ab')
        ->call('simpanIdentitas')
        ->assertHasErrors('identitas.npsn');
});

it('jam absensi yang diubah langsung dipakai portal absen', function () {
    Livewire::actingAs($this->admin)->test(PengaturanSistem::class, ['tab' => 'absensi'])
        ->set('jam.guru.senin.masuk_tepat', '06:45')
        ->set('jam.guru.senin.toleransi_menit', 0)
        ->call('simpanJam')
        ->assertHasNoErrors();

    // Senin 9 Nov 2026 07:00 → kini terlambat
    $hasil = app(AturanAbsensiService::class)->evaluasi(KategoriAbsensi::GURU, JenisAbsenGuru::MASUK, Carbon::parse('2026-11-09 07:00'), false, false);
    expect($hasil['status'])->toBe('terlambat');
});

it('menolak urutan jam yang tidak masuk akal', function () {
    Livewire::actingAs($this->admin)->test(PengaturanSistem::class, ['tab' => 'absensi'])
        ->set('jam.guru.selasa.masuk_tutup', '06:30') // sebelum jam tepat
        ->call('simpanJam')
        ->assertHasErrors('jam.guru.selasa.masuk_tutup');

    expect(AturanAbsensi::where('kategori', 'guru')->where('hari', 'selasa')->first()->jam('masuk_tutup'))->toBe('12:00');
});

it('hari libur kerja tidak divalidasi urutannya & salin jam bekerja', function () {
    Livewire::actingAs($this->admin)->test(PengaturanSistem::class, ['tab' => 'absensi'])
        ->set('jam.guru.selasa.masuk_tepat', '07:30')
        ->call('salinKeHariLain', 'selasa')
        ->assertSet('jam.guru.rabu.masuk_tepat', '07:30')
        ->assertSet('jam.guru.senin.masuk_tepat', '07:00') // Senin tidak ikut
        ->assertSet('jam.guru.sabtu.hari_kerja', false)    // hari libur tidak diaktifkan
        ->call('simpanJam')
        ->assertHasNoErrors();
});

it('lokasi & notifikasi tersimpan', function () {
    Livewire::actingAs($this->admin)->test(PengaturanSistem::class, ['tab' => 'lokasi'])
        ->set('lat_sekolah', '0.5401820')->set('lng_sekolah', '123.0592930')->set('radius_absen', 75)
        ->call('simpanLokasi')->assertHasNoErrors()
        ->set('radius_absen', 5)->call('simpanLokasi')->assertHasErrors('radius_absen')
        ->set('reminder_jurnal_aktif', false)->set('reminder_jurnal_jam', '15:30')->set('ambang_alfa_siswa', 4)
        ->call('simpanNotifikasi')->assertHasNoErrors();

    $p = app(PengaturanService::class);
    expect($p->nilai('radius_absen'))->toBe('75')
        ->and($p->aktif('reminder_jurnal_aktif'))->toBeFalse()
        ->and($p->nilai('reminder_jurnal_jam'))->toBe('15:30')
        ->and($p->nilai('ambang_alfa_siswa'))->toBe('4');
});

it('jam yang diketik 7.15 dirapikan jadi 07:15, jam ngawur ditolak', function () {
    Livewire::actingAs($this->admin)->test(PengaturanSistem::class, ['tab' => 'absensi'])
        ->set('jam.guru.rabu.masuk_tepat', '7.20')
        ->call('simpanJam')
        ->assertHasNoErrors()
        ->assertSet('jam.guru.rabu.masuk_tepat', '07:20')
        ->set('jam.guru.rabu.pulang_tutup', '25:00')
        ->call('simpanJam')
        ->assertHasErrors('jam.guru.rabu.pulang_tutup');

    expect(AturanAbsensi::where('kategori', 'guru')->where('hari', 'rabu')->first()->jam('masuk_tepat'))->toBe('07:20');
});
