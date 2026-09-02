<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_petugas_penghubungs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_informasi_id')->constrained('permohonan_informasis')->onDelete('cascade');
            $table->foreignId('petugas_penghubung_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('unit_pengolah_id')->constrained('unit_pengolahs')->onDelete('cascade');
            $table->foreignId('ditugaskan_oleh')->constrained('users')->onDelete('cascade');
            $table->text('instruksi')->nullable();
            $table->timestamp('batas_waktu')->nullable();
            $table->string('status')->default('ditugaskan'); // ditugaskan, dikerjakan, diserahkan
            $table->string('data_path')->nullable();
            $table->text('catatan_petugas_penghubung')->nullable();
            $table->timestamp('diserahkan_at')->nullable();
            $table->string('hasil_uji')->nullable()->default('pending'); // pending, sesuai, perlu_revisi
            $table->text('catatan_uji')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_petugas_penghubungs');
    }
};
