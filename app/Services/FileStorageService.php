<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileStorageService
{
    /**
     * Upload logbook or claim file attachment safely.
     */
    public function uploadFile(UploadedFile $file, string $folder, string $prefix = 'file'): string
    {
        $destinationPath = public_path('uploads/' . trim($folder, '/'));
        
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filename = $prefix . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $filename);

        return 'uploads/' . trim($folder, '/') . '/' . $filename;
    }
}
