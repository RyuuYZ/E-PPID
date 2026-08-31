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
        Schema::create('surat_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->nullable(); // nullable until finalized
            $table->string('tujuan');
            $table->string('perihal');
            $table->date('tanggal_keluar')->nullable();
            $table->string('file_draft')->nullable();
            $table->string('file_final')->nullable();
            $table->enum('status', ['Draft', 'Menunggu TTE', 'Terkirim'])->default('Draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluars');
    }
};
