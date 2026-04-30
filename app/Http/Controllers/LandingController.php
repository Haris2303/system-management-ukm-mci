<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Member;
use App\Models\Pengurusmen;
use App\Models\Post;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /** Tampilkan landing page utama */
    public function index(): View
    {
        $programs  = Program::where('is_active', true)->orderBy('urut')->get();
        $galleries = Gallery::where('is_featured', true)->orderBy('urut')->take(9)->get();
        $pengurus  = Pengurusmen::where('is_active', true)->orderBy('urut')->get();

        // Berita terbaru untuk section landing page (maks 6)
        $posts = Post::published()
            ->with('author:id,name')
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('landing', compact('programs', 'galleries', 'pengurus', 'posts'));
    }

    /** Proses formulir pendaftaran anggota */
    public function daftar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nim'          => ['required', 'string', 'unique:members,nim'],
            'email'        => ['required', 'email', 'unique:members,email'],
            'no_hp'        => ['required', 'string', 'max:20'],
            'jurusan'      => ['required', 'string', 'max:100'],
            'angkatan'     => ['required', 'string', 'max:10'],
            'motivasi'     => ['nullable', 'string', 'max:1000'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nim.required'          => 'NIM wajib diisi.',
            'nim.unique'            => 'NIM ini sudah terdaftar.',
            'email.required'        => 'Email wajib diisi.',
            'email.unique'          => 'Email ini sudah terdaftar.',
            'no_hp.required'        => 'Nomor HP wajib diisi.',
            'jurusan.required'      => 'Jurusan wajib diisi.',
            'angkatan.required'     => 'Angkatan wajib diisi.',
        ]);

        Member::create($validated);

        return redirect()
            ->to('/#daftar')
            ->with('sukses', 'Pendaftaran berhasil! Kami akan menghubungi Anda segera. 🎉');
    }
}
