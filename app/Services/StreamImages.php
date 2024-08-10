<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StreamImages
{
    public static function stream($path)
    {

        if (Storage::disk('wasabi')->exists($path)) {
            $stream = Storage::disk('wasabi')->readStream($path);
            $size = Storage::disk('wasabi')->size($path);
            $mimeType = Storage::disk('wasabi')->mimeType($path);

            // Stream the image content as base64
            $imageContent = stream_get_contents($stream);
            fclose($stream);

            return  'data:' . $mimeType . ';base64,' . base64_encode($imageContent);
        }
        return 'image not found';

    }
}
