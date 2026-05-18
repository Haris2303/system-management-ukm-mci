<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramKerja;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProkerController extends Controller
{
    // ═════════════════════════════════════════════════════════════
    // GET /api/proker
    //
    // Logika query:
    //   Ambil proker WHERE divisi_id = user.divisi_id OR divisi_id IS NULL
    //
    // Anggota akan menerima:
    //   1. Proker umum (divisi_id NULL) — proker Ketua UKM
    //   2. Proker yang ditujukan untuk divisinya
    // ═════════════════════════════════════════════════════════════
    public function index(Request $request): JsonResponse
    {
        $user     = $request->user();
        $divisiId = $user->divisi_id ?? null;

        // Query utama dengan scope yang sudah didefinisikan di Model
        $prokers = ProgramKerja::query()
            ->untukUser($divisiId)
            ->with([
                'divisi:id,nama,icon',
                'pic:id,name,email,avatar',
            ])
            ->withCount([
                'tugasProkers as total_tugas',
                'tugasProkers as tugas_selesai' => fn($q) => $q->where('is_selesai', true),
            ])
            ->latest()
            ->get();

        // Format response
        $data = $prokers->map(fn(ProgramKerja $p) => [
            'id'              => $p->id,
            'nama_proker'     => $p->nama_proker,
            'deskripsi'       => $p->deskripsi,

            // Status & progress
            'status'          => $p->status,
            'label_status'    => $p->label_status,
            'progress_persen' => $p->progress_persen,
            'warna_progress'  => $p->warna_progress,

            // Tugas
            'total_tugas'     => $p->total_tugas,
            'tugas_selesai'   => $p->tugas_selesai,

            // Tanggal
            'tanggal_mulai'        => $p->tanggal_mulai?->translatedFormat('d M Y'),
            'tanggal_selesai'      => $p->tanggal_selesai?->translatedFormat('d M Y'),
            'is_terlambat'         => $p->isTerlambat(),
            'sisa_hari'            => $p->sisaHari(),
            'sisa_hari_label'      => $p->status === 'completed' ? 'Selesai'
                : ($p->isTerlambat() ? 'Terlambat ' . abs($p->sisaHari()) . ' hari'
                    : $p->sisaHari() . ' hari lagi'),

            // Klasifikasi
            'is_umum'         => $p->divisi_id === null,
            'jenis_label'     => $p->divisi_id === null
                ? '🌐 Proker Umum'
                : ($p->divisi->icon . ' ' . $p->divisi->nama),

            // Detail divisi
            'divisi' => $p->divisi ? [
                'id'   => $p->divisi->id,
                'nama' => $p->divisi->nama,
                'icon' => $p->divisi->icon,
            ] : null,

            // PIC
            'pic' => $p->pic ? [
                'id'     => $p->pic->id,
                'name'   => $p->pic->name,
                'email'  => $p->pic->email,
                'avatar' => $p->pic->avatar,
            ] : null,
        ]);

        // Statistik agregat
        $stats = [
            'total'       => $prokers->count(),
            'planning'    => $prokers->where('status', 'planning')->count(),
            'active'      => $prokers->where('status', 'active')->count(),
            'completed'   => $prokers->where('status', 'completed')->count(),
            'terlambat'   => $prokers->filter(fn($p) => $p->isTerlambat())->count(),
        ];

        return response()->json([
            'pesan' => $prokers->isEmpty()
                ? 'Belum ada program kerja yang dapat ditampilkan.'
                : "Berhasil memuat {$prokers->count()} program kerja.",
            'data' => [
                'statistik' => $stats,
                'proker'    => $data,
            ],
        ]);
    }

    // ═════════════════════════════════════════════════════════════
    // GET /api/proker/{id}
    // Detail proker dengan daftar tugas
    // ═════════════════════════════════════════════════════════════
    public function show(Request $request, int $id): JsonResponse
    {
        $user     = $request->user();
        $divisiId = $user->divisi_id ?? null;

        $proker = ProgramKerja::query()
            ->untukUser($divisiId)
            ->with(['divisi:id,nama,icon', 'pic:id,name,email,avatar', 'tugasProkers'])
            ->find($id);

        if (! $proker) {
            return response()->json([
                'pesan' => 'Program kerja tidak ditemukan atau Anda tidak punya akses.',
            ], 404);
        }

        return response()->json([
            'pesan' => 'Detail program kerja berhasil dimuat.',
            'data' => [
                'id'              => $proker->id,
                'nama_proker'     => $proker->nama_proker,
                'deskripsi'       => $proker->deskripsi,
                'status'          => $proker->status,
                'label_status'    => $proker->label_status,
                'progress_persen' => $proker->progress_persen,

                'tanggal_mulai'   => $proker->tanggal_mulai?->translatedFormat('d F Y'),
                'tanggal_selesai' => $proker->tanggal_selesai?->translatedFormat('d F Y'),
                'is_terlambat'    => $proker->isTerlambat(),
                'sisa_hari'       => $proker->sisaHari(),

                'divisi' => $proker->divisi ? [
                    'id'   => $proker->divisi->id,
                    'nama' => $proker->divisi->nama,
                    'icon' => $proker->divisi->icon,
                ] : null,

                'pic' => $proker->pic ? [
                    'id'     => $proker->pic->id,
                    'name'   => $proker->pic->name,
                    'avatar' => $proker->pic->avatar,
                ] : null,

                // Daftar tugas
                'tugas' => $proker->tugasProkers->sortBy('urut')->values()->map(fn($t) => [
                    'id'         => $t->id,
                    'nama_tugas' => $t->nama_tugas,
                    'is_selesai' => (bool) $t->is_selesai,
                    'urut'       => $t->urut,
                ]),
            ],
        ]);
    }
}
