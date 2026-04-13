<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputAspirasi extends Model
{
    protected $table = 'tbl_input_aspirasi';

    protected $fillable = [
        'user_id',
        'id_kategori',
        'lokasi',
        'keterangan',
        'foto',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function aspirasi()
    {
        return $this->hasOne(Aspirasi::class, 'id_pelaporan');
    }
}