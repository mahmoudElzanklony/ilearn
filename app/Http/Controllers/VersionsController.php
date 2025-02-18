<?php

namespace App\Http\Controllers;

use App\Models\versions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VersionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('update');
    }
    //
    public function index(){
        return Cache::remember('versions',now()->addDays(2), function(){
            return versions::query()->first();
        });
    }

    public function update(Request $request){
        versions::query()->first()->update($request->all());
    }
}
