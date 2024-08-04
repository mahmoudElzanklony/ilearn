<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subjects_videos extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','subject_id','video'];

    public function subject()
    {
        return $this->belongsTo(subjects::class,'subject_id');
    }
}
