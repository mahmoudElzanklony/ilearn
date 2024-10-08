<?php

namespace App\Services;

use App\Models\subjects_videos;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamImages
{
    public static function video_stream($filePath)
    {



        if (!Storage::disk('wasabi')->exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $size = Storage::disk('wasabi')->size($filePath);
        $mimeType = Storage::disk('wasabi')->mimeType($filePath);
        $stream = Storage::disk('wasabi')->readStream($filePath);

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $size,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
        ];

        $response = new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, $headers);

        // Send the streamed response to the client
        $url = url("/video/$filePath"); // Generate a URL that points to your streaming route



    }
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
