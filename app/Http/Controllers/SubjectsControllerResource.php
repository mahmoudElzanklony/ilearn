<?php

namespace App\Http\Controllers;

use App\Actions\CheckForUploadImage;
use App\Filters\CategoryIdFilter;
use App\Filters\EndDateFilter;
use App\Filters\NameFilter;
use App\Filters\StartDateFilter;
use App\Filters\subjects\UniversityFilter;
use App\Http\Requests\categoriesFormRequest;
use App\Http\Requests\subjectsFormRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PropertyHeadingResource;
use App\Http\Resources\SubjectsResource;
use App\Http\Resources\SubscriptionsResource;
use App\Models\categories;
use App\Models\categories_properties;
use App\Models\properties;
use App\Models\properties_heading;
use App\Models\subjects;
use App\Models\subscriptions;
use App\Services\CacheSubjectVideosService;
use App\Services\FormRequestHandleInputs;
use App\Services\Messages;
use Illuminate\Http\Request;
use App\Http\Traits\upload_image;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

class SubjectsControllerResource extends Controller
{
    use upload_image;
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function index()
    {
        $data = subjects::query()
            ->with('user')
            ->when(auth()->user()->type == 'doctor',fn($e) => $e->where('user_id','=',auth()->id()))
            ->with(['image','category.university'])
            ->orderBy('id','DESC');


        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                NameFilter::class,
                CategoryIdFilter::class,
                UniversityFilter::class,
            ])
            ->thenReturn()
            ->paginate(request('limit') ?? 10);
        return SubjectsResource::collection($output);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save($data , $image)
    {
        DB::beginTransaction();
        // prepare data to be created or updated
        $data['user_id'] = request('user_id') ?? auth()->id();
        // start save category data
        $subject = subjects::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
// check if there is any image related to this category and save it
        if(!(array_key_exists('id',$data)) || (array_key_exists('id',$data) && $image != null)){
            $this->check_upload_image($image,'subjects',$subject->id,'subjects');
        }
        // Load the category with the associated image
        $subject->load('image');
        $subject->load('category');
        $subject->load('user');

        DB::commit();
        // return response
        return Messages::success(__('messages.saved_successfully'),SubjectsResource::make($subject));
    }

    public function store(subjectsFormRequest $request)
    {
        return $this->save($request->validated(),request()->file('image'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data  = subjects::query()->with('videos.qualities')
            ->where('id', $id)->FailIfNotFound(__('errors.not_found_data'));
      //  $data = CacheSubjectVideosService::get_cached($id);

        if(auth()->user()->type == 'client'){
            $check_sub = subscriptions::query()
                ->where('user_id','=',auth()->id())
                ->where('subject_id','=',$id)
                ->exists();
            if(!$check_sub){
                return Messages::error('غير مسموح لك برؤيه محتوي الماده');
            }
        }

        return SubjectsResource::make($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(subjectsFormRequest $request , $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        return $this->save($data,request()->file('image'));
    }


    public function per_user()
    {
        $data = subscriptions::query()
            ->with('subject',function ($e){
                $e->with(['image','user']);
            })
            ->whereHas('subject',function ($e){
                $e->where('deleted_at','=',null);
            })
            ->where('user_id','=',auth()->id())
            ->where('is_locked','=',0)
            ->orderBy('id','DESC')
            ->get();
        return SubscriptionsResource::collection($data);
    }


    public function lock()
    {
        if(request()->filled('id')){
            $subscription = subscriptions::query()->find(request('id'));
            $subscription->is_locked = 1;
            $subscription->save();
            return Messages::success(__('messages.saved_successfully'),SubscriptionsResource::make($subscription));
        }else if(request()->filled('ids')){
            $subscription  = subscriptions::query()->whereIn('id',request('ids'))->update(['is_locked'=>1]);
            $subscription = subscriptions::whereIn('id', request('ids'))->get();
            return Messages::success(__('messages.saved_successfully'),SubscriptionsResource::collection($subscription));
        }
        return Messages::error('not found id or ids in request');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
