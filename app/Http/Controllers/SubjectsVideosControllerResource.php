<?php

namespace App\Http\Controllers;

use App\Actions\CheckForUploadImage;
use App\Filters\EndDateFilter;
use App\Filters\NameFilter;
use App\Filters\StartDateFilter;
use App\Filters\SubjectIdFilter;
use App\Filters\subjects_videos\UniversityFilter;
use App\Filters\TypeFilter;
use App\Filters\UniversityIdFilter;
use App\Filters\UserIdFilter;
use App\Http\Requests\categoriesFormRequest;
use App\Http\Requests\subjectsFormRequest;
use App\Http\Requests\subjectsVideoFormRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PropertyHeadingResource;
use App\Http\Resources\SubjectsResource;
use App\Http\Resources\SubjectsVideosResource;
use App\Jobs\GenerateExpiringWasabiUrls;
use App\Models\categories;
use App\Models\categories_properties;
use App\Models\images;
use App\Models\properties;
use App\Models\properties_heading;
use App\Models\subjects;
use App\Models\subjects_videos;
use App\Models\video_qualities;
use App\Services\CacheSubjectVideosService;
use App\Services\FormRequestHandleInputs;
use App\Services\Messages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Traits\upload_image;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubjectsVideosControllerResource extends Controller
{
    use upload_image;
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('wasbi_generation');
    }
    public function index()
    {
        $data = subjects_videos::query()
            ->when(auth()->user()->type == 'doctor',function ($query){
                $query->whereHas('subject',function($s){
                    $s->where('user_id',auth()->id());
                });
            })
            ->with(['subject.category.university'])
            ->orderBy('id','DESC');



        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                SubjectIdFilter::class,
                UserIdFilter::class,
                NameFilter::class,
                UniversityFilter::class,
                TypeFilter::class
            ])
            ->thenReturn()
            ->paginate(request('limit') ?? 10);
        request()->merge(['no_video'=>true]);
        return SubjectsVideosResource::collection($output);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save($data,$image)
    {
        DB::beginTransaction();
        // prepare data to be created or updated

        if(!(array_key_exists('user_id',$data))){
            $data['user_id'] = auth()->id();
        }




        // delete old video
        if(array_key_exists('video',$data) && $data['video'] != null && array_key_exists('id',$data)){
            $video = subjects_videos::query()->find($data['id']);
            if(file_exists(public_path('videos/'.$video->video))) {
                unlink(public_path('videos/' . $video->video));
            }
        }


        if(array_key_exists('video',$data) && $data['video'] != null){
            $data['video'] =$this->upload_video($data['video']);
        }

        if(env('WAS_STATUS') == 1) {
            $data['wasbi_url'] = Storage::disk('wasabi')->temporaryUrl(
                'videos/' . $data['video'], now()->addMinutes(400) // URL expires in 3 hours
            );
        }

        $subject = subjects_videos::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);

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

    public function store(subjectsVideoFormRequest $request)
    {
        return $this->save($request->validated(),request()->file('image'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data  = subjects_videos::query()
            ->where('id', $id)
            ->FailIfNotFound(__('errors.not_found_data'));
        return response()->json([
            'data'=>SubjectsVideosResource::make($data),
            'video_size'=>$this->get_size_video($data)
        ]);
    }

    public function get_size()
    {
        $video_obj = subjects_videos::query()->find(request('video_id'));
        if(request()->filled('quality_id')){
            $video_obj = video_qualities::query()->find(request('quality_id'));
        }
        if($video_obj == null){
            return Messages::error('الفديو غير موجود');
        }
        $output = $this->get_size_video($video_obj);


        return Messages::success('',$output);
    }

    public function get_size_video($video_obj)
    {
        if(isset($video_obj->quality)){
            $video_obj->video = $video_obj->name;
        }
        if(env('WAS_STATUS')){
            $filePath = 'videos/' . $video_obj->video;
            // Use the Wasabi disk to get file metadata
            $fileSize = Storage::disk('wasabi')->size($filePath);

            // Convert size to megabytes for easier readability (optional)
            $fileSizeInMb = round($fileSize / 1024 / 1024, 2);
            return [
                'size_in_bytes' => $fileSize,
                'size_in_mb' => $fileSizeInMb
            ];
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(subjectsVideoFormRequest $request , $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        return $this->save($data,request()->file('image'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function stream()
    {
        $video = subjects_videos::query()->find(request('id'));

        if ($video === null) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        $filePath = 'videos/' . $video->video;

        if (!Storage::disk('wasabi')->exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        Log::info($video->wasbi_url);
        if(request()->filled('quality')){
            $qual = request()->get('quality') == 'HD' ? 'original':request('quality');
            $quality = video_qualities::query()
                ->where('quality',$qual)
                ->where('subject_video_id',$video->id)
                ->first();

            if($quality != null && $quality->wasbi_url != null){
                return redirect()->away($video->wasbi_url);
            }
        }
        if($video->wasbi_url != ''){
            return redirect()->away($video->wasbi_url);
        }

        // Generate a presigned URL (valid for 1 hour)
        $disk = Storage::disk('wasabi');
        $expiration = now()->addMinutes(700); // Set the expiration time after 12 hour
        $presignedUrl = $disk->temporaryUrl($filePath, $expiration);

        // Redirect to the presigned URL
        return redirect()->away($presignedUrl);



        /*
        return new StreamedResponse(function () use ($stream) {
            while (ob_get_level()) {
                ob_end_flush();
            }
            fpassthru($stream);
        }, 200, $headers);*/
    }

    public function wasbi_generation()
    {
        $obj = new GenerateExpiringWasabiUrls();
        $obj->handle();

        return response()->json(['message' => 'Job dispatched to update Wasabi URLs'], 200);

    }
}
