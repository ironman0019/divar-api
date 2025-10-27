<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{

    /**
     * Upload a file to a private storage directory and return file details.
     */
    public function uploadFile(UploadedFile $file, $name = null): array
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        $day = Carbon::now()->day;

        $filePath = "$year/$month/$day";
        
        if ($name === null) {
            $fileName = str()->random() . time() . '.' . $file->extension();
        } else {
            $fileName = $name . '.' . $file->extension();
        }

        $fullPath = $file->storeAs($filePath, $fileName);

        return [
            'path' => $fullPath,
            'type' => $file->getClientMimeType(),
            'name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize()
        ];
    }

    /**
     * Delete a file from storage.
     */
    public function deleteFile(string $filePath): bool
    {
        return Storage::delete($filePath);
    }
}
