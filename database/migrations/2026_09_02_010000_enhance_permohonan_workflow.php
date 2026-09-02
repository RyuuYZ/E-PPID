<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_informasis', function (Blueprint $table) {
            // New SLA & workflow columns
            $table->string('subjek_informasi')->nullable()->after('rincian_informasi');
            $table->timestamp('batas_waktu_lengkapi')->nullable()->after('tanggal_jatuh_tempo');
            $table->timestamp('batas_waktu_jawaban')->nullable()->after('batas_waktu_lengkapi');
            $table->boolean('diperpanjang')->default(false)->after('batas_waktu_jawaban');
            $table->string('surat_jawaban_path')->nullable()->after('file_jawaban');
            $table->foreignId('ditandatangani_oleh')->nullable()->constrained('users')->nullOnDelete()->after('surat_jawaban_path');
            $table->timestamp('ditandatangani_at')->nullable()->after('ditandatangani_oleh');
            $table->timestamp('dikirim_at')->nullable()->after('ditandatangani_at');
            $table->timestamp('ditutup_at')->nullable()->after('dikirim_at');
            $table->softDeletes()->after('updated_at');
        });

        // Migrate existing status values to the new enum values
        // Old statuses: masuk, diproses, selesai, ditolak, ditutup
        // Old tahapan: Diterima, Diverifikasi, Ditugaskan, Diuji, Menunggu TTE, Selesai, Ditutup, Ditolak
        DB::table('permohonan_informasis')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $newStatus = $this->mapStatus($row->status, $row->tahapan_proses);
                DB::table('permohonan_informasis')
                    ->where('id', $row->id)
                    ->update(['status' => $newStatus]);
            }
        });

        // Drop the old tahapan_proses column (now merged into status)
        Schema::table('permohonan_informasis', function (Blueprint $table) {
            $table->dropColumn('tahapan_proses');
        });
    }

    private function mapStatus(?string $oldStatus, ?string $oldTahapan): string
    {
        // Map based on tahapan_proses (more granular), falling back to status
        return match ($oldTahapan) {
            'Diterima' => 'diajukan',
            'Diverifikasi' => 'diverifikasi',
            'Ditugaskan' => 'ditugaskan',
            'Diuji' => 'data_diuji',
            'Menunggu TTE' => 'menunggu_tanda_tangan',
            'Selesai' => 'selesai',
            'Ditutup' => 'ditutup_tidak_lengkap',
            'Ditolak' => 'ditutup_tidak_lengkap',
            default => match ($oldStatus) {
                'masuk' => 'diajukan',
                'diproses' => 'diverifikasi',
                'selesai' => 'selesai',
                'ditolak' => 'ditutup_tidak_lengkap',
                'ditutup' => 'ditutup_tidak_lengkap',
                default => 'diajukan',
            },
        };
    }

    public function down(): void
    {
        Schema::table('permohonan_informasis', function (Blueprint $table) {
            $table->enum('tahapan_proses', [
                'Diterima', 'Diverifikasi', 'Ditugaskan', 'Diuji',
                'Menunggu TTE', 'Selesai', 'Ditutup'
            ])->default('Diterima')->after('status');

            $table->dropForeign(['ditandatangani_oleh']);
            $table->dropColumn([
                'subjek_informasi', 'batas_waktu_lengkapi', 'batas_waktu_jawaban',
                'diperpanjang', 'surat_jawaban_path', 'ditandatangani_oleh',
                'ditandatangani_at', 'dikirim_at', 'ditutup_at', 'deleted_at',
            ]);
        });
    }
};
