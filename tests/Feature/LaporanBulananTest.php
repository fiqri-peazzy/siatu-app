<?php

use App\Exports\LaporanAbsensiSiswaBulananExport;
use App\Exports\LaporanJurnalBulananExport;
use App\Models\AbsensiSiswa;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterSiswa;
use App\Models\User;
use App\Services\JadwalService;
use App\Services\JurnalService;
use App\Services\LaporanBulananService;
use Carbon\Carbon;
use Database\Seeders\JadwalGanjil20262027Seeder;
use Database\Seeders\MasterGurusTableSeeder;
use Database\Seeders\MasterKelasTableSeeder;
use Database\Seeders\MasterMapelsTableSeeder;
use Database\Seeders\MasterSiswasTableSeeder;
use Database\Seeders\MasterTahunAjaransTableSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    $this->seed([
        RolesAndPermissionsSeeder::class,
        MasterTahunAjaransTableSeeder::class,
        MasterMapelsTableSeeder::class,
        MasterGurusTableSeeder::class,
        MasterKelasTableSeeder::class,
        MasterSiswasTableSeeder::class,
    ]);
    Schema::enableForeignKeyConstraints();
    $this->seed(JadwalGanjil20262027Seeder::class);
    Carbon::setTestNow('2026-11-13 16:00'); // Jumat

    $this->kelas = MasterKelas::whereHas('siswa', fn ($q) => $q->where('status', 'aktif'))->orderBy('id')->firstOrFail();
    $this->siswa = MasterSiswa::where('kelas_id', $this->kelas->id)->where('status', 'aktif')->orderBy('nama')->take(2)->get();
});

afterEach(fn () => Carbon::setTestNow());

/** Muat file xlsx hasil export ke PhpSpreadsheet */
function bukaXlsxLaporan(object $export): \PhpOffice\PhpSpreadsheet\Spreadsheet
{
    $file = tempnam(sys_get_temp_dir(), 'lap').'.xlsx';
    file_put_contents($file, Excel::raw($export, ExcelFormat::XLSX));

    return IOFactory::load($file);
}

/** Cari baris yang kolom B-nya berisi nama */
function barisNamaLaporan(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $nama): ?int
{
    foreach ($ws->getRowIterator() as $row) {
        if ($ws->getCell('B'.$row->getRowIndex())->getValue() === $nama) {
            return $row->getRowIndex();
        }
    }

    return null;
}

test('kode harian siswa = status terbanyak, jam alfa tetap terhitung', function () {
    [$a, $b] = $this->siswa;
    $catat = fn ($s, $tgl, $jam, $status) => AbsensiSiswa::create(['siswa_id' => $s->id, 'kelas_id' => $this->kelas->id, 'tanggal' => $tgl, 'jam_ke' => $jam, 'status' => $status, 'dicatat_oleh' => User::factory()->create()->id]);

    // Siswa A — Selasa 10: 3 jam hadir + 1 jam alfa (bolos 1 jam) → H, jam alfa 1
    foreach ([1, 2, 3] as $j) { $catat($a, '2026-11-10', $j, 'hadir'); }
    $catat($a, '2026-11-10', 4, 'alfa');
    // Siswa A — Rabu 11: alfa penuh → A
    foreach ([1, 2] as $j) { $catat($a, '2026-11-11', $j, 'alfa'); }
    // Siswa B — Selasa 10: 1 hadir 1 sakit (seri) → S (lebih berat)
    $catat($b, '2026-11-10', 1, 'hadir');
    $catat($b, '2026-11-10', 2, 'sakit');

    $data = app(LaporanBulananService::class)->absensiSiswa(Carbon::parse('2026-11-01'));
    $kelas = collect($data['kelas'])->firstWhere('nama', $this->kelas->nama);
    $rA = collect($kelas['siswa'])->firstWhere('nama', $a->nama);
    $rB = collect($kelas['siswa'])->firstWhere('nama', $b->nama);

    expect($rA['sel'][10])->toBe('H')
        ->and($rA['sel'][11])->toBe('A')
        ->and($rA['jam_alfa'])->toBe(3)
        ->and($rA['total'])->toBe(['H' => 1, 'I' => 0, 'S' => 0, 'A' => 1])
        ->and($rA['persen'])->toBe(50.0)
        ->and($rB['sel'][10])->toBe('S')
        ->and($rB['sel'][12])->toBe('')          // hari sekolah tanpa catatan: kosong, bukan alfa
        ->and($rA['sel'][8])->toBe('L')           // Minggu
        ->and($data['hari'][8]['off'])->toBeTrue()
        ->and($data['hari'][9]['off'])->toBeFalse();
});

test('excel absensi siswa: sheet ringkasan + sheet per rombel berkop', function () {
    AbsensiSiswa::create(['siswa_id' => $this->siswa[0]->id, 'kelas_id' => $this->kelas->id, 'tanggal' => '2026-11-10', 'jam_ke' => 1, 'status' => 'izin', 'dicatat_oleh' => User::factory()->create()->id]);

    $xlsx = bukaXlsxLaporan(new LaporanAbsensiSiswaBulananExport(Carbon::parse('2026-11-01')));
    $nama = $xlsx->getSheetNames();
    $jumlahRombel = MasterKelas::whereHas('siswa', fn ($q) => $q->where('status', 'aktif'))->count();

    expect($nama[0])->toBe('Ringkasan')
        ->and(count($nama))->toBe(1 + $jumlahRombel)
        ->and($nama)->toContain($this->kelas->nama);

    $ringkasan = $xlsx->getSheetByName('Ringkasan');
    expect((string) $ringkasan->getCell('A1')->getValue())->not->toBe('');  // kop
    $r = barisNamaLaporan($ringkasan, $this->kelas->nama);
    expect($r)->not->toBeNull()->and($ringkasan->getCell("G{$r}")->getValue())->toBe(1); // 1 izin

    $matriks = $xlsx->getSheetByName($this->kelas->nama);
    $r = barisNamaLaporan($matriks, $this->siswa[0]->nama);
    // kolom D = tanggal 1 → tanggal 10 = kolom M
    expect($matriks->getCell("M{$r}")->getValue())->toBe('I');
    $semua = collect($matriks->toArray())->flatten()->filter()->implode(' ');
    expect($semua)->toContain('Wali Kelas')->toContain('Kepala Sekolah');
});

test('rekap jurnal menghitung pertemuan wajib vs terisi per guru & excel terbentuk', function () {
    $jadwal = app(JadwalService::class);
    $selasa = Carbon::parse('2026-11-10');

    // Guru pertama yang punya pertemuan pada Selasa
    $guruId = \App\Models\JadwalSesi::whereNotNull('guru_id')->whereHas('slot', fn ($q) => $q->where('hari', 'selasa'))->value('guru_id');
    $blok = collect($jadwal->jadwalGuruHarian($guruId, $selasa)['items'])->where('tipe', 'blok')->where('lanjutan', false)->where('jurnal', '!=', 'tidak_wajib')->first();
    app(JurnalService::class)->simpan($guruId, 1, $blok['jurnal_sesi_ids'], $selasa, ['materi' => 'Teks eksposisi', 'kegiatan' => 'Diskusi'], []);

    $data = app(LaporanBulananService::class)->jurnal(Carbon::parse('2026-11-01'));
    $guru = collect($data['guru'])->firstWhere('nama', \App\Models\Master\MasterGuru::find($guruId)->nama);

    expect($guru['wajib'])->toBeGreaterThan(1)
        ->and($guru['diisi'])->toBe(1)
        ->and($guru['belum'] + $guru['sebagian'])->toBe($guru['wajib'] - 1)
        ->and($data['detail'])->toHaveCount(1)
        ->and($data['detail'][0]['materi'])->toBe('Teks eksposisi');

    $xlsx = bukaXlsxLaporan(new LaporanJurnalBulananExport(Carbon::parse('2026-11-01')));
    expect($xlsx->getSheetNames())->toBe(['Rekap per Guru', 'Daftar Jurnal']);
    $daftar = collect($xlsx->getSheetByName('Daftar Jurnal')->toArray())->flatten()->filter()->implode(' ');
    expect($daftar)->toContain('Teks eksposisi');
});

test('pusat laporan: hanya pimpinan/TU, unduhan berfungsi, bulan depan ditolak', function () {
    $kepsek = User::factory()->create();
    $kepsek->assignRole('kepala-sekolah');
    $guru = User::factory()->create();
    $guru->assignRole('guru');

    $this->actingAs($guru)->get(route('laporan.index'))->assertForbidden();
    $this->actingAs($kepsek)->get(route('laporan.index'))->assertOk()->assertSee('Pusat Laporan');

    foreach (['kehadiran-guru', 'kehadiran-tendik', 'absensi-siswa', 'jurnal'] as $jenis) {
        $this->actingAs($kepsek)->get(route('laporan.unduh', ['jenis' => $jenis, 'bulan' => '2026-11']))
            ->assertOk()->assertDownload();
    }
    $this->actingAs($kepsek)->get(route('laporan.unduh', ['jenis' => 'jurnal', 'bulan' => '2026-12']))->assertSessionHasErrors('bulan');
    $this->actingAs($kepsek)->get(route('laporan.unduh', ['jenis' => 'ngawur', 'bulan' => '2026-11']))->assertNotFound();
});
