<?php

namespace App\Http\Controllers;

use App\Models\PenilaianKinerja;
use App\Models\RiwayatHukumanDisiplin;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatKgb;
use App\Models\RiwayatLatihanJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPendidikan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    private const TYPE_MODEL_MAP = [
        'pangkat' => RiwayatPangkat::class,
        'jabatan' => RiwayatJabatan::class,
        'kgb' => RiwayatKgb::class,
        'hukuman' => RiwayatHukumanDisiplin::class,
        'pendidikan' => RiwayatPendidikan::class,
        'latihan' => RiwayatLatihanJabatan::class,
        'skp' => PenilaianKinerja::class,
    ];

    public function download(string $type, int $id): BinaryFileResponse
    {
        $modelClass = self::TYPE_MODEL_MAP[$type] ?? null;

        if (!$modelClass) {
            abort(404, 'Tipe dokumen tidak valid.');
        }

        $record = $modelClass::findOrFail($id);

        if (!$record->file_pdf_path || !Storage::disk('documents')->exists($record->file_pdf_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $fullPath = Storage::disk('documents')->path($record->file_pdf_path);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($record->file_pdf_path) . '"',
        ]);
    }
}
