<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// ── Public Routes ──────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/login',  [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// ═════════════════════════════════════════════════════════════
// E-KAS — semua endpoint WAJIB autentikasi sanctum
// ═════════════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->prefix('kas')->name('api.kas')->group(function () {
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
