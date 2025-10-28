<?php

namespace App\Services;

use Carbon\Carbon;

class ImageUploadService {

    public function uploadImage($file, $name = null)
    {
        $folder = public_path();
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        $day = Carbon::now()->day;

        $filePath = '/' . 'upload' . '/' . $year . '/' . $month . '/' . $day . '/';

        if($name === null) {
            $fileName = str()->random() . time() . '.' . $file->extension();

        } else {
            $fileName = $name . '.' . $file->extension();
        }

        $file->move($folder . $filePath, $fileName);
        $file = "$filePath" . "$fileName";
        return $file;
    }


    public function removeImage($file)
    {
        $file = ltrim($file, '/');
        if(file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Upload multiple images and return array of file paths
     */
    public function uploadMultipleImages($files, $name = null)
    {
        $uploadedFiles = [];
        
        foreach ($files as $file) {
            $uploadedFiles[] = $this->uploadImage($file, $name);
        }
        
        return $uploadedFiles;
    }

    /**
     * Remove multiple images
     */
    public function removeMultipleImages($files)
    {
        foreach ($files as $file) {
            $this->removeImage($file);
        }
    }

}