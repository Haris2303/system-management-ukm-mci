<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LaporanAbsensiService
{
    /**
     * Kumpulkan seluruh data laporan absensi: rekap kehadiran per anggota
     * (hadir, izin, absen, total agenda, persentase kehadiran).
     *
     * @return array{
     *     periode: string,
     *     anggota: Collection<int, User>,
     * }
     */
    public function generate(): array
    {
        $anggota = User::whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'demisioner']))
            ->withCount([
                'presensis as hadir_count' => fn ($q) => $q->where('status', 'Hadir'),
                'presensis as izin_count' => fn ($q) => $q->where('status', 'Izin'),
                'presensis as absen_count' => fn ($q) => $q->where('status', 'Absen'),
                'presensis as total_presensi',
            ])
            ->with('divisi')
            ->orderByDesc('hadir_count')
            ->get();

        return [
            'periode' => Carbon::now('Asia/Jayapura')->translatedFormat('d F Y H:i') . ' WIT',
            'anggota' => $anggota,
        ];
    }
}
