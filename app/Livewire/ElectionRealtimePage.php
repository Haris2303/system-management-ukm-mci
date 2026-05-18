<?php

namespace App\Livewire;

use App\Models\Election;
use Livewire\Component;

class ElectionRealtimePage extends Component
{
    public int $electionId;

    public function render()
    {
        $election = Election::with(['candidates.user', 'candidates.votes'])->findOrFail($this->electionId);
        $totalSuara = $election->votes()->count();

        $base = request()->getSchemeAndHttpHost();
        $storageUrl = fn(?string $path) => match (true) {
            !$path                        => null,
            str_starts_with($path, 'http') => $path,
            default                       => $base . '/storage/' . $path,
        };

        $candidates = $election->candidates->map(fn($c) => [
            'urut'         => $c->urut,
            'nama'         => $c->user->name,
            'foto'         => $storageUrl($c->foto) ?? $storageUrl($c->user->avatar),
            'jumlah_suara' => $c->votes->count(),
            'persentase'   => $totalSuara > 0 ? round(($c->votes->count() / $totalSuara) * 100, 1) : 0,
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
