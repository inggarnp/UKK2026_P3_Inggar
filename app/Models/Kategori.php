<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'tbl_kategori';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    // ✅ Relasi ke tbl_input_aspirasi
    public function inputAspirasi()
    {
        return $this->hasMany(InputAspirasi::class, 'id_kategori');
    }
}