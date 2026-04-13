<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    public function index()
    {
        return view('pages.kategori.index');
    }

    public function data(Request $request)
    {
        $query = Kategori::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_kategori', 'like', "%{$request->search}%")
                  ->orWhere('deskripsi', 'like', "%{$request->search}%");
            });
        }

        $perPage = $request->per_page ?? 10;
        $result  = $query->orderBy('nama_kategori')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $result->map(fn($k) => [
                'id'              => $k->id,
                'nama_kategori'   => $k->nama_kategori,
                'deskripsi'       => $k->deskripsi ?? '-',
                // ✅ Hitung jumlah aspirasi langsung via DB
                'jumlah_aspirasi' => $k->inputAspirasi()->count(),
                'created_at'      => $k->created_at->format('d M Y'),
            ]),
            'meta' => [
                'total'        => $result->total(),
                'from'         => $result->firstItem() ?? 0,
                'to'           => $result->lastItem() ?? 0,
                'current_page' => $result->currentPage(),
                'last_page'    => $result->lastPage(),
            ]
        ]);
    }

    public function show($id)
    {
        $k = Kategori::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id'            => $k->id,
                'nama_kategori' => $k->nama_kategori,
                'deskripsi'     => $k->deskripsi ?? '',
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:tbl_kategori,nama_kategori',
            'deskripsi'     => 'nullable|string|max:255',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi'     => $request->deskripsi,
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil ditambahkan!']);
    }

    public function update(Request $request, $id)
    {
        $k = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => ['required', 'string', 'max:50',
                Rule::unique('tbl_kategori', 'nama_kategori')->ignore($k->id)],
            'deskripsi'     => 'nullable|string|max:255',
        ]);

        $k->update([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi'     => $request->deskripsi,
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil diupdate!']);
    }

    public function destroy($id)
    {
        $k = Kategori::findOrFail($id);

        if ($k->inputAspirasi()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak bisa dihapus karena masih digunakan oleh ' . $k->inputAspirasi()->count() . ' aspirasi!'
            ], 422);
        }

        $k->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus!']);
    }
}