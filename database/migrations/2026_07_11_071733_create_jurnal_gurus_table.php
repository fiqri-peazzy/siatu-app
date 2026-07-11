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
        Schema::create('jurnal_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('master_gurus')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('master_kelas')->cascadeOnDelete();
            // Since mapels might not be fully migrated yet (in PRD, master_mapels is mentioned but I didn't create it in previous PRD phase A, wait, let me use foreignId('mapel_id')->nullable()), PRD mentions mapel_id.
            $table->foreignId('mapel_id')->nullable();
            $table->date('tanggal');
            $table->tinyInteger('jam_ke');
            $table->text('materi');
            $table->text('kegiatan');
            $table->string('ttd')->nullable();
            $table->string('status', 20)->default('belum_diisi'); // belum_diisi / diisi
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_gurus');
    }
};
