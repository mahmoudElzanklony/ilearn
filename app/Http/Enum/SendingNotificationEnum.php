<?php

namespace App\Http\Enum;

enum SendingNotificationEnum: string
{
    case sms = 'sms';
    case email = 'email';
    case notification = 'notification';
}
