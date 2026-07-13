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
        Schema::create('master_tenaga_kependidikans', function (Blueprint $table) {
            $table->id();
            $table->string('nip_nik', 30)->unique();
            $table->string('nama');
            $table->string('jenis_kelamin', 1)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('jabatan', 30);
            $table->string('unit_kerja')->nullable();
            $table->string('status_kepegawaian', 20)->nullable();
            $table->date('tmt_tugas')->nullable();
            $table->string('foto_path')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status_akun', 20)->default('belum_ada');
            $table->string('status', 20)->default('aktif');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_tenaga_kependidikans');
    }
};
