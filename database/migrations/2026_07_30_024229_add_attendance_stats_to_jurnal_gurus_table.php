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
        Schema::table('jurnal_gurus', function (Blueprint $table) {
            $table->integer('jumlah_hadir')->default(0)->after('status');
            $table->integer('jumlah_izin')->default(0)->after('jumlah_hadir');
            $table->integer('jumlah_sakit')->default(0)->after('jumlah_izin');
            $table->integer('jumlah_alfa')->default(0)->after('jumlah_sakit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_gurus', function (Blueprint $table) {
            $table->dropColumn(['jumlah_hadir', 'jumlah_izin', 'jumlah_sakit', 'jumlah_alfa']);
        });
    }
};
