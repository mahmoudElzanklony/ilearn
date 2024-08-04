<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class orders extends Model
{
    use SoftDeletes;
    use HasFactory;
    // status ==> make it default okay
    // status ==> maybe wait_client_reply in case item of order removed and wait client to accept  this change
    // note make it json {client:"",system_refund:""} so if order is refund money wallet or anything happen put at refund admin or client
    protected $fillable = ['user_id','city','region','address','street','house_number','coordinates','status','note'];

    public function status():Attribute
    {
        return Attribute::make(set: fn()  => 'okay');
    }
    public function scopeActiveMode(Builder $query)
    {
        return $query->where('status','=','working');
    }

    public function scopeCompleted(Builder $query)
    {
        return $query->whereHas('statues',function($e){
            $e->where('status','=','completed');
        })->orderBy('id','DESC');
    }
    public function statues()
    {
        return $this->hasMany(orders_tracking::class,'order_id');
    }
    public function last_status(){
        return $this->hasOne(orders_tracking::class,'order_id')->latestOfMany();
    }


    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function items()
    {
        return $this->hasMany(orders_items::class,'order_id');
    }

    public function rate()
    {
        return $this->hasOne(orders_rates::class,'order_id');
    }



    public function coupon_order()
    {
        return $this->hasOne(orders_coupons::class,'order_id');
    }

    public function coupon_info()
    {
        return $this->hasOneThrough(
            coupons::class,
            orders_coupons::class,
            'order_id', // Foreign key on the intermediate table
            'id', // Foreign key on the related table (coupons table)
            'id', // Local key on the orders table
            'coupon_id' // Local key on the intermediate table
        );
    }

    public function payment()
    {
        return $this->morphOne(payments::class,'paymentable');
    }
}
