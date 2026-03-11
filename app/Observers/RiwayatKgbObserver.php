<?php

namespace App\Observers;

use App\Models\RiwayatKgb;
use App\Services\DashboardService;
use App\Services\PegawaiService;
use App\Services\SalaryCalculatorService;

class RiwayatKgbObserver
{
    public function __construct(private SalaryCalculatorService $salaryService) {}

    /**
     * On KGB saved (created or updated): sync via TMT Relay Baton.
     */
    public function saved(RiwayatKgb $riwayatKgb): void
    {
        $this->salaryService->syncCurrentSalary($riwayatKgb->pegawai);
        DashboardService::clearCache();
        PegawaiService::clearTimelineCache($riwayatKgb->pegawai_id);
    }

    /**
     * On KGB deleted: sync via TMT Relay Baton (rolls back gracefully).
     */
    public function deleted(RiwayatKgb $riwayatKgb): void
    {
        $this->salaryService->syncCurrentSalary($riwayatKgb->pegawai);
        DashboardService::clearCache();
        PegawaiService::clearTimelineCache($riwayatKgb->pegawai_id);
    }
}
