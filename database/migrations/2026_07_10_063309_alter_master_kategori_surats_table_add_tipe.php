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
        Schema::table('master_kategori_surats', function (Blueprint $table) {
            $table->string('tipe', 20)->default('keduanya')->after('nama')->comment('masuk, keluar, keduanya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kategori_surats', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
