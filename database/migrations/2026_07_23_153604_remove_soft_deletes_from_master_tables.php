<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'master_tahun_ajarans',
            'master_gurus',
            'master_kelas',
            'master_siswas',
            'master_kategori_surats',
            'master_mapels',
            'master_klasifikasi_arsips',
            'master_tenaga_kependidikans',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                if (Schema::hasColumn($t->getTable(), 'deleted_at')) {
                    $t->dropSoftDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'master_tahun_ajarans',
            'master_gurus',
            'master_kelas',
            'master_siswas',
            'master_kategori_surats',
            'master_mapels',
            'master_klasifikasi_arsips',
            'master_tenaga_kependidikans',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->softDeletes();
            });
        }
    }
};
