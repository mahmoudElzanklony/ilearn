<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\userFormRequest;
use App\Models\roles;
use App\Models\students_subjects_years;
use App\Models\User;
use App\Notifications\UserRegisteryNotification;
use App\Services\Messages;
use App\Services\RegisterSubscriptionsToStudentService;
use App\Services\SendWhatApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('send_msg');
    }
    //
    public function register(userFormRequest $request)
    {

        DB::beginTransaction();
        $data = $request->validated();

        $usernamePart = substr($data['phone'], 5, 3);
        $phonePart = substr($data['phone'], -3);

        // Hash the combined string using bcrypt
        $data['password'] = $usernamePart . $phonePart;
        if(request()->filled('type')){
            if(request('type') == 'admin' || request('type') == 'doctor'){
                if(auth()->user()->type == 'doctor'){
                    return Messages::error('غير مسموح لك باضافه دكتور او مدير');
                }
            }
        }
       // $data['role_id'] = roles::query()->where('name','=','client')->first()->id;
        if(request()->filled('year_id')){
            $year_id = $data['year_id'];
            unset($data['year_id']);
        }
        $data['added_by'] = auth()->id();

        // create user
        if($data['type'] == 'client'){
            if(!request()->filled('year_id')){
                return Messages::error('الكليه غير موجوده يرجي ارفاقها في عملية الحفظ');
            }
        }
        $user = User::query()->create($data);
        // create user year
        if(request()->filled('year_id')){
            students_subjects_years::query()->create([
                'user_id'=>$user->id,
                'year_id'=>$year_id
            ]);
            if(request()->filled('subscriptions_status') && request('subscriptions_status') == '1' && auth()->user()->type == 'doctor'){
                RegisterSubscriptionsToStudentService::register($user->id,auth()->id(),$year_id);
            }
        }




        SendWhatApp::send($data['phone'],$data['password'],$data['username']);
        $user->createToken($data['phone'])->plainTextToken;
        DB::commit();
        return Messages::success(message: __('messages.user_registered_successfully'));
    }

    public function send_msg()
    {
         SendWhatApp::send('+201152296646','522646','محمود عبد الله');
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return Messages::success(__('messages.logout_successfully'));
    }
}
