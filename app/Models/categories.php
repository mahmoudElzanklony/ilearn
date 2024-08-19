<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class categories extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id','university_id','name','info','parent_id'];

    public function parent()
    {
        return $this->belongsTo(categories::class,'id')->withTrashed();
    }

    public function university()
    {
        return $this->belongsTo(universities::class,'university_id')->withTrashed();
    }

    public function subjects()
    {
        return $this->hasMany(subjects::class,'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function image()
    {
        return $this->morphOne(images::class,'imageable');
    }
    public function properties()
    {
        return $this->belongsToMany(properties::class,categories_properties::class,'category_id','property_id');
    }

    public function students_years()
    {
        return $this->hasMany(User::class,'year_id');
    }


}
