<?php

namespace App\Services;

class WalletUserService
{
    public static function add_money_to_user_acc($money)
    {
        auth()->user()->update(['wallet'=>auth()->user()->wallet + $money]);
    }
}
