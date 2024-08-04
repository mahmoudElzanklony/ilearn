<?php

namespace App\Services;

class Messages
{
    public static function success($message = '' , $data = '')
    {
        return response()->json(['data'=>$data,'message'=>$message]);
    }

    public static function error($error,$status = 400)
    {
        return response()->json(['errors'=>$error],$status);
    }
}
