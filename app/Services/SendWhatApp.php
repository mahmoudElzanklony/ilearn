<?php

namespace App\Services;

class SendWhatApp
{
    public static function send($number,$password,$username = '')
    {
        $curl = curl_init();
        $google_play = 'https://play.google.com/store/apps/details?id=com.easyntech.ilearn';
        $apple_store = 'سيكون قريبا';
        $message = 'تم انشاء حساب جديد في تطبيق I Learn بأسم  '.$username.' رقم الهاتف هو '.$number.' و كلمة المرور الخاصة بك هي '.$password.' علما بأن رابط التطبيق علي جوجل بلاي هو '.$google_play.' و ابل ستور  '.$apple_store.' ملحوظه هامه عند  مسح التطبيق أو نسيان الباسورد يرجعي التواصل مع الدعم الفني من خلال هذا الرقم او +201001889517';

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
            //echo $response;
            //dd($response);
            curl_close($curl);
          // echo $response;
        }
    }
}