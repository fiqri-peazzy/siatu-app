<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pembagian tugas mengajar: guru pengampu untuk satu mapel di satu rombel
 * pada satu tahun ajaran. Admin cukup mengatur di sini; guru pada setiap
 * sesi jadwal disinkronkan otomatis oleh JadwalService.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_pengampus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('master_tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('master_kelas')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('master_mapels')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('master_gurus')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tahun_ajaran_id', 'kelas_id', 'mapel_id']);
            $table->index('guru_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_pengampus');
    }
};
