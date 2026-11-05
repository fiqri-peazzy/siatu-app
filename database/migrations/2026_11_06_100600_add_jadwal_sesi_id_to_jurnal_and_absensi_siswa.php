<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menautkan jurnal & absensi siswa ke sesi jadwal v2 (jadwal_sesis).
 * Nullable: data lama (dari master_jadwal_pelajarans) tetap valid.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_gurus', function (Blueprint $table) {
            $table->foreignId('jadwal_sesi_id')->nullable()->after('mapel_id')
                ->constrained('jadwal_sesis')->nullOnDelete();
            $table->index(['guru_id', 'tanggal']);
        });

        Schema::table('absensi_siswas', function (Blueprint $table) {
            $table->foreignId('jadwal_sesi_id')->nullable()->after('kelas_id')
                ->constrained('jadwal_sesis')->nullOnDelete();
            $table->index(['kelas_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::table('absensi_siswas', function (Blueprint $table) {
            $table->dropIndex(['kelas_id', 'tanggal']);
            $table->dropConstrainedForeignId('jadwal_sesi_id');
        });

        Schema::table('jurnal_gurus', function (Blueprint $table) {
            $table->dropIndex(['guru_id', 'tanggal']);
            $table->dropConstrainedForeignId('jadwal_sesi_id');
        });
    }
};
