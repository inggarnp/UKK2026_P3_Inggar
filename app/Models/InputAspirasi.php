<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputAspirasi extends Model
{
    protected $table = 'tbl_input_aspirasi';

    protected $fillable = [
        'user_id', 'id_kategori', 'ruangan_id', 'lokasi_manual',
        'saksi_id', 'status_alur', 'reviewed_by', 'reviewed_at', 'catatan_review',
        'keterangan', 'foto',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ─── Status Alur Constants ─────────────────────────────
    const ALUR_MENUNGGU  = 'menunggu_review';
    const ALUR_DISETUJUI = 'disetujui';
    const ALUR_DITOLAK   = 'ditolak';

    // ─── Relasi ───────────────────────────────────────────
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function saksi()
    {
        return $this->belongsTo(Siswa::class, 'saksi_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Guru::class, 'reviewed_by');
    }

    public function aspirasi()
    {
        return $this->hasOne(Aspirasi::class, 'id_pelaporan');
    }

    // ─── Accessor: Lokasi display ──────────────────────────
    public function getLokasiDisplayAttribute(): string
    {
        return $this->ruangan?->nama_ruangan ?? $this->lokasi_manual ?? '-';
    }

    // ─── Accessor: URL foto ───────────────────────────────
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto
            ? asset('assets/images/aspirasi/' . $this->foto)
            : null;
    }

    // ─── Accessor: tanggal format ─────────────────────────
    public function getCreatedAtFormatAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->locale('id')->isoFormat('D MMM Y, HH:mm')
            : '-';
    }

    // ─── Accessor: label & badge status alur ──────────────
    public function getStatusAlurLabelAttribute(): string
    {
        return match ($this->status_alur) {
            self::ALUR_MENUNGGU  => 'Menunggu Review Wali Kelas',
            self::ALUR_DISETUJUI => 'Disetujui',
            self::ALUR_DITOLAK   => 'Ditolak',
            default              => '-',
        };
    }

    public function getStatusAlurBadgeAttribute(): string
    {
        return match ($this->status_alur) {
            self::ALUR_MENUNGGU  => 'bg-soft-warning text-warning',
            self::ALUR_DISETUJUI => 'bg-soft-success text-success',
            self::ALUR_DITOLAK   => 'bg-soft-danger text-danger',
            default              => 'bg-secondary',
        };
    }

    // ─── Scope: filter by status alur ─────────────────────
    public function scopeMenungguReview($query)
    {
        return $query->where('status_alur', self::ALUR_MENUNGGU);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status_alur', self::ALUR_DISETUJUI);
    }
}