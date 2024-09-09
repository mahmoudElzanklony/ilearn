<?php

namespace App\Filters\orders;
use Closure;
class CategoryOrderFilter
{
    public function handle($request, Closure $next)
    {
        if (request()->filled('category_id')) {
            return $next($request)->whereHas('subject.category', function ($e) {
                $e->where('id','=',request('category_id'));
            });
        }
        return $next($request);
    }
}
