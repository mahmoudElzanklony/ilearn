<?php

namespace App\Http\Controllers;

use App\Actions\CheckForUploadImage;
use App\Http\Requests\categoriesFormRequest;
use App\Http\Requests\subjectsFormRequest;
use App\Http\Requests\subjectsVideoFormRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PropertyHeadingResource;
use App\Http\Resources\SubjectsResource;
use App\Http\Resources\SubjectsVideosResource;
use App\Models\categories;
use App\Models\categories_properties;
use App\Models\properties;
use App\Models\properties_heading;
use App\Models\subjects;
use App\Models\subjects_videos;
use App\Services\FormRequestHandleInputs;
use App\Services\Messages;
use Illuminate\Http\Request;
use App\Http\Traits\upload_image;
use Illuminate\Support\Facades\DB;
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
        $this->middleware('auth:sanctum')->except('stream');
    }
    public function index()
    {
        $data = subjects_videos::query()
            ->when(auth()->user()->type == 'doctor',function ($query){
                $query->whereHas('subject',function($s){
                    $s->where('user_id',auth()->id());
                });
            })
            ->with(['subject','image'])
            ->orderBy('id','DESC')
            ->paginate(request('limit') ?? 5);
        return SubjectsVideosResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save($data,$image)
    {
        DB::beginTransaction();
        // prepare data to be created or updated
        $data['user_id'] = auth()->id();



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



        $subject = subjects_videos::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);

        // check if there is any image related to this category and save it
        if(!(array_key_exists('id',$data)) || (array_key_exists('id',$data) && $image != null)){
            $this->check_upload_image($image,'videos_frames',$subject->id,'subjects_videos');
        }


        // Load the category with the associated image
        $subject->load('subject');
        $subject->load('image');

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
        $data  = subjects_videos::query()->where('id', $id)->FailIfNotFound(__('errors.not_found_data'));
        return SubjectsVideosResource::make($data);
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
        if ($video == null) {
            return Messages::error('video not found');
        }

        $filePath = 'videos/' . $video->video;

        if (!Storage::disk('wasabi')->exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $size = Storage::disk('wasabi')->size($filePath);
        $mimeType = Storage::disk('wasabi')->mimeType($filePath);
        $stream = Storage::disk('wasabi')->readStream($filePath);

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $size,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
        ];

        $videoPath = $filePath; // The path to the video file on Wasabi

        // Fetching the file size from Wasabi
        $disk = Storage::disk('wasabi');
        $size = $disk->size($videoPath);

        // Handling the range header
        $range = request()->header('Range');
        $start = 0;
        $end = $size - 1;

        if ($range) {
            $range = str_replace('bytes=', '', $range);
            [$start, $end] = explode('-', $range);

            $start = (int) $start;
            $end = $end ? (int) $end : $size - 1;
        }

        $length = $end - $start + 1;

        $headers = [
            'Content-Type' => 'video/mp4',
            'Content-Length' => $length,
            'Content-Range' => "bytes $start-$end/$size",
            'Accept-Ranges' => 'bytes',
        ];

        return new StreamedResponse(function () use ($disk, $videoPath, $start, $end) {
            $stream = $disk->readStream($videoPath);

            fseek($stream, $start);

            $bufferSize = 1024;
            while (!feof($stream) && ($pos = ftell($stream)) <= $end) {
                if ($pos + $bufferSize > $end) {
                    $bufferSize = $end - $pos + 1;
                }
                echo fread($stream, $bufferSize);
                flush();
            }

            fclose($stream);
        }, 206, $headers);

        return new StreamedResponse(function () use ($stream) {
            while (ob_get_level()) {
                ob_end_flush();
            }
            fpassthru($stream);
        }, 200, $headers);
    }
}
