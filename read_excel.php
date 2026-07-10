<?php
require 'vendor/autoload.php';
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('prd/datasiswa.xlsx');
$worksheet = $spreadsheet->getActiveSheet();
$highestColumn = $worksheet->getHighestColumn();

$data = $worksheet->rangeToArray('A5:' . $highestColumn . '6');
print_r($data);
