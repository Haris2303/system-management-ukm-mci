<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Election;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

            $now = now();

            $newElection = Election::create([
                'judul'              => $parentElection->judul . ' — Putaran Kedua',
                'deskripsi'          => 'Pemilihan putaran kedua akibat hasil seri pada putaran pertama.',
                'posisi'             => $parentElection->posisi,
                'status'             => 'aktif',
                'is_anonim'          => $parentElection->is_anonim,
                'tampil_realtime'    => $parentElection->tampil_realtime,
                'created_by'         => Auth::id(),
                'parent_election_id' => $parentElection->id,
                'waktu_mulai'        => $now,
                'waktu_selesai'      => $now->copy()->addMinutes(10),
            ]);

            foreach ($tiedCandidates as $candidate) {
                Candidate::create([
                    'election_id' => $newElection->id,
                    'user_id'     => $candidate->user_id,
                    'visi'        => $candidate->visi,
                    'misi'        => $candidate->misi,
                    'foto'        => $candidate->foto,
                    'urut'        => $candidate->urut,
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
}
