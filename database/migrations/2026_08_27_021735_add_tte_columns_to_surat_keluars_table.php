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
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->foreignId('penandatangan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ditandatangani_pada')->nullable();
            $table->string('kode_verifikasi')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropForeign(['penandatangan_id']);
            $table->dropColumn(['penandatangan_id', 'ditandatangani_pada', 'kode_verifikasi']);
        });
    }
};
