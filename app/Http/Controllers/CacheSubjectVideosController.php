<?php

namespace App\Http\Controllers;

use App\Models\subjects;
use App\Services\CacheSubjectVideosService;
use Illuminate\Http\Request;

class CacheSubjectVideosController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //
        $subjects = subjects::query()->get();
        foreach ($subjects as $subject){
            CacheSubjectVideosService::set_cached($subject->id);
        }
        return response()->json(['status'=>'done']);
    }
}
