<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_progres', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('keterangan_progres');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_progres', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};