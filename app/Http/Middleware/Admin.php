<?php

namespace App\Http\Middleware;

use App\Services\Messages;
use Illuminate\Http\Request;
use Closure;
class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if(auth()->user()->role->name == 'client'){
            return Messages::error('Unauthorized',401);
        }
        /* if(!(request()->hasHeader('api_key') && request()->header('api_key') == env('api_key','skillar2023'))){
             return messages::error_output('api key is missing !!');
         }*/
        return $next($request);
    }
}
