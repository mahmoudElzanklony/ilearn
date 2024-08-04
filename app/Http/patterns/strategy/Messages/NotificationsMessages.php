<?php

namespace App\Http\patterns\strategy\Messages;

use App\Notifications\AdminSendNotification;

class NotificationsMessages implements MessagesInterface
{

    public function send($data)
    {
        // TODO: Implement send() method.
        $user = $data['user'];
        $user->notify(new AdminSendNotification($data));
    }
}
