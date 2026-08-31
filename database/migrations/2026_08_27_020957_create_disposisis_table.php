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
            $table->foreignId('surat_masuk_id')->constrained('surat_masuks')->cascadeOnDelete();
            $table->foreignId('pemberi_tugas_id')->constrained('users')->cascadeOnDelete();
            
            // Penerima bisa User atau UnitPengolah. Kita pakai unit_pengolah_id untuk disposisi ke bidang
            $table->foreignId('unit_pengolah_id')->nullable()->constrained('unit_pengolahs')->nullOnDelete();
            
            $table->text('instruksi');
            $table->enum('status_disposisi', ['Menunggu', 'Diproses', 'Selesai'])->default('Menunggu');
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
