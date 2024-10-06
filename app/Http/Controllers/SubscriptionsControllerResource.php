<?php

namespace App\Http\Controllers;

use App\Actions\CheckForUploadImage;
use App\Filters\CategoryIdFilter;
use App\Filters\EndDateFilter;
use App\Filters\orders\CategoryOrderFilter;
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
        $this->middleware('auth:sanctum');
    }

    public function total_money()
    {
        $data = subscriptions::query()
            ->with(['subject', 'user.year.category.university'])
            ->when(auth()->user()->type == 'doctor', function ($e) {
                $e->whereHas('subject', function ($q) {
                    $q->where('user_id', '=', auth()->id());
                });
            });

        // Apply filters through the pipeline
        $data = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                CategoryOrderFilter::class,
                UserIdFilter::class,
                SubjectIdFilter::class,
                SubscriptionDoctorFilter::class
            ])
            ->thenReturn();

        // Get the total sum of price and discount
        $totals = $data->selectRaw('SUM(price) as total_price, SUM(discount) as total_discount')
            ->first();

        // Calculate total money as total price minus total discount
        $totalMoney = $totals->total_price - $totals->total_discount;

        // Return paginated results with total money information
        $result =  [
            'final' => $totalMoney,
            'total_price' => $totals->total_price,
            'total_discount' => $totals->total_discount,
        ];
        return Messages::success('',$result);
    }
    public function index()
    {


        $data = subscriptions::query()
            ->with(['subject','user.year.category.university'])
            ->when(auth()->user()->type == 'doctor',function ($e){
                $e->whereHas('subject',function($q){
                    $q->where('user_id','=',auth()->id());
                });
            });
           // ->orderBy('id','DESC');
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                CategoryOrderFilter::class,
                UserIdFilter::class,
                SubjectIdFilter::class,
                SubscriptionDoctorFilter::class
            ])
            ->thenReturn()
            ->paginate(request('limit') ?? 10);
        return SubscriptionsResource::collection($output);
    }


    public function check_subscription($data)
    {

        $check = subscriptions::query()
            ->where('user_id',$data['user_id'])
            ->where('subject_id',$data['subject_id'])->first();
        if($check != null){
            return ['status'=>1,'subject_id'=>$data['subject_id']];
            //return Messages::error('هذا الطالب تم اشتراكه في هذه الماده من قبل');
        }
        return null;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save($data)
    {
        DB::beginTransaction();

        $data['added_by'] = auth()->id();
        $data['is_locked'] = 0;

        $output_saved = [];
        if(is_array($data['subject_id'])){
            foreach ($data['subject_id'] as $datum){
                $saved = $data;
                $subject_obj =  subjects::query()->find($datum);

                $saved['price'] = $subject_obj->price;
                unset($saved['subject_id']);
                $saved['subject_id'] = $datum;

                $check = $this->check_subscription($saved);
                if(is_array($check)){

                    return Messages::error('هذا الطالب تم اشتراكه في مادة '.$subject_obj->name.' لذا يرجي ازالتها من الاشتراك ');
                }
                $saved['created_at'] = now();
                array_push($output_saved,$saved);
                $related = '';
            }
            subjects::query()->insert($output_saved);

        }else {

            $subject = subscriptions::query()->updateOrCreate([
                'id' => $data['id'] ?? null
            ], $data);
            // Load the category with the associated image
            $subject->load('user');
            $subject->load('subject');
            $related = SubscriptionsResource::make($subject);
        }


        DB::commit();
        // return response
        return Messages::success(__('messages.saved_successfully'),$related);
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
