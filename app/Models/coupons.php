<?php

namespace App\Models;

use App\Http\Enum\CouponsTypesEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class coupons extends Model
{
    use HasFactory;
    use SoftDeletes;
    // type =======> percentage or fixed_value
    // max value ===> in case type is percentage so max value discount will be 100 SAR
    protected $fillable = ['user_id','name','serial','expiration_at','max_number_of_users','max_usage_per_user','type','value','max_value'];

    protected $casts = [
        'type'=>CouponsTypesEnum::class
    ];


}
