<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiKas;
use App\Models\User;
use App\Services\KasService;
use Carbon\Carbon;
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
        $penggunaAktif  = User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['super_admin', 'demisioner']))->count();

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
            Stat::make('Saldo Kas Saat Ini', $kas->formatRupiah($totalSaldo))
                ->description('Iuran Lunas + Kas Masuk − Kas Keluar')
                ->descriptionIcon($iconSaldo)
                ->color($warnaSaldo)
                ->chart($this->getSaldoChart()),

            // ── ⚠️ TUNGGAKAN (jika ada) ───────────────────────────
            Stat::make('Total Tunggakan', $kas->formatRupiah($totalTunggakan))
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

            // ── 👥 PENGGUNA AKTIF ─────────────────────────────────
            Stat::make('Member Aktif', $penggunaAktif . ' orang')
                ->description('Total Member aktif saat ini')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }

    /**
     * Saldo harian (kas masuk − kas keluar) untuk 7 hari terakhir.
     */
    private function getSaldoChart(): array
    {
        $points = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);

            $masuk  = TransaksiKas::masuk()->whereDate('tanggal', $tanggal)->sum('nominal');
            $keluar = TransaksiKas::keluar()->whereDate('tanggal', $tanggal)->sum('nominal');

            $points[] = max(0, $masuk - $keluar);
        }

        return $points;
    }
}
