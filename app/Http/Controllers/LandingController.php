<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Gallery;
use App\Models\JawabanPendaftar;
use App\Models\Pendaftar;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /** Tampilkan landing page utama */
    public function index(): View
    {
        $divisis  = Divisi::all();
        $galleries = Gallery::where('is_featured', true)->orderBy('urut')->take(9)->get();

        // Berita terbaru untuk section landing page (maks 6)
        $posts = Post::published()
            ->with('author:id,name')
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('landing', compact('divisis', 'galleries', 'posts'));
    }

    /** Proses formulir pendaftaran anggota */
    public function daftar(Request $request): RedirectResponse
    {
        // ── Validasi data utama ────────────────────────────────
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:255'],
            'nim'       => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:255'],
            'no_hp'     => ['required', 'string', 'max:20'],
            'angkatan'  => ['required', 'string', 'max:10'],
            'divisi_id' => ['required', 'exists:divisis,id'],
            'jawaban'   => ['nullable', 'array'],
            'jawaban.*' => ['nullable', 'string', 'max:2000'],
        ], [
            'nama.required'      => 'Nama lengkap wajib diisi.',
            'nim.required'       => 'NIM wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'no_hp.required'     => 'Nomor HP wajib diisi.',
            'angkatan.required'  => 'Angkatan wajib dipilih.',
            'divisi_id.required' => 'Pilih divisi yang ingin Anda masuki.',
            'divisi_id.exists'   => 'Divisi yang dipilih tidak valid.',
        ]);

        // ── Cek duplikasi NIM di divisi yang sama ──────────────
        $sudahDaftar = Pendaftar::where('nim', $validated['nim'])
            ->where('divisi_id', $validated['divisi_id'])
            ->exists();

        if ($sudahDaftar) {
            return back()
                ->withInput()
                ->withErrors(['nim' => 'NIM Anda sudah terdaftar di divisi ini.']);
        }

        // ── Cek divisi masih menerima pendaftar ────────────────
        $divisi = Divisi::findOrFail($validated['divisi_id']);
        if (! $divisi->is_active) {
            return back()
                ->withInput()
                ->withErrors(['divisi_id' => 'Divisi ini sedang tidak membuka pendaftaran.']);
        }

        // ── Simpan pendaftar ───────────────────────────────────
        $pendaftar = Pendaftar::create([
            'nama'      => $validated['nama'],
            'nim'       => $validated['nim'],
            'email'     => $validated['email'],
            'no_hp'     => $validated['no_hp'],
            'angkatan'  => $validated['angkatan'],
            'divisi_id' => $validated['divisi_id'],
            'status'    => 'menunggu',
        ]);

        // ── Simpan jawaban (jika ada pertanyaan seleksi) ───────
        if (! empty($validated['jawaban'])) {
            foreach ($validated['jawaban'] as $pertanyaanId => $jawabanTeks) {
                if (! empty(trim((string) $jawabanTeks))) {
                    JawabanPendaftar::create([
                        'pendaftar_id'  => $pendaftar->id,
                        'pertanyaan_id' => (int) $pertanyaanId,
                        'jawaban_teks'  => $jawabanTeks,
                        'nilai_skor'    => null,     // Diisi Ketua Divisi nanti
                    ]);
                }
            }
        }

        return redirect()
            ->to('/#daftar')
            ->with('sukses', "Pendaftaran berhasil dikirim, {$pendaftar->nama}! Kami akan segera menghubungi Anda. 🎉");
    }
}
