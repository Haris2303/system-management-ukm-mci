<?php

namespace App\Filament\Widgets;

use App\Models\TagihanKas;
use App\Models\TransaksiKas;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class GrafikTransaksiKasWidget extends ChartWidget
{
    protected ?string $heading = 'Grafik Transaksi & Tagihan Kas';

    protected ?string $description = 'Perbandingan iuran kas, kas masuk, dan kas keluar (dalam Ribuan Rp)';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 2;

    public ?string $filter = 'bulan';

    public function mount(): void
    {
        $latestYear = $this->getTahunTersedia()->first();

        $this->filter = $latestYear ? 'tahun_' . $latestYear : 'bulan';

        parent::mount();
    }

    /**
     * Daftar tahun (descending) yang punya data transaksi kas atau tagihan
     * kas lunas. Diambil di PHP (bukan SQL YEAR()) supaya kompatibel lintas
     * driver DB.
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    private function getTahunTersedia(): \Illuminate\Support\Collection
    {
        $tahunTransaksi = TransaksiKas::query()
            ->get(['tanggal'])
            ->map(fn(TransaksiKas $t) => $t->tanggal->year);

        $tahunTagihan = TagihanKas::lunas()
            ->whereNotNull('tanggal_bayar')
            ->get(['tanggal_bayar'])
            ->map(fn(TagihanKas $t) => $t->tanggal_bayar->year);

        return $tahunTransaksi
            ->merge($tahunTagihan)
            ->unique()
            ->sortDesc()
            ->values();
    }

    protected function getFilters(): ?array
    {
        $tahunList = $this->getTahunTersedia()
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

        [$labels, $iuranData, $masukData, $keluarData] = match (true) {
            $this->filter === 'minggu'                      => $this->getDataMinggu($now),
            $this->filter === 'bulan'                       => $this->getDataBulan($now),
            str_starts_with($this->filter ?? '', 'tahun_')  => $this->getDataTahun((int) substr($this->filter, 6)),
            default                                         => $this->getDataBulan($now),
        };

        return [
            'datasets' => [
                [
                    'label'           => 'Iuran Kas',
                    'data'            => $iuranData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor'     => 'rgb(59, 130, 246)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
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
        $iuran  = [];
        $masuk  = [];
        $keluar = [];

        for ($i = 0; $i < 7; $i++) {
            $hari = $start->copy()->addDays($i);

            $labels[] = $hariIndo[$i] . ' ' . $hari->format('d');

            $iuran[]  = (int) (TagihanKas::lunas()
                ->whereDate('tanggal_bayar', $hari->toDateString())
                ->sum('nominal'));

            $masuk[]  = (int) (TransaksiKas::masuk()
                ->whereDate('tanggal', $hari->toDateString())
                ->sum('nominal'));

            $keluar[] = (int) (TransaksiKas::keluar()
                ->whereDate('tanggal', $hari->toDateString())
                ->sum('nominal'));
        }

        return [$labels, $iuran, $masuk, $keluar];
    }

    private function getDataBulan(Carbon $now): array
    {
        $year        = $now->year;
        $month       = $now->month;
        $daysInMonth = $now->daysInMonth;

        $labels = [];
        $iuran  = [];
        $masuk  = [];
        $keluar = [];

        $day      = 1;
        $mingguKe = 1;

        while ($day <= $daysInMonth) {
            $endDay    = min($day + 6, $daysInMonth);
            $startDate = Carbon::create($year, $month, $day)->toDateString();
            $endDate   = Carbon::create($year, $month, $endDay)->toDateString();

            $labels[] = 'Minggu ' . $mingguKe;

            $iuran[]  = (int) (TagihanKas::lunas()
                ->whereBetween('tanggal_bayar', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->sum('nominal'));

            $masuk[]  = (int) (TransaksiKas::masuk()
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('nominal'));

            $keluar[] = (int) (TransaksiKas::keluar()
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('nominal'));

            $day += 7;
            $mingguKe++;
        }

        return [$labels, $iuran, $masuk, $keluar];
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
        $iuran  = [];
        $masuk  = [];
        $keluar = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $startDate = Carbon::create($year, $bulan, 1)->startOfMonth()->toDateString();
            $endDate   = Carbon::create($year, $bulan, 1)->endOfMonth()->toDateString();

            $labels[] = $bulanIndo[$bulan];

            $iuran[]  = (int) (TagihanKas::lunas()
                ->whereBetween('tanggal_bayar', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->sum('nominal'));

            $masuk[]  = (int) (TransaksiKas::masuk()
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('nominal'));

            $keluar[] = (int) (TransaksiKas::keluar()
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('nominal'));
        }

        return [$labels, $iuran, $masuk, $keluar];
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
