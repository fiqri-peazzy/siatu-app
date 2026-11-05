<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\PengaturanService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Identitas sekolah dari Pengaturan Sistem menimpa config('sekolah')
        if (! $this->app->runningUnitTests()) {
            app(\App\Services\PengaturanService::class)->terapkanIdentitas();
        }
    }
}
