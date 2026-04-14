<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'tbl_feedback';

    protected $fillable = [
        'id_aspirasi',
        'user_id',
        'isi_feedback',
    ];

    // ─── Relasi ───────────────────────────────────────────
    public function aspirasi()
    {
        return $this->belongsTo(Aspirasi::class, 'id_aspirasi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Accessor: tanggal format Indonesia ───────────────
    public function getCreatedAtFormatAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->locale('id')->isoFormat('D MMM Y, HH:mm')
            : '-';
    }
}