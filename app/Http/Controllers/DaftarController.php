<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\OpenRecruitment;
use Illuminate\View\View;

class DaftarController extends Controller
{
    public function index(): View
    {
        $openRecruitment = OpenRecruitment::active()->latest()->first();

        // Mengambil tahun saat ini secara otomatis (saat ini: 2026)
        $tahunSekarang = (int) date('Y');

        // Membuat array: Tahun Sekarang sampai 3 tahun ke belakang (4 tahun total: 2026, 2025, 2024, 2023)
        $angkatanList = range($tahunSekarang, $tahunSekarang - 3);

        // Tampilkan divisi aktif hanya saat rekrutmen sedang berlangsung
        $divisis = $openRecruitment
            ? Divisi::active()->with(['pertanyaanSeleksis' => fn($q) => $q->active()->orderBy('urut')])->get()
            : collect();

        return view('landing.daftar.index', compact('divisis', 'openRecruitment', 'angkatanList'));
    }
}
