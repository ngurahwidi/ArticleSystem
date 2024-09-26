<?php

namespace App\Services\Misc;

use Illuminate\Http\UploadedFile;

class FileUpload
{
    public static function upload(UploadedFile $file, string $name, string $path)
    {
        $sanitizedTitle = str_replace(' ', '_', $name);
        $lowerTitle = strtolower($sanitizedTitle);
        $extension = $file->getClientOriginalExtension();
        $filename = $lowerTitle.'.'.$extension;
        return $file->storeAs($path, $filename, 'public');
    }
}