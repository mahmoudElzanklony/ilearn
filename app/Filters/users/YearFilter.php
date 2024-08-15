<?php

namespace App\Filters\users;
use Closure;
class YearFilter
{
    public function handle($request, Closure $next){
        if(request()->has('year_id')){
            return $next($request)->has('year')->whereHas('year',function($e){
                $e->where('year_id','=',request('year_id'));
            });
        }
        return $next($request);
    }
}
