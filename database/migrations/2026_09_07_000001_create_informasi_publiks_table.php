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
        Schema::create('informasi_publiks', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->foreignId('kategori_informasi_publik_id')->constrained('kategori_informasi_publiks')->cascadeOnDelete();
            $table->string('jenis_dokumen'); // RPJPD, RPJMD, Renstra, RKPD, Renja, Musrenbang, RTRW, RDTR, Kajian & Riset, Roadmap SIDa, Regulasi, Profil Lembaga, Lainnya
            $table->year('tahun')->index();
            $table->text('ringkasan')->nullable();
            $table->string('penanggung_jawab')->default('Bapperida Kabupaten Ciamis');
            $table->foreignId('unit_pengolah_id')->nullable()->constrained('unit_pengolahs')->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->string('file_size')->nullable(); // e.g. "3.5 MB"
            $table->string('tipe_media')->default('PDF');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('download_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasi_publiks');
    }
};
