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
        // 1. Tahun Ajaran
        Schema::create('master_tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g., 2023/2024
            $table->string('semester'); // Ganjil/Genap
            $table->date('mulai');
            $table->date('selesai');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Guru
        Schema::create('master_gurus', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique()->nullable();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('mapel_utama')->nullable();
            $table->string('status')->default('aktif'); // aktif/nonaktif
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Kelas
        Schema::create('master_kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tingkat'); // X, XI, XII
            $table->string('jurusan'); // IPA, IPS, Bahasa, etc.
            $table->foreignId('wali_kelas_id')->nullable()->constrained('master_gurus')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('master_tahun_ajarans')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Siswa
        Schema::create('master_siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique()->nullable();
            $table->string('nisn')->unique()->nullable();
            $table->string('nama');
            $table->foreignId('kelas_id')->nullable()->constrained('master_kelas')->nullOnDelete();
            $table->string('status')->default('aktif'); // aktif/lulus/pindah/keluar
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Kategori Surat
        Schema::create('master_kategori_surats', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('format_nomor');
            $table->integer('counter')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Mapel
        Schema::create('master_mapels', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->integer('jam')->default(2);
            $table->string('kurikulum')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_mapels');
        Schema::dropIfExists('master_kategori_surats');
        Schema::dropIfExists('master_siswas');
        Schema::dropIfExists('master_kelas');
        Schema::dropIfExists('master_gurus');
        Schema::dropIfExists('master_tahun_ajarans');
    }
};
