<?php
// ─── Jurusan.php ──────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table    = 'tbl_jurusan';
    protected $fillable = ['kode_jurusan', 'nama_jurusan', 'deskripsi'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'jurusan_id');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'jurusan_id');
    }
}