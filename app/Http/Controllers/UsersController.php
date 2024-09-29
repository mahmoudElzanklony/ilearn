<?php

namespace App\Http\Controllers;

use App\Filters\DoctorIdFilter;
use App\Filters\EndDateFilter;
use App\Filters\StartDateFilter;
use App\Filters\TypeFilter;
use App\Filters\users\IsBlockFilter;
use App\Filters\users\NationalityFilter;
use App\Filters\users\UserNameFilter;
use App\Filters\users\YearFilter;
use App\Http\Resources\BillResource;
use App\Http\Resources\UserResource;
use App\Models\bills;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    //
    public function index()
    {
        $data = User::query()
            ->with('year',fn($q)=>$q->with(['category.university']))
            ->with('subscriptions')
            ->when(auth()->user()->type == 'doctor',function ($e){
                $e
                    ->where('added_by','=',auth()->id())
                    ->orWhereHas('subscriptions',function($e){
                        $e->whereHas('subject',function ($q){
                            $q->where('user_id','=',auth()->id());
                        });
                });
            })
            ->withCount(['students_subscriptions as unique_students' => function($query) {
                $query->select(DB::raw('COUNT(DISTINCT `user_id`)'));
            }])
            ->orderBy('id','DESC');

        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                TypeFilter::class,
                UserNameFilter::class,
                YearFilter::class,
                NationalityFilter::class,
                IsBlockFilter::class
            ])
            ->thenReturn()
            ->paginate(request('limit') ?? 10);
        return $data->get();
        return UserResource::collection($output);
    }
}
