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
            $data = request()->all();
            $data['type']  = 'admin';

            dd($data,auth()->attempt($data));
            if(auth()->attempt($data)){
                return 'login success';
            }
            return 'error';
        }
    }
}