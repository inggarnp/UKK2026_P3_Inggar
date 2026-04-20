<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'tbl_notifikasi';

    protected $fillable = [
        'user_id', 'judul', 'pesan', 'tipe', 'icon', 'url', 'dibaca',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCreatedAtFormatAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->locale('id')->diffForHumans()
            : '-';
    }

    // ─── Helper static: kirim notif ke user ───────────────────
    public static function kirim(int $userId, string $judul, string $pesan, string $tipe = 'info', ?string $url = null, string $icon = 'solar:bell-bold-duotone'): self
    {
        return self::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => $tipe,
            'icon'    => $icon,
            'url'     => $url,
            'dibaca'  => false,
        ]);
    }
}