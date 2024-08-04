<?php

namespace App\Http\Traits;
use App\Models\notifications;
use Illuminate\Notifications\Notifiable as Notifier;

trait NotifiableTrait
{
    use Notifier;

    public function notifications()
    {
        return $this->morphMany(notifications::class, 'notifiable')->latest();
    }
}
