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
        if(isset($data['id'])){
            $user = User::query()->findOrFail($data['id']);
        }else{
            $user = auth()->user();
        }


        $user->update($data);
        return Messages::success(__('messages.updated_successfully'),UserResource::make(auth()->user()));
    }
}
