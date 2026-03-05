<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KGBExport implements FromArray, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private array $data) {}

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['NIP', 'Nama Lengkap', 'Pangkat', 'TMT KGB Terakhir', 'Jatuh Tempo', 'Hari Menuju', 'Status'];
    }

    public function map($row): array
    {
        return [
            $row['nip'],
            $row['nama_lengkap'],
            $row['pangkat_terakhir'],
            $row['tmt_kgb_terakhir']->format('d/m/Y'),
            $row['tanggal_jatuh_tempo']->format('d/m/Y'),
            $row['hari_menuju_jatuh_tempo'],
            $row['status'],
        ];
    }

    public function title(): string
    {
        return 'KGB';
    }
}
