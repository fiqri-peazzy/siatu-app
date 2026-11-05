<?php

namespace Database\Seeders;

use App\Services\AturanAbsensiService;
use Illuminate\Database\Seeder;

/**
 * Aturan jendela waktu absensi default (guru & tendik, Senin–Sabtu).
 * Aman di production — tidak menimpa aturan yang sudah diubah admin:
 *   php artisan db:seed --class=AturanAbsensiSeeder --force
 */
class AturanAbsensiSeeder extends Seeder
{
    public function run(AturanAbsensiService $service): void
    {
        $service->pasangDefault();
    }
}
