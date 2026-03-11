<?php

namespace App\Observers;

use App\Models\Pegawai;
use App\Services\DashboardService;

class PegawaiObserver
{
    public function saved(Pegawai $pegawai): void
    {
        DashboardService::clearCache();
    }

    public function deleted(Pegawai $pegawai): void
    {
        DashboardService::clearCache();
    }

    public function restored(Pegawai $pegawai): void
    {
        DashboardService::clearCache();
    }
}
