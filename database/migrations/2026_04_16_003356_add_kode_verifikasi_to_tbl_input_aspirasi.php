<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_input_aspirasi', function (Blueprint $table) {
            // Kode verifikasi yang diinput siswa saat submit aspirasi
            $table->string('kode_verifikasi', 20)->nullable()->after('saksi_id');

            // Ubah status_alur default jadi disetujui (langsung ke petugas, tidak perlu approve guru)
            $table->enum('status_alur', [
                'menunggu_review',
                'disetujui',
                'ditolak',
            ])->default('disetujui')->change(); // langsung disetujui
        });
    }

    public function down(): void
    {
        Schema::table('tbl_input_aspirasi', function (Blueprint $table) {
            $table->dropColumn('kode_verifikasi');
            $table->enum('status_alur', [
                'menunggu_review',
                'disetujui',
                'ditolak',
            ])->default('menunggu_review')->change();
        });
    }
};