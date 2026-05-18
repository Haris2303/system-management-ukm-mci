<?php

namespace App\Services;

use App\Models\TagihanKas;
use App\Models\TransaksiKas;

class KasService
{
    /**
     * Hitung total saldo kas organisasi saat ini.
     *
     * Rumus:
     *   (Total tagihan lunas) + (Total transaksi masuk) − (Total transaksi keluar)
     *
     * @return int  Saldo dalam Rupiah (integer)
     */
    public function totalSaldo(): int
    {
        $totalLunas       = TagihanKas::lunas()->sum('nominal');
        $totalKasMasuk    = TransaksiKas::masuk()->sum('nominal');
        $totalKasKeluar   = TransaksiKas::keluar()->sum('nominal');

        return (int) ($totalLunas + $totalKasMasuk - $totalKasKeluar);
    }

    /**
     * Total iuran lunas dari anggota.
     */
    public function totalIuranLunas(): int
    {
        return (int) TagihanKas::lunas()->sum('nominal');
    }

    /**
     * Total kas masuk (donasi/bantuan).
     */
    public function totalKasMasuk(): int
    {
        return (int) TransaksiKas::masuk()->sum('nominal');
    }

    /**
     * Total kas keluar (pengeluaran).
     */
    public function totalKasKeluar(): int
    {
        return (int) TransaksiKas::keluar()->sum('nominal');
    }

    /**
     * Total tunggakan (tagihan belum dibayar) - untuk transparansi.
     */
    public function totalTunggakan(): int
    {
        return (int) TagihanKas::belumDibayar()->sum('nominal');
    }

    /**
     * Format integer rupiah ke string "Rp 1.500.000".
     */
    public function formatRupiah(int $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }
}
