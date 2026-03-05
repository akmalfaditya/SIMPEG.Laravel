<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SatyalencanaExport implements FromArray, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private array $data) {}

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['NIP', 'Nama Lengkap', 'Pangkat', 'Jabatan', 'Masa Kerja (Tahun)', 'Milestone', 'Penghargaan'];
    }

    public function map($row): array
    {
        return [
            $row['nip'],
            $row['nama_lengkap'],
            $row['pangkat_terakhir'],
            $row['jabatan_terakhir'],
            $row['masa_kerja_tahun'],
            $row['milestone'] . ' Tahun',
            $row['nama_penghargaan'],
        ];
    }

    public function title(): string
    {
        return 'Satyalencana';
    }
}
