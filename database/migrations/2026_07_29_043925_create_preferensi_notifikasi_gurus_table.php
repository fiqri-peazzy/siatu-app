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
        Schema::create('preferensi_notifikasi_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('master_gurus')->cascadeOnDelete();
            $table->boolean('reminder_jurnal_aktif')->default(true);
            $table->string('kanal_notifikasi', 20)->default('wa_saja'); // wa_saja, wa_dan_inapp
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferensi_notifikasi_gurus');
    }
};
