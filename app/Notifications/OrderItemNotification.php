<?php

namespace App\Notifications;

use App\Services\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderItemNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $order;
    public function __construct($order)
    {
        //
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database','mail'];
    }


    public function toDatabase(object $notifiable)
    {

        return [
            'data'=>json_encode(
                [
                    'ar'=>$this->order->order->user->username.'قام بالغاء خدمه رقم '.$this->order->id.' بنجاح من الاوردر التابع له رقم '.$this->order->order->id,
                    'en'=>$this->order->order->user->username.'cancel service successfully and its id is '.$this->order->id.' from order that id is'.$this->order->order->id,
                ],JSON_UNESCAPED_UNICODE),
            'sender'=>auth()->id()
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        SendEmail::send('تم الغاء الخدمه في  '.env('APP_NAME'),'لقد قمت بالغاء الخدمة رقم  '.$this->order->id.' من الاوردر التابع له رقم '.$this->order->order->id,'','',$this->order->order->user->email);
        return (new MailMessage)
            ->subject('Service cancelled successfully at '.env('APP_NAME'))
            ->view( 'emails.email', ['details' => ['title'=>'Service cancelled successfully at '.env('APP_NAME'),
                'body'=>'You cancel service successfully and its id is '.$this->order->id.' from order that id is'.$this->order->order->id,'link'=>'','link_msg'=>'']]);


    }

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
