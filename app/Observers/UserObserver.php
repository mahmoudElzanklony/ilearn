<?php

namespace App\Observers;

use App\Http\patterns\strategy\Messages\SMSMessages;
use App\Models\User;
use App\Notifications\UserRegisteryNotification;
use App\Http\Traits\AdminTrait;
use App\Notifications\WalletChargingNotification;

class UserObserver
{
    use AdminTrait;
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
         // $this->currentAdmin()->notify(new UserRegisteryNotification($user,false));
         $user->notify(new UserRegisteryNotification($user,true,true));
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->isDirty('wallet')) {
            // The "wallet" column has been changed
            $originalStatus = $user->getOriginal('wallet');
            $user->notify(new WalletChargingNotification($user,$originalStatus));
        }

    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
