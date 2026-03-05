<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentUploadService
{
    public function upload(UploadedFile $file, string $subfolder): string
    {
        return $file->store($subfolder, 'documents');
    }

    public function delete(?string $path): bool
    {
        if (!$path || !Storage::disk('documents')->exists($path)) {
            return false;
        }

        return Storage::disk('documents')->delete($path);
    }

    public function getUrl(string $path): ?string
    {
        if (!Storage::disk('documents')->exists($path)) {
            return null;
        }

        return Storage::disk('documents')->path($path);
    }
}
