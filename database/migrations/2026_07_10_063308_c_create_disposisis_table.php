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
        Schema::create('disposisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuks')->onDelete('cascade');
            $table->foreignId('parent_disposisi_id')->nullable()->constrained('disposisis')->onDelete('cascade');
            $table->tinyInteger('level')->default(1);
            $table->foreignId('dari_user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('ke_user_id')->constrained('users')->onDelete('restrict');
            $table->text('catatan');
            $table->string('status', 30)->default('diterima');
            $table->timestamp('tgl_disposisi')->useCurrent();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisis');
    }
};
