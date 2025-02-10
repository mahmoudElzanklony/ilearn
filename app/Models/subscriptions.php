<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class subscriptions extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id','subject_id','is_locked','price','discount','note','added_by'];


    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }
    public function subject()
    {
        return $this->belongsTo(subjects::class,'subject_id')->withTrashed();
    }

    public function added_by()
    {
        return $this->belongsTo(User::class,'added_by');
    }





}
