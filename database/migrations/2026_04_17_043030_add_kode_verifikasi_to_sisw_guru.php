<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_siswa', function (Blueprint $table) {
            // Null = belum diset, isi = sudah punya kode (disimpan as hash)
            $table->string('kode_verifikasi')->nullable()->after('foto');
        });

        Schema::table('tbl_guru', function (Blueprint $table) {
            $table->string('kode_verifikasi')->nullable()->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_siswa', function (Blueprint $table) {
            $table->dropColumn('kode_verifikasi');
        });
        Schema::table('tbl_guru', function (Blueprint $table) {
            $table->dropColumn('kode_verifikasi');
        });
    }
};