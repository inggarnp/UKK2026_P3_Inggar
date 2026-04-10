<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas', 20);
            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->foreignId('jurusan_id')
                  ->constrained('tbl_jurusan')
                  ->onDelete('restrict');
            $table->foreignId('ruangan_id')
                  ->nullable()
                  ->constrained('tbl_ruangan')
                  ->onDelete('set null');
            $table->string('tahun_ajaran', 10);
            $table->timestamps();

            $table->unique(['nama_kelas', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_kelas');
    }
};