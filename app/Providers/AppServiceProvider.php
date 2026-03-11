<?php

namespace App\Providers;

use App\Models\Pegawai;
use App\Models\PenilaianKinerja;
use App\Models\RiwayatHukumanDisiplin;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatKgb;
use App\Models\RiwayatLatihanJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPendidikan;
use App\Models\RiwayatPenghargaan;
use App\Observers\PegawaiObserver;
use App\Observers\RiwayatKgbObserver;
use App\Observers\RiwayatPangkatObserver;
use App\Services\PegawaiService;
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

        // Clear career timeline cache when any riwayat changes
        $clearTimeline = function ($model) {
            PegawaiService::clearTimelineCache($model->pegawai_id);
        };
        foreach ([RiwayatJabatan::class, RiwayatHukumanDisiplin::class, RiwayatPendidikan::class, RiwayatLatihanJabatan::class, PenilaianKinerja::class, RiwayatPenghargaan::class] as $modelClass) {
            $modelClass::saved($clearTimeline);
            $modelClass::deleted($clearTimeline);
        }
    }
}
