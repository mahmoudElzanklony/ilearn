<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subscriptions extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','subject_id','is_locked','price','discount','note'];


    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function subject()
    {
        return $this->belongsTo(subjects::class,'subject_id');
    }
}
