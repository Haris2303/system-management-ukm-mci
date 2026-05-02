<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    // ═════════════════════════════════════════════════════════════
    // GET /api/materi
    //
    // Logika query:
    //   Ambil materi WHERE divisi_id = user.divisi_id OR divisi_id IS NULL
    //
    // Artinya user akan menerima:
    //   1. Semua materi umum (divisi_id NULL)
    //   2. Materi yang ditujukan untuk divisinya
    // ═════════════════════════════════════════════════════════════
    public function index(Request $request): JsonResponse
    {
        $user      = $request->user();
        $divisiId  = $user->divisi_id ?? null;

        // Query utama dengan scope yang sudah didefinisikan di Model
        $materis = Materi::query()
            ->untukUser($divisiId)
            ->with([
                'divisi:id,nama,icon',
                'uploader:id,name',
            ])
            ->latest()
            ->get();

        // Format response
        $data = $materis->map(fn(Materi $m) => [
            'id'          => $m->id,
            'judul'       => $m->judul,
            'deskripsi'   => $m->deskripsi,

            // File PDF
            'has_file'    => $m->hasFile(),
            'file_url'    => $m->file_url,
            'file_size'   => $m->file_size,

            // Link eksternal
            'has_link'    => $m->hasLink(),
            'link_url'    => $m->link_url,

            // Klasifikasi materi
            'is_umum'     => $m->isUmum(),
            'jenis'       => $m->isUmum() ? 'umum' : 'divisi',
            'jenis_label' => $m->isUmum() ? '🌐 Umum' : ($m->divisi->icon . ' ' . $m->divisi->nama),

            // Detail divisi (jika ada)
            'divisi'      => $m->divisi ? [
                'id'   => $m->divisi->id,
                'nama' => $m->divisi->nama,
                'icon' => $m->divisi->icon,
            ] : null,

            'uploader'    => $m->uploader?->name ?? 'Admin',
            'tanggal'     => $m->created_at?->translatedFormat('d F Y'),
        ]);

        return response()->json([
            'pesan' => $materis->isEmpty()
                ? 'Belum ada materi yang tersedia untuk Anda.'
                : "Berhasil memuat {$materis->count()} materi.",
            'data'  => [
                'total'         => $materis->count(),
                'jumlah_umum'   => $materis->whereNull('divisi_id')->count(),
                'jumlah_divisi' => $materis->whereNotNull('divisi_id')->count(),
                'materi'        => $data,
            ],
        ]);
    }
}
