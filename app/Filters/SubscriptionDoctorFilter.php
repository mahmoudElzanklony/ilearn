<?php

namespace App\Filters;
use Closure;
class SubscriptionDoctorFilter extends FilterRequest
{
    public function handle($request, Closure $next){
        if(request()->filled('doctor_id')){
            return $next($request)->whereHas('subject',function ($e){
                $e->whereHas('user',function ($u){
                    $u->where('id',request('doctor_id'));
                });
            });
        }
        return $next($request);
    }
}
