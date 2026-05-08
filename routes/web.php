<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DaftarController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PostController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// ── Landing Page ──────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/daftar', [LandingController::class, 'daftar'])->name('daftar');

// POST /daftar — sekarang menangani Pendaftar + JawabanPendaftar
Route::post('/daftar', [LandingController::class, 'daftar'])->name('daftar');

// ── Berita & Kegiatan ─────────────────────────────────────────
Route::get('/berita',        [PostController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}', [PostController::class, 'show'])->name('berita.show');

// ── Struktur Kepemimpinan ─────────────────────────────────────────────────────
Route::get('/pengurus', function () {
    $roleOrder = ['ketua_ukm' => 1, 'sekretaris' => 2, 'bendahara' => 3, 'ketua_divisi' => 4];
    $roleLabel = [
        'ketua_ukm'    => 'Ketua UKM',
        'sekretaris'   => 'Sekretaris',
        'bendahara'    => 'Bendahara',
        'ketua_divisi' => 'Ketua Divisi',
    ];

    $pengurus = User::role(array_keys($roleOrder))
        ->with(['divisi', 'roles'])
        ->get()
        ->sortBy(fn($u) => $roleOrder[$u->roles->first()?->name] ?? 99)
        ->values()
        ->map(fn($u) => (object) [
            'nama'      => $u->name,
            'jabatan'   => $roleLabel[$u->roles->first()?->name] ?? 'Pengurus',
            'divisi'    => $u->divisi?->nama,
            'foto'      => $u->avatar,
            'instagram' => null,
            'linkedin'  => null,
            'angkatan'  => null,
        ]);

    return view('landing.pengurus.index', ['pengurus' => $pengurus]);
})->name('pengurus.index');

// ── Pendaftaran ─────────────────────────────────────────────────────
Route::get('/daftar', [DaftarController::class, 'index']);

// ── Chatbot RAG ────────────────────────────────────────────────
Route::prefix('chatbot')->name('chatbot.')->group(function () {
    Route::post('/chat',             [ChatbotController::class, 'chat'])->name('chat');
    Route::get('/suggested',         [ChatbotController::class, 'suggested'])->name('suggested');
    Route::post('/upload',           [ChatbotController::class, 'upload'])->name('upload')->middleware('auth');
    Route::get('/status/{id}',       [ChatbotController::class, 'status'])->name('status');
});
