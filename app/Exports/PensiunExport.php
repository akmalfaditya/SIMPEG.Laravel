<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PensiunExport implements FromArray, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private array $data) {}

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['NIP', 'Nama Lengkap', 'Jabatan', 'BUP', 'Tgl Pensiun', 'Sisa (Bulan)', 'Level'];
    }

    public function map($row): array
    {
        return [
            $row['nip'],
            $row['nama_lengkap'],
            $row['jabatan_terakhir'],
            $row['bup'],
            $row['tanggal_pensiun']->format('d/m/Y'),
            $row['bulan_menuju_pensiun'],
            $row['alert_level'],
        ];
    }

    public function title(): string
    {
        return 'Pensiun';
    }
}
