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

class SubjectsVideosControllerResource extends Controller
{
    use upload_image;
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index','show');
    }
    public function index()
    {
        $data = subjects_videos::query()
            ->with(['subject'])
            ->orderBy('id','DESC')->paginate(request('limit') ?? 10);
        return SubjectsVideosResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save($data)
    {
        DB::beginTransaction();
        // prepare data to be created or updated
        $data['user_id'] = auth()->id();

        // delete old video
        if(array_key_exists('video',$data) && $data['video'] != null && array_key_exists('id',$data)){
            $video = subjects_videos::query()->find($data['id']);
            unlink(public_path('videos/'.$video->video));
        }


        if(array_key_exists('video',$data) && $data['video'] != null){
            $data['video'] =$this->upload_video($data['video']);
        }



        $subject = subjects_videos::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        // Load the category with the associated image
        $subject->load('subject');

        DB::commit();
        // return response
        return Messages::success(__('messages.saved_successfully'),SubjectsVideosResource::make($subject));
    }

    public function store(subjectsVideoFormRequest $request)
    {
        return $this->save($request->validated());
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
        return $this->save($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
