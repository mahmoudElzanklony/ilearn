<?php

namespace App\Http\ConcreateInterfaces;

use App\Http\interfaces\CheckDeleteInterface;
use App\Models\categories;
use App\Models\universities;

class categoriesConcrete implements CheckDeleteInterface
{

    public function check_delete($ids)
    {
        $err = 0;
        $check = categories::query()->whereIn('id',$ids)
            ->withCount('subjects')
            ->withCount('students_years')->get();
        foreach($check as $item){
            if($item->students_years_count > 0 || $item->subjects_count > 0){
                $err++;
                break;
            }
        }
        return $err;
    }
}
