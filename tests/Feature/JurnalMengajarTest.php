<?php

use App\Enums\AksiPengecualianJadwal;
use App\Enums\JenisSesi;
use App\Models\AbsensiSiswa;
use App\Models\JadwalPengecualian;
use App\Models\JadwalSesi;
use App\Models\JurnalGuru;
use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterSlotJam;
use App\Services\JadwalService;
use App\Services\JurnalService;
use Carbon\Carbon;
use Database\Seeders\JadwalGanjil20262027Seeder;
use Database\Seeders\MasterGurusTableSeeder;
use Database\Seeders\MasterKelasTableSeeder;
use Database\Seeders\MasterMapelsTableSeeder;
use Database\Seeders\MasterSiswasTableSeeder;
use Database\Seeders\MasterTahunAjaransTableSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;

uses(DatabaseMigrations::class);

const SENIN_JURNAL = '2026-11-09';
const SELASA_JURNAL = '2026-11-10';

beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    $this->seed([
        MasterTahunAjaransTableSeeder::class,
        MasterMapelsTableSeeder::class,
        MasterGurusTableSeeder::class,
        MasterKelasTableSeeder::class,
        MasterSiswasTableSeeder::class,
    ]);
    Schema::enableForeignKeyConstraints();
    $this->seed(JadwalGanjil20262027Seeder::class);

    // Waktu sekarang setelah tanggal uji (jurnal hanya untuk hari ini / yang sudah lewat)
    Carbon::setTestNow('2026-11-13 16:00');

    $this->jadwal = app(JadwalService::class);
    $this->jurnal = app(JurnalService::class);
});

/** Sesi pembelajaran (bukan kegiatan) pada hari & jam tertentu */
function sesiMengajar(string $hari, int $jamKe): JadwalSesi
{
    return JadwalSesi::query()
        ->where('jenis', JenisSesi::PEMBELAJARAN)
        ->whereNotNull('guru_id')
        ->whereHas('slot', fn ($q) => $q->where('hari', $hari)->where('jam_ke', $jamKe))
        ->with('kelas', 'slot')
        ->firstOrFail();
}

test('timeline harian guru memuat blok mengajar, istirahat, dan upacara', function () {
    $sesi = sesiMengajar('senin', 4);
    $data = $this->jadwal->jadwalGuruHarian($sesi->guru_id, Carbon::parse(SENIN_JURNAL), Carbon::parse(SENIN_JURNAL.' 06:00'));

    $items = collect($data['items']);
    expect($data['hari_kerja'])->toBeTrue()
        ->and($items->first()['label'])->toBe('Upacara Bendera')
        ->and($items->where('tipe', 'jeda')->pluck('label'))->toContain('Istirahat / Shalat Dhuha', 'ISHOMA')
        ->and($items->where('tipe', 'blok')->flatMap(fn ($b) => $b['sesi_ids']))->toContain($sesi->id)
        ->and($items->where('tipe', 'blok')->every(fn ($b) => $b['fase'] === 'mendatang'))->toBeTrue();
});

test('sesi berurutan digabung; istirahat di tengah memisahkan blok', function () {
    // Senin X-SCI: Fisika jam 4–5, istirahat 09.45–10.15, Fisika jam 6
    $sesi = JadwalSesi::query()->whereHas('slot', fn ($q) => $q->where('hari', 'senin')->where('jam_ke', 4))
        ->whereHas('kelas', fn ($q) => $q->where('kode', 'X-SCI'))->whereHas('mapel', fn ($q) => $q->where('kode', 'MPL-008'))->sole();

    $items = collect($this->jadwal->jadwalGuruHarian($sesi->guru_id, Carbon::parse(SENIN_JURNAL))['items']);
    $fisika = $items->where('tipe', 'blok')->where('mapel_id', $sesi->mapel_id)
        ->filter(fn ($b) => in_array($sesi->kelas->first()->id, $b['kelas_ids'], true))->values();

    expect($fisika)->toHaveCount(2)
        ->and($fisika[0]['jam_ke'])->toBe([4, 5])
        ->and([$fisika[0]['mulai'], $fisika[0]['selesai']])->toBe(['08:45', '09:45'])
        ->and($fisika[1]['jam_ke'])->toBe([6]);

    // ...tetapi tetap SATU pertemuan untuk jurnal
    $semua = array_merge($fisika[0]['sesi_ids'], $fisika[1]['sesi_ids']);
    expect($fisika[0]['jurnal_sesi_ids'])->toBe($semua)
        ->and($fisika[1]['jurnal_sesi_ids'])->toBe($semua)
        ->and($fisika[0]['lanjutan'])->toBeFalse()
        ->and($fisika[1]['lanjutan'])->toBeTrue();

    $this->jurnal->simpan($sesi->guru_id, 1, $semua, Carbon::parse(SENIN_JURNAL), ['materi' => 'Gerak lurus', 'kegiatan' => 'Praktikum'], []);
    $setelah = collect($this->jadwal->jadwalGuruHarian($sesi->guru_id, Carbon::parse(SENIN_JURNAL))['items'])
        ->where('tipe', 'blok')->filter(fn ($b) => $b['jurnal_sesi_ids'] === $semua);
    expect($setelah->pluck('jurnal')->unique()->all())->toBe(['diisi']);
});

test('simpan jurnal satu blok mencatat jurnal & absensi per jam per rombel lalu status menjadi diisi', function () {
    $sesi = sesiMengajar('selasa', 7);
    $tanggal = Carbon::parse(SELASA_JURNAL);
    $cariBlok = fn () => collect($this->jadwal->jadwalGuruHarian($sesi->guru_id, $tanggal)['items'])
        ->first(fn ($i) => $i['tipe'] === 'blok' && in_array($sesi->id, $i['sesi_ids'], true));
    $sesiIds = $cariBlok()['sesi_ids'];

    $siswa = $this->jurnal->siapkan($sesi->guru_id, $sesiIds, $tanggal)['siswa'];
    $absensi = $siswa->values()->mapWithKeys(fn ($s, $i) => [$s->id => $i === 0 ? 'sakit' : 'hadir'])->all();

    $this->jurnal->simpan($sesi->guru_id, 1, $sesiIds, $tanggal,
        ['kd_cp' => null, 'materi' => 'Persamaan kuadrat', 'kegiatan' => 'Ceramah, latihan soal'], $absensi);

    $jumlahBaris = count($sesiIds) * $sesi->kelas->count();
    $jurnal = JurnalGuru::whereIn('jadwal_sesi_id', $sesiIds)->get();
    expect($jurnal)->toHaveCount($jumlahBaris)
        ->and($jurnal->first()->materi)->toBe('Persamaan kuadrat')
        ->and(AbsensiSiswa::whereIn('jadwal_sesi_id', $sesiIds)->count())->toBe($siswa->count() * count($sesiIds))
        ->and($jurnal->where('jadwal_sesi_id', $sesiIds[0])->sum('jumlah_sakit'))->toBe($siswa->isEmpty() ? 0 : 1)
        ->and($cariBlok()['jurnal'])->toBe('diisi');
});

test('simpan ulang memperbarui jurnal, bukan menggandakan', function () {
    $sesi = sesiMengajar('selasa', 7);
    $tanggal = Carbon::parse(SELASA_JURNAL);
    foreach (['Materi pertama', 'Materi revisi'] as $materi) {
        $this->jurnal->simpan($sesi->guru_id, 1, [$sesi->id], $tanggal, ['materi' => $materi, 'kegiatan' => 'Diskusi kelompok'], []);
    }

    // satu baris per rombel untuk sesi ini (tidak tergandakan oleh simpan ulang)
    expect(JurnalGuru::where('jadwal_sesi_id', $sesi->id)->count())->toBe($sesi->kelas->count())
        ->and(JurnalGuru::where('jadwal_sesi_id', $sesi->id)->value('materi'))->toBe('Materi revisi');
    expect($this->jurnal->siapkan($sesi->guru_id, [$sesi->id], $tanggal)['jurnal'])->not->toBeNull();
});

test('guru lain tidak bisa mengisi jurnal sesi ini', function () {
    $sesi = sesiMengajar('selasa', 7);
    $guruLain = \App\Models\Master\MasterGuru::where('id', '!=', $sesi->guru_id)->value('id');

    $this->jurnal->siapkan($guruLain, [$sesi->id], Carbon::parse(SELASA_JURNAL));
})->throws(AuthorizationException::class);

test('jam yang tergeser upacara tidak bisa diisi; bila upacara ditiadakan bisa', function () {
    $sesi = sesiMengajar('senin', 1);
    $senin = Carbon::parse(SENIN_JURNAL);

    expect(fn () => $this->jurnal->siapkan($sesi->guru_id, [$sesi->id], $senin))
        ->toThrow(InvalidArgumentException::class, 'tergeser');

    JadwalPengecualian::create([
        'tanggal' => SENIN_JURNAL,
        'slot_jam_id' => MasterSlotJam::where('hari', 'senin')->where('jenis', 'upacara')->value('id'),
        'aksi' => AksiPengecualianJadwal::TIADAKAN,
    ]);

    expect($this->jurnal->siapkan($sesi->guru_id, [$sesi->id], $senin)['sesis'])->toHaveCount(1);
});

test('sesi di hari lain ditolak', function () {
    $sesi = sesiMengajar('selasa', 7);

    $this->jurnal->siapkan($sesi->guru_id, [$sesi->id], Carbon::parse(SENIN_JURNAL));
})->throws(InvalidArgumentException::class, 'tidak sesuai dengan hari');

test('mapel pilihan hanya menampilkan siswa peminat', function () {
    $pilihan = JadwalSesi::where('jenis', JenisSesi::PILIHAN)->whereNotNull('guru_id')
        ->whereHas('slot', fn ($q) => $q->where('hari', 'senin')->where('jam_ke', 3))->with('kelas', 'slot.polaJam')->firstOrFail();
    $kelasIds = $pilihan->kelas->pluck('id');
    $peminat = MasterSiswa::whereIn('kelas_id', $kelasIds)->where('status', 'aktif')->limit(2)->pluck('id');
    foreach ($peminat as $id) {
        \Illuminate\Support\Facades\DB::table('peminatan_siswas')->insert([
            'siswa_id' => $id, 'mapel_id' => $pilihan->mapel_id, 'tahun_ajaran_id' => $pilihan->slot->polaJam->tahun_ajaran_id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    $siswa = $this->jurnal->siapkan($pilihan->guru_id, [$pilihan->id], Carbon::parse(SENIN_JURNAL))['siswa'];

    expect($siswa->pluck('id')->sort()->values()->all())->toBe($peminat->sort()->values()->all());
});

afterEach(fn () => Carbon::setTestNow());

test('jurnal dapat diisi untuk hari mengajar yang sudah lewat', function () {
    Carbon::setTestNow('2026-11-20 08:00'); // seminggu lebih setelah Selasa 10 Nov
    $sesi = sesiMengajar('selasa', 7);

    expect($this->jurnal->siapkan($sesi->guru_id, [$sesi->id], Carbon::parse(SELASA_JURNAL))['sesis'])->toHaveCount(1);
});

test('jurnal tidak dapat diisi untuk tanggal yang belum terjadi', function () {
    Carbon::setTestNow('2026-11-06 08:00');
    $sesi = sesiMengajar('selasa', 7);

    $this->jurnal->siapkan($sesi->guru_id, [$sesi->id], Carbon::parse(SELASA_JURNAL));
})->throws(InvalidArgumentException::class, 'belum terjadi');
