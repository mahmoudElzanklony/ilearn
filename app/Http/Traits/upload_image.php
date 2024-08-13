<?php


namespace App\Http\Traits;


use App\Actions\ImageModalSave;
use App\Models\images;
use App\Services\Messages;
use Illuminate\Support\Facades\Storage;
use FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Mockery\Exception\RuntimeException;

trait upload_image
{
    public function upload($file,$folder_name,$type = 'one'){
        $valid_extensions = ['png','jpg','jpeg','gif','svg'];
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
            // Get the uploaded file

            // Set up AWS S3 Client with Wasabi credentials
            $s3Client = new S3Client([
                'version' => 'latest',
                'region'  => env('WAS_DEFAULT_REGION'), // Ensure this is set in your .env
                'endpoint' => env('WAS_ENDPOINT'), // Wasabi's S3-compatible endpoint
                'credentials' => [
                    'key'    => env('WAS_ACCESS_KEY_ID'),
                    'secret' => env('WAS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $bucket = env('WAS_BUCKET');
            $key = 'videos/' . $name;

            // Define a temporary file path
            $temporaryFilePath = storage_path('app/tmp/') . $name;

            try {
                // Create a new FFMpeg instance
                $ffmpeg = FFMpeg\FFMpeg::create();

                // Open the video file with FFmpeg
                $video = $ffmpeg->open($file->getRealPath());

                // Set the format for the video compression
                $format = new X264();
                $format->setKiloBitrate(1000); // Adjust the bitrate as needed

                // Save the compressed video to a temporary file
                $video->save($format, $temporaryFilePath);

                // Stream the compressed video file directly to Wasabi
                $stream = fopen($temporaryFilePath, 'r+');
                $result = $s3Client->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $key,
                    'Body'   => $stream,
                    'ACL'    => 'public-read', // Adjust as needed
                ]);

                // Close the stream and delete the temporary file
                fclose($stream);
                unlink($temporaryFilePath);


            } catch (S3Exception $e) {
                // Handle S3 exceptions
                return response()->json(['error' => 'S3 upload failed: ' . $e->getMessage()], 500);
            } catch (\Exception $e) {
                // Handle general exceptions
                return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
            }
        }else{
            $file->move(public_path('videos/'), $name);
        }
        return $name;
        //$file->move(public_path('videos/'), $name);
    }


}
