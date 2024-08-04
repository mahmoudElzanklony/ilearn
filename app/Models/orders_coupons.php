<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_coupons extends Model
{
    use HasFactory;

    // coupon value is fixed value

    protected $fillable = ['order_id','coupon_id','coupon_value'];

    public function order()
    {
        return $this->belongsTo(orders::class,'order_id')->withTrashed();
    }
    public function coupon()
    {
        return $this->belongsTo(coupons::class,'coupon_id')->withTrashed();
    }
}
