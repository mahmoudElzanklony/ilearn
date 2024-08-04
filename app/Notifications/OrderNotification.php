<?php

namespace App\Notifications;

use App\Services\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderNotification extends Notification
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
                    'ar'=>$this->order->user->username.' قام بأنشاء اوردر رقم '.$this->order->id.' بتكلفة تصل الي '.$this->order->payment->money,
                    'en'=>$this->order->user->username.' made an order with id : '.$this->order->id.' and order cost is '.$this->order->payment->money,
                ],JSON_UNESCAPED_UNICODE),
            'sender'=>auth()->id()
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        SendEmail::send('تم انشاء طلب جديد في '.env('APP_NAME'),'تم القيام بأنشاء طلب جديد من قبلك و هذه رسالة تأكيدية بذلك و الطلب تحت مراجعه الادارة الان من فضلك راجع اشعارات النظام بشكل مستمر لتحصل علي كل جديد','','',$this->order->user->email);
        return (new MailMessage)
            ->subject('New order made successfully at '.env('APP_NAME'))
            ->view( 'emails.email', ['details' => ['title'=>'New order made successfully at '.env('APP_NAME'),
                'body'=>'A new order has been created by you, and this is a confirmation message, and the order is under management review now. Please check the system notifications on an ongoing basis to get everything new.','link'=>'','link_msg'=>'']]);

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
