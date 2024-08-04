<?php

namespace App\Http\Controllers;

use App\Http\Requests\propertiesHeadingFormRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PropertyHeadingResource;
use App\Models\categories;
use App\Models\properties_heading;
use App\Services\FormRequestHandleInputs;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertiesHeadingControllerResource extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index','show');
    }
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        //
        $data = properties_heading::query()->orderBy('id','DESC')->get();
        return PropertyHeadingResource::collection($data);
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
        $property_heading = properties_heading::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);


        DB::commit();
        // return response
        return Messages::success(__('messages.saved_successfully'),PropertyHeadingResource::make($property_heading));
    }
    public function store(propertiesHeadingFormRequest $request)
    {
        //
        $data = $request->validated();
        return $this->save($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data = properties_heading::query()
            ->where('id', $id)->FailIfNotFound(__('errors.not_found_data'));
        return PropertyHeadingResource::make($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(propertiesHeadingFormRequest $request , string $id)
    {

        properties_heading::query()->where('id', $id)->FailIfNotFound(__('errors.not_found_data'));
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
