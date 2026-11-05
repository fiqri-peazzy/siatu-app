<?php

use App\Enums\KategoriAbsensi;
use App\Exports\RekapKehadiranBulananExport;
use App\Models\AbsensiGuru;
use App\Models\Master\MasterGuru;
use App\Services\RekapKehadiranService;
use Carbon\Carbon;
use Database\Seeders\MasterGurusTableSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    $this->seed(MasterGurusTableSeeder::class);
    Schema::enableForeignKeyConstraints();
    Carbon::setTestNow('2026-11-12 16:00'); // Kamis

    $this->guru = MasterGuru::where('status', 'aktif')->orderBy('nama')->firstOrFail();
    $absen = fn ($tgl, $jenis, $jam, $status, $ket = null) => AbsensiGuru::create([
        'guru_id' => $this->guru->id, 'tanggal' => $tgl, 'jenis' => $jenis, 'status' => $status,
        'waktu_absen' => "{$tgl} {$jam}:00", 'keterangan' => $ket,
    ]);
    $absen('2026-11-09', 'masuk', '07:25', 'terlambat');   // Senin — terlambat
    $absen('2026-11-09', 'pulang', '15:05', 'hadir');
    // Selasa 10 Nov: tidak ada catatan → tanpa keterangan
    $absen('2026-11-11', 'masuk', '07:05', 'hadir');       // Rabu — lupa absen pulang
    $absen('2026-11-12', 'masuk', '07:00', 'hadir');       // Kamis — pulang cepat
    $absen('2026-11-12', 'pulang', '11:00', 'pulang_cepat', 'Dinas ke Dikbud');

    $this->rekap = app(RekapKehadiranService::class)->bulanan(KategoriAbsensi::GURU, Carbon::parse('2026-11-01'));
    $this->baris = collect($this->rekap['pegawai'])->firstWhere('nama', $this->guru->nama);
});

afterEach(fn () => Carbon::setTestNow());

test('matriks berisi 30 hari november dengan akhir pekan sebagai hari off', function () {
    expect($this->rekap['hari'])->toHaveCount(30)
        ->and($this->rekap['hari'][6]['off'])->toBeTrue()      // Sabtu 7
        ->and($this->rekap['hari'][7]['off'])->toBeTrue()      // Minggu 8
        ->and($this->rekap['hari'][8]['off'])->toBeFalse();    // Senin 9
});

test('kode harian: terlambat, tanpa keterangan, hadir, pulang cepat, libur, mendatang', function () {
    expect($this->baris['sel'][7])->toBe('L')
        ->and($this->baris['sel'][9])->toBe('T')
        ->and($this->baris['sel'][10])->toBe('A')
        ->and($this->baris['sel'][11])->toBe('H')
        ->and($this->baris['sel'][12])->toBe('PC')
        ->and($this->baris['sel'][13])->toBe(''); // belum terjadi
});

test('detail harian memuat jam datang, jam pulang, dan keterangan', function () {
    $detail = collect($this->baris['detail'])->keyBy(fn ($d) => $d['tanggal']->day);

    expect($detail[9]['datang'])->toBe('07:25')->and($detail[9]['pulang'])->toBe('15:05')
        ->and($detail[10]['keterangan'])->toBe('Tidak hadir tanpa keterangan')
        ->and($detail[11]['keterangan'])->toContain('Tidak absen pulang')
        ->and($detail[12]['keterangan'])->toContain('Pulang cepat: Dinas ke Dikbud')
        ->and($detail->has(7))->toBeFalse(); // hari off tanpa catatan tidak masuk detail
});

test('persentase kehadiran dihitung dari hari efektif yang sudah lewat', function () {
    // Hari kerja 2–12 Nov = 9 hari; hadir (H/T/PC) = 3
    expect($this->baris['efektif'])->toBe(9)
        ->and($this->baris['persen'])->toBe(round(3 / 9 * 100, 1));
});

test('file excel berisi 2 sheet: rekap bulanan & detail harian dengan kop sekolah', function () {
    $isi = Excel::raw(new RekapKehadiranBulananExport(KategoriAbsensi::GURU, Carbon::parse('2026-11-01')), ExcelFormat::XLSX);
    $file = tempnam(sys_get_temp_dir(), 'rekap').'.xlsx';
    file_put_contents($file, $isi);
    $buku = IOFactory::load($file);

    expect($buku->getSheetNames())->toBe(['Rekap Bulanan', 'Detail Harian']);
    $teks = collect($buku->getSheet(0)->toArray())->flatten()->filter()->implode(' ');
    expect($teks)->toContain(mb_strtoupper(config('sekolah.nama')))->toContain('REKAP KEHADIRAN GURU')->toContain('Kepala Sekolah');

    // Sel hari off (Sabtu 7 Nov) berwarna abu-abu
    $sheet = $buku->getSheet(0);
    $barisGuru = collect($sheet->toArray())->search(fn ($r) => in_array($this->guru->nama, $r, true)) + 1;
    $kolomSabtu = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + 7);
    expect($sheet->getStyle($kolomSabtu.$barisGuru)->getFill()->getStartColor()->getARGB())->toBe(RekapKehadiranService::KODE['L'][1]);

    @unlink($file);
});
