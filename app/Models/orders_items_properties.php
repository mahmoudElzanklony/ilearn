<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_items_properties extends Model
{
    use HasFactory;

    protected $fillable = ['order_item_id','property_id','price'];

    public function property()
    {
        return $this->belongsTo(properties::class,'property_id')->withTrashed();
    }
}
