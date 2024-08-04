<?php

namespace App\Http\patterns\strategy\Messages;

use App\Services\SendEmail;

class EmailMessages implements MessagesInterface
{

    public function send($data)
    {

        $user = $data['user'];
        SendEmail::send($data['title'],$data['message'],'','',$user->email);
    }
}
