<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Presensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    /**
     * Catat presensi anggota berdasarkan token QR Code yang di-scan.
     *
     * Flow:
     *  1. Validasi input token
     *  2. Cek agenda berdasarkan qr_code_token + is_active
     *  3. Cek apakah waktu presensi sudah ditutup
     *  4. Cek duplikasi presensi
     *  5. Simpan data presensi
     *  6. Return response sukses
     */
    public function store(Request $request): JsonResponse
    {
        // ── 1. Validasi Input ──────────────────────────────────────
        $validated = $request->validate([
            'token' => ['required', 'string', 'size:32'],
        ], [
            'token.required' => 'Token QR Code wajib disertakan.',
            'token.string'   => 'Format token tidak valid.',
            'token.size'     => 'Panjang token tidak sesuai. Pastikan QR Code yang Anda scan benar.',
        ]);

        // ── 2. Cari Agenda berdasarkan Token & Status Aktif ────────
        $agenda = Agenda::where('qr_code_token', $validated['token'])
            ->where('is_active', true)
            ->first();

        if (! $agenda) {
            return response()->json([
                'pesan'  => 'QR Code tidak valid atau agenda sudah tidak aktif. Silakan hubungi panitia.',
                'status' => 'gagal',
            ], 404);
        }

        // ── 3. Validasi Waktu Presensi ─────────────────────────────
        if (now()->gt($agenda->waktu_selesai)) {
            return response()->json([
                'pesan'        => 'Jadwal presensi sudah ditutup.',
                'status'       => 'gagal',
                'waktu_selesai' => $agenda->waktu_selesai->format('d M Y, H:i'),
            ], 400);
        }

        if (!now()->gte($agenda->waktu_mulai)) {
            return response()->json([
                'pesan'         => 'Jadwal presensi belum dimulai.',
                'status'        => 'Gagal',
                'waktu_mulai'   => $agenda->waktu_mulai->format('d M Y, H:i')
            ], 400);
        }

        $userId = $request->user()->id;

        // ── 4. Cek Duplikasi Presensi ──────────────────────────────
        $sudahAbsen = Presensi::where('user_id', $userId)
            ->where('agenda_id', $agenda->id)
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'pesan'  => 'Anda sudah melakukan presensi untuk agenda ini.',
                'status' => 'gagal',
            ], 422);
        }

        // ── 5. Simpan Data Presensi ────────────────────────────────
        $presensi = Presensi::create([
            'user_id'   => $userId,
            'agenda_id' => $agenda->id,
            'jam_hadir' => now(),
            'status'    => 'Hadir',
        ]);

        // ── 6. Return Response Sukses ──────────────────────────────
        return response()->json([
            'pesan'  => 'Presensi berhasil dicatat. Selamat datang, ' . Auth::user()->name . '!',
            'status' => 'berhasil',
            'data'   => [
                'agenda'    => $agenda->nama_agenda,
                'jam_hadir' => $presensi->jam_hadir->format('d M Y, H:i:s'),
                'status'    => $presensi->status,
            ],
        ], 201);
    }

    /**
     * Ambil riwayat presensi milik user yang sedang login.
     */
    public function riwayat(Request $request): JsonResponse
    {
        $presensis = Presensi::with('agenda:id,nama_agenda,lokasi,waktu_mulai')
            ->where('user_id', Auth::id())
            ->latest('jam_hadir')
            ->paginate(15);

        return response()->json([
            'pesan' => 'Riwayat presensi berhasil dimuat.',
            'data'  => $presensis,
        ]);
    }
}
