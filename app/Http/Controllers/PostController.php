<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    /** Halaman daftar berita (dengan filter & pagination) */
    public function index(Request $request): View
    {
        $kategori = $request->query('kategori');
        $search   = $request->query('q');

        $query = Post::published()
            ->with('author:id,name')
            ->latest('published_at');

        if ($kategori && $kategori !== 'semua') {
            $query->byKategori($kategori);
        }

        if ($search) {
            $query->where(
                fn($q) =>
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('ringkasan', 'like', "%{$search}%")
            );
        }

        $posts    = $query->paginate(9)->withQueryString();
        $featured = Post::published()->featured()->latest('published_at')->first();

        $kategoris = ['semua', 'Berita', 'Kegiatan', 'Prestasi', 'Pengumuman'];

        return view('berita.index', compact('posts', 'featured', 'kategoris', 'kategori', 'search'));
    }

    /** Halaman detail satu berita */
    public function show(string $slug): View
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        // Tambah view count (throttle per session)
        $sessionKey = "viewed_post_{$post->id}";
        if (! session()->has($sessionKey)) {
            $post->incrementViews();
            session()->put($sessionKey, true);
        }

        // Berita terkait (kategori sama, bukan artikel ini)
        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->where('kategori', $post->kategori)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('berita.show', compact('post', 'related'));
    }
}
