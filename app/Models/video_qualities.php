<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class video_qualities extends Model
{
    use HasFactory;

    protected $fillable = ['subject_video_id','name', 'quality','wasbi_url'];
}
