<?php

namespace App\Actions\v2;

class BaseUrlForVideoAction
{
    public static function handle(string $url){
        if(env('WAS_BASE_URL')){
            if(env('WAS_BASE_URL') == "https://ilearn.b-cdn.net"){
                $newUrl = str_replace(
                    'https://ilearn.s3.ap-northeast-1.wasabisys.com/videos',
                    'https://ilearn.b-cdn.net',
                    $url
                );
                return $newUrl;
            }
        }
        return $url;
    }
}
