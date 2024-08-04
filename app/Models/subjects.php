<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class subjects extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id','category_id','name','price','semester','note'];

    public function category()
    {
        return $this->belongsTo(categories::class,'category_id');
    }

    public function videos()
    {
        return $this->hasMany(subjects_videos::class,'subject_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function image()
    {
        return $this->morphOne(images::class,'imageable');
    }

    public function students()
    {
        return $this->belongsToMany(User::class,subscriptions::class,'subject_id','user_id')
            ->withPivot(['is_locked'])
            ->as('subjects_students')
            ->wherePivot('is_locked','=',0);
    }
}
