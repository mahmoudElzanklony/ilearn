<?php

namespace App\Http\Controllers;

use App\Models\versions;
use Illuminate\Http\Request;

class VersionsController extends Controller
{
    //
    public function index(){
        return versions::query()->first();
    }

    public function update(Request $request){
        versions::query()->first()->update($request->all());
    }
}
