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

        // Middleware role/tendik juga diterapkan ulang pada setiap aksi Livewire (/livewire/update),
        // bukan hanya saat halaman pertama dibuka.
        \Livewire\Livewire::addPersistentMiddleware([
            \Spatie\Permission\Middleware\RoleMiddleware::class,
            \App\Http\Middleware\PastikanTendik::class,
        ]);

        // Identitas sekolah dari Pengaturan Sistem menimpa config('sekolah')
        if (! $this->app->runningUnitTests()) {
            app(\App\Services\PengaturanService::class)->terapkanIdentitas();
        }
    }
}
