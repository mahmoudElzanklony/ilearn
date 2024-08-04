<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_rates extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','delivery_rate','print_rate','comment'];

    public function order()
    {
        return $this->belongsTo(orders::class,'order_id');
    }
}
