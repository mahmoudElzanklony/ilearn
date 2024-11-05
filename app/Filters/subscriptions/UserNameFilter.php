<?php

namespace App\Filters\subscriptions;

use Closure;

class UserNameFilter
{
    public function handle($request, Closure $next){
        if(request()->filled('name')){
            return $next($request)->whereHas('user',function($e){
                $e->where('users.username','LIKE','%'.request('name').'%');
            });

        }
        return $next($request);
    }
}
