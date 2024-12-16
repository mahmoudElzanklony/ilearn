<?php

namespace App\Services;

use App\Models\subjects;
use Illuminate\Support\Facades\Cache;

class CacheSubjectVideosService
{
    private static function get_data($subject_id)
    {
        return subjects::query()->with('videos')
            ->where('id', $subject_id)->first();
    }

    public static function get_cached($subject_id)
    {
        return Cache::remember('subject-'.$subject_id,now()->addDays(10),function () use ($subject_id){
            return self::get_data($subject_id);
        });
    }

    public static function set_cached($subject_id)
    {
        $data = self::get_data($subject_id);
        return Cache::put('subject-'.$subject_id,$data,now()->addDays(10));
    }
}
