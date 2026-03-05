<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DUKExport implements FromArray, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private array $data) {}

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['No', 'NIP', 'Nama Lengkap', 'Golongan', 'Jabatan', 'Masa Kerja', 'Pendidikan', 'Usia'];
    }

    public function map($row): array
    {
        return [
            $row['ranking'],
            $row['nip'],
            $row['nama_lengkap'],
            $row['golongan_ruang'],
            $row['jabatan_terakhir'],
            $row['masa_kerja'],
            $row['pendidikan_terakhir'],
            $row['usia'],
        ];
    }

    public function title(): string
    {
        return 'DUK';
    }
}
