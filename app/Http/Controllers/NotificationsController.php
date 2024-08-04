<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notifications;
use App\Services\Messages;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //
    public function index()
    {
        if(request()->filled('type')){
            $method = request('type').'Notifications';

            return NotificationResource::collection(auth()->user()->$method()->paginate(request('limit') ?? 10));
        }
        return NotificationResource::collection(auth()->user()->Notifications()->paginate(request('limit') ?? 10));
    }

    public function seen()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return Messages::success(__('messages.operation_done_successfully'));
    }
}
