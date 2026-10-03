<?php

namespace App\Providers;

use App\Models\TugasProker;
use App\Observers\TugasProkerObserver;
use App\Services\ElectionService;
use App\Services\PendaftarService;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ElectionService::class);
        $this->app->singleton(PendaftarService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Log aktivitas: login/logout & perubahan data model
        ActivityLogger::register();

        // ⭐ Daftarkan observer untuk auto-update progress proker
        TugasProker::observe(TugasProkerObserver::class);

        // ⭐ Sinkronkan URL storage disk 'public' dengan host request aktual.
        // Ini diperlukan saat APP_URL diset ke IP lokal (misal: testing di mobile)
        // agar Filament FileUpload & Storage::url() generate URL yang bisa diakses.
        if (!app()->runningInConsole()) {
            $host = request()->getSchemeAndHttpHost();
            config(['filesystems.disks.public.url' => $host . '/storage']);
            URL::forceRootUrl($host);
        }

        // Paksa HTTPS kecuali di host lokal (*.test / localhost) yang berjalan di HTTP
        $isLocalHost = ! app()->runningInConsole()
            && (bool) preg_match('/(^localhost$|^127\.0\.0\.1$|\.test$|\.localhost$)/', request()->getHost());

        if (! $isLocalHost) {
            URL::forceScheme('https');
        }
    }
}
