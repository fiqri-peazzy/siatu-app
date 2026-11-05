<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kode rombel singkat (mis. X-SCI, XI-SOC) sebagai kunci stabil untuk
 * import jadwal dari Excel dan referensi antar-modul.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_kelas', function (Blueprint $table) {
            $table->string('kode', 20)->nullable()->after('id');
            $table->unique(['tahun_ajaran_id', 'kode']);
        });
    }

    public function down(): void
    {
        Schema::table('master_kelas', function (Blueprint $table) {
            $table->dropUnique(['tahun_ajaran_id', 'kode']);
            $table->dropColumn('kode');
        });
    }
};
