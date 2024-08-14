<?php

namespace App\Filters\subjects;

use App\Filters\FilterRequest;
use Closure;
class UniversityFilter extends FilterRequest
{
    public function handle($request, Closure $next){
        if(request()->has('university_id')){
            return $next($request)->whereHas('subject',fn($e)=>
                $e->whereHas('category',fn($q)=>$q->where('university_id','=',request('university_id')))
            );
        }
        return $next($request);
    }
}
