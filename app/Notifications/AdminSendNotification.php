<?php

namespace App\Notifications;

use App\Services\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminSendNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $data;
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toDatabase(object $notifiable)
    {

        return [
            'data'=>json_encode(
                [
                    'ar'=>$this->data['message'],
                    'en'=>$this->data['message'],
                ],JSON_UNESCAPED_UNICODE),
            'sender'=>auth()->id()
        ];
    }
    /**
     * Get the mail representation of the notification.
     */


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
