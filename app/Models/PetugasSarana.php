<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetugasSarana extends Model
{
    protected $table = 'tbl_petugas_sarana';

    protected $fillable = [
        'user_id', 'nip', 'nama',
        'jenis_kelamin', 'tanggal_lahir',
        'alamat', 'no_hp', 'foto', 'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('assets/images/users/avatar-1.jpg');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'aktif' ? 'Aktif' : 'Nonaktif';
    }

    public function getTanggalLahirFormatAttribute(): string
    {
        return $this->tanggal_lahir
            ? $this->tanggal_lahir->isoFormat('D MMMM Y')
            : '-';
    }
}