<?php

namespace App\Filters\subscriptions;

use Closure;

class UserNameFilter
{
    public function handle($request, Closure $next){
        if(request()->filled('name')){
            $name = request('name');
            return $next($request)->whereHas('user', function ($e) use ($name) {
                $e->where('username', 'LIKE', "%{$name}%")
                    ->orWhere('phone', 'LIKE', "%{$name}%");
            });

        }
        return $next($request);
    }
}
