<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payments extends Model
{
    use HasFactory;
    // type ==========> visa or wallet
    protected $fillable = ['paymentable_id','paymentable_type','money','tax','type'];

    public function paymentable()
    {
        return $this->morphTo();
    }
}
