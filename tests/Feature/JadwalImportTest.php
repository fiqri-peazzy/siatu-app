<?php

use App\Enums\JenisSesi;
use App\Models\JadwalSesi;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterPengampu;
use App\Models\Master\MasterSlotJam;
use App\Models\Master\MasterTahunAjaran;
use Database\Seeders\JadwalGanjil20262027Seeder;
use Database\Seeders\MasterGurusTableSeeder;
use Database\Seeders\MasterKelasTableSeeder;
use Database\Seeders\MasterMapelsTableSeeder;
use Database\Seeders\MasterTahunAjaransTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(DatabaseMigrations::class);

beforeEach(function () {
    // Seeder data real saling merujuk (users ↔ master_gurus); FK dimatikan hanya saat memuat data dasar.
    Schema::disableForeignKeyConstraints();
    $this->seed([
        MasterTahunAjaransTableSeeder::class,
        MasterMapelsTableSeeder::class,
        MasterGurusTableSeeder::class,
        MasterKelasTableSeeder::class,
    ]);
    Schema::enableForeignKeyConstraints();
    $this->ta = MasterTahunAjaran::where('nama', '2026/2027')->where('semester', 'Ganjil')->firstOrFail();
});

function sesiPada(string $hari, int $jamKe)
{
    return JadwalSesi::query()
        ->whereHas('slot', fn ($q) => $q->where('hari', $hari)->where('jam_ke', $jamKe))
        ->with(['kelas', 'mapel'])
        ->get();
}

test('membangun 6 rombel dan mengambil alih rombel lama tanpa kehilangan id', function () {
    $kelasXLama = MasterKelas::where('tahun_ajaran_id', $this->ta->id)->where('tingkat', 'X')->orderBy('id')->first();

    $this->seed(JadwalGanjil20262027Seeder::class);

    $rombel = MasterKelas::where('tahun_ajaran_id', $this->ta->id)->whereNotNull('kode')->pluck('kode')->sort()->values();
    expect($rombel->all())->toBe(['X-SCI', 'X-SOC', 'XI-SCI', 'XI-SOC', 'XII-SCI', 'XII-SOC']);

    if ($kelasXLama) {
        expect($kelasXLama->fresh()->kode)->toBe('X-SCI');
    }
});

test('membuat slot per hari sesuai dokumen jadwal', function () {
    $this->seed(JadwalGanjil20262027Seeder::class);

    $perHari = MasterSlotJam::query()->get()->groupBy(fn ($s) => $s->hari->value)->map->count();

    expect($perHari->all())->toMatchArray([
        'senin' => 15, 'selasa' => 12, 'rabu' => 12, 'kamis' => 12, 'jumat' => 12,
    ]);
});

test('sesi gabungan, pilihan paralel, dan kegiatan terbentuk dengan benar', function () {
    $this->seed(JadwalGanjil20262027Seeder::class);

    // Senin jam ke-1: PAI gabungan X-SCI + X-SOC
    $pai = sesiPada('senin', 1)->first(fn ($s) => $s->mapel?->kode === 'MPL-001');
    expect($pai)->not->toBeNull()
        ->and($pai->kelas->pluck('kode')->sort()->values()->all())->toBe(['X-SCI', 'X-SOC']);

    // Senin jam ke-3: XII punya 3 mapel pilihan paralel
    $pilihan = sesiPada('senin', 3)->where('jenis', JenisSesi::PILIHAN);
    expect($pilihan)->toHaveCount(3)
        ->and($pilihan->pluck('grup_pilihan')->unique()->all())->toBe(['BIO/MATL/BINGTL']);

    // Jumat: kegiatan untuk semua rombel, tanpa mapel
    $ortom = sesiPada('jumat', 1)->sole();
    expect($ortom->jenis)->toBe(JenisSesi::KEGIATAN)
        ->and($ortom->mapel_id)->toBeNull()
        ->and($ortom->kelas)->toHaveCount(6);
});

test('setiap sesi mapel mendapat guru dari pengampu acak', function () {
    $this->seed(JadwalGanjil20262027Seeder::class);

    expect(MasterPengampu::whereNull('guru_id')->count())->toBe(0)
        ->and(JadwalSesi::whereNotNull('mapel_id')->whereNull('guru_id')->count())->toBe(0);
});

test('idempoten dan mempertahankan pengampu yang sudah diatur', function () {
    $this->seed(JadwalGanjil20262027Seeder::class);
    $jumlahSesi = JadwalSesi::count();
    $pengampu = MasterPengampu::orderBy('id')->pluck('guru_id', 'id');

    $this->seed(JadwalGanjil20262027Seeder::class);

    expect(JadwalSesi::count())->toBe($jumlahSesi)
        ->and(MasterPengampu::orderBy('id')->pluck('guru_id', 'id')->all())->toBe($pengampu->all());
});
