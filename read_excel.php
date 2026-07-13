<?php
require 'vendor/autoload.php';
$s = \PhpOffice\PhpSpreadsheet\IOFactory::load('prd/daftar-tendik-SMAS MUHAMMADIYAH GORONTALO-2026-07-09 13_36_42.xlsx');
print_r(array_slice($s->getActiveSheet()->toArray(), 0, 5));
$s2 = \PhpOffice\PhpSpreadsheet\IOFactory::load('prd/daftar-guru-SMAS MUHAMMADIYAH GORONTALO-2026-07-09 13_36_37.xlsx');
print_r(array_slice($s2->getActiveSheet()->toArray(), 0, 5));
