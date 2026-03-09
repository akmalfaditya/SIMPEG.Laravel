<?php

namespace App\Http\Controllers;

use App\Enums\GolonganRuang;
use App\Models\TabelGaji;

class GolonganController extends Controller
{
    public function index()
    {
        $gajiStats = TabelGaji::selectRaw('golongan_ruang, MIN(gaji_pokok) as gaji_min, MAX(gaji_pokok) as gaji_max, COUNT(*) as jumlah_mkg')
            ->groupBy('golongan_ruang')
            ->get()
            ->keyBy(fn ($row) => $row->golongan_ruang->value);

        $golonganList = collect(GolonganRuang::cases())->map(function (GolonganRuang $g) use ($gajiStats) {
            $stat = $gajiStats->get($g->value);
            return (object) [
                'enum' => $g,
                'value' => $g->value,
                'label' => $g->label(),
                'pangkat' => self::pangkat($g),
                'gaji_min' => $stat?->gaji_min,
                'gaji_max' => $stat?->gaji_max,
                'jumlah_mkg' => $stat?->jumlah_mkg ?? 0,
            ];
        });

        return view('admin.golongan.index', compact('golonganList'));
    }

    private static function pangkat(GolonganRuang $g): string
    {
        return match ($g) {
            GolonganRuang::I_a => 'Juru Muda',
            GolonganRuang::I_b => 'Juru Muda Tingkat I',
            GolonganRuang::I_c => 'Juru',
            GolonganRuang::I_d => 'Juru Tingkat I',
            GolonganRuang::II_a => 'Pengatur Muda',
            GolonganRuang::II_b => 'Pengatur Muda Tingkat I',
            GolonganRuang::II_c => 'Pengatur',
            GolonganRuang::II_d => 'Pengatur Tingkat I',
            GolonganRuang::III_a => 'Penata Muda',
            GolonganRuang::III_b => 'Penata Muda Tingkat I',
            GolonganRuang::III_c => 'Penata',
            GolonganRuang::III_d => 'Penata Tingkat I',
            GolonganRuang::IV_a => 'Pembina',
            GolonganRuang::IV_b => 'Pembina Tingkat I',
            GolonganRuang::IV_c => 'Pembina Utama Muda',
            GolonganRuang::IV_d => 'Pembina Utama Madya',
            GolonganRuang::IV_e => 'Pembina Utama',
        };
    }
}
