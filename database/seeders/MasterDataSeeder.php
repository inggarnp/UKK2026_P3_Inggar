<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $jurusan = [
            ['kode_jurusan' => 'RPL',  'nama_jurusan' => 'Rekayasa Perangkat Lunak',  'deskripsi' => null],
            ['kode_jurusan' => 'TKJ',  'nama_jurusan' => 'Teknik Komputer Jaringan',  'deskripsi' => null],
            ['kode_jurusan' => 'MM',   'nama_jurusan' => 'Multimedia',                 'deskripsi' => null],
            ['kode_jurusan' => 'AK',   'nama_jurusan' => 'Akuntansi',                  'deskripsi' => null],
            ['kode_jurusan' => 'AP',   'nama_jurusan' => 'Administrasi Perkantoran',   'deskripsi' => null],
            ['kode_jurusan' => 'PM',   'nama_jurusan' => 'Pemasaran',                  'deskripsi' => null],
            ['kode_jurusan' => 'TKR',  'nama_jurusan' => 'Teknik Kendaraan Ringan',    'deskripsi' => null],
        ];

        foreach ($jurusan as $j) {
            DB::table('tbl_jurusan')->updateOrInsert(
                ['kode_jurusan' => $j['kode_jurusan']],
                array_merge($j, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $ruangan = [
            ['kode_ruangan' => 'R-101', 'nama_ruangan' => 'Ruang Kelas 101', 'jenis_ruangan' => 'kelas',        'lantai' => '1', 'gedung' => 'Gedung A', 'kapasitas' => 36, 'kondisi' => 'baik'],
            ['kode_ruangan' => 'R-102', 'nama_ruangan' => 'Ruang Kelas 102', 'jenis_ruangan' => 'kelas',        'lantai' => '1', 'gedung' => 'Gedung A', 'kapasitas' => 36, 'kondisi' => 'baik'],
            ['kode_ruangan' => 'R-103', 'nama_ruangan' => 'Ruang Kelas 103', 'jenis_ruangan' => 'kelas',        'lantai' => '1', 'gedung' => 'Gedung A', 'kapasitas' => 36, 'kondisi' => 'baik'],
            ['kode_ruangan' => 'R-201', 'nama_ruangan' => 'Ruang Kelas 201', 'jenis_ruangan' => 'kelas',        'lantai' => '2', 'gedung' => 'Gedung A', 'kapasitas' => 36, 'kondisi' => 'baik'],
            ['kode_ruangan' => 'R-202', 'nama_ruangan' => 'Ruang Kelas 202', 'jenis_ruangan' => 'kelas',        'lantai' => '2', 'gedung' => 'Gedung A', 'kapasitas' => 36, 'kondisi' => 'baik'],

            ['kode_ruangan' => 'LAB-KOM-1', 'nama_ruangan' => 'Lab Komputer 1', 'jenis_ruangan' => 'laboratorium', 'lantai' => '1', 'gedung' => 'Gedung B', 'kapasitas' => 40, 'kondisi' => 'baik'],
            ['kode_ruangan' => 'LAB-KOM-2', 'nama_ruangan' => 'Lab Komputer 2', 'jenis_ruangan' => 'laboratorium', 'lantai' => '2', 'gedung' => 'Gedung B', 'kapasitas' => 40, 'kondisi' => 'baik'],

            ['kode_ruangan' => 'PERPUS',    'nama_ruangan' => 'Perpustakaan',   'jenis_ruangan' => 'perpustakaan', 'lantai' => '1', 'gedung' => 'Gedung C', 'kapasitas' => 60, 'kondisi' => 'baik'],
            ['kode_ruangan' => 'AULA',      'nama_ruangan' => 'Aula Sekolah',   'jenis_ruangan' => 'aula',         'lantai' => '1', 'gedung' => 'Gedung C', 'kapasitas' => 300,'kondisi' => 'baik'],
        ];

        foreach ($ruangan as $r) {
            DB::table('tbl_ruangan')->updateOrInsert(
                ['kode_ruangan' => $r['kode_ruangan']],
                array_merge($r, ['deskripsi' => null, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        $rplId  = DB::table('tbl_jurusan')->where('kode_jurusan', 'RPL')->value('id');
        $tkjId  = DB::table('tbl_jurusan')->where('kode_jurusan', 'TKJ')->value('id');
        $mmId   = DB::table('tbl_jurusan')->where('kode_jurusan', 'MM')->value('id');

        $r101 = DB::table('tbl_ruangan')->where('kode_ruangan', 'R-101')->value('id');
        $r102 = DB::table('tbl_ruangan')->where('kode_ruangan', 'R-102')->value('id');
        $r103 = DB::table('tbl_ruangan')->where('kode_ruangan', 'R-103')->value('id');
        $r201 = DB::table('tbl_ruangan')->where('kode_ruangan', 'R-201')->value('id');
        $r202 = DB::table('tbl_ruangan')->where('kode_ruangan', 'R-202')->value('id');

        $kelas = [
            ['nama_kelas' => 'XII RPL 1', 'tingkat' => 'XII', 'jurusan_id' => $rplId, 'ruangan_id' => $r101, 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XII RPL 2', 'tingkat' => 'XII', 'jurusan_id' => $rplId, 'ruangan_id' => $r102, 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XII TKJ 1', 'tingkat' => 'XII', 'jurusan_id' => $tkjId, 'ruangan_id' => $r103, 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XII MM 1',  'tingkat' => 'XII', 'jurusan_id' => $mmId,  'ruangan_id' => $r201, 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XI RPL 1',  'tingkat' => 'XI',  'jurusan_id' => $rplId, 'ruangan_id' => $r202, 'tahun_ajaran' => '2025/2026'],
        ];

        foreach ($kelas as $k) {
            DB::table('tbl_kelas')->updateOrInsert(
                ['nama_kelas' => $k['nama_kelas'], 'tahun_ajaran' => $k['tahun_ajaran']],
                array_merge($k, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $kategori = [
            ['nama_kategori' => 'Kebersihan',          'deskripsi' => 'Pengaduan terkait kebersihan sekolah'],
            ['nama_kategori' => 'Fasilitas Kelas',     'deskripsi' => 'Kerusakan meja, kursi, papan tulis dll'],
            ['nama_kategori' => 'Toilet/Kamar Mandi',  'deskripsi' => 'Pengaduan toilet dan kamar mandi'],
            ['nama_kategori' => 'Laboratorium',        'deskripsi' => 'Komputer, peralatan lab rusak dll'],
            ['nama_kategori' => 'Perpustakaan',        'deskripsi' => 'Fasilitas perpustakaan'],
            ['nama_kategori' => 'Kantin',              'deskripsi' => 'Pengaduan terkait kantin'],
            ['nama_kategori' => 'Lapangan/Olahraga',   'deskripsi' => 'Fasilitas olahraga dan lapangan'],
            ['nama_kategori' => 'Listrik/Air',         'deskripsi' => 'Gangguan listrik dan air'],
            ['nama_kategori' => 'Lainnya',             'deskripsi' => 'Pengaduan lain-lain'],
        ];

        foreach ($kategori as $k) {
            DB::table('tbl_kategori')->updateOrInsert(
                ['nama_kategori' => $k['nama_kategori']],
                array_merge($k, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('Master data seeder selesai!');
        $this->command->info('   - Jurusan: ' . count($jurusan));
        $this->command->info('   - Ruangan: ' . count($ruangan));
        $this->command->info('   - Kelas: '   . count($kelas));
        $this->command->info('   - Kategori: '. count($kategori));
    }
}