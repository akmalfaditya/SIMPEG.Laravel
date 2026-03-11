<?php

namespace App\Providers;

use App\Models\Pegawai;
use App\Models\RiwayatKgb;
use App\Models\RiwayatPangkat;
use App\Observers\PegawaiObserver;
use App\Observers\RiwayatKgbObserver;
use App\Observers\RiwayatPangkatObserver;
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
        Pegawai::observe(PegawaiObserver::class);
        RiwayatKgb::observe(RiwayatKgbObserver::class);
        RiwayatPangkat::observe(RiwayatPangkatObserver::class);
    }
}
