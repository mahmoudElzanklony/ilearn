<?php

namespace App\Http\Controllers\Auth;

use App\Actions\DefaultAddress;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\roles;
use App\Models\User;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    //

    public function login()
    {
        $data = ['phone'=>request('phone'),'password'=>request('password'),'deleted_at'=>null];
        if(auth()->attempt($data)){
            // check ip
            $user = User::query()->where('phone',$data['phone'])->first();
            if($user->type == 'client' && $user->phone != '+201111354352'){
                if(request()->filled('from')){
                    return Messages::error('غير مسموح لك بالدخول');
                }
                if(!(request()->filled('device_id'))){
                    return Messages::error('رقم الجهاز لم يتم ارساله');
                }
                if(request()->filled('device_id') && $user->type != 'client'){
                    return Messages::error('ليس لديك صلاحيات الدخول فهذا التطبيق للطلبه فقط');
                }
                if($user->otp_secret == null){
                    $user->otp_secret = request('device_id');
                    $user->save();
                }else if($user->otp_secret != request('device_id')){
                    return Messages::error('هذا الجهاز ليس الجهاز الاول الذي قمت بالدخول الي التطبيق من خلاله');
                }
            }
            // check if blocked
            if($user->is_block == 1){
                return Messages::error('لقد تم حظر حسابك من فضلك قم بالتواصل مع دكتور الماده لفك الحظر عنك');
            }
            $user['token'] = $user->createToken($data['phone'])->plainTextToken;
            return Messages::success(__('messages.login_successfully'),UserResource::make($user));
        }else{
            return Messages::error(__('errors.email_or_password_is_not_correct'));
        }
    }



    public function get_user_by_token(){
        if(request()->hasHeader('Authorization')) {
            $token = request()->header('Authorization');
            if ($token) {
                [$id, $user_token] = explode('|', $token, 2);
                $token_data = DB::table('personal_access_tokens')->where('token', hash('sha256', $user_token))->first();
                $user_id = $token_data->tokenable_id; // !!!THIS ID WE CAN USE TO GET DATA OF YOUR USER!!!
                $user = User::query()->find($user_id);
                $user['token'] =  request()->header('Authorization');

                return Messages::success('',UserResource::make($user));
            }


        }
    }

    public function getToken(Request $request)
    {

        return response()->json(['csrf_token' => csrf_token()]);
    }

}
