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
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('no_agenda', 50)->unique();
            $table->foreignId('kategori_surat_id')->constrained('master_kategori_surats')->onDelete('restrict');
            $table->foreignId('klasifikasi_arsip_id')->nullable()->constrained('master_klasifikasi_arsips')->onDelete('set null');
            $table->date('tanggal_terima');
            $table->date('tanggal_surat');
            $table->string('asal_instansi');
            $table->string('perihal');
            $table->string('sifat', 20)->default('biasa');
            $table->string('file_path')->nullable();
            $table->string('status', 30)->default('baru');
            $table->foreignId('input_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuks');
    }
};
