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
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('sifat');
        });
        
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('isi_ringkas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
        
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
