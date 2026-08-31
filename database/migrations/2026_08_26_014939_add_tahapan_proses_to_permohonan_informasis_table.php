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
            $table->enum('tahapan_proses', [
                'Diterima',
                'Diverifikasi',
                'Ditugaskan',
                'Diuji',
                'Menunggu TTE',
                'Selesai',
                'Ditutup'
            ])->default('Diterima')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_informasis', function (Blueprint $table) {
            $table->dropColumn('tahapan_proses');
        });
    }
};
