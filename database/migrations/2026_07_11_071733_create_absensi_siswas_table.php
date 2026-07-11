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
        Schema::create('absensi_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('master_siswas')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('master_kelas')->cascadeOnDelete();
            $table->date('tanggal');
            $table->tinyInteger('jam_ke');
            $table->string('status', 20); // hadir / izin / sakit / alfa
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_siswas');
    }
};
