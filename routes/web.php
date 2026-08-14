<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DaftarController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\IdCardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LaporanAbsensiController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\PostController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// ── robots.txt ──────────────────────────────────────────────────
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Allow: /',
        'Disallow: /administrasi',
        'Disallow: /id-card',
        'Disallow: /elections',
        'Disallow: /anggota',
        'Disallow: /laporan-keuangan',
        'Disallow: /rag-documents',
        'Disallow: /chatbot',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
    ];

    return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain']);
})->name('robots');

// ── Landing Page ──────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('landing');

// POST /daftar —  menangani Pendaftar + JawabanPendaftar
Route::post('/daftar', [LandingController::class, 'daftar'])->name('daftar');
Route::post('/daftar/validate', [LandingController::class, 'validatePendaftar'])->name('daftar.validate');

// ── Berita & Kegiatan ─────────────────────────────────────────
Route::get('/berita',        [PostController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}', [PostController::class, 'show'])->name('berita.show');

// ── Galeri ────────────────────────────────────────────────────
Route::get('/galeri', [GalleryController::class, 'index'])->name('galeri.index');

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
Route::get('/daftar', [DaftarController::class, 'index'])->name('daftar.form');

// ── E-Voting Rekap Suara (publik, full-screen) ────────────────
Route::get('/elections/{election}/rekap', function (App\Models\Election $election) {
    return view('elections.rekap', ['election' => $election]);
})->name('elections.rekap');

// ── Profil Anggota Publik (scan QR code) ─────────────────────
Route::get('/anggota/{publicId}', [IdCardController::class, 'publicProfile'])->name('anggota.show');

// ── ID Card Template Guide — harus sebelum wildcard {userId} ──
Route::get('/id-card/template', fn() => view('id-card.template-guide'))->name('id-card.template');

// ── ID Card (autentikasi: admin atau user sendiri) ────────────
Route::middleware('auth')->group(function () {
    Route::get('/id-card/preview',  [IdCardController::class, 'preview'])->name('id-card.preview');
    Route::get('/id-card/{userId}', [IdCardController::class, 'show'])->name('id-card.show');
});

// ── Laporan Keuangan E-Kas (dashboard, gated oleh lihat_saldo_kas) ──
Route::middleware('auth')
    ->get('/laporan-keuangan/pdf', [LaporanKeuanganController::class, 'pdf'])
    ->name('laporan-keuangan.pdf');

// ── Laporan Absensi (dashboard, gated oleh kelola_presensi) ──
Route::middleware('auth')
    ->get('/laporan-absensi/pdf', [LaporanAbsensiController::class, 'pdf'])
    ->name('laporan-absensi.pdf');

// ── RAG Document Download ──────────────────────────────────────
Route::middleware('auth')->get('/rag-documents/{document}/download', function (App\Models\RagDocument $document) {
    $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($document->path_file);
    abort_unless(file_exists($filePath), 404);

    return response()->download($filePath, $document->nama_file . '.pdf');
})->name('rag-documents.download');

// ── Chatbot RAG ────────────────────────────────────────────────
Route::prefix('chatbot')->name('chatbot.')->group(function () {
    Route::post('/chat',             [ChatbotController::class, 'chat'])->name('chat');
    Route::get('/suggested',         [ChatbotController::class, 'suggested'])->name('suggested');
    Route::post('/upload',           [ChatbotController::class, 'upload'])->name('upload')->middleware('auth');
    Route::get('/status/{id}',       [ChatbotController::class, 'status'])->name('status');
});

use Illuminate\Support\Facades\Mail;

Route::get('/tes-gmail', function () {
    try {
        Mail::raw('Halo! Ini adalah tes pengiriman email menggunakan Gmail SMTP dari aplikasi Laravel.', function ($message) {
            $message->to('ahostweb13@gmail.com') // Ganti ke email pribadi Anda untuk ngetes
                ->subject('Tes Konfigurasi Gmail SMTP');
        });
        return "<h1>SUKSES!</h1> Email berhasil dikirim via Gmail.";
    } catch (\Exception $e) {
        return "<h1>GAGAL!</h1> Pesan Error Aslinya: <br><br> <b>" . $e->getMessage() . "</b>";
    }
});
