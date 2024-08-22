<?php


namespace App\Filters\users;
use Closure;

class UserNameFilter
{
    public function handle($request, Closure $next){
        if(request()->has('username')){
            return $next($request)
                ->where('username','LIKE','%'.request('username').'%');
        }
        if(request()->has('name')){
            return $next($request)
                ->where('username','LIKE','%'.request('name').'%')
                ->orWhere('phone','LIKE','%'.request('name').'%');
        }
        return $next($request);
    }
}
