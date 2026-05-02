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

        if ($user && $user->hasRole('demisioner')) {
            // Logout dari sesi web
            Auth::logout();

            // Invalidate sesi agar tidak bisa di-resume
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->to(filament()->getLoginUrl())
                ->withErrors([
                    'email' => 'Akun ada telah dinonaktifkan (demisioner) dan tidak dapat mengakses panel admin.'
                ]);
        }
        return $next($request);
    }
}
