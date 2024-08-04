<?php

namespace App\Http\Controllers;

use App\Actions\OrdersWithAllDataAction;
use App\Filters\EndDateFilter;
use App\Filters\orders\StatusOrderFilter;
use App\Filters\StartDateFilter;
use App\Filters\users\UserNameFilter;
use App\Filters\users\WalletFilter;
use App\Http\Enum\OrderStatuesEnum;
use App\Http\Enum\SendingNotificationEnum;
use App\Http\patterns\strategy\Messages\MessagesInterface;
use App\Http\Requests\controlWalletFormRequest;
use App\Http\Requests\notificationsScheduleFormRequest;
use App\Http\Requests\taxFormRequest;
use App\Http\Resources\UserResource;
use App\Models\notifications_data_schedule;
use App\Models\notifications_data_schedule_users;
use App\Models\orders;
use App\Models\payments;
use App\Models\taxes;
use App\Models\User;
use App\Services\Messages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

    public function users()
    {
        $data = User::query()->orderBy('id','DESC');
        $output  = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                WalletFilter::class,
                UserNameFilter::class
            ])
            ->thenReturn()
            ->paginate(request('limit') ?? 10);
        return UserResource::collection($output);
    }

    public function orders()
    {
        $data = [
          'pending'=>orders::query()->whereHas('last_status',fn($e)=> $e->where('status',OrderStatuesEnum::pending))->count(),
          'delivery'=>orders::query()->whereHas('last_status',fn($e)=> $e->where('status',OrderStatuesEnum::delivery))->count(),
          'cancelled'=>orders::query()->whereHas('last_status',fn($e)=> $e->where('status',OrderStatuesEnum::cancelled))->count(),
          'review'=>orders::query()->whereHas('last_status',fn($e)=> $e->where('status',OrderStatuesEnum::review))->count(),
          'completed'=>orders::query()->whereHas('last_status',fn($e)=> $e->where('status',OrderStatuesEnum::completed))->count(),
          'printing'=>orders::query()->whereHas('last_status',fn($e)=> $e->where('status',OrderStatuesEnum::printing))->count(),
        ];
        return Messages::success('',$data);
    }

    public function orders_summary()
    {
        $output = [];
        for($i = 0; $i < 12; $i++) {
            $month = Carbon::parse( (request('year') ?? date('Y')).'-'.($i+1).'-01')->firstOfMonth()->addDay();
            $value = payments::query()->where('paymentable_type','=','App\Models\orders')
                ->whereMonth('created_at',intval($i+1))
              //  ->whereYear('created_at',request('year') ?? date('Y'))
                ->sum('money');
            $output[$i] = ['placeholder'=>$month , 'value'=> floatval($value)];
        }
        return $output;
    }

    public function add_money_to_wallet(controlWalletFormRequest $request)
    {
        $data = $request->validated();
        $user = User::query()->find($data['user_id']);
        // add wallet to user
        $user->update([
            'wallet'=>$user->wallet + $data['money']
        ]);
        return Messages::success(__('messages.operation_done_successfully'));
    }

    public function update_tax(taxFormRequest $request)
    {
        $data = $request->validated();

        taxes::query()->update($data,$data);
        //
        return Messages::success(__('messages.operation_done_successfully'));
    }

    public function get_tax()
    {

        $data = taxes::query()->first();
        //
        return $data;
    }

    public function create_notification_content(notificationsScheduleFormRequest $request , MessagesInterface $messageObj)
    {
        $data = $request->validated();
        DB::beginTransaction();
        // create content first
        $noti = notifications_data_schedule::query()->updateOrCreate([
            'id' => request('id') ?? null
        ], [
            'content' => $data['content']
        ]);
        // send to users
        foreach($data['users'] as $user){
            notifications_data_schedule_users::query()->create([
                'user_id'=>$user,
                'notification_data_schedule_id'=>$noti->id,
            ]);
            // send notification
            $data_info = [
              'message'=>$data['content'],
              'user'=>User::query()->find($user)
            ];
            if($data['sending_type'] == SendingNotificationEnum::email->value){
                $data_info['title'] = 'اشعار من الادارة';
            }
            $messageObj->send($data_info);
        }
        DB::commit();

        return Messages::success(__('messages.operation_done_successfully'));
    }
}
