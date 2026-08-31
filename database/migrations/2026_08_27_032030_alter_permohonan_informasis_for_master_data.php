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
        Schema::table('permohonan_informasis', function (Blueprint $table) {
            $table->dropColumn('kategori_pemohon');
            $table->dropColumn('cara_memperoleh_informasi');
            
            $table->foreignId('kategori_pemohon_id')->nullable()->constrained('kategori_pemohons')->nullOnDelete();
            $table->foreignId('cara_memperoleh_informasi_id')->nullable()->constrained('cara_memperoleh_informasis')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_informasis', function (Blueprint $table) {
            $table->dropForeign(['kategori_pemohon_id']);
            $table->dropForeign(['cara_memperoleh_informasi_id']);
            
            $table->dropColumn('kategori_pemohon_id');
            $table->dropColumn('cara_memperoleh_informasi_id');
            
            $table->string('kategori_pemohon')->nullable();
            $table->string('cara_memperoleh_informasi')->nullable();
        });
    }
};
