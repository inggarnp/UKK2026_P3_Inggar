<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_guru', function (Blueprint $table) {
            $table->enum('jabatan', [
                'guru',
                'kepala_sekolah',
                'wakil_kepala_sekolah',
                'wali_kelas',
                'kepala_jurusan',
                'bendahara',
                'tata_usaha',
            ])->default('guru')->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_guru', function (Blueprint $table) {
            $table->dropColumn(['jabatan', 'foto']);
        });
    }
};