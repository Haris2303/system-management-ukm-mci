<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KasController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProkerController;
use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\IdCardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// ── Public Routes ──────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/login',  [AuthController::class, 'login'])->middleware('cek.demisioner');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// ═════════════════════════════════════════════════════════════
// E-KAS — semua endpoint WAJIB autentikasi sanctum
// ═════════════════════════════════════════════════════════════
Route::middleware(['auth:sanctum', 'cek.demisioner'])->prefix('kas')->name('api.kas.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Info user yang sedang login
    Route::get('/user', fn(Request $request) => response()->json([
        'pesan' => 'Data pengguna berhasil dimuat.',
        'data'  => $request->user(),
    ]));

    // Tagihan milik user yang login (belum dibayar)
    Route::get('/tunggakan', [KasController::class, 'tunggakan'])->name('tunggakan');

    // Riwayat pembayaran user yang login (lunas)
    Route::get('/riwayat', [KasController::class, 'riwayat'])->name('riwayat');

    // Saldo total organisasi (untuk transparansi)
    Route::get('/saldo-transparansi', [KasController::class, 'saldoTransparansi'])->name('saldo');
});

// Presensi
Route::middleware('auth:sanctum')->prefix('presensi')->group(function () {
    Route::post('/', [PresensiController::class, 'store']);
    Route::get('/riwayat', [PresensiController::class, 'riwayat']);
});

// ── E-Voting ───────────────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('elections')->group(function () {
    Route::get('/',              [VoteController::class, 'index']);   // Daftar pemilihan
    Route::get('/{id}',         [VoteController::class, 'show']);    // Detail pemilihan
    Route::post('/{id}/vote',   [VoteController::class, 'vote']);    // Kirim suara
    Route::get('/{id}/hasil',   [VoteController::class, 'hasil']);   // Hasil voting
});

// ═════════════════════════════════════════════════════════════
// MATERI — Distribusi Materi
// Wajib autentikasi sanctum agar query bisa pakai auth()->user()->divisi_id
// ═════════════════════════════════════════════════════════════
Route::middleware(['auth:sanctum', 'cek.demisioner'])->prefix('materi')->name('api.materi.')->group(function () {
    Route::get('/', [MateriController::class, 'index'])->name('index');
});

// ═════════════════════════════════════════════════════════════
// PROGRAM KERJA — Tracking proker untuk anggota
// ═════════════════════════════════════════════════════════════
Route::middleware(['auth:sanctum', 'cek.demisioner'])->prefix('proker')->name('api.proker.')->group(function () {

    Route::get('/',     [ProkerController::class, 'index'])->name('index');
    Route::get('/{id}', [ProkerController::class, 'show'])->name('show');
});

// ═════════════════════════════════════════════════════════════
// PROFIL ANGGOTA
// ═════════════════════════════════════════════════════════════
Route::middleware(['auth:sanctum', 'cek.demisioner'])->prefix('profile')->group(function () {
    Route::get('/',          [ProfileController::class, 'show']);
    Route::post('/avatar',   [ProfileController::class, 'updateAvatar']);
    Route::post('/password', [ProfileController::class, 'changePassword']);
});

// ═════════════════════════════════════════════════════════════
// ID CARD
// ═════════════════════════════════════════════════════════════
// Milik user yang login
Route::middleware('auth:sanctum')->get('/id-card/me', [IdCardController::class, 'apiMe'])->name('api.id-card.me');
// Publik — untuk ditampilkan di mobile profile orang lain
Route::get('/id-card/{userId}', [IdCardController::class, 'apiShowUser'])->name('api.id-card.show');
