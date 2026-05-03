<?php

namespace App\Providers;

use App\Models\TugasProker;
use App\Observers\TugasProkerObserver;
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
    }
}
