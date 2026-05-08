<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KasController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\ProkerController;
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
