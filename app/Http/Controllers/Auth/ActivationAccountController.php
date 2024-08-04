<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\activationAccountFormRequest;
use App\Models\User;

class ActivationAccountController extends Controller
{
    //
    public function index(activationAccountFormRequest $request)
    {
        $data = $request->validated();
        return User::query()->where($data)->updateOrFailWithCustomError(['phone_verified_at'=>now()],__('errors.otp_not_correct'),__('messages.activation_done'));
    }
}
