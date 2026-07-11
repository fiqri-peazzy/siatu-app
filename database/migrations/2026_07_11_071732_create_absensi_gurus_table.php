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
        Schema::create('absensi_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('master_gurus')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis', 10); // masuk / pulang
            $table->string('status', 20); // hadir / izin / sakit / alfa / dinas_luar / terlambat
            $table->timestamp('waktu_absen');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('jarak_dari_sekolah_meter')->nullable();
            $table->string('foto_path')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('file_izin')->nullable();
            $table->string('device_info')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_gurus');
    }
};
