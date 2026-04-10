<?php
// ============================================================
// app/Http/Controllers/JurusanController.php
// ============================================================
namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JurusanController extends Controller
{
    public function index() { return view('pages.jurusan.index'); }

    public function data(Request $request)
    {
        $q = Jurusan::withCount('kelas');
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function($q2) use ($s) {
                $q2->where('nama_jurusan','like',"%$s%")->orWhere('kode_jurusan','like',"%$s%");
            });
        }
        $q->orderBy($request->get('sort_column','kode_jurusan'), $request->get('sort_dir','asc'));
        $data = $q->paginate($request->get('per_page',10));
        return response()->json([
            'data'         => $data->items(),
            'total'        => $data->total(),
            'per_page'     => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage(),
        ]);
    }

    public function show($id) { return response()->json(Jurusan::withCount('kelas')->findOrFail($id)); }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'kode_jurusan' => 'required|string|max:10|unique:tbl_jurusan,kode_jurusan',
            'nama_jurusan' => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);
        Jurusan::create($request->only(['kode_jurusan','nama_jurusan','deskripsi']));
        return response()->json(['success'=>true,'message'=>'Jurusan berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $j = Jurusan::findOrFail($id);
        $v = Validator::make($request->all(), [
            'kode_jurusan' => 'required|string|max:10|unique:tbl_jurusan,kode_jurusan,'.$id,
            'nama_jurusan' => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);
        $j->update($request->only(['kode_jurusan','nama_jurusan','deskripsi']));
        return response()->json(['success'=>true,'message'=>'Jurusan berhasil diupdate.']);
    }

    public function destroy($id)
    {
        $j = Jurusan::withCount('kelas')->findOrFail($id);
        if ($j->kelas_count > 0) return response()->json(['success'=>false,'message'=>"Jurusan tidak bisa dihapus, masih ada {$j->kelas_count} kelas."], 422);
        $j->delete();
        return response()->json(['success'=>true,'message'=>'Jurusan berhasil dihapus.']);
    }
}