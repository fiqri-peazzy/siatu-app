<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Exports\SiswaTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

try {
    echo "Starting export...\n";
    Excel::store(new SiswaTemplateExport, 'test_template.xlsx', 'local');
    echo "Export success!\n";
} catch (\Exception $e) {
    echo "Export failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
