<?php

namespace App\Http\Enum;

enum OrderStatuesEnum : string
{
    case pending = 'pending';
    case review = 'review';
    case printing = 'printing';
    case delivery = 'delivery';
    case completed = 'completed';
    case cancelled = 'cancelled';

}
