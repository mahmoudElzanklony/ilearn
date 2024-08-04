<?php

namespace App\Notifications;

use App\Mail\Myemail;
use App\Services\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletChargingNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $user;
    private $old_wallet;
    public function __construct($user , $old_wallet)
    {
        //
        $this->user = $user;
        $this->old_wallet = $old_wallet;
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

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {

        return [
            'data'=>json_encode(
                [
                    'ar'=>'تم شحن رصيد المحفظه بقيمه '.$this->user->wallet - $this->old_wallet.' و اصبح الرصيد الحالي هو '.$this->user->wallet,
                    'en'=>'The wallet balance has been charged '.$this->user->wallet - $this->old_wallet.' The current balance becomes '.$this->user->wallet,
                ],JSON_UNESCAPED_UNICODE),
            'sender'=>auth()->id()
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {

        SendEmail::send('تم شحن رصيد المحفظه في '.env('APP_NAME'),'تم شحن رصيد المحفظه بقيمه '.$this->user->wallet - $this->old_wallet.' و اصبح الرصيد الحالي هو '.$this->user->wallet,'','',$this->user->email);
        return (new MailMessage)
            ->subject('The wallet balance has been charged at '.env('APP_NAME'))
            ->view( 'emails.email', ['details' => ['title'=>'The wallet balance has been charged at '.env('APP_NAME'),'body'=>'The wallet balance has been charged '.$this->user->wallet - $this->old_wallet.' The current balance becomes '.$this->user->wallet,'link'=>'','link_msg'=>'']]);

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
