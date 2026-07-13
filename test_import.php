<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $file = 'prd/daftar-guru-SMAS MUHAMMADIYAH GORONTALO-2026-07-09 13_36_37.xlsx';
    Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\GuruImport, $file);
    echo "Success!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
