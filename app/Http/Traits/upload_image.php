<?php


namespace App\Http\Traits;


use App\Actions\ImageModalSave;
use App\Models\images;
use App\Services\Messages;

trait upload_image
{
    public function upload($file,$folder_name,$type = 'one'){
        $valid_extensions = ['png','jpg','jpeg','gif','svg'];
        if($type == 'one') {
            if (in_array($file->getClientOriginalExtension(), $valid_extensions)) {
                $name = time().rand(0,9999999999999). '_image.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/' . $folder_name), $name);
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
        $file->move(public_path('videos/'), $name);
        return $name;
    }


}
