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
            $table->string('kd_cp')->nullable()->after('mapel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_gurus', function (Blueprint $table) {
            $table->dropColumn('kd_cp');
        });
    }
};
