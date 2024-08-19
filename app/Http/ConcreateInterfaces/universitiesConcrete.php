<?php

namespace App\Http\ConcreateInterfaces;

use App\Http\interfaces\CheckDeleteInterface;
use App\Services\Messages;
use App\Models\universities;
class universitiesConcrete implements CheckDeleteInterface
{

    public function check_delete($ids)
    {
        $err = 0;
        $check = universities::query()->whereIn('id',$ids)->withCount('categories')->get();
        foreach($check as $item){
            if($item->categories_count > 0){
                $err++;
                break;
            }
        }
        return $err;
    }
}
