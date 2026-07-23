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
        Schema::create('master_jadwal_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('master_tahun_ajarans')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('master_kelas')->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('master_mapels')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('master_gurus')->onDelete('cascade');
            $table->string('hari', 20); // Senin, Selasa, etc.
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('jam_ke')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_jadwal_pelajarans');
    }
};
