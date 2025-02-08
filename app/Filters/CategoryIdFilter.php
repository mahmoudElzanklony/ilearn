<?php

namespace App\Filters;
use App\Models\categories;
use Closure;
class CategoryIdFilter
{
    public function handle($request, Closure $next)
    {
        if(request()->filled('all_subjects') && request()->filled('category_id')){
            return $next($request)->whereHas('category', function ($query) {
                $categoryId = request('category_id');

                // Find the university_id for the given category
                $universityId = categories::where('id', $categoryId)->value('university_id');

                // Get all subjects that belong to categories of the same university
                $query->whereHas('university', function ($subQuery) use ($universityId) {
                    $subQuery->where('university_id', $universityId);
                });
            });
        }else if (request()->filled('category_id')) {
            return $next($request)->where('category_id','=',request('category_id'));
        }
        return $next($request);
    }
}
