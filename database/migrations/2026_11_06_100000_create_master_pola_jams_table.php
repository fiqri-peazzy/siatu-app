<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pola jam = template struktur waktu sekolah per tahun ajaran.
 * Satu pola berisi banyak slot (per hari), sehingga perubahan format jadwal
 * (kurikulum baru, jam masuk berubah, dsb.) cukup dengan membuat pola baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_pola_jams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('master_tahun_ajarans')->cascadeOnDelete();
            $table->string('nama');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('master_slot_jams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pola_jam_id')->constrained('master_pola_jams')->cascadeOnDelete();
            $table->string('hari', 10);                 // senin..sabtu (App\Enums\Hari)
            $table->unsignedTinyInteger('urutan');      // urutan tampil dalam satu hari
            $table->unsignedTinyInteger('jam_ke')->nullable(); // null untuk istirahat/upacara
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('jenis', 30)->default('pelajaran'); // App\Enums\JenisSlotJam
            $table->string('label')->nullable();        // "Istirahat / Shalat Dhuha", "Upacara Bendera"
            $table->timestamps();

            $table->unique(['pola_jam_id', 'hari', 'urutan']);
            $table->index(['pola_jam_id', 'hari', 'jam_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_slot_jams');
        Schema::dropIfExists('master_pola_jams');
    }
};
