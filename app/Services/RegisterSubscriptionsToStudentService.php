<?php

namespace App\Services;

use App\Models\subjects;
use App\Models\subscriptions;

class RegisterSubscriptionsToStudentService
{
    public static function register($student_id,$doctor_id,$category_id){
        $subjects = self::get_subjects($doctor_id,$category_id);
        $subscriptionsData = [];
        foreach ($subjects as $subject) {
            $subscriptionsData[] = [
                'user_id' => $student_id,
                'subject_id' => $subject->id,
                'is_locked' => false,
                'price' => $subject->price,
                'discount' => 0,
                'added_by' => $doctor_id,
            ];
        }
        subscriptions::insert($subscriptionsData);
    }

    public static function get_subjects($doctor_id,$category_id){
        return  subjects::query()->where('user_id',$doctor_id)
            ->where('category_id',$category_id)->get();
    }
}
