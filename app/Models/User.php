<?php

namespace App\Models;

use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    protected $table = 'tbl_users';

    protected $fillable = ['email', 'password', 'role'];
    protected $hidden   = ['password'];

    // ─── JWT ──────────────────────────────────────────────
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => $this->role];
    }

    // ─── Relasi profil ────────────────────────────────────
    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    public function guru()
    {
        return $this->hasOne(Guru::class, 'user_id');
    }

    public function petugasSarana()
    {
        return $this->hasOne(PetugasSarana::class, 'user_id');
    }

    // ─── Relasi aspirasi ──────────────────────────────────
    public function inputAspirasi()
    {
        return $this->hasMany(InputAspirasi::class, 'user_id');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    public function progres()
    {
        return $this->hasMany(Progres::class, 'user_id');
    }

    // ─── Accessor: nama display sesuai role ───────────────
    public function getNamaAttribute(): string
    {
        return match ($this->role) {
            'siswa'  => $this->siswa?->nama  ?? $this->email,
            'guru'   => $this->guru?->nama   ?? $this->email,
            'petugas'=> $this->petugasSarana?->nama ?? $this->email,
            default  => $this->email,
        };
    }
}