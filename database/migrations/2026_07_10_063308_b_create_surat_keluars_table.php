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
        Schema::create('surat_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat', 50)->nullable();
            $table->foreignId('kategori_surat_id')->constrained('master_kategori_surats')->onDelete('restrict');
            $table->foreignId('klasifikasi_arsip_id')->nullable()->constrained('master_klasifikasi_arsips')->onDelete('set null');
            $table->foreignId('surat_masuk_id')->nullable()->constrained('surat_masuks')->onDelete('set null');
            $table->date('tanggal');
            $table->string('tujuan');
            $table->string('perihal');
            $table->text('isi_ringkas');
            $table->string('file_draft_path')->nullable();
            $table->string('file_final_path')->nullable();
            $table->string('status', 30)->default('draft');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->text('catatan_revisi')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disetujui_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluars');
    }
};
