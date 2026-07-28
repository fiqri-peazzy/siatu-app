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
        Schema::table('master_tenaga_kependidikans', function (Blueprint $table) {
            $table->string('jabatan', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('master_tenaga_kependidikans', function (Blueprint $table) {
            $table->string('jabatan', 30)->nullable(false)->change();
        });
    }
};
