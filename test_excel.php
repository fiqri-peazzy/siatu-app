<?php
require 'vendor/autoload.php';

function readHeaders($file) {
    echo "--- $file ---\n";
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
    $spreadsheet = $reader->load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    for($i = 0; $i < 6; $i++) {
        if (isset($rows[$i])) {
            print_r($rows[$i]);
        }
    }
}

readHeaders('prd/daftar-guru-SMAS MUHAMMADIYAH GORONTALO-2026-07-09 13_36_37.xlsx');
readHeaders('prd/daftar-tendik-SMAS MUHAMMADIYAH GORONTALO-2026-07-09 13_36_42.xlsx');
