<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RuanganController extends Controller
{
    public function index() { return view('pages.ruangan.index'); }

    public function data(Request $request)
    {
        $q = Ruangan::withCount('kelas');
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function($q2) use ($s) {
                $q2->where('nama_ruangan','like',"%$s%")
                   ->orWhere('kode_ruangan','like',"%$s%")
                   ->orWhere('jenis_ruangan','like',"%$s%");
            });
        }
        $q->orderBy($request->get('sort_column','kode_ruangan'), $request->get('sort_dir','asc'));
        $data = $q->paginate($request->get('per_page',10));
        return response()->json([
            'data'         => $data->items(),
            'total'        => $data->total(),
            'per_page'     => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage(),
        ]);
    }

    public function show($id) { return response()->json(Ruangan::withCount('kelas')->findOrFail($id)); }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'kode_ruangan'  => 'required|string|max:20|unique:tbl_ruangan,kode_ruangan',
            'nama_ruangan'  => 'required|string|max:100',
            'jenis_ruangan' => 'required|in:kelas,laboratorium,perpustakaan,aula,kantor,toilet,lapangan,lainnya',
            'lantai'        => 'nullable|string|max:5',
            'gedung'        => 'nullable|string|max:50',
            'kapasitas'     => 'nullable|integer|min:1',
            'kondisi'       => 'required|in:baik,rusak_ringan,rusak_berat',
            'deskripsi'     => 'nullable|string',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);
        Ruangan::create($request->only(['kode_ruangan','nama_ruangan','jenis_ruangan','lantai','gedung','kapasitas','kondisi','deskripsi']));
        return response()->json(['success'=>true,'message'=>'Ruangan berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $r = Ruangan::findOrFail($id);
        $v = Validator::make($request->all(), [
            'kode_ruangan'  => 'required|string|max:20|unique:tbl_ruangan,kode_ruangan,'.$id,
            'nama_ruangan'  => 'required|string|max:100',
            'jenis_ruangan' => 'required|in:kelas,laboratorium,perpustakaan,aula,kantor,toilet,lapangan,lainnya',
            'lantai'        => 'nullable|string|max:5',
            'gedung'        => 'nullable|string|max:50',
            'kapasitas'     => 'nullable|integer|min:1',
            'kondisi'       => 'required|in:baik,rusak_ringan,rusak_berat',
            'deskripsi'     => 'nullable|string',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);
        $r->update($request->only(['kode_ruangan','nama_ruangan','jenis_ruangan','lantai','gedung','kapasitas','kondisi','deskripsi']));
        return response()->json(['success'=>true,'message'=>'Ruangan berhasil diupdate.']);
    }

    public function destroy($id)
    {
        $r = Ruangan::withCount('kelas')->findOrFail($id);
        if ($r->kelas_count > 0) return response()->json(['success'=>false,'message'=>"Ruangan tidak bisa dihapus, dipakai {$r->kelas_count} kelas."], 422);
        $r->delete();
        return response()->json(['success'=>true,'message'=>'Ruangan berhasil dihapus.']);
    }
}