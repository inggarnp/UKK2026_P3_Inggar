<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_kelas', function (Blueprint $table) {
            $table->unsignedBigInteger('wali_kelas_id')->nullable()->after('tahun_ajaran');
            $table->foreign('wali_kelas_id')
                  ->references('id')->on('tbl_guru')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_kelas', function (Blueprint $table) {
            $table->dropForeign(['wali_kelas_id']);
            $table->dropColumn('wali_kelas_id');
        });
    }
};