<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subjects_videos extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','subject_id','video','wasbi_url','type'];

    public function subject()
    {
        return $this->belongsTo(subjects::class,'subject_id')
            ->withTrashed();
    }

    public function watchers()
    {
        return $this->hasMany(users_videos_views::class,'video_id');
    }

    public function image()
    {
        return $this->morphOne(images::class,'imageable');
    }

    public function qualities(){
        return $this->hasMany(video_qualities::class,'subject_video_id');
    }
}
