<?php

namespace App\Observers;

use App\Http\Traits\AdminTrait;
use App\Models\orders;
use App\Notifications\OrderNotification;
use App\Notifications\UserRegisteryNotification;

class OrderObserver
{
    use AdminTrait;
    /**
     * Handle the orders "created" event.
     */
    public function created(orders $order): void
    {
        // this will send notification to admin and email confirmation to client
        $this->currentAdmin()->notify(new OrderNotification($order));
    }

    /**
     * Handle the orders "updated" event.
     */
    public function updated(orders $orders): void
    {
        //
    }

    /**
     * Handle the orders "deleted" event.
     */
    public function deleted(orders $orders): void
    {
        //
    }

    /**
     * Handle the orders "restored" event.
     */
    public function restored(orders $orders): void
    {
        //
    }

    /**
     * Handle the orders "force deleted" event.
     */
    public function forceDeleted(orders $orders): void
    {
        //
    }
}
