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
        Schema::create('permohonan_informasis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi')->unique();
            $table->string('nama_pemohon');
            $table->string('kategori_pemohon')->default('Perorangan'); // Perorangan atau Badan Hukum
            $table->string('nik_atau_no_badan_hukum')->nullable();
            $table->string('email')->nullable();
            $table->string('no_telp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('file_identitas')->nullable(); // KTP / Akta
            $table->text('rincian_informasi');
            $table->text('tujuan_penggunaan');
            $table->string('cara_memperoleh_informasi')->nullable(); 
            $table->string('cara_mendapatkan_salinan')->nullable();
            $table->enum('status', ['masuk', 'tidak_lengkap', 'diproses', 'dikecualikan', 'selesai', 'keberatan'])->default('masuk');
            
            // Relasi Role / PIC (Staff)
            $table->foreignId('desk_layanan_id')->nullable()->constrained('users');
            $table->foreignId('ppid_pelaksana_id')->nullable()->constrained('users');
            $table->foreignId('petugas_penghubung_id')->nullable()->constrained('users');
            
            // Responses & SLA
            $table->text('keterangan_tidak_lengkap')->nullable();
            $table->text('jawaban_informasi')->nullable();
            $table->string('file_jawaban')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable(); // Untuk Tracking SLA 10 Hari
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_informasis');
    }
};
