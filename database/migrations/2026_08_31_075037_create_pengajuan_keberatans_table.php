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
        Schema::create('pengajuan_keberatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_informasi_id')->constrained('permohonan_informasis')->onDelete('cascade');
            $table->string('alasan_keberatan');
            $table->text('keterangan_tambahan')->nullable();
            $table->text('tanggapan_atasan')->nullable();
            $table->string('status')->default('Masuk');
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_keberatans');
    }
};
