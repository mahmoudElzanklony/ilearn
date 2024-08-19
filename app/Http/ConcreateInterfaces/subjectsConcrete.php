<?php

namespace App\Http\ConcreateInterfaces;

use App\Http\interfaces\CheckDeleteInterface;
use App\Models\categories;
use App\Models\subjects;

class subjectsConcrete implements CheckDeleteInterface
{

    public function check_delete($ids)
    {
        $err = 0;
        $check = subjects::query()->whereIn('id',$ids)->withCount('videos')->get();
        foreach($check as $item){
            if($item->videos_count > 0){
                $err++;
                break;
            }
        }
        return $err;
    }
}
