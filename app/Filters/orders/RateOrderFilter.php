<?php


namespace App\Filters\orders;


use App\Filters\FilterRequest;
use Closure;
class RateOrderFilter extends FilterRequest
{
    public function handle($request, Closure $next){
        if(request()->has('is_rated')){
            return $next($request)->has('rate');
        }
        return $next($request);
    }
}
