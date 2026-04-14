<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_input_aspirasi', function (Blueprint $table) {
            // Hapus kolom lokasi lama
            $table->dropColumn('lokasi');

            // Tambah ruangan_id (nullable agar bisa ketik manual juga)
            $table->unsignedBigInteger('ruangan_id')->nullable()->after('id_kategori');
            // Kolom lokasi_manual untuk fallback ketik sendiri
            $table->string('lokasi_manual', 150)->nullable()->after('ruangan_id');

            // Foreign key ke tbl_ruangan
            $table->foreign('ruangan_id')
                  ->references('id')
                  ->on('tbl_ruangan')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_input_aspirasi', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id']);
            $table->dropColumn(['ruangan_id', 'lokasi_manual']);
            $table->string('lokasi', 100)->nullable()->after('id_kategori');
        });
    }
};