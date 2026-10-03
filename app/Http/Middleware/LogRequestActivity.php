<?php

namespace App\Http\Middleware;

use App\Support\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mencatat setiap request web (method, url, status, durasi, user, ip)
 * dan membagikan request_id ke semua log selama request berjalan.
 */
class LogRequestActivity
{
    /** Path yang tidak dicatat agar log tidak penuh noise. */
    private const IGNORED = ['up', 'livewire/*', 'robots.txt', 'sitemap.xml', 'storage/*', 'build/*'];

    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();
        $request->attributes->set('request_id', $requestId);
        $start = microtime(true);

        Log::shareContext(['request_id' => $requestId]);

        $response = $next($request);

        if (ActivityLogger::enabled() && ! $request->is(...self::IGNORED)) {
            $status = $response->getStatusCode();

            ActivityLogger::log(
                $status >= 500 ? 'error' : ($status >= 400 ? 'warning' : 'info'),
                'request',
                [
                    'method'      => $request->method(),
                    'url'         => $request->fullUrl(),
                    'status'      => $status,
                    'duration_ms' => round((microtime(true) - $start) * 1000, 1),
                    'user_id'     => $request->user()?->getAuthIdentifier(),
                    'ip'          => $request->ip(),
                    'user_agent'  => Str::limit((string) $request->userAgent(), 120, ''),
                ],
            );
        }

        return $response;
    }
}
