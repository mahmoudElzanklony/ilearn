<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class properties extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id','property_id_heading','name','price'];

    public function heading()
    {
        return $this->belongsTo(properties_heading::class,'property_id_heading');
    }
}
