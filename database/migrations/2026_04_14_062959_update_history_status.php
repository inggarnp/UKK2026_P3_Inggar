<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Fix error: Column 'status_lama' cannot be null
    // Jadikan status_lama nullable karena saat pertama kali dibuat tidak ada status lama

    public function up(): void
    {
        Schema::table('tbl_history_status', function (Blueprint $table) {
            $table->string('status_lama')->nullable()->change();
            $table->string('status_baru')->nullable()->change();
            $table->unsignedBigInteger('diubah_oleh')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_history_status', function (Blueprint $table) {
            $table->string('status_lama')->nullable(false)->change();
            $table->string('status_baru')->nullable(false)->change();
            $table->unsignedBigInteger('diubah_oleh')->nullable(false)->change();
        });
    }
};