<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiKas;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class GrafikTransaksiKasWidget extends ChartWidget
{
    protected ?string $heading = 'Grafik Transaksi Kas';

    protected ?string $description = 'Perbandingan kas masuk dan kas keluar (dalam Ribuan Rp)';

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = 'bulan';

    public function mount(): void
    {
        $latestYear = TransaksiKas::query()
            ->selectRaw('YEAR(tanggal) as tahun')
            ->orderByDesc('tahun')
            ->value('tahun');

        $this->filter = $latestYear ? 'tahun_' . $latestYear : 'bulan';

        parent::mount();
    }

    protected function getFilters(): ?array
    {
        $tahunList = TransaksiKas::query()
            ->selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->mapWithKeys(fn($tahun) => ['tahun_' . $tahun => 'Tahun ' . $tahun])
            ->all();

        return [
            'minggu' => 'Minggu Ini',
            'bulan'  => 'Bulan Ini',
            ...$tahunList,
        ];
    }

    protected function getData(): array
    {
        $tz  = 'Asia/Jayapura';
        $now = Carbon::now($tz);

        [$labels, $masukData, $keluarData] = match (true) {
            $this->filter === 'minggu'                      => $this->getDataMinggu($now),
            $this->filter === 'bulan'                       => $this->getDataBulan($now),
            str_starts_with($this->filter ?? '', 'tahun_')  => $this->getDataTahun((int) substr($this->filter, 6)),
            default                                         => $this->getDataBulan($now),
        };

        return [
            'datasets' => [
                [
                    'label'           => 'Kas Masuk',
                    'data'            => $masukData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor'     => 'rgb(34, 197, 94)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => 'Kas Keluar',
                    'data'            => $keluarData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'borderColor'     => 'rgb(239, 68, 68)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getDataMinggu(Carbon $now): array
    {
        $hariIndo = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $start    = $now->copy()->startOfWeek();

        $labels = [];
        $masuk  = [];
        $keluar = [];

        for ($i = 0; $i < 7; $i++) {
            $hari = $start->copy()->addDays($i);

            $labels[] = $hariIndo[$i] . ' ' . $hari->format('d');

            $masuk[]  = (int) (TransaksiKas::masuk()
                ->whereDate('tanggal', $hari->toDateString())
                ->sum('nominal'));

            $keluar[] = (int) (TransaksiKas::keluar()
                ->whereDate('tanggal', $hari->toDateString())
                ->sum('nominal'));
        }

        return [$labels, $masuk, $keluar];
    }

    private function getDataBulan(Carbon $now): array
    {
        $year        = $now->year;
        $month       = $now->month;
        $daysInMonth = $now->daysInMonth;

        $labels = [];
        $masuk  = [];
        $keluar = [];

        $day      = 1;
        $mingguKe = 1;

        while ($day <= $daysInMonth) {
            $endDay    = min($day + 6, $daysInMonth);
            $startDate = Carbon::create($year, $month, $day)->toDateString();
            $endDate   = Carbon::create($year, $month, $endDay)->toDateString();

            $labels[] = 'Minggu ' . $mingguKe;

            $masuk[]  = (int) (TransaksiKas::masuk()
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('nominal'));

            $keluar[] = (int) (TransaksiKas::keluar()
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('nominal'));

            $day += 7;
            $mingguKe++;
        }

        return [$labels, $masuk, $keluar];
    }

    private function getDataTahun(int $year): array
    {
        $bulanIndo = [
            1 => 'Jan',
            2 => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7  => 'Jul',
            8  => 'Agt',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        $labels = [];
        $masuk  = [];
        $keluar = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $startDate = Carbon::create($year, $bulan, 1)->startOfMonth()->toDateString();
            $endDate   = Carbon::create($year, $bulan, 1)->endOfMonth()->toDateString();

            $labels[] = $bulanIndo[$bulan];

            $masuk[]  = (int) (TransaksiKas::masuk()
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('nominal'));

            $keluar[] = (int) (TransaksiKas::keluar()
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('nominal'));
        }

        return [$labels, $masuk, $keluar];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            scales: {
                y: {
                    ticks: {
                        callback: (value) => 'Rp. ' + value.toLocaleString('id-ID'),
                    },
                },
            },
        }
    JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
