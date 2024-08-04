<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_videos_views extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','video_id'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function video()
    {
        return $this->belongsTo(subjects_videos::class,'video_id');
    }
}
