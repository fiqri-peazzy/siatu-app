<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_mapels', function (Blueprint $table) {
            $table->string('kelompok')->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('master_mapels', function (Blueprint $table) {
            $table->dropColumn('kelompok');
        });
    }
};
