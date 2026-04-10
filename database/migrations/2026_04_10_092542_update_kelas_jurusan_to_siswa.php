<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_siswa', function (Blueprint $table) {
            // Cek dulu kolom belum ada sebelum ditambah
            if (!Schema::hasColumn('tbl_siswa', 'kelas_id')) {
                $table->foreignId('kelas_id')
                      ->nullable()
                      ->after('user_id')
                      ->constrained('tbl_kelas')
                      ->onDelete('set null');
            }

            if (!Schema::hasColumn('tbl_siswa', 'jurusan_id')) {
                $table->foreignId('jurusan_id')
                      ->nullable()
                      ->after('kelas_id')
                      ->constrained('tbl_jurusan')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_siswa', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_siswa', 'kelas_id')) {
                $table->dropForeign(['kelas_id']);
                $table->dropColumn('kelas_id');
            }
            if (Schema::hasColumn('tbl_siswa', 'jurusan_id')) {
                $table->dropForeign(['jurusan_id']);
                $table->dropColumn('jurusan_id');
            }
        });
    }
};