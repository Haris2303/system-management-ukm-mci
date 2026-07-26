<?php

namespace App\Services;

use App\Models\TagihanKas;
use App\Models\TransaksiKas;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LaporanKeuanganService
{
    public function __construct(private readonly KasService $kas)
    {
    }

    /**
     * Kumpulkan seluruh data laporan keuangan: ringkasan, detail tagihan
     * kas, dan detail arus kas (transaksi kas).
     *
     * @return array{
     *     periode: string,
     *     ringkasan: array<string, int|string>,
     *     tagihan: Collection<int, TagihanKas>,
     *     transaksi: Collection<int, TransaksiKas>,
     * }
     */
    public function generate(): array
    {
        return [
            'periode' => Carbon::now('Asia/Jayapura')->translatedFormat('d F Y H:i') . ' WIT',
            'ringkasan' => [
                'total_saldo'      => $this->kas->totalSaldo(),
                'total_iuran_lunas' => $this->kas->totalIuranLunas(),
                'total_kas_masuk'  => $this->kas->totalKasMasuk(),
                'total_kas_keluar' => $this->kas->totalKasKeluar(),
                'total_tunggakan'  => $this->kas->totalTunggakan(),
            ],
            'tagihan' => TagihanKas::query()
                ->with('user')
                ->orderByDesc('bulan_tagihan')
                ->get(),
            'transaksi' => TransaksiKas::query()
                ->with('pencatat')
                ->orderByDesc('tanggal')
                ->get(),
        ];
    }
}
