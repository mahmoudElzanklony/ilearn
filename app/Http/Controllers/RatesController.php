<?php

namespace App\Http\Controllers;

use App\Actions\OrdersWithAllDataAction;
use App\Http\Requests\rateFormRequest;
use App\Http\Resources\OrderResource;
use App\Models\orders_rates;
use App\Policies\TestPolicy;
use App\Services\Messages;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RatesController extends Controller
{
    //
    public function index()
    {
        $this->authorize('index', TestPolicy::class);
        return auth()->user()->can('facility-facility-settings');

    }

    public function create(rateFormRequest $request)
    {
        $data = $request->validated();
        // save order rate
        orders_rates::query()->updateOrCreate([
            'order_id'=>$data['order_id']
        ],$data);
        return Messages::success(__('messages.operation_done_successfully'),OrderResource::make(OrdersWithAllDataAction::get()->find($data['order_id'])));
        // return auth()->user()->can('facility-facility-settings');

       // $this->authorize('index', TestPolicy::class);
    }
}
