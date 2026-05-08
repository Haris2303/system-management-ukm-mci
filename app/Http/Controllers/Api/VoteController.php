<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // GET /api/elections
    // Daftar semua pemilihan yang aktif untuk ditampilkan di mobile
    // ─────────────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        $elections = Election::with(['candidates.user:id,name', 'candidates' => function ($q) {
            $q->orderBy('urut');
        }])
            ->whereIn('status', ['aktif', 'selesai'])
            ->orderByDesc('waktu_mulai')
            ->get()
            ->map(fn(Election $e) => $this->formatElection($e));

        return response()->json([
            'pesan' => 'Daftar pemilihan berhasil dimuat.',
            'data'  => $elections,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // GET /api/elections/{id}
    // Detail satu pemilihan + status apakah user sudah vote
    // ─────────────────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $election = Election::with(['candidates' => function ($q) {
            $q->with('user:id,name')->orderBy('urut');
        }])
            ->findOrFail($id);

        return response()->json([
            'pesan' => 'Detail pemilihan berhasil dimuat.',
            'data'  => $this->formatElection($election, withHasil: true),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // POST /api/elections/{id}/vote
    // Kirim suara — inti dari fitur e-voting
    // ─────────────────────────────────────────────────────────
    public function vote(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'candidate_id' => ['required', 'integer'],
        ], [
            'candidate_id.required' => 'Kandidat pilihan wajib ditentukan.',
            'candidate_id.integer'  => 'Format kandidat tidak valid.',
        ]);

        $election = Election::with('candidates')->findOrFail($id);

        // 1. Cek status pemilihan
        if ($election->status !== 'aktif') {
            return response()->json([
                'pesan' => $election->status === 'draft'
                    ? 'Pemilihan belum dibuka. Silakan tunggu hingga waktu voting dimulai.'
                    : 'Pemilihan sudah ditutup. Terima kasih atas partisipasi Anda.',
            ], 400);
        }

        // 2. Cek waktu
        if (now()->lt($election->waktu_mulai)) {
            return response()->json([
                'pesan'       => 'Pemilihan belum dimulai.',
                'waktu_mulai' => $election->waktu_mulai->format('d M Y, H:i'),
            ], 400);
        }
        if (now()->gt($election->waktu_selesai)) {
            $election->updateQuietly(['status' => 'selesai']);
            return response()->json(['pesan' => 'Waktu voting telah berakhir.'], 400);
        }

        // 3. Cek kandidat valid & milik pemilihan ini
        $candidate = $election->candidates->firstWhere('id', $request->candidate_id);
        if (! $candidate) {
            return response()->json([
                'pesan' => 'Kandidat tidak ditemukan dalam pemilihan ini.',
            ], 404);
        }

        $userId    = Auth::id();
        $voterHash = hash('sha256', $userId . '-' . $election->id . '-ukm-mci-secret');

        // 4. Cek duplikasi suara
        if ($election->sudahDivote($userId)) {
            return response()->json([
                'pesan' => 'Anda sudah memberikan suara pada pemilihan ini. Satu anggota hanya boleh memilih satu kali.',
            ], 422);
        }

        // 5. Simpan suara dalam transaksi
        DB::transaction(function () use ($election, $candidate, $voterHash): void {
            Vote::create([
                'election_id'  => $election->id,
                'candidate_id' => $candidate->id,
                'voter_hash'   => $voterHash,   // identitas anonim
            ]);
        });

        return response()->json([
            'pesan'   => 'Suara Anda berhasil dicatat. Terima kasih telah berpartisipasi!',
            'status'  => 'berhasil',
        ], 201);
    }

    // ─────────────────────────────────────────────────────────
    // GET /api/elections/{id}/hasil
    // Hasil pemilihan (hanya tampil jika diizinkan)
    // ─────────────────────────────────────────────────────────
    public function hasil(int $id): JsonResponse
    {
        $election = Election::with(['candidates.user:id,name', 'candidates.votes'])->findOrFail($id);

        if (! $election->hasilBolehDitampilkan()) {
            return response()->json([
                'pesan'         => 'Hasil pemilihan akan ditampilkan setelah voting ditutup.',
                'waktu_selesai' => $election->waktu_selesai->format('d M Y, H:i'),
            ], 403);
        }

        $totalSuara = $election->totalSuara();

        $hasil = $election->candidates
            ->sortByDesc(fn(Candidate $c) => $c->jumlahSuara())
            ->values()
            ->map(fn(Candidate $c, int $rank) => [
                'urut'          => $c->urut,
                'nama'          => $c->user->name,
                'jumlah_suara'  => $c->jumlahSuara(),
                'persentase'    => $c->persentase($totalSuara),
                'peringkat'     => $rank + 1,
            ]);

        $pemenang = $hasil->first();

        return response()->json([
            'pesan'        => 'Hasil pemilihan berhasil dimuat.',
            'data' => [
                'election'    => [
                    'id'     => $election->id,
                    'judul'  => $election->judul,
                    'posisi' => $election->posisi,
                    'status' => $election->status,
                ],
                'total_suara' => $totalSuara,
                'pemenang'    => $pemenang,
                'kandidat'    => $hasil,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Helper: format data election untuk response
    // ─────────────────────────────────────────────────────────
    private function formatElection(Election $e, bool $withHasil = false): array
    {
        $userId     = Auth::id();
        $sudahVote  = $userId ? $e->sudahDivote($userId) : false;
        $totalSuara = $e->totalSuara();

        $candidates = $e->candidates->map(function (Candidate $c) use ($e, $withHasil, $totalSuara) {
            $data = [
                'id'   => $c->id,
                'urut' => $c->urut,
                'nama' => $c->user->name ?? '–',
                'visi' => $c->visi,
                'misi' => $c->misi,
                'foto' => $c->foto ? asset('storage/' . $c->foto) : null,
            ];
            // Tampilkan hasil hanya jika diizinkan
            if ($withHasil && $e->hasilBolehDitampilkan()) {
                $data['jumlah_suara'] = $c->jumlahSuara();
                $data['persentase']   = $c->persentase($totalSuara);
            }
            return $data;
        });

        return [
            'id'              => $e->id,
            'judul'           => $e->judul,
            'deskripsi'       => $e->deskripsi,
            'posisi'          => $e->posisi,
            'status'          => $e->status,
            'waktu_mulai'     => $e->waktu_mulai->format('d M Y, H:i'),
            'waktu_selesai'   => $e->waktu_selesai->format('d M Y, H:i'),
            'is_anonim'       => $e->is_anonim,
            'tampil_realtime' => $e->tampil_realtime,
            'total_suara'     => $totalSuara,
            'sudah_vote'      => $sudahVote,
            'hasil_tersedia'  => $e->hasilBolehDitampilkan(),
            'kandidat'        => $candidates,
        ];
    }
}
