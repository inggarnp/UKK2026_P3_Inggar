<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'tbl_siswa';

    protected $fillable = [
        'user_id', 'nis', 'nama',
        'kelas_id', 'jurusan_id',
        'kelas', 'jurusan',
        'jenis_kelamin', 'tanggal_lahir',
        'alamat', 'no_hp', 'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}