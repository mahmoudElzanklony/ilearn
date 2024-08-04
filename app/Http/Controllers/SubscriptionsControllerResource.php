<?php

namespace App\Http\Controllers;

use App\Actions\CheckForUploadImage;
use App\Filters\EndDateFilter;
use App\Filters\orders\RateOrderFilter;
use App\Filters\orders\StatusOrderFilter;
use App\Filters\StartDateFilter;
use App\Filters\SubjectIdFilter;
use App\Filters\SubscriptionDoctorFilter;
use App\Filters\UserIdFilter;
use App\Http\Requests\categoriesFormRequest;
use App\Http\Requests\subjectsFormRequest;
use App\Http\Requests\subscriptionsFormRequest;
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
use App\Services\FormRequestHandleInputs;
use App\Services\Messages;
use Illuminate\Http\Request;
use App\Http\Traits\upload_image;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

class SubscriptionsControllerResource extends Controller
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

        return subjects::query()
            ->with('students')
            ->first();

        $data = subscriptions::query()
            ->with(['subject','user'])
            ->orderBy('id','DESC');
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                UserIdFilter::class,
                SubjectIdFilter::class,
                SubscriptionDoctorFilter::class
            ])
            ->thenReturn()
            ->paginate(request('limit') ?? 10);
        return SubscriptionsResource::collection($output);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save($data)
    {
        DB::beginTransaction();
        $data['price'] = subjects::query()->find($data['subject_id'])->price;
        $data['is_locked'] = 0;
        if(!(array_key_exists('id',$data))){
            $check = subscriptions::query()
                ->where('user_id',$data['user_id'])
                ->where('subject_id',$data['subject_id'])->first();
            if($check != null){
                return Messages::error('هذا الطالب تم اشتراكه في هذه الماده من قبل');
            }
        }

        $subject = subscriptions::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        // Load the category with the associated image
        $subject->load('user');
        $subject->load('subject');

        DB::commit();
        // return response
        return Messages::success(__('messages.saved_successfully'),SubscriptionsResource::make($subject));
    }

    public function store(subscriptionsFormRequest $request)
    {
        return $this->save($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data  = subjects::query()->where('id', $id)->FailIfNotFound(__('errors.not_found_data'));
        return SubjectsResource::make($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(subscriptionsFormRequest $request , $id)
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
