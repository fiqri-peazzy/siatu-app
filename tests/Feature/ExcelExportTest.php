<?php

use App\Exports\GuruTemplateExport;
use App\Exports\SiswaTemplateExport;
use App\Exports\TendikTemplateExport;

test('siswa template export contains correct headers and structure', function () {
    $export = new SiswaTemplateExport();
    $data = $export->array();

    expect($data)->toBeArray()
        ->and($data[0][0])->toBe('Daftar Peserta Didik')
        ->and($data[4])->toContain('Nama', 'NISN', 'NIK');
});

test('guru template export contains correct headers and structure', function () {
    $export = new GuruTemplateExport();
    $data = $export->array();

    expect($data)->toBeArray()
        ->and($data[0][0])->toBe('Daftar Guru')
        ->and($data[4])->toContain('Nama', 'NUPTK', 'NIP');
});

test('tendik template export contains correct headers and structure', function () {
    $export = new TendikTemplateExport();
    $data = $export->array();

    expect($data)->toBeArray()
        ->and($data[0][0])->toBe('Daftar Tenaga Kependidikan')
        ->and($data[4])->toContain('Nama', 'NUPTK', 'NIP');
});

