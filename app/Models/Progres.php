<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progres extends Model
{
    protected $table = 'tbl_progres';

    protected $fillable = [
        'id_aspirasi',
        'user_id',
        'keterangan_progres',
        'foto',
    ];

    public function aspirasi()
    {
        return $this->belongsTo(Aspirasi::class, 'id_aspirasi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCreatedAtFormatAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->locale('id')->isoFormat('D MMM Y, HH:mm')
            : '-';
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto
            ? asset('assets/images/progres/' . $this->foto)
            : null;
    }
}