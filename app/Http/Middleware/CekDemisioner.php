<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekDemisioner
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('demisioner')) {
            // Hapus semua token Sanctum agar tidak bisa pakai lagi
            // Penting agar perangkat lama langsung kehilangan akses
            $user->tokens()->delete();

            return response()->json([
                'pesan' => 'Akun Anda telah dinonaktifkan (demisioner). Silakan hubungi admin jika ada pertanyaan.',
                'kode'  => 'AKUN_DEMISIONER',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
