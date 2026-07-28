<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_jadwal_pelajarans', function (Blueprint $table) {
            $table->string('kategori_kegiatan')->default('pembelajaran')->after('guru_id');
            $table->string('keterangan_kegiatan')->nullable()->after('kategori_kegiatan');
            
            // Allow null on mapel_id, guru_id, kelas_id, jam_ke
            $table->foreignId('kelas_id')->nullable()->change();
            $table->foreignId('mapel_id')->nullable()->change();
            $table->foreignId('guru_id')->nullable()->change();
            $table->integer('jam_ke')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('master_jadwal_pelajarans', function (Blueprint $table) {
            $table->dropColumn('kategori_kegiatan');
            $table->dropColumn('keterangan_kegiatan');
            
            // Reverting to not null might cause issues if data exists, but for down migration:
            $table->foreignId('kelas_id')->nullable(false)->change();
            $table->foreignId('mapel_id')->nullable(false)->change();
            $table->foreignId('guru_id')->nullable(false)->change();
            $table->integer('jam_ke')->nullable(false)->change();
        });
    }
};
