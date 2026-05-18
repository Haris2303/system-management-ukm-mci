<?php

namespace App\Livewire;

use App\Models\Election;
use Livewire\Component;

class ElectionResults extends Component
{
    public int $electionId;

    public function render()
    {
        $election = Election::with(['candidates.user', 'candidates.votes'])->findOrFail($this->electionId);
        $totalSuara = $election->votes()->count();

        $candidates = $election->candidates->map(fn($c) => [
            'urut'         => $c->urut,
            'nama'         => $c->user->name,
            'jumlah_suara' => $c->votes->count(),
            'persentase'   => $totalSuara > 0 ? round(($c->votes->count() / $totalSuara) * 100, 1) : 0,
        ])->sortByDesc('jumlah_suara')->values();

        return view('livewire.election-results', [
            'election'   => $election,
            'totalSuara' => $totalSuara,
            'candidates' => $candidates,
        ]);
    }
}
