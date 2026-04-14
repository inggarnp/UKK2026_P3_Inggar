<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table    = 'tbl_ruangan';
    protected $fillable = [
        'kode_ruangan', 'nama_ruangan', 'jenis_ruangan',
        'lantai', 'gedung', 'kapasitas', 'deskripsi', 'kondisi'
    ];

    // ─── Relasi ───────────────────────────────────────────
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'ruangan_id');
    }

    public function inputAspirasi()
    {
        return $this->hasMany(InputAspirasi::class, 'ruangan_id');
    }

    // ─── Accessor: label lokasi lengkap ───────────────────
    public function getLabelLengkapAttribute(): string
    {
        $parts = array_filter([
            $this->nama_ruangan,
            $this->lantai  ? 'Lantai ' . $this->lantai  : null,
            $this->gedung  ? 'Gedung ' . $this->gedung  : null,
        ]);
        return implode(' — ', $parts);
    }
}