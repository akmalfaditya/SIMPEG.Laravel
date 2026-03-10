<?php

namespace App\Observers;

use App\Models\RiwayatPangkat;
use App\Services\SalaryCalculatorService;

class RiwayatPangkatObserver
{
    public function __construct(private SalaryCalculatorService $salaryService) {}

    /**
     * On Pangkat saved (created or updated): sync via TMT Relay Baton.
     */
    public function saved(RiwayatPangkat $riwayatPangkat): void
    {
        $this->salaryService->syncCurrentSalary($riwayatPangkat->pegawai);
    }

    /**
     * On Pangkat deleted: sync via TMT Relay Baton (rolls back gracefully).
     */
    public function deleted(RiwayatPangkat $riwayatPangkat): void
    {
        $this->salaryService->syncCurrentSalary($riwayatPangkat->pegawai);
    }
}
