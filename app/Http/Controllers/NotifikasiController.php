<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    // ─── POLLING: ambil notif belum dibaca (dipanggil setiap X detik) ─
    public function polling()
    {
        $userId  = auth()->id();
        $notifs  = Notifikasi::where('user_id', $userId)
            ->where('dibaca', false)
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'judul'      => $n->judul,
                'pesan'      => $n->pesan,
                'tipe'       => $n->tipe,
                'icon'       => $n->icon,
                'url'        => $n->url,
                'waktu'      => $n->created_at_format,
            ]);

        $totalBelumDibaca = Notifikasi::where('user_id', $userId)->where('dibaca', false)->count();

        return response()->json([
            'notifikasi'       => $notifs,
            'total_belum_baca' => $totalBelumDibaca,
        ]);
    }

    // ─── Ambil semua notif (untuk dropdown) ───────────────────
    public function index()
    {
        $userId = auth()->id();
        $notifs = Notifikasi::where('user_id', $userId)
            ->latest()->limit(20)->get()
            ->map(fn($n) => [
                'id'     => $n->id,
                'judul'  => $n->judul,
                'pesan'  => $n->pesan,
                'tipe'   => $n->tipe,
                'icon'   => $n->icon,
                'url'    => $n->url,
                'dibaca' => $n->dibaca,
                'waktu'  => $n->created_at_format,
            ]);

        $totalBelumDibaca = Notifikasi::where('user_id', $userId)->where('dibaca', false)->count();

        return response()->json([
            'notifikasi'       => $notifs,
            'total_belum_baca' => $totalBelumDibaca,
        ]);
    }

    // ─── Tandai satu notif sebagai dibaca ─────────────────────
    public function baca($id)
    {
        $notif = Notifikasi::where('id', $id)->where('user_id', auth()->id())->first();
        if ($notif) $notif->update(['dibaca' => true]);
        return response()->json(['success' => true]);
    }

    // ─── Tandai semua sebagai dibaca ──────────────────────────
    public function bacaSemua()
    {
        Notifikasi::where('user_id', auth()->id())->where('dibaca', false)->update(['dibaca' => true]);
        return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai dibaca.']);
    }

    // ─── Hapus notif ──────────────────────────────────────────
    public function destroy($id)
    {
        Notifikasi::where('id', $id)->where('user_id', auth()->id())->delete();
        return response()->json(['success' => true]);
    }
}