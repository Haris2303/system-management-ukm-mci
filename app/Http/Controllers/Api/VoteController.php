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
        $elections = Election::with(['candidates.user:id,name'])
            ->whereIn('status', ['aktif', 'selesai', 'tie'])
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
        $election = Election::with(['candidates.user:id,name'])
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
        $userId   = Auth::id();

        // 1. Cek status pemilihan
        if ($election->status !== 'aktif') {
            if ($election->status === 'tie') {
                return response()->json([
                    'pesan' => 'Pemilihan sedang dalam proses musyawarah. Voting reguler ditutup sementara.',
                ], 400);
            }

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
            $election->detectAndHandleTie();
            return response()->json(['pesan' => 'Waktu voting telah berakhir.'], 400);
        }

        // 3. Cek kandidat valid & milik pemilihan ini
        $candidate = $election->candidates->firstWhere('id', $request->candidate_id);
        if (! $candidate) {
            return response()->json([
                'pesan' => 'Kandidat tidak ditemukan dalam pemilihan ini.',
            ], 404);
        }

        $voterHash = hash('sha256', $userId . '-' . $election->id . '-ukm-mci-secret');

        // 4. Cek duplikasi suara
        if ($election->sudahDivote($userId)) {
            return response()->json([
                'pesan' => 'Anda sudah memberikan suara pada pemilihan ini. Satu anggota hanya boleh memilih satu kali.',
            ], 422);
        }

        // 5. Simpan suara
        DB::transaction(function () use ($election, $candidate, $voterHash): void {
            Vote::create([
                'election_id'  => $election->id,
                'candidate_id' => $candidate->id,
                'voter_hash'   => $voterHash,
            ]);
        });

        return response()->json([
            'pesan'  => 'Suara Anda berhasil dicatat. Terima kasih telah berpartisipasi!',
            'status' => 'berhasil',
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
                'waktu_selesai' => $election->waktu_selesai?->format('d M Y, H:i'),
            ], 403);
        }

        $totalSuara = $election->totalSuara();

        $hasil = $election->candidates
            ->sortByDesc(fn(Candidate $c) => $c->jumlahSuara())
            ->values()
            ->map(fn(Candidate $c, int $rank) => [
                'id'           => $c->id,
                'nama'         => $c->user->name,
                'jumlah_suara' => $c->jumlahSuara(),
                'persentase'   => $c->persentase($totalSuara),
                'peringkat'    => $rank + 1,
            ]);

        $pemenang = match (true) {
            // Pemenang ditetapkan lewat musyawarah — gunakan tie_winner_candidate_id
            $election->tie_resolution_type === 'deliberation'
            => $hasil->firstWhere('id', $election->tie_winner_candidate_id),
            // Dilanjutkan ke putaran kedua — belum ada pemenang dari putaran ini
            $election->tie_resolution_type === 'revote'
            => null,
            // Masih menunggu resolusi tie
            $election->status === 'tie'
            => null,
            // Election normal atau aktif — kandidat suara terbanyak
            default
            => $hasil->first(),
        };

        $catatan = match (true) {
            $election->status === 'tie'
            => 'Hasil sementara — pemilihan berakhir seri dan sedang menunggu resolusi dari presidium.',
            $election->tie_resolution_type === 'revote'
            => 'Pemilihan ini dilanjutkan ke Putaran Kedua. Pemenang ditentukan dari hasil putaran selanjutnya.',
            $election->tie_resolution_type === 'deliberation'
            => 'Pemenang ditetapkan melalui musyawarah mufakat.',
            default => null,
        };

        return response()->json([
            'pesan' => 'Hasil pemilihan berhasil dimuat.',
            'data'  => [
                'election' => [
                    'id'                  => $election->id,
                    'judul'               => $election->judul,
                    'posisi'              => $election->posisi,
                    'status'              => $election->status,
                    'tie_resolution_type' => $election->tie_resolution_type,
                ],
                'total_suara'          => $totalSuara,
                'pemenang'             => $pemenang,
                'kandidat'             => $hasil,
                'is_tie'               => $election->status === 'tie',
                'tie_resolution_notes' => $election->tie_resolution_notes,
                'catatan'              => $catatan,
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
                'foto' => $c->foto_url,
            ];
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
            'waktu_mulai'     => $e->waktu_mulai?->format('d M Y, H:i'),
            'waktu_selesai'   => $e->waktu_selesai?->format('d M Y, H:i'),
            'is_anonim'       => $e->is_anonim,
            'tampil_realtime' => $e->tampil_realtime,
            'total_suara'     => $totalSuara,
            'sudah_vote'      => $sudahVote,
            'hasil_tersedia'  => $e->hasilBolehDitampilkan(),
            'is_tie'          => $e->status === 'tie',
            'tie_status_label' => $e->status === 'tie' ? match ($e->tie_resolution_type) {
                'revote'       => 'Telah dijadwalkan Putaran Kedua. Pantau pengumuman selanjutnya.',
                'deliberation' => 'Pemenang telah ditetapkan melalui musyawarah mufakat.',
                default        => 'Pemilihan sedang ditunda. Presidium sedang bermusyawarah untuk menentukan langkah selanjutnya.',
            } : null,
            'kandidat'        => $candidates,
        ];
    }
}
