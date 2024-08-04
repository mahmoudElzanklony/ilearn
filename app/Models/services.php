<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class services extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id','category_id','name','info','price'];

    public function category()
    {
        return $this->belongsTo(categories::class,'category_id');
    }

    public function image()
    {
        return $this->morphOne(images::class,'imageable');
    }
}
