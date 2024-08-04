<?php

namespace App\Notifications;

use App\Services\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
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
                    'ar'=>'حاله الطلب الخاصه بك رقم '.$this->order->order_id.'تم تحديث حالته الي '.__('keywords.'.$this->order->status->value),
                    'en'=>'Order number'.$this->order->order_id.' changed its status to '.$this->order->status->value,
                ],JSON_UNESCAPED_UNICODE),
            'sender'=>auth()->id()
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        SendEmail::send('تحديث في طلب خاص بك لدي '.env('APP_NAME'),'حاله الطلب الخاصه بك رقم '.$this->order->order_id.'تم تحديث حالته الي '.$this->order->status->value,'','',$this->order->order->user->email);
        return (new MailMessage)
            ->subject('Update on your order of '.env('APP_NAME'))
            ->view( 'emails.email', ['details' => ['title'=>'Update on your order of '.env('APP_NAME'),
                'body'=>'Order number'.$this->order->order_id.' changed its status to '.$this->order->status->value,'link'=>'','link_msg'=>'']]);

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
