<?php

namespace App\Filament\Widgets;

use App\Models\Divisi;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class MemberAktifChartWidget extends ChartWidget
{
    protected ?string $heading = 'Member Aktif per Divisi';

    protected ?string $description = 'Distribusi member aktif berdasarkan divisi';

    /**
     * Kolom 3 baris kedua dashboard (dari 3 kolom), di sebelah Grafik
     * Transaksi Kas (sort 0, span 2).
     */
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 1;

    /**
     * Palet warna konsisten per divisi (urut sesuai Divisi::urut),
     * ditambah satu warna untuk member tanpa divisi.
     */
    private const COLORS = [
        'rgba(59, 130, 246, 0.8)',   // blue
        'rgba(168, 85, 247, 0.8)',   // purple
        'rgba(236, 72, 153, 0.8)',   // pink
        'rgba(34, 197, 94, 0.8)',    // green
        'rgba(249, 115, 22, 0.8)',   // orange
        'rgba(234, 179, 8, 0.8)',    // yellow
        'rgba(148, 163, 184, 0.8)',  // slate (fallback / tanpa divisi)
    ];

    protected function getData(): array
    {
        $divisis = Divisi::orderBy('urut')->get();

        $baseQuery = fn() => User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['super_admin', 'demisioner']));

        $labels = [];
        $data   = [];
        $colors = [];

        foreach ($divisis as $i => $divisi) {
            $jumlah = (clone $baseQuery())->where('divisi_id', $divisi->id)->count();

            if ($jumlah > 0) {
                $labels[] = $divisi->nama;
                $data[]   = $jumlah;
                $colors[] = self::COLORS[$i % count(self::COLORS)];
            }
        }

        $tanpaDivisi = (clone $baseQuery())->whereNull('divisi_id')->count();
        if ($tanpaDivisi > 0) {
            $labels[] = 'Tanpa Divisi';
            $data[]   = $tanpaDivisi;
            $colors[] = 'rgba(148, 163, 184, 0.8)';
        }

        return [
            'datasets' => [
                [
                    'data'            => $data,
                    'backgroundColor' => $colors,
                    'borderWidth'     => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
