<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StreamImages
{
    public static function stream($path)
    {
        if (!Storage::disk('wasabi')->exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $fileUrl = Storage::disk('wasabi')->temporaryUrl(
            $path, now()->addMinutes(180) // URL expires in 5 minutes
        );

        return $fileUrl;

    }
}
