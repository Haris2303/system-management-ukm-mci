<?php

namespace App\Services;

use App\Models\Election;

class ElectionService
{
    /**
     * Buat sesi revote baru dari election yang seri.
     * Hanya kandidat yang seri yang diikutsertakan.
     *
     * @todo Ticket 4 — belum diimplementasi
     */
    public function createRevote(Election $election): Election
    {
        throw new \RuntimeException('ElectionService::createRevote() belum diimplementasi. Lihat Ticket 4.');
    }

    /**
     * Beri hak casting vote ekstra kepada presidium sidang.
     *
     * @todo Ticket 4 — belum diimplementasi
     */
    public function grantCastingVote(Election $election, int $userId): void
    {
        throw new \RuntimeException('ElectionService::grantCastingVote() belum diimplementasi. Lihat Ticket 4.');
    }
}
