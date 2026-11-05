<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jendela waktu check-in / check-out per kategori pegawai per hari.
 * Dievaluasi oleh AturanAbsensiService; diatur admin di Pengaturan Sistem.
 *
 *   masuk_buka ── masuk_tepat (+toleransi) ── masuk_tutup      pulang_buka ── pulang_tutup
 *   |  hadir                 | terlambat      | ditolak        |  (sebelum: pulang cepat + alasan)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aturan_absensis', function (Blueprint $table) {
            $table->id();
            $table->string('kategori', 20);        // App\Enums\KategoriAbsensi: guru / tendik
            $table->string('hari', 10);            // App\Enums\Hari
            $table->boolean('hari_kerja')->default(true);
            $table->time('masuk_buka');            // check-in paling awal
            $table->time('masuk_tepat');           // lewat dari ini (+toleransi) = terlambat
            $table->unsignedSmallInteger('toleransi_menit')->default(0);
            $table->time('masuk_tutup');           // lewat dari ini check-in ditolak (ajukan izin)
            $table->time('pulang_buka');           // check-out normal paling awal
            $table->time('pulang_tutup');          // check-out paling akhir
            $table->boolean('pulang_cepat_diizinkan')->default(true); // sebelum pulang_buka, wajib alasan
            $table->timestamps();

            $table->unique(['kategori', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aturan_absensis');
    }
};
