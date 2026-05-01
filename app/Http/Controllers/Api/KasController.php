<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TagihanKas;
use App\Services\KasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KasController extends Controller
{
    public function __construct(
        private readonly KasService $kasService,
    ) {}

    // ═════════════════════════════════════════════════════════════
    // GET /api/kas/tunggakan
    // Tagihan milik user yang login dengan status belum_dibayar
    // ═════════════════════════════════════════════════════════════
    public function tunggakan(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $tagihan = TagihanKas::query()
            ->milikUser($userId)
            ->belumDibayar()
            ->orderBy('bulan_tagihan', 'asc')
            ->get();

        $totalTunggakan = (int) $tagihan->sum('nominal');

        $data = $tagihan->map(fn(TagihanKas $t) => [
            'id'                  => $t->id,
            'bulan_tagihan'       => $t->bulan_tagihan,
            'bulan_tagihan_format' => $t->bulan_tagihan_format,
            'nominal'             => (int) $t->nominal,
            'nominal_format'      => $t->nominal_format,
            'status'              => $t->status,
            'catatan'             => $t->catatan,
            'dibuat_pada'         => $t->created_at?->setTimezone('Asia/Jayapura')->format('d M Y'),
        ]);

        return response()->json([
            'pesan' => $tagihan->isEmpty()
                ? 'Anda tidak memiliki tunggakan. Terima kasih atas ketertiban Anda!'
                : 'Anda memiliki ' . $tagihan->count() . ' tagihan yang belum dibayar.',
            'data' => [
                'jumlah_tunggakan' => $tagihan->count(),
                'total_nominal'    => $totalTunggakan,
                'total_format'     => $this->kasService->formatRupiah($totalTunggakan),
                'tagihan'          => $data,
            ],
        ]);
    }

    // ═════════════════════════════════════════════════════════════
    // GET /api/kas/riwayat
    // Tagihan milik user yang login dengan status lunas
    // ═════════════════════════════════════════════════════════════
    public function riwayat(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $riwayat = TagihanKas::query()
            ->milikUser($userId)
            ->lunas()
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        $totalDibayar = (int) $riwayat->sum('nominal');

        $data = $riwayat->map(fn(TagihanKas $t) => [
            'id'                   => $t->id,
            'bulan_tagihan'        => $t->bulan_tagihan,
            'bulan_tagihan_format' => $t->bulan_tagihan_format,
            'nominal'              => (int) $t->nominal,
            'nominal_format'       => $t->nominal_format,
            'status'               => $t->status,
            'tanggal_bayar'        => $t->tanggal_bayar?->setTimezone('Asia/Jayapura')->toIso8601String(),
            'tanggal_bayar_format' => $t->tanggal_bayar?->setTimezone('Asia/Jayapura')->translatedFormat('d F Y, H:i'),
            'catatan'              => $t->catatan,
        ]);

        return response()->json([
            'pesan' => $riwayat->isEmpty()
                ? 'Anda belum memiliki riwayat pembayaran.'
                : 'Riwayat pembayaran berhasil dimuat.',
            'data' => [
                'jumlah_pembayaran'   => $riwayat->count(),
                'total_dibayar'       => $totalDibayar,
                'total_dibayar_format' => $this->kasService->formatRupiah($totalDibayar),
                'riwayat'             => $data,
            ],
        ]);
    }

    // ═════════════════════════════════════════════════════════════
    // GET /api/kas/saldo-transparansi
    // Total saldo kas organisasi (untuk transparansi anggota)
    // ═════════════════════════════════════════════════════════════
    public function saldoTransparansi(): JsonResponse
    {
        $totalSaldo = $this->kasService->totalSaldo();

        return response()->json([
            'pesan' => 'Saldo kas organisasi berhasil dimuat.',
            'data' => [
                'total_saldo'        => $totalSaldo,
                'total_saldo_format' => $this->kasService->formatRupiah($totalSaldo),
                'rincian' => [
                    'iuran_lunas' => [
                        'nominal' => $this->kasService->totalIuranLunas(),
                        'format'  => $this->kasService->formatRupiah($this->kasService->totalIuranLunas()),
                    ],
                    'kas_masuk' => [
                        'nominal' => $this->kasService->totalKasMasuk(),
                        'format'  => $this->kasService->formatRupiah($this->kasService->totalKasMasuk()),
                    ],
                    'kas_keluar' => [
                        'nominal' => $this->kasService->totalKasKeluar(),
                        'format'  => $this->kasService->formatRupiah($this->kasService->totalKasKeluar()),
                    ],
                ],
                'diperbarui_pada' => now('Asia/Jayapura')->translatedFormat('d F Y, H:i') . ' WIT',
            ],
        ]);
    }
}
