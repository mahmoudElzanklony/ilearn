<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class WebLoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if(request()->filled('phone') && request()->filled('password')){
            $user = User::query()
                ->where('phone','+'.request('phone'))
                ->where('password',bcrypt(request('password')))
                ->first();
            return $user;
            if($user && $user->type == 'admin'){
                auth()->login($user);
                return 'login success';
            }
            return 'error';
        }
    }
}
