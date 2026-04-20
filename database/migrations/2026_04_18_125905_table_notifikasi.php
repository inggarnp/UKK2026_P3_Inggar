<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_notifikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe')->default('info'); // info, success, warning, danger
            $table->string('icon')->default('solar:bell-bold-duotone');
            $table->string('url')->nullable(); // link saat diklik
            $table->boolean('dibaca')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('tbl_users')->onDelete('cascade');
            $table->index(['user_id', 'dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_notifikasi');
    }
};