<?php

namespace App\Http\Controllers;

use App\Services\LaporanAbsensiService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LaporanAbsensiController extends Controller
{
    public function __construct(private readonly LaporanAbsensiService $laporan)
    {
        abort_unless(
            Auth::user()?->hasAnyRole(['super_admin', 'ketua_ukm', 'sekretaris']) ?? false,
            403
        );
    }

    public function pdf(): Response
    {
        $data = $this->laporan->generate();

        $pdf = app('dompdf.wrapper')->loadView('pdf.laporan-absensi', $data);

        return $pdf->download(
            'laporan-absensi-' . now('Asia/Jayapura')->format('Y-m-d') . '.pdf'
        );
    }
}
