<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DaftarController extends Controller
{
    public function index(): View
    {
        $divisis = Divisi::aktif()
            ->with(['pertanyaanSeleksis' => fn($q) => $q->aktif()->orderBy('urut')])->get();

        return view('landing.daftar.index', compact('divisis'));
    }
}
