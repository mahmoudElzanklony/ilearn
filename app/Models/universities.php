<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class universities extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['user_id','name'];

    public function categories()
    {
        return $this->hasMany(categories::class,'university_id');
    }
}
