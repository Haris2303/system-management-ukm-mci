<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $kategori = $request->query('kategori');

        $query = Gallery::orderByDesc('created_at');

        if ($kategori && $kategori !== 'semua') {
            $query->where('kategori', $kategori);
        }

        $galleries = $query->paginate(24)->withQueryString();

        $kategoris = ['semua', 'Umum', 'Kegiatan', 'Prestasi', 'Rapat', 'Pelatihan'];

        return view('landing.galeri.index', compact('galleries', 'kategoris', 'kategori'));
    }
}
