<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\userFormRequest;
use App\Models\roles;
use App\Models\User;
use App\Notifications\UserRegisteryNotification;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    //
    public function register(userFormRequest $request)
    {


        DB::beginTransaction();
        $data = $request->validated();
       // $data['role_id'] = roles::query()->where('name','=','client')->first()->id;
        $user = User::query()->create($data);

        $user->createToken($data['phone'])->plainTextToken;
        DB::commit();
        return Messages::success(message: __('messages.user_registered_successfully'));
    }
}
