<?php

namespace App\Observers;

use App\Http\Traits\AdminTrait;
use App\Models\orders_items;
use App\Notifications\OrderItemNotification;
use App\Notifications\OrderNotification;

class OrderItemObserver
{
    use AdminTrait;
    /**
     * Handle the orders_items "created" event.
     */
    public function created(orders_items $orders_items): void
    {
        //
    }

    /**
     * Handle the orders_items "updated" event.
     */
    public function updated(orders_items $orders_items): void
    {
        //
        $this->currentAdmin()->notify(new OrderItemNotification($orders_items));
    }

    /**
     * Handle the orders_items "deleted" event.
     */
    public function deleted(orders_items $orders_items): void
    {
        //
    }

    /**
     * Handle the orders_items "restored" event.
     */
    public function restored(orders_items $orders_items): void
    {
        //
    }

    /**
     * Handle the orders_items "force deleted" event.
     */
    public function forceDeleted(orders_items $orders_items): void
    {
        //
    }
}
