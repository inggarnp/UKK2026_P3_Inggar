<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_ruangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruangan', 20)->unique();
            $table->string('nama_ruangan', 100);
            $table->enum('jenis_ruangan', [
                'kelas',
                'laboratorium',
                'perpustakaan',
                'aula',
                'kantor',
                'toilet',
                'lapangan',
                'lainnya'
            ])->default('kelas');
            $table->string('lantai', 5)->nullable();
            $table->string('gedung', 50)->nullable();
            $table->integer('kapasitas')->nullable();
            $table->string('deskripsi', 255)->nullable();
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_ruangan');
    }
};