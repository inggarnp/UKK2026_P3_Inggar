<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryStatus extends Model
{
    protected $table = 'tbl_history_status';

    protected $fillable = [
        'id_aspirasi',
        'status_lama',   // ← ditambah
        'status_baru',   // ← ditambah
        'status',
        'keterangan',
        'diubah_oleh',   // ← ditambah
    ];

    // ─── Relasi ───────────────────────────────────────────
    public function aspirasi()
    {
        return $this->belongsTo(Aspirasi::class, 'id_aspirasi');
    }

    // ─── Accessor: tanggal format Indonesia ───────────────
    public function getCreatedAtFormatAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->locale('id')->isoFormat('D MMM Y, HH:mm')
            : '-';
    }

    // ─── Accessor: badge class status ─────────────────────
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