<?php

namespace App\Http\Enum;

enum CouponsTypesEnum : string
{
    case fixed_value = 'fixed';

    case percentage = 'percentage';
}
