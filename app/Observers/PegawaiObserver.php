<?php

namespace App\Observers;

use App\Models\Pegawai;
use App\Services\DashboardService;
use App\Services\PegawaiService;

class PegawaiObserver
{
    public function saved(Pegawai $pegawai): void
    {
        DashboardService::clearCache();
        PegawaiService::clearTimelineCache($pegawai->id);
    }

    public function deleted(Pegawai $pegawai): void
    {
        DashboardService::clearCache();
        PegawaiService::clearTimelineCache($pegawai->id);
    }

    public function restored(Pegawai $pegawai): void
    {
        DashboardService::clearCache();
        PegawaiService::clearTimelineCache($pegawai->id);
    }
}
