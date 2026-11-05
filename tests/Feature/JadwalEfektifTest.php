<?php

use App\Enums\AksiPengecualianJadwal;
use App\Models\JadwalPengecualian;
use App\Models\Master\MasterSlotJam;
use App\Services\JadwalService;
use Carbon\Carbon;
use Database\Seeders\JadwalGanjil20262027Seeder;
use Database\Seeders\MasterGurusTableSeeder;
use Database\Seeders\MasterKelasTableSeeder;
use Database\Seeders\MasterMapelsTableSeeder;
use Database\Seeders\MasterTahunAjaransTableSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;

uses(DatabaseMigrations::class);

const SENIN = '2026-11-09';
const SELASA = '2026-11-10';

beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    $this->seed([
        MasterTahunAjaransTableSeeder::class,
        MasterMapelsTableSeeder::class,
        MasterGurusTableSeeder::class,
        MasterKelasTableSeeder::class,
    ]);
    Schema::enableForeignKeyConstraints();

    $this->seed(JadwalGanjil20262027Seeder::class);
    $this->service = app(JadwalService::class);
});

function jamKe($jadwal, int $jamKe): array
{
    return $jadwal->first(fn ($i) => $i['jam_ke'] === $jamKe);
}

function slotUpacara(): MasterSlotJam
{
    return MasterSlotJam::where('hari', 'senin')->where('jenis', 'upacara')->sole();
}

test('senin normal: upacara menggeser jam ke-1, jam ke-2 tetap', function () {
    $jadwal = $this->service->jadwalEfektif(Carbon::parse(SENIN));

    expect(jamKe($jadwal, 1)['status'])->toBe('tergeser')
        ->and(jamKe($jadwal, 2)['status'])->toBe('normal')
        ->and(jamKe($jadwal, 2)['mulai'])->toBe('07:45');
});

test('upacara ditiadakan: jam ke-1 berjalan normal', function () {
    JadwalPengecualian::create([
        'tanggal' => SENIN,
        'slot_jam_id' => slotUpacara()->id,
        'aksi' => AksiPengecualianJadwal::TIADAKAN,
        'keterangan' => 'Hujan',
    ]);

    $jadwal = $this->service->jadwalEfektif(Carbon::parse(SENIN));

    expect(jamKe($jadwal, 1)['status'])->toBe('normal')
        ->and($jadwal->firstWhere('jenis', 'upacara')['status'])->toBe('ditiadakan');
});

test('upacara molor sampai 08:00: jam ke-1 tergeser, jam ke-2 terpotong mulai 08:00', function () {
    JadwalPengecualian::create([
        'tanggal' => SENIN,
        'slot_jam_id' => slotUpacara()->id,
        'aksi' => AksiPengecualianJadwal::UBAH_WAKTU,
        'jam_mulai' => '07:00',
        'jam_selesai' => '08:00',
    ]);

    $jadwal = $this->service->jadwalEfektif(Carbon::parse(SENIN));

    expect(jamKe($jadwal, 1)['status'])->toBe('tergeser')
        ->and(jamKe($jadwal, 2)['status'])->toBe('terpotong')
        ->and(jamKe($jadwal, 2)['mulai'])->toBe('08:00')
        ->and(jamKe($jadwal, 2)['selesai'])->toBe('08:15');
});

test('kegiatan sisipan di hari lain menggeser jam yang tertutup', function () {
    JadwalPengecualian::create([
        'tanggal' => SELASA,
        'aksi' => AksiPengecualianJadwal::TAMBAH,
        'label' => 'Upacara HUT Sekolah',
        'jam_mulai' => '07:15',
        'jam_selesai' => '08:15',
    ]);

    $jadwal = $this->service->jadwalEfektif(Carbon::parse(SELASA));

    expect(jamKe($jadwal, 1)['status'])->toBe('tergeser')
        ->and(jamKe($jadwal, 2)['status'])->toBe('tergeser')
        ->and(jamKe($jadwal, 3)['status'])->toBe('normal')
        ->and($jadwal->firstWhere('status', 'sisipan')['label'])->toBe('Upacara HUT Sekolah');
});

test('pengecualian hanya berlaku pada tanggalnya', function () {
    JadwalPengecualian::create([
        'tanggal' => SENIN,
        'slot_jam_id' => slotUpacara()->id,
        'aksi' => AksiPengecualianJadwal::TIADAKAN,
    ]);

    $seninDepan = $this->service->jadwalEfektif(Carbon::parse(SENIN)->addWeek());

    expect(jamKe($seninDepan, 1)['status'])->toBe('tergeser');
});
