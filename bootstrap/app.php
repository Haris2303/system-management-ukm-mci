<?php

use App\Http\Middleware\CekDemisioner;
use App\Http\Middleware\LogRequestActivity;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->preventRequestForgery(except: [
            'chatbot/*'
        ]);
        $middleware->web(append: [LogRequestActivity::class]);
        $middleware->alias([
            'cek.demisioner' => CekDemisioner::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Sertakan konteks request di setiap exception yang dilaporkan ke laravel.log
        $exceptions->context(fn () => [
            'request_id' => request()->attributes->get('request_id'),
            'user_id'    => auth()->id(),
            'method'     => request()->method(),
            'url'        => request()->fullUrl(),
            'ip'         => request()->ip(),
        ]);
    })->create();
