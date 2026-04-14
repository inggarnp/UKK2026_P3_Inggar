<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah petugas_sarana ke enum role
        DB::statement("ALTER TABLE tbl_users MODIFY COLUMN role ENUM('siswa','guru','admin','petugas_sarana') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tbl_users MODIFY COLUMN role ENUM('siswa','guru','admin') NOT NULL");
    }
};