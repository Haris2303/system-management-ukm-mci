<?php

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// ── Landing Page ──────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/daftar', [LandingController::class, 'daftar'])->name('daftar');
