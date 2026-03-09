<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KenaikanPangkatExport implements FromArray, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private array $data) {}

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['NIP', 'Nama Lengkap', 'Golongan Saat Ini', 'Golongan Berikutnya', 'Masa Kerja Gol.', 'MK', 'SKP', 'Latihan', 'Disiplin', 'Status', 'Proyeksi Periode', 'Catatan Hukdis', 'Keterangan'];
    }

    public function map($row): array
    {
        return [
            $row['nip'],
            $row['nama_lengkap'],
            $row['golongan_saat_ini'],
            $row['golongan_berikutnya'],
            $row['masa_kerja_golongan'],
            $row['syarat_masa_kerja'] ? '✓' : '✗',
            $row['syarat_skp'] ? '✓' : '✗',
            $row['syarat_latihan'] ? '✓' : '✗',
            $row['syarat_hukuman'] ? '✓' : '✗',
            $row['is_eligible'] ? 'Eligible' : 'Belum',
            $row['proyeksi_periode'] ?? '-',
            $row['hukdis_pangkat_note'] ?? '-',
            $row['alasan_tidak_eligible'] ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Kenaikan Pangkat';
    }
}
