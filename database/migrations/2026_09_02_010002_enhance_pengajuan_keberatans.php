<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_keberatans', function (Blueprint $table) {
            $table->string('bukti_pendukung_path')->nullable()->after('keterangan_tambahan');
            $table->foreignId('diputuskan_oleh')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->timestamp('diputuskan_at')->nullable()->after('diputuskan_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_keberatans', function (Blueprint $table) {
            $table->dropForeign(['diputuskan_oleh']);
            $table->dropColumn(['bukti_pendukung_path', 'diputuskan_oleh', 'diputuskan_at']);
        });
    }
};
