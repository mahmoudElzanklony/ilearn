<?php

namespace App\Services;

class SendWhatApp
{
    public static function send($number,$password,$username = '')
    {
        $curl = curl_init();
        $google_play = 'https://play.google.com/store/apps/details?id=com.easyntech.ilearn';
        $apple_store = 'https://apps.apple.com/us/app/ilearn/id6670336964';
        $message = "*مساء الخير.*\n".
               " يرجي تسجيل الرقم\n".
            "تم انشاء حساب جديد في تطبيق *I Learn* بأسم ".$username." رقم الهاتف هو "."*".$number."*"." و كلمة المرور الخاصة بك هي "."*".$password."*"."\n"
            ."علما بأن رابط التطبيق علي جوجل بلاي هو ".$google_play."\n"." و ابل ستور "."\n".$apple_store."\n"
            ."ملحوظه هامه عند مسح التطبيق أو نسيان الباسورد يرجي التواصل مع الدعم الفني من خلال هذا الرقم او +201001889517";

        if(env('whatAppStatus')) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://wapp.upgrade-s.com/api/create-message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'appkey' => env('whatAppKey'),
                    'authkey' => env('whatAppAuthKey'),
                    'to' => $number,
                    'message' =>$message,
                    'sandbox' => 'false'
                ),));

            $response = curl_exec($curl);


            curl_close($curl);
          // echo $response;
        }
    }
}
