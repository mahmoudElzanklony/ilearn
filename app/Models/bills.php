<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class bills extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['doctor_id','start_date','end_date','total_money','remain','note'];

    public function doctor()
    {
        return $this->belongsTo(User::class,'doctor_id','id')->withTrashed();
    }
}
