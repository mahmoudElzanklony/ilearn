<?php

namespace App\Models;

use App\Http\Enum\OrderStatuesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_tracking extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','status'];

    protected $casts = [
        'status'=>OrderStatuesEnum::class
    ];

    public function order()
    {
        return $this->belongsTo(orders::class,'order_id');
    }
}
