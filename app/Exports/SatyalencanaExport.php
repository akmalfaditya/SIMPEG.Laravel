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
        return ['NIP', 'Nama Lengkap', 'Pangkat', 'Jabatan', 'Tgl Mulai Hitung', 'Masa Kerja Murni (Tahun)', 'Milestone', 'Penghargaan'];
    }

    public function map($row): array
    {
        return [
            $row['nip'],
            $row['nama_lengkap'],
            $row['pangkat_terakhir'],
            $row['jabatan_terakhir'],
            $row['tanggal_mulai_hitung'] . ($row['is_reset'] ? ' (RESET)' : ''),
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
