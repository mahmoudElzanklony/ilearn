<?php

namespace App\Actions\v2;

use Illuminate\Support\Facades\Storage;

class GenerateWasbiTmpUrl
{
    public static function execute($video_name){
        if(env('WAS_STATUS') == 1) {
            $url = Storage::disk('wasabi')->temporaryUrl(
                'videos/' . $video_name, now()->addMinutes(400) // URL expires in 3 hours
            );
            return $url;
        }
        return null;
    }
}
