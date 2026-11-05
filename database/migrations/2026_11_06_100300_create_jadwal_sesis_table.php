<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sesi = isi satu slot jam untuk satu/lebih rombel.
 *  - Sesi gabungan  : satu sesi, banyak rombel (pivot jadwal_sesi_kelas)
 *  - Sesi pilihan   : beberapa sesi paralel di slot yang sama (grup_pilihan sama),
 *                     peserta ditentukan dari peminatan_siswas
 *  - Sesi kegiatan  : tahfidz, pembinaan ORTOM, ekstrakurikuler (tanpa mapel)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_sesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_jam_id')->constrained('master_slot_jams')->cascadeOnDelete();
            $table->string('jenis', 30)->default('pembelajaran'); // App\Enums\JenisSesi
            $table->foreignId('mapel_id')->nullable()->constrained('master_mapels')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('master_gurus')->nullOnDelete();
            $table->boolean('guru_manual')->default(false); // true = tidak ditimpa sinkron pengampu
            $table->string('label')->nullable();          // nama kegiatan / catatan
            $table->string('grup_pilihan', 50)->nullable(); // mis. "BIO/MAT/BINGTL"
            $table->timestamps();

            $table->index(['slot_jam_id', 'jenis']);
            $table->index('guru_id');
        });

        Schema::create('jadwal_sesi_kelas', function (Blueprint $table) {
            $table->foreignId('jadwal_sesi_id')->constrained('jadwal_sesis')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('master_kelas')->cascadeOnDelete();
            $table->primary(['jadwal_sesi_id', 'kelas_id']);
            $table->index('kelas_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_sesi_kelas');
        Schema::dropIfExists('jadwal_sesis');
    }
};
