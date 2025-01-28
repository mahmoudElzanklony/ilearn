<?php

namespace App\Http\Controllers\v2;

use App\Actions\v2\GenerateWasbiTmpUrl;
use App\Actions\v2\VideoQualities;
use App\Filters\EndDateFilter;
use App\Filters\NameFilter;
use App\Filters\StartDateFilter;
use App\Filters\SubjectIdFilter;
use App\Filters\subjects_videos\UniversityFilter;
use App\Filters\UserIdFilter;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SubjectsVideosControllerResource;
use App\Http\Requests\subjectsVideoFormRequest;
use App\Http\Resources\SubjectsVideosResource;
use App\Http\Resources\v2\v2SubjectVideoResource;
use App\Models\subjects_videos;
use App\Models\video_qualities;
use App\Services\Messages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubjectsVideosController extends SubjectsVideosControllerResource
{
    //
    public function show(string $id)
    {
        //
        $data  = subjects_videos::query()->with('qualities')
            ->where('id', $id)
            ->FailIfNotFound(__('errors.not_found_data'));
        return response()->json([
            'data'=>v2SubjectVideoResource::make($data),
            'video_size'=>$this->get_size_video($data)
        ]);
    }
    public function delete_old_file($id,$type = 'videos')
    {
        $video = subjects_videos::query()->with('qualities')->find($id);

        if(file_exists(public_path($type.'/'.$video->video))) {
            unlink(public_path($type.'/' . $video->video));
        }
        if($type == 'videos') {
            foreach ($video['qualities'] as $videoQuality) {
                if (file_exists(public_path('videos/' . $videoQuality->name))) {
                    unlink(public_path('videos/' . $videoQuality->name));
                }
            }
        }
    }

    public function save_qualities_inDB($subject_video)
    {
        $videos = VideoQualities::getFinalNames();
        array_shift($videos);
        video_qualities::query()->where('subject_video_id',$subject_video->id)->delete();
        foreach ($videos as $video) {
            video_qualities::query()->create([
                'subject_video_id' => $subject_video->id,
                'name'=>$video['name'],
                'quality'=>$video['quality'],
                'wasbi_url'=>GenerateWasbiTmpUrl::execute($video['name']),
            ]);
        }
    }

    public function save($data,$image)
    {
        $extension = $data['video']->extension() ?? null;
        DB::beginTransaction();
        // prepare data to be created or updated

        if(!(array_key_exists('user_id',$data))){
            $data['user_id'] = auth()->id();
        }




        // delete old video
        if(array_key_exists('video',$data) && $data['video'] != null && array_key_exists('id',$data)){
            $this->delete_old_file($data['id']);
        }


        if(array_key_exists('video',$data) && $data['video'] != null){

            if($extension == 'pdf'){
                $name = time().rand(0,9999999999999). '_file.' . $data['video']->getClientOriginalExtension();
                exec("ffmpeg -i ".$data['video']."  ".storage_path('app/tmp/')."$name");
                VideoQualities::save_at_wasabi($name,storage_path('app/tmp/').$name,'pdfs');
                $data['video'] = $name;
                $data['type'] = 'pdf';
            }else{
                $name = time().rand(0,9999999999999). '_file';
                VideoQualities::execute($data['video'],$name);
                $data['video'] = VideoQualities::getFinalNames()[0]['name'];
            }

        }

        $data['wasbi_url'] = GenerateWasbiTmpUrl::execute($data['video']);


        $subject = subjects_videos::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        // save qualities
        if(array_key_exists('video',$data) && $data['video'] != null){
            $this->save_qualities_inDB($subject);
        }

        if(isset($data['id'])){
            $record = subjects_videos::find($data['id']);
            $record->created_at = Carbon::now(); //
            $record->save();
        }

        // check if there is any image related to this category and save it
        if(!(array_key_exists('id',$data)) || (array_key_exists('id',$data) && $image != null)){
            $this->check_upload_image($image,'videos_frames',$subject->id,'subjects_videos');
        }


        // Load the category with the associated image
        $subject->load('subject.category.university');
        $subject->load('image');

        // cache subject info videos
        //CacheSubjectVideosService::set_cached($subject->subject_id);

        DB::commit();
        // return response
        return Messages::success(__('messages.saved_successfully'),SubjectsVideosResource::make($subject));
    }
}
