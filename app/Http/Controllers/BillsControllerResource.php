<?php

namespace App\Http\Controllers;

use App\Actions\CheckForUploadImage;
use App\Filters\DoctorIdFilter;
use App\Filters\EndDateFilter;
use App\Filters\orders\RateOrderFilter;
use App\Filters\orders\StatusOrderFilter;
use App\Filters\StartDateFilter;
use App\Filters\SubjectIdFilter;
use App\Filters\UserIdFilter;
use App\Http\Requests\billFormRequest;
use App\Http\Requests\categoriesFormRequest;
use App\Http\Requests\subjectsFormRequest;
use App\Http\Requests\subscriptionsFormRequest;
use App\Http\Resources\BillResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PropertyHeadingResource;
use App\Http\Resources\SubjectsResource;
use App\Http\Resources\SubscriptionsResource;
use App\Models\bills;
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

class BillsControllerResource extends Controller
{
    use upload_image;
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $data = bills::query()
            ->with('doctor')
            ->orderBy('id','DESC');

        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                DoctorIdFilter::class,
            ])
            ->thenReturn()
            ->paginate(request('limit') ?? 10);
        return BillResource::collection($output);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save($data)
    {
        DB::beginTransaction();

        // Check for overlapping bills
        $overlappingBill = bills::where('doctor_id', $data['doctor_id'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_date', [ $data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date', [ $data['start_date'], $data['end_date'] ])
                    ->orWhere(function ($query) use ($data) {
                        $query->where('start_date', '<=', $data['start_date'])
                            ->where('end_date', '>=', $data['end_date']);
                    });
            })->exists();

        if ($overlappingBill) {
            return Messages::error('الفتره الزمنيه لانشاء الفاتوره لهذا الدكتور غير صحيحه حيث انها موجوده بالفعل');
        }


        $bill = bills::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);

        // Load the category with the associated image
        $bill->load('doctor');

        DB::commit();
        // return response
        return Messages::success(__('messages.saved_successfully'),BillResource::make($bill));
    }

    public function store(billFormRequest $request)
    {
        return $this->save($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(billFormRequest $request , $id)
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
