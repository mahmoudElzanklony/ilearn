<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bills extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id','start_date','end_date','total_money','profit','remain','note'];

    public function doctor()
    {
        return $this->belongsTo(User::class,'doctor_id','id');
    }
}
