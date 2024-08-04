<?php

namespace App\Http\Controllers;

use App\Http\Requests\userFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Messages;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    public function update_info(userFormRequest $request)
    {
        $data = $request->validated();
        if(auth()->user()->phone != $data['phone']){
           $data['phone_verified_at'] = null;
        }
        if($data['otp_secret'] == ''){
            $data['otp_secret'] = null;
        }
        auth()->user()->update($data);
        return Messages::success(__('messages.updated_successfully'),UserResource::make(auth()->user()));
    }
}
