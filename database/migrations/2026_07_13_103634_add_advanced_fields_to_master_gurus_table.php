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
        Schema::table('master_gurus', function (Blueprint $table) {
            $table->string('nuptk', 30)->nullable()->after('nip');
            $table->string('jenis_kelamin', 1)->nullable()->after('nama');
            $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('no_hp')->nullable()->after('tanggal_lahir');
            $table->string('email')->nullable()->after('no_hp');
            $table->text('alamat')->nullable()->after('email');
            
            // Replaces mapel_utama
            $table->dropColumn('mapel_utama');
            $table->foreignId('mapel_id')->nullable()->after('jabatan')->constrained('master_mapels')->nullOnDelete();
            
            $table->string('pendidikan_terakhir')->nullable()->after('mapel_id');
            $table->string('status_kepegawaian', 20)->nullable()->after('pendidikan_terakhir');
            $table->date('tmt_tugas')->nullable()->after('status_kepegawaian');
            $table->string('foto_path')->nullable()->after('tmt_tugas');
            $table->string('status_akun', 20)->default('belum_ada')->after('user_id');

            // user_id unique constraint
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_gurus', function (Blueprint $table) {
            $table->dropForeign(['mapel_id']);
            $table->dropUnique(['user_id']);
            $table->dropColumn([
                'nuptk',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'no_hp',
                'email',
                'alamat',
                'mapel_id',
                'pendidikan_terakhir',
                'status_kepegawaian',
                'tmt_tugas',
                'foto_path',
                'status_akun'
            ]);
            $table->string('mapel_utama')->nullable();
        });
    }
};
