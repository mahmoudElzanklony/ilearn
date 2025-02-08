<?php

namespace App\Http\Controllers;

use App\Http\Requests\userFormRequest;
use App\Http\Resources\UserResource;
use App\Models\students_subjects_years;
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
            if(request()->has('ip')){
                $data['otp_secret'] = null;
            }
        }else{
            $user = auth()->user();
        }

        if(request()->filled('year_id')){
            students_subjects_years::query()
                ->where('user_id','=',$user->id)
                ->updateOrCreate([
                    'year_id'=>request('year_id')
                ],[
                    'user_id'=>$user->id,
                    'year_id'=>request('year_id')
                ]);
            $user->load('year');
        }


        $user->update($data);
        return Messages::success(__('messages.updated_successfully'),UserResource::make($user));
    }
}
