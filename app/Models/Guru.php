<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'tbl_guru';

    protected $fillable = [
        'user_id', 'nip', 'nama',
        'jabatan',           // ✅ tambah ini
        'mata_pelajaran',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat', 'no_hp', 'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // ─── Relasi ───────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Accessor: Label jabatan yang readable ─────────────
    public function getJabatanLabelAttribute(): string
    {
        return [
            'guru'                  => 'Guru',
            'kepala_sekolah'        => 'Kepala Sekolah',
            'wakil_kepala_sekolah'  => 'Wakil Kepala Sekolah',
            'wali_kelas'            => 'Wali Kelas',
            'kepala_jurusan'        => 'Kepala Jurusan',
            'bendahara'             => 'Bendahara',
            'tata_usaha'            => 'Tata Usaha',
        ][$this->jabatan] ?? ucfirst(str_replace('_', ' ', $this->jabatan ?? ''));
    }

    // ─── Accessor: URL foto ────────────────────────────────
    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('assets/images/users/avatar-1.jpg');
    }

    // ─── Accessor: Tanggal lahir format Indonesia ──────────
    public function getTanggalLahirFormatAttribute(): string
    {
        return $this->tanggal_lahir
            ? $this->tanggal_lahir->isoFormat('D MMMM Y')
            : '-';
    }
}