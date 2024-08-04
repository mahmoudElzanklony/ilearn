<?php

namespace App\Services;

class OrderStatuesService
{
    public static function check($status_obj,$available)
    {
        if(in_array($status_obj->status->value,$available)){
            return true;
        }
        return false;
    }
}
