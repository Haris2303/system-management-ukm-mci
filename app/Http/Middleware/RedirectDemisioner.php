<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectDemisioner
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->hasRole('demisioner') || $user->hasRole('anggota') || $user->isKicked())) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = match (true) {
                $user->hasRole('anggota')    => 'Akun dengan role Anggota tidak memiliki akses ke panel admin.',
                $user->hasRole('demisioner') => 'Akun Anda telah dinonaktifkan (demisioner) dan tidak dapat mengakses panel admin.',
                $user->isKicked()            => 'Akun Anda telah dikeluarkan dari sistem.',
                default                      => 'Akses ditolak.',
            };

            return redirect()->to(filament()->getLoginUrl())
                ->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
