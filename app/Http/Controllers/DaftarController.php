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

        // Tampilkan divisi aktif hanya saat rekrutmen sedang berlangsung
        $divisis = $openRecruitment
            ? Divisi::active()->with(['pertanyaanSeleksis' => fn($q) => $q->active()->orderBy('urut')])->get()
            : collect();

        return view('landing.daftar.index', compact('divisis', 'openRecruitment'));
    }
}
