<?php

namespace App\Filament\Resources\TransaksiKas\Widgets;

use App\Models\TransaksiKas;
use App\Services\KasService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ArusKasWidget extends BaseWidget
{
    /**
     * Polling: refresh otomatis setiap 30 detik.
     */
    protected ?string $pollingInterval = '30s';

    /**
     * Span penuh di halaman list TransaksiKasResource.
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Cache hasil hitung dalam 30 detik agar tidak query berulang.
     */
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $kas = app(KasService::class);

        // ── Total keseluruhan ──
        $totalKasMasuk    = $kas->totalKasMasuk();
        $totalKasKeluar   = $kas->totalKasKeluar();
        $totalIuranLunas  = $kas->totalIuranLunas();

        // ── Pemasukan & pengeluaran bulan ini ──
        $bulanIniMulai  = Carbon::now('Asia/Jayapura')->startOfMonth();
        $bulanIniSelesai = Carbon::now('Asia/Jayapura')->endOfMonth();

        $masukBulanIni = (int) TransaksiKas::masuk()
            ->whereBetween('tanggal', [$bulanIniMulai, $bulanIniSelesai])
            ->sum('nominal');

        $keluarBulanIni = (int) TransaksiKas::keluar()
            ->whereBetween('tanggal', [$bulanIniMulai, $bulanIniSelesai])
            ->sum('nominal');

        // ── Persentase pengeluaran terhadap pemasukan ──
        $totalPemasukan = $totalIuranLunas + $totalKasMasuk;
        $persenKeluar   = $totalPemasukan > 0
            ? round(($totalKasKeluar / $totalPemasukan) * 100, 1)
            : 0;

        return [

            // ── ⬆️ TOTAL PEMASUKAN ────────────────────────────────
            Stat::make('⬆️ Total Pemasukan', $kas->formatRupiah($totalPemasukan))
                ->description(
                    'Iuran: ' . $kas->formatRupiah($totalIuranLunas) .
                        ' · Donasi: ' . $kas->formatRupiah($totalKasMasuk)
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($this->getMonthlyChart('masuk')),

            // ── ⬇️ TOTAL PENGELUARAN ──────────────────────────────
            Stat::make('⬇️ Total Pengeluaran', $kas->formatRupiah($totalKasKeluar))
                ->description(
                    $persenKeluar > 0
                        ? "{$persenKeluar}% dari total pemasukan"
                        : 'Belum ada pengeluaran tercatat'
                )
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart($this->getMonthlyChart('keluar')),

            // ── 📅 BULAN INI ──────────────────────────────────────
            Stat::make(
                '📅 Bulan ' . Carbon::now('Asia/Jayapura')->translatedFormat('F Y'),
                $kas->formatRupiah($masukBulanIni - $keluarBulanIni)
            )
                ->description(
                    'Masuk: ' . $kas->formatRupiah($masukBulanIni) .
                        ' · Keluar: ' . $kas->formatRupiah($keluarBulanIni)
                )
                ->descriptionIcon('heroicon-m-calendar')
                ->color(($masukBulanIni - $keluarBulanIni) >= 0 ? 'info' : 'warning'),
        ];
    }

    /**
     * Chart 7 hari terakhir untuk jenis transaksi tertentu.
     *
     * @param  'masuk'|'keluar'  $jenis
     */
    private function getMonthlyChart(string $jenis): array
    {
        $points = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now('Asia/Jayapura')->subDays($i);

            $total = (int) TransaksiKas::query()
                ->where('jenis', $jenis)
                ->whereDate('tanggal', $tanggal->toDateString())
                ->sum('nominal');

            // Skala ke ribuan agar chart tidak terlalu landai
            $points[] = (int) ($total / 1000);
        }

        return $points;
    }
}
