<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Penyesuaian jadwal untuk satu tanggal tanpa mengubah pola jam induk:
 *  - tiadakan  : slot tidak berlangsung (mis. upacara batal karena hujan)
 *  - ubah_waktu: slot bergeser/molor (mis. upacara sampai 08.00)
 *  - tambah    : kegiatan sisipan (upacara HUT RI, rapat, pulang cepat)
 * Jam pelajaran yang bertabrakan dihitung ulang oleh JadwalService::jadwalEfektif().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pengecualians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('slot_jam_id')->nullable()->constrained('master_slot_jams')->cascadeOnDelete();
            $table->string('aksi', 30); // App\Enums\AksiPengecualianJadwal
            $table->string('label')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pengecualians');
    }
};
