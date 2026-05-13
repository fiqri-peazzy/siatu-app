<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom profil tambahan ke tabel users sesuai kebutuhan multi-role SITU-SMA.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Informasi identitas
            $table->string('nip')->nullable()->after('name');          // Untuk Guru & Staf TU
            $table->string('nis')->nullable()->after('nip');           // Untuk Siswa
            $table->string('no_hp')->nullable()->after('nis');         // WhatsApp notifikasi
            $table->string('avatar')->nullable()->after('no_hp');      // Foto profil

            // Informasi jabatan/posisi
            $table->string('jabatan')->nullable()->after('avatar');    // Jabatan struktural
            $table->string('jenis_kelamin')->nullable()->after('jabatan'); // L / P

            // Relasi ke data master (nullable karena tidak semua role punya)
            $table->foreignId('guru_id')
                  ->nullable()
                  ->after('jenis_kelamin')
                  ->constrained('master_gurus')
                  ->nullOnDelete();

            $table->foreignId('siswa_id')
                  ->nullable()
                  ->after('guru_id')
                  ->constrained('master_siswas')
                  ->nullOnDelete();

            // Status akun
            $table->boolean('is_active')->default(true)->after('siswa_id');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('guru_id');
            $table->dropConstrainedForeignId('siswa_id');
            $table->dropSoftDeletes();
            $table->dropColumn([
                'nip', 'nis', 'no_hp', 'avatar', 'jabatan',
                'jenis_kelamin', 'is_active', 'last_login_at',
            ]);
        });
    }
};
