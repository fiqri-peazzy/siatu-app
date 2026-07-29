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
        Schema::create('peminatan_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('master_siswas')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('master_mapels')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('master_tahun_ajarans')->cascadeOnDelete();
            $table->timestamps();

            // A student should only be assigned once per mapel per tahun ajaran
            $table->unique(['siswa_id', 'mapel_id', 'tahun_ajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminatan_siswas');
    }
};
