<?php

namespace App\Livewire;

use App\Models\Election;
use Livewire\Component;

class ElectionRealtimePage extends Component
{
    public int $electionId;

    public function render()
    {
        $election = Election::with([
            'candidates' => fn($q) => $q->withCount('votes as jumlah_suara')->with('user'),
        ])->findOrFail($this->electionId);
        $totalSuara = $election->votes()->count();

        $candidates = $election->candidates->map(fn($c) => [
            'urut'         => $c->urut,
            'nama'         => $c->user->name,
            'foto'         => $c->foto_url,
            'jumlah_suara' => $c->jumlah_suara,
            'persentase'   => $totalSuara > 0 ? round(($c->jumlah_suara / $totalSuara) * 100, 1) : 0,
        ])->sortByDesc('jumlah_suara')->values();

        $topVotes        = $candidates->first()['jumlah_suara'] ?? 0;
        $hasUniqueLeader = $totalSuara > 0
            && ($candidates->count() === 1 || $candidates->get(1)['jumlah_suara'] < $topVotes);

        if ($hasUniqueLeader && $election->status === 'selesai') {
            $this->dispatch('election-winner-declared');
        }

        return view('livewire.election-realtime-page', [
            'election'        => $election,
            'totalSuara'      => $totalSuara,
            'candidates'      => $candidates,
            'hasUniqueLeader' => $hasUniqueLeader,
        ]);
    }
}
