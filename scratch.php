<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;

$publicDisk = Storage::disk('public');
$r2Disk = Storage::disk('r2');

$files = $publicDisk->allFiles();
$uploaded = 0;

foreach ($files as $file) {
    if ($file === '.gitignore') continue;
    
    try {
        $contents = $publicDisk->get($file);
        $r2Disk->put($file, $contents);
        echo "Uploaded: $file\n";
        $uploaded++;
    } catch (\Exception $e) {
        echo "Failed: $file - " . $e->getMessage() . "\n";
    }
}
echo "Total uploaded: $uploaded\n";
