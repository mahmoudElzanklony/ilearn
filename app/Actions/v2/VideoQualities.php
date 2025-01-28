<?php

namespace App\Actions\v2;

use Aws\S3\S3Client;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\File;

class VideoQualities
{
    private static $final_names = [];

    public static function getFinalNames(): array
    {
        return self::$final_names;
    }

    private static function getQualities()
    {
        return  [
            '144p' => ['width' => 192, 'height' => 144, 'bitrate' => 100],
            '360p' => ['width' => 640, 'height' => 360, 'bitrate' => 300],
        ];
    }
    private static function saveQualities($qualities,$video,$compressedFilePath,$name){
        foreach ($qualities as $quality => $settings) {
            $format = new X264('aac', 'libx264');
            $format->setVideoCodec('libx264')
                ->setKiloBitrate($settings['bitrate'])
                ->setAudioCodec('aac')
                ->setAudioKiloBitrate(48) // Lower audio bitrate
                ->setAdditionalParameters([
                    '-crf', '36',           // High CRF for aggressive compression
                    '-preset', 'medium',    // Balanced speed and compression
                    '-profile:v', 'baseline', // Basic profile for compatibility
                    '-r', '15',             // Lower frame rate for smaller size
                ]);


            $video->filters()->resize(new Dimension($settings['width'], $settings['height']));
            $path = $compressedFilePath."/".$name."_".$quality.".mp4";

            if(env('WAS_STATUS') == 1){
                // save at wasbi
                self::save_at_wasabi($name,$path);
            }else{
                $video->save($format, $path);
            }
            // Delete the temporary compressed file
            //unlink($path);

        }
    }

    public static function save_videos_using_commands($video_obj,$compressedFilePath,$name)
    {
        self::$final_names = [];
        $items = [
          [
            'quality'=>'144',
            'scale'=>'-vf scale=-1:144',
          ],
          [
            'quality'=>'360',
            'scale'=>'-vf scale=640:-1',
          ],
          [
            'quality'=>'original',
            'scale'=>'',
          ],
        ];

        foreach ($items as $item){
            $file_name = $name."-".$item['quality'].".mp4";

            exec("ffmpeg -i $video_obj ".$item['scale']." $compressedFilePath$file_name");
            self::save_at_wasabi($name,$compressedFilePath.$file_name);
            array_push(self::$final_names,['quality'=>$item['quality'],'name'=>$file_name]);

        }

    }

    public static function save_at_wasabi($name,$compressedFilePath,$start_path_wasbi = 'videos')
    {
        if(env('WAS_STATUS') == 1){
            // Set up AWS S3 Client with Wasabi credentials
            $s3Client = new S3Client([
                'version' => 'latest',
                'region'  => env('WAS_DEFAULT_REGION'),
                'endpoint' => env('WAS_ENDPOINT'),
                'credentials' => [
                    'key'    => env('WAS_ACCESS_KEY_ID'),
                    'secret' => env('WAS_SECRET_ACCESS_KEY'),
                ],
            ]);
            // Upload the compressed video to Wasabi
            dd($start_path_wasbi. $name,$compressedFilePath,is_file($compressedFilePath));
            $result = $s3Client->putObject([
                'Bucket' => env('WAS_BUCKET'),
                'Key'    => $start_path_wasbi. $name,
                'SourceFile' => $compressedFilePath,
                'ACL'    => 'public-read',
            ]);
        }


    }
    public static function execute($file,$name,$is_command = true)
    {
        // Define the paths

        $originalFilePath = $file->getRealPath();
        $compressedFilePath = storage_path('app/tmp/');
        $filePath = $file->getRealPath();

        if($is_command){
            self::save_videos_using_commands($file,$compressedFilePath,$name);
            if(env('WAS_STATUS') == 1){
                File::cleanDirectory($compressedFilePath);
            }
            return 1;
        }
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => env('APP_ENV') == 'local'?'C:/ffmpeg/ffmpeg.exe':'/usr/bin/ffmpeg',
            'ffprobe.binaries' => env('APP_ENV') == 'local'?'C:/ffmpeg/ffprobe.exe':'/usr/bin/ffprobe',
            'timeout'          => 3600, // seconds
            'ffmpeg.threads'   => 12,
        ]);



        $video = $ffmpeg->open($filePath);



        // Quality Settings
        $qualities = self::getQualities();

        // save qualities
        self::saveQualities($qualities,$video,$compressedFilePath,$name);



    }
}
