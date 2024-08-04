<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categories_properties extends Model
{
    use HasFactory;

    protected $fillable = ['category_id','property_id','price'];

    public function category()
    {
        return $this->belongsTo(categories::class,'category_id');
    }

    public function property()
    {
        return $this->belongsTo(properties::class,'property_id');
    }
}
