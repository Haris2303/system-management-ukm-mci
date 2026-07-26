<?php

namespace App\Http\Controllers;

use App\Services\KasService;
use App\Services\LaporanKeuanganService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LaporanKeuanganController extends Controller
{
    public function __construct(private readonly LaporanKeuanganService $laporan)
    {
        abort_unless(Auth::user()?->can('lihat_saldo_kas') ?? false, 403);
    }

    public function pdf(): Response
    {
        $data = $this->laporan->generate();

        $pdf = app('dompdf.wrapper')->loadView('pdf.laporan-keuangan', [
            ...$data,
            'kas' => app(KasService::class),
        ]);

        return $pdf->download(
            'laporan-keuangan-ekas-' . now('Asia/Jayapura')->format('Y-m-d') . '.pdf'
        );
    }
}
