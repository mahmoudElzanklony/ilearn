<?php

namespace App\Http\patterns\strategy\payment;

use App\Models\User;
use App\Services\Messages;

class WalletPayment implements PaymentInterface
{

    public function validate($price)
    {
        if(auth()->user()->wallet >= $price){
            $this->remove_from_my_wallet($price);
            return true;
        }
        return Messages::error(__('errors.you_dont_have_enough_money_at_wallet'));
    }

    public function remove_from_my_wallet($price)
    {
        User::query()->find(auth()->id())->update(['wallet'=>auth()->user()->wallet - $price]);
    }
}
