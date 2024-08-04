<?php

namespace App\Http\Controllers;

use App\Http\Requests\categoriesFormRequest;
use App\Http\Requests\couponFormRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CouponResource;
use App\Models\categories;
use App\Models\coupons;
use App\Services\FormRequestHandleInputs;
use App\Services\Messages;
use Illuminate\Http\Request;
use App\Http\Traits\upload_image;
use Illuminate\Support\Facades\DB;

class CouponsControllerResource extends Controller
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
        $data = coupons::query()->orderBy('id','DESC')->orderBy('id','DESC')
            ->paginate(request('limit') ?? 10);
        return CouponResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save($data)
    {
        DB::beginTransaction();
        // prepare data to be created or updated
        $data['user_id'] = auth()->id();
        $data =  FormRequestHandleInputs::handle_inputs_langs($data,['name']);
        // start save category data

        $coupon = coupons::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);


        DB::commit();
        // return response
        return Messages::success(__('messages.saved_successfully'),CouponResource::make($coupon));
    }

    public function store(couponFormRequest $request)
    {
        return $this->save($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data = coupons::query()->where('id', $id)
            ->FailIfNotFound(__('errors.not_found_data'));
        return CouponResource::make($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(couponFormRequest $request , $id)
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
