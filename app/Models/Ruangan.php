<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table    = 'tbl_ruangan';
    protected $fillable = ['kode_ruangan', 'nama_ruangan', 'jenis_ruangan', 'lantai', 'gedung', 'kapasitas', 'deskripsi', 'kondisi'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'ruangan_id');
    }
}