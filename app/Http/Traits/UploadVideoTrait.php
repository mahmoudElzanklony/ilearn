<?php

namespace App\Http\Traits;

use Aws\S3\S3Client;

trait UploadVideoTrait
{
    public function upload_video($file)
    {
        $name = time().rand(0,9999999999999). '_video.' . $file->getClientOriginalExtension();
        $filePath = 'videos/'. $name;


        if(env('WAS_STATUS') == 1) {


            // Define the paths

            $originalFilePath = $file->getRealPath();
            $compressedFilePath = storage_path('app/tmp/') . $name;

            try {

                $command = "ffmpeg -i $originalFilePath -vcodec libx264 -crf 28 -preset slower -b:v 500k -maxrate 1M -bufsize 1M -tune zerolatency -acodec aac -b:a 128k -movflags +faststart $compressedFilePath";
                exec($command . ' 2>&1', $output, $returnVar);

                // Check if FFmpeg failed
                if ($returnVar !== 0) {
                    throw new \Exception("FFmpeg compression failed: " . implode("\n", $output));
                }

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
                $result = $s3Client->putObject([
                    'Bucket' => env('WAS_BUCKET'),
                    'Key'    => 'videos/' . $name,
                    'SourceFile' => $compressedFilePath,
                    'ACL'    => 'public-read',
                ]);

                // Delete the temporary compressed file
                unlink($compressedFilePath);


            } catch (\Exception $e) {
                // Handle exceptions
                if (file_exists($compressedFilePath)) {
                    unlink($compressedFilePath);
                }
                return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
            }


        }else{
            $file->move(public_path('videos/'), $name);
        }
        return $name;
        //$file->move(public_path('videos/'), $name);
    }


}
