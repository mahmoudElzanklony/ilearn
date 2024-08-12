<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\userFormRequest;
use App\Models\roles;
use App\Models\students_subjects_years;
use App\Models\User;
use App\Notifications\UserRegisteryNotification;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    //
    public function register(userFormRequest $request)
    {

        DB::beginTransaction();
        $data = $request->validated();

        $usernamePart = substr($data['phone'], 0, 3);
        $phonePart = substr($data['phone'], -3);


        // Hash the combined string using bcrypt
        $data['password'] = $usernamePart . $phonePart;
        dd($data['password']);
       // $data['role_id'] = roles::query()->where('name','=','client')->first()->id;
        if(request()->filled('year_id')){
            $year_id = $data['year_id'];
            unset($data['year_id']);
        }
        $data['added_by'] = auth()->id();

        // create user
        $user = User::query()->create($data);
        // create user year
        if(request()->filled('year_id')){
            students_subjects_years::query()->create([
                'user_id'=>$user->id,
                'year_id'=>$year_id
            ]);
        }

        $user->createToken($data['phone'])->plainTextToken;
        DB::commit();
        return Messages::success(message: __('messages.user_registered_successfully'));
    }
}
