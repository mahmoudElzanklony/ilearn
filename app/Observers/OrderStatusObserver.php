<?php

namespace App\Observers;

use App\Http\Enum\OrderStatuesEnum;
use App\Models\orders_tracking;
use App\Notifications\OrderStatusNotification;
use App\Notifications\UserRegisteryNotification;
use App\Services\SendEmail;

class OrderStatusObserver
{
    /**
     * Handle the orders_tracking "created" event.
     */
    public function created(orders_tracking $orders_tracking): void
    {
        auth()->user()->notify(new OrderStatusNotification($orders_tracking));
        // check if order tracking is cancelled
        if($orders_tracking->status == OrderStatuesEnum::cancelled->value){
            SendEmail::send('تم ارجاع المبلغ الخاص بالطلب الي محفظتك في '.env('APP_NAME'),
                'تم ارجاع مبلغ قدرة '.$orders_tracking->order->payment->money.' الخاص بالطلب رقم '.$orders_tracking->order->id,
                '','',$orders_tracking->order->user->email
            );
        }
    }

    /**
     * Handle the orders_tracking "updated" event.
     */
    public function updated(orders_tracking $orders_tracking): void
    {
        //
    }

    /**
     * Handle the orders_tracking "deleted" event.
     */
    public function deleted(orders_tracking $orders_tracking): void
    {
        //
    }

    /**
     * Handle the orders_tracking "restored" event.
     */
    public function restored(orders_tracking $orders_tracking): void
    {
        //
    }

    /**
     * Handle the orders_tracking "force deleted" event.
     */
    public function forceDeleted(orders_tracking $orders_tracking): void
    {
        //
    }
}
