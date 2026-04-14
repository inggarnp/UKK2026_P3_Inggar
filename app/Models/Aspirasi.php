<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspirasi extends Model
{
    protected $table = 'tbl_aspirasi';

    protected $fillable = [
        'id_pelaporan',
        'status',
    ];

    // ─── Relasi ───────────────────────────────────────────
    public function inputAspirasi()
    {
        return $this->belongsTo(InputAspirasi::class, 'id_pelaporan');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'id_aspirasi');
    }

    public function historyStatus()
    {
        return $this->hasMany(HistoryStatus::class, 'id_aspirasi');
    }

    public function progres()
    {
        return $this->hasMany(Progres::class, 'id_aspirasi');
    }

    // ─── Accessor: class badge Bootstrap untuk status ─────
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'Menunggu' => 'bg-soft-warning text-warning',
            'Proses'   => 'bg-soft-info text-info',
            'Selesai'  => 'bg-soft-success text-success',
            default    => 'bg-secondary',
        };
    }
}