<?php

use App\Exports\GuruExport;
use App\Exports\GuruTemplateExport;
use App\Exports\JurnalExport;
use App\Exports\RekapAbsensiExport;
use App\Exports\RekapAbsensiSiswaExport;
use App\Exports\RekapAbsensiTendikExport;
use App\Exports\SiswaTemplateExport;
use App\Exports\TendikDataExport;
use App\Exports\TendikTemplateExport;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterTahunAjaran;
use Database\Seeders\MasterGurusTableSeeder;
use Database\Seeders\MasterKelasTableSeeder;
use Database\Seeders\MasterMapelsTableSeeder;
use Database\Seeders\MasterSiswasTableSeeder;
use Database\Seeders\MasterTahunAjaransTableSeeder;
use Database\Seeders\MasterTenagaKependidikansTableSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

uses(DatabaseMigrations::class);

/*
 * Setiap export dibangun sungguhan (render view → PhpSpreadsheet → xlsx).
 * Peringatan PHP (mis. '&' mentah di HTML) menjadi exception di test, sama seperti di browser.
 */
beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    $this->seed([
        MasterTahunAjaransTableSeeder::class, MasterMapelsTableSeeder::class, MasterGurusTableSeeder::class,
        MasterKelasTableSeeder::class, MasterSiswasTableSeeder::class, MasterTenagaKependidikansTableSeeder::class,
    ]);
    Schema::enableForeignKeyConstraints();
});

dataset('export', [
    'data guru' => fn () => new GuruExport,
    'template guru' => fn () => new GuruTemplateExport,
    'template siswa' => fn () => new SiswaTemplateExport,
    'data tendik' => fn () => new TendikDataExport,
    'template tendik' => fn () => new TendikTemplateExport,
    'rekap absensi guru' => fn () => new RekapAbsensiExport('2026-11-01', '2026-11-30', '', ''),
    'rekap absensi tendik' => fn () => new RekapAbsensiTendikExport('2026-11-01', '2026-11-30', '', ''),
    'jurnal mengajar' => fn () => new JurnalExport('2026-11-01', '2026-11-30', null, null, null),
    'rekap absensi siswa (bulanan)' => fn () => new RekapAbsensiSiswaExport(
        MasterTahunAjaran::where('is_active', true)->value('id'), MasterKelas::value('id'), '', 'bulanan', null, '2026-11'),
]);

function bukaXlsx(object $export): \PhpOffice\PhpSpreadsheet\Spreadsheet
{
    $file = tempnam(sys_get_temp_dir(), 'exp').'.xlsx';
    file_put_contents($file, Excel::raw($export, ExcelFormat::XLSX));

    return \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
}

test('export menghasilkan file xlsx yang valid', function (object $export) {
    $isi = Excel::raw($export, ExcelFormat::XLSX);

    expect($isi)->toBeString()->and(substr($isi, 0, 2))->toBe('PK'); // xlsx = arsip zip
})->with('export');

test('setiap export memuat kop resmi sekolah', function (object $export) {
    $teks = collect(bukaXlsx($export)->getActiveSheet()->toArray())->flatten()->filter()->implode(' ');

    expect($teks)->toContain(mb_strtoupper(config('sekolah.nama')));
})->with('export');

test('laporan (bukan template) memuat tanda tangan kepala sekolah', function (object $export) {
    $teks = collect(bukaXlsx($export)->getActiveSheet()->toArray())->flatten()->filter()->implode(' ');

    expect($teks)->toContain('Kepala Sekolah');
})->with([
    'data guru' => fn () => new GuruExport,
    'data tendik' => fn () => new TendikDataExport,
    'rekap absensi guru' => fn () => new RekapAbsensiExport('2026-11-01', '2026-11-30', '', ''),
    'rekap absensi tendik' => fn () => new RekapAbsensiTendikExport('2026-11-01', '2026-11-30', '', ''),
    'jurnal mengajar' => fn () => new JurnalExport('2026-11-01', '2026-11-30', null, null, null),
]);

test('template import: kop di baris 1–4, judul kolom tetap di baris 5 (kompatibel Dapodik)', function (object $export, array $kolomWajib) {
    $baris = bukaXlsx($export)->getActiveSheet()->toArray();

    expect(implode(' ', array_filter($baris[0])))->toContain(mb_strtoupper(config('sekolah.nama')))
        ->and($baris[4])->toContain(...$kolomWajib);
})->with([
    'guru' => [fn () => new GuruTemplateExport, ['Nama', 'NUPTK', 'NIP']],
    'siswa' => [fn () => new SiswaTemplateExport, ['Nama', 'NISN', 'NIK']],
    'tendik' => [fn () => new TendikTemplateExport, ['Nama', 'NUPTK', 'NIP']],
]);
