<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StreamImages
{
    public static function stream($path)
    {
        if (!Storage::disk('wasabi')->exists($path)) {
             return 'File not found';
        }

        $fileUrl = Storage::disk('wasabi')->temporaryUrl(
            $path, now()->addMinutes(180) // URL expires in 3 hours
        );

        return $fileUrl;

    }
}
