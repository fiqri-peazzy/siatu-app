<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;

try {
    echo "Starting import...\n";
    Excel::import(new SiswaImport, 'prd/datasiswa.xlsx');
    echo "Import success!\n";
    echo "Siswa count: " . \App\Models\Master\MasterSiswa::count() . "\n";
} catch (\Exception $e) {
    echo "Import failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
