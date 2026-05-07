<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DaftarController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PostController;
use App\Models\Pengurusmen;
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
    $pengurus  = Pengurusmen::where('is_active', true)->orderBy('urut')->get();

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
