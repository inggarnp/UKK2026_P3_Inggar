<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_input_aspirasi', function (Blueprint $table) {
            // Saksi (siswa lain sebagai saksi, nullable)
            $table->unsignedBigInteger('saksi_id')->nullable()->after('lokasi_manual');
            $table->foreign('saksi_id')
                  ->references('id')->on('tbl_siswa')
                  ->onDelete('set null');

            // Alur status aspirasi
            // draft → menunggu_review → disetujui → ditolak
            $table->enum('status_alur', [
                'menunggu_review',  // sudah dikirim siswa, menunggu wali kelas
                'disetujui',        // wali kelas approve → lanjut ke petugas
                'ditolak',          // wali kelas reject → dikembalikan ke siswa
            ])->default('menunggu_review')->after('saksi_id');

            // Siapa yang approve/reject (guru id)
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('status_alur');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('catatan_review')->nullable()->after('reviewed_at');

            $table->foreign('reviewed_by')
                  ->references('id')->on('tbl_guru')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_input_aspirasi', function (Blueprint $table) {
            $table->dropForeign(['saksi_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['saksi_id', 'status_alur', 'reviewed_by', 'reviewed_at', 'catatan_review']);
        });
    }
};