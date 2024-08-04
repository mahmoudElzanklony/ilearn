<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class properties_heading extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id','name'];

    public function properties()
    {
        return $this->hasMany(properties::class,'property_id_heading');
    }
}
