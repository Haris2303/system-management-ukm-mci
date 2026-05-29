<?php

namespace App\Livewire;

use App\Models\Election;
use Livewire\Component;

class ElectionResults extends Component
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
            'jumlah_suara' => $c->jumlah_suara,
            'persentase'   => $totalSuara > 0 ? round(($c->jumlah_suara / $totalSuara) * 100, 1) : 0,
        ])->sortByDesc('jumlah_suara')->values();

        return view('livewire.election-results', [
            'election'   => $election,
            'totalSuara' => $totalSuara,
            'candidates' => $candidates,
        ]);
    }
}
