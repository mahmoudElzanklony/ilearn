<?php

namespace App\Filters;
use App\Models\categories;
use Closure;
class CategoryIdFilter
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
