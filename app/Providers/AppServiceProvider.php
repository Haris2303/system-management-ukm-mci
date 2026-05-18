<?php

namespace App\Providers;

use App\Models\TugasProker;
use App\Observers\TugasProkerObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
    }
}
