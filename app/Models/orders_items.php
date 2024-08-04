<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_items extends Model
{
    use HasFactory;

    // is_cancelled ==> if item is removed from order will be reasons for remove if null so item is okay
    // client can remove it in stage pending or review ==> {"who":"client","reason":""}

    protected $fillable = ['order_id','service_id','is_cancelled','price','file','paper_number','copies_number'];

    public function order()
    {
        return $this->belongsTo(orders::class,'order_id');
    }

    public function service()
    {
        return $this->belongsTo(services::class,'service_id')->withTrashed();
    }

    public function properties()
    {
        return $this->hasMany(orders_items_properties::class,'order_item_id');
    }
}
