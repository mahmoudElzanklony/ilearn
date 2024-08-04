<?php


namespace App\Filters\orders;


use App\Filters\FilterRequest;
use Closure;
class StatusOrderFilter extends FilterRequest
{
    public function handle($request, Closure $next){
        if(request()->has('status')){
            return $next($request)->whereHas('last_status', function ($e) {
                $e->where('status', '=', request('status'));
            });
        }
        return $next($request);
    }
}
