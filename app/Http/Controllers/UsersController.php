<?php

namespace App\Http\Controllers;

use App\Filters\DoctorIdFilter;
use App\Filters\EndDateFilter;
use App\Filters\StartDateFilter;
use App\Filters\TypeFilter;
use App\Filters\users\UserNameFilter;
use App\Http\Resources\BillResource;
use App\Http\Resources\UserResource;
use App\Models\bills;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class UsersController extends Controller
{
    //
    public function index()
    {
        $data = User::query()
            ->with('subscriptions')
            ->when(auth()->user()->type == 'doctor',function ($e){
                $e->whereHas('subscriptions',function($e){
                    $e->whereHas('subject',function ($q){
                        $q->where('user_id','=',auth()->id());
                    });
                });
            })
            ->orderBy('id','DESC');

        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                TypeFilter::class,
                UserNameFilter::class
            ])
            ->thenReturn()
            ->paginate(request('limit') ?? 10);
        return UserResource::collection($output);
    }
}
