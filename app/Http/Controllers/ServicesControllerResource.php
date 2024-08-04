<?php

namespace App\Http\Controllers;

use App\Http\Requests\categoriesFormRequest;
use App\Http\Requests\servicesFormRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ServiceResource;
use App\Models\categories;
use App\Models\services;
use App\Services\FormRequestHandleInputs;
use App\Services\Messages;
use Illuminate\Http\Request;
use App\Http\Traits\upload_image;
use Illuminate\Support\Facades\DB;

class ServicesControllerResource extends Controller
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
        $data = services::query()->with('category')->orderBy('id','DESC')
            ->orderBy('id','DESC')->paginate(request('limit') ?? 10);
        return ServiceResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save($data , $image)
    {
        DB::beginTransaction();
        // prepare data to be created or updated
        $data['user_id'] = auth()->id();
        $data =  FormRequestHandleInputs::handle_inputs_langs($data,['name','info']);
        // start save category data
        $service = services::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        // check if there is any image related to this category and save it
        if(!(array_key_exists('id',$data)) || (array_key_exists('id',$data) && $image != null)){
            $this->check_upload_image($image,'services',$service->id,'services');
        }
        // Load the category with the associated image
        $service->load('image');

        DB::commit();
        // return response
        return Messages::success(__('messages.saved_successfully'),ServiceResource::make($service));
    }

    public function store(servicesFormRequest $request)
    {
        return $this->save($request->validated(),request()->file('image'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $data = services::query()->with('category')
            ->where('id', $id)->FailIfNotFound(__('errors.not_found_data'));
        return ServiceResource::make($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(categoriesFormRequest $request , $id)
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
}
