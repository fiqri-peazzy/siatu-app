<?php

use App\Exports\GuruTemplateExport;
use App\Imports\GuruImport;
use App\Models\Master\MasterGuru;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

uses(DatabaseMigrations::class);

/*
 * Pulang-pergi: unduh template → isi baris data tepat di bawah judul kolom → import.
 * Menangkap regresi posisi baris (bug lama: baris kosong template dibuang sehingga
 * judul kolom ada di baris 3, sedangkan import mulai membaca dari baris 6).
 */
test('template guru yang diisi operator dapat diimport tanpa kehilangan baris pertama', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $file = tempnam(sys_get_temp_dir(), 'tpl').'.xlsx';
    file_put_contents($file, Excel::raw(new GuruTemplateExport, ExcelFormat::XLSX));

    $buku = IOFactory::load($file);
    $sheet = $buku->getActiveSheet();
    $barisJudul = collect($sheet->toArray())->search(fn ($r) => in_array('Nama', $r, true)) + 1;

    // Operator mengisi 2 guru tepat di bawah judul kolom
    foreach ([['Guru Pertama Uji', '1111111111111111'], ['Guru Kedua Uji', '2222222222222222']] as $i => [$nama, $nuptk]) {
        $r = $barisJudul + 1 + $i;
        $sheet->setCellValue("A{$r}", $i + 1)->setCellValue("B{$r}", $nama)->setCellValueExplicit("C{$r}", $nuptk, 's')->setCellValue("D{$r}", 'L');
    }
    (new Xlsx($buku))->save($file);

    Excel::import(new GuruImport, $file);

    expect($barisJudul)->toBe(5)
        ->and(MasterGuru::where('nama', 'Guru Pertama Uji')->exists())->toBeTrue()
        ->and(MasterGuru::where('nama', 'Guru Kedua Uji')->exists())->toBeTrue();

    @unlink($file);
});
