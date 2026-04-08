<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_history_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_aspirasi')->constrained('tbl_aspirasi');
            $table->enum('status_lama', ['Menunggu', 'Proses', 'Selesai']);
            $table->enum('status_baru', ['Menunggu', 'Proses', 'Selesai']);
            $table->foreignId('diubah_oleh')->constrained('tbl_users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_history_status');
    }
};
