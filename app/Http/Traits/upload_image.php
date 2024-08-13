<?php


namespace App\Http\Traits;


use App\Actions\ImageModalSave;
use App\Models\images;
use App\Services\Messages;
use Illuminate\Support\Facades\Storage;
use FFMpeg;
use FFMpeg\Format\Video\X264;

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

            // Store the video temporarily in the local storage
            // Create a temporary file
            $tempPath = sys_get_temp_dir() . '/' . $filePath;

            // Open the video with FFmpeg
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($file->getRealPath());

            // Set the format for the video compression
            $format = new X264();
            $format->setKiloBitrate(1000); // Adjust the bitrate as needed

            // Save the compressed video to the temporary file
            $video->save($format, $tempPath);

            // Stream the file to Wasabi
            $stream = fopen($tempPath, 'r+');
            Storage::disk('wasabi')->put($filePath, $stream);

            // Close the stream and delete the temporary file
            fclose($stream);
            unlink($tempPath);
        }else{
            $file->move(public_path('videos/'), $name);
        }
        return $name;
        //$file->move(public_path('videos/'), $name);
    }


}
