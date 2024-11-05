<?php

namespace App\Filters\subscriptions;

use Closure;

class UserNameFilter
{
    public function handle($request, Closure $next){
        if(request()->filled('name')){
            dd(request('name'));
            return $next($request)->whereHas('user',function($e){
                $e->where('usernamessssssssssss','LIKE','%'.request('name').'%');
            });

        }
        return $next($request);
    }
}
