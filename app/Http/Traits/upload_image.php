<?php


namespace App\Http\Traits;


use App\Actions\ImageModalSave;
use App\Models\images;
use App\Services\Messages;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use FFMpeg;
use FFMpeg\Format\Video\X264;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\CachingStream;

trait upload_image
{
    public function upload($file,$folder_name,$type = 'one'){
        $valid_extensions = ['png','jpg','jpeg','gif','svg','webp'];
        if($type == 'one') {
            if (in_array($file->getClientOriginalExtension(), $valid_extensions)) {
                $name = time().rand(0,9999999999999). '_image.' . $file->getClientOriginalExtension();

                $filePath = $folder_name.'/'. $name;
                if(env('WAS_STATUS') == 1) {
                    Storage::disk('wasabi')
                        ->put(
                            $filePath,
                            file_get_contents($file->getRealPath())
                        );
                }else{
                    $file->move(public_path('images/' . $folder_name), $name);
                }

                return $name;
            } else {
                return Messages::error('image extension is not correct');
            }
        }
    }

    public  function check_upload_image($image,$folder_name,$model_id ,$model_name)
    {
        if($image != null){
            $name = $folder_name.'/'.$this->upload($image,$folder_name);
        }else{
            $name = $folder_name.'/default.png';
        }
        images::query()
            ->where('imageable_id','=',$model_id)
            ->where('imageable_type','=','App\Models\\'.$model_name)->delete();
        ImageModalSave::make($model_id,$model_name,$name);
    }

    public function upload_video($file)
    {
        $name = time().rand(0,9999999999999). '_video.' . $file->getClientOriginalExtension();
        $filePath = 'videos/'. $name;




        if(env('WAS_STATUS') == 1) {
            /*$s3Client = new S3Client([
                'version' => 'latest',
                'region'  => env('WAS_DEFAULT_REGION'),
                'endpoint' => env('WAS_ENDPOINT'),
                'credentials' => [
                    'key'    => env('WAS_ACCESS_KEY_ID'),
                    'secret' => env('WAS_SECRET_ACCESS_KEY'),
                ],
            ]);

            try {
                // Define the FFmpeg command
                // Simplified FFmpeg command
                $command = [
                    'ffmpeg',
                    '-i', $file->getRealPath(),
                    '-f', 'mp4', // Ensure the output format is MP4
                    'pipe:1' // Output to standard output
                ];

                // Open a process with pipes
                $descriptors = [
                    1 => ['pipe', 'w'], // Standard output
                    2 => ['pipe', 'w']  // Standard error
                ];
                $process = proc_open($command, $descriptors, $pipes);

                if (!is_resource($process)) {
                    throw new \Exception("Failed to start FFmpeg process.");
                }

                if (!is_resource($pipes[1])) {
                    throw new \Exception("FFmpeg did not return a valid stream resource.");
                }

                // Create a Guzzle stream from the FFmpeg output
                $stream = new Stream($pipes[1]);

                // Wrap the stream in a CachingStream to make it seekable
                $cachingStream = new CachingStream($stream);

                // Upload the CachingStream to Wasabi
                $result = $s3Client->putObject([
                    'Bucket' => env('WAS_BUCKET'),
                    'Key'    => $filePath,
                    'Body'   => $cachingStream, // Use the seekable CachingStream
                    'ACL'    => 'public-read',
                ]);

                // Close the pipes and process
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);
                // Return the URL of the uploaded video on Wasabi

                // Return the URL of the uploaded video on Wasabi

            } catch (\Exception $e) {
                return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
            }

*/


// Define the paths

            $originalFilePath = $file->getRealPath();
            $compressedFilePath = storage_path('app/tmp/') . $name;

            try {
                // Compress the video using FFmpeg
                //$command = "ffmpeg -i $originalFilePath -vcodec libx264 -b:v 1000k -acodec aac -b:a 128k $compressedFilePath";
               /* $command = "ffmpeg -i $originalFilePath -vcodec libx264 -crf 23 -preset medium -acodec aac -b:a 128k -threads 4 $compressedFilePath";
                exec($command . ' 2>&1', $output, $returnVar);*/

                $command = "ffmpeg -i $originalFilePath -vcodec libx264 -b:v 1000k -acodec aac -b:a 128k -vf scale=-1:360 $compressedFilePath";
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
