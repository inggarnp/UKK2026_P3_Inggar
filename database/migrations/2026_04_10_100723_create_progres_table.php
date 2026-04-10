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
        Schema::create('tbl_progres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_aspirasi')->constrained('tbl_aspirasi');
            $table->foreignId('user_id')->constrained('tbl_users');
            $table->text('keterangan_progres');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_progres');
    }
};
