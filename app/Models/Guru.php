<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'tbl_guru';

    protected $fillable = [
        'user_id', 'nip', 'nama', 'jabatan',
        'mata_pelajaran', 'jenis_kelamin',
        'tanggal_lahir', 'alamat', 'no_hp', 'foto',
    ];

    protected $casts = ['tanggal_lahir' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Kelas yang di-wali oleh guru ini
    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    // Aspirasi yang sudah di-review oleh guru ini
    public function aspirasiDireview()
    {
        return $this->hasMany(InputAspirasi::class, 'reviewed_by');
    }

    public function getJabatanLabelAttribute(): string
    {
        return [
            'guru'                 => 'Guru',
            'kepala_sekolah'       => 'Kepala Sekolah',
            'wakil_kepala_sekolah' => 'Wakil Kepala Sekolah',
            'wali_kelas'           => 'Wali Kelas',
            'kepala_jurusan'       => 'Kepala Jurusan',
            'bendahara'            => 'Bendahara',
            'tata_usaha'           => 'Tata Usaha',
        ][$this->jabatan] ?? ucfirst(str_replace('_', ' ', $this->jabatan ?? ''));
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('assets/images/users/guru/');
    }

    public function getTanggalLahirFormatAttribute(): string
    {
        return $this->tanggal_lahir
            ? $this->tanggal_lahir->isoFormat('D MMMM Y')
            : '-';
    }
}