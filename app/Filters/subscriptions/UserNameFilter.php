<?php

namespace App\Filters\subscriptions;

use Closure;

class UserNameFilter
{
    public function handle($request, Closure $next){
        if(request()->has('name')){
            return $next($request)->whereHas('user',function($e){
                $e->where('username','LIKE','%'.request('name').'%');
            });

        }
        return $next($request);
    }
}
