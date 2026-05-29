<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Election;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ElectionService
{
    /**
     * Buat sesi revote dari election yang seri.
     * Hanya kandidat yang seri yang diikutsertakan.
     * Parent election ditandai selesai dengan tie_resolution_type = 'revote'.
     */
    public function createRevote(Election $parentElection): Election
    {
        return DB::transaction(function () use ($parentElection): Election {
            $tiedCandidates = $parentElection->getTiedCandidates();

            $newElection = Election::create([
                'judul'              => $parentElection->judul . ' — Putaran Kedua',
                'deskripsi'          => 'Pemilihan putaran kedua akibat hasil seri pada putaran pertama.',
                'posisi'             => $parentElection->posisi,
                'status'             => 'draft',
                'is_anonim'          => $parentElection->is_anonim,
                'tampil_realtime'    => $parentElection->tampil_realtime,
                'created_by'         => Auth::id(),
                'parent_election_id' => $parentElection->id,
                'waktu_mulai'        => null,
                'waktu_selesai'      => null,
            ]);

            foreach ($tiedCandidates as $candidate) {
                Candidate::create([
                    'election_id' => $newElection->id,
                    'user_id'     => $candidate->user_id,
                    'visi'        => $candidate->visi,
                    'misi'        => $candidate->misi,
                    'foto'        => $candidate->foto,
                    // urut di-auto-assign oleh Candidate::booted()
                ]);
            }

            $parentElection->update([
                'tie_resolution_type' => 'revote',
                'tie_resolved_at'     => now(),
                'status'              => 'selesai',
            ]);

            return $newElection;
        });
    }

    /**
     * Tetapkan presidium sidang sebagai pemegang casting vote.
     * Casting vote digunakan melalui endpoint vote reguler — tidak ada token khusus.
     * Syarat: pengguna belum pernah vote pada putaran ini.
     */
    public function grantCastingVote(Election $election, int $userId): void
    {
        if ($election->status !== 'tie') {
            throw new InvalidArgumentException(
                'Casting vote hanya dapat diberikan pada pemilihan yang berstatus seri.'
            );
        }

        if ($election->sudahDivote($userId)) {
            throw new InvalidArgumentException(
                'Pengguna ini sudah memberikan suara pada putaran reguler.'
            );
        }

        $election->update([
            'tie_casting_voter_id' => $userId,
            'tie_resolution_type'  => 'casting_vote',
        ]);
    }
}
