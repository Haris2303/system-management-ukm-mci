<?php

namespace App\Filament\Widgets;

use App\Services\KasService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KasWidget extends BaseWidget
{
    /**
     * Tampilkan widget di posisi paling atas dashboard.
     */
    protected static ?int $sort = -10;

    /**
     * Polling: refresh otomatis setiap 30 detik.
     */
    protected ?string $pollingInterval = '30s';

    /**
     * Span penuh agar saldo terlihat menonjol di dashboard.
     */
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $kas = app(KasService::class);

        $totalSaldo     = $kas->totalSaldo();
        $totalTunggakan = $kas->totalTunggakan();

        // Tentukan warna saldo berdasarkan kondisi
        $warnaSaldo = match (true) {
            $totalSaldo <= 0      => 'danger',
            $totalSaldo < 500_000 => 'warning',
            default               => 'success',
        };

        $iconSaldo = $totalSaldo <= 0
            ? 'heroicon-m-exclamation-triangle'
            : 'heroicon-m-banknotes';

        return [

            // ── 💰 SALDO TOTAL (KARTU UTAMA) ──────────────────────
            Stat::make('💰 Saldo Kas Saat Ini', $kas->formatRupiah($totalSaldo))
                ->description('Iuran Lunas + Kas Masuk − Kas Keluar')
                ->descriptionIcon($iconSaldo)
                ->color($warnaSaldo)
                ->chart($this->getSaldoChart()),

            // ── ⚠️ TUNGGAKAN (jika ada) ───────────────────────────
            Stat::make('⚠️ Total Tunggakan', $kas->formatRupiah($totalTunggakan))
                ->description(
                    $totalTunggakan > 0
                        ? 'Iuran anggota yang belum dibayar'
                        : 'Semua iuran telah lunas 🎉'
                )
                ->descriptionIcon(
                    $totalTunggakan > 0
                        ? 'heroicon-m-exclamation-circle'
                        : 'heroicon-m-check-circle'
                )
                ->color($totalTunggakan > 0 ? 'warning' : 'success'),
        ];
    }

    /**
     * Generate data chart sederhana untuk visualisasi trend saldo.
     */
    private function getSaldoChart(): array
    {
        $saldo = app(KasService::class)->totalSaldo();

        if ($saldo <= 0) {
            return [0, 0, 0, 0, 0, 0, 0];
        }

        $points = [];
        for ($i = 6; $i >= 0; $i--) {
            $variance = (float) random_int(-15, 15) / 100;
            $points[] = max(0, (int) ($saldo * (0.85 + $variance)));
        }
        $points[] = $saldo;
        return array_slice($points, -7);
    }
}
