<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// ── Landing Page ──────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/daftar', [LandingController::class, 'daftar'])->name('daftar');

// ── Chatbot RAG ────────────────────────────────────────────────
Route::prefix('chatbot')->name('chatbot.')->group(function () {
    Route::post('/chat',             [ChatbotController::class, 'chat'])->name('chat');
    Route::get('/suggested',         [ChatbotController::class, 'suggested'])->name('suggested');
    Route::post('/upload',           [ChatbotController::class, 'upload'])->name('upload')->middleware('auth');
    Route::get('/status/{id}',       [ChatbotController::class, 'status'])->name('status');
});
