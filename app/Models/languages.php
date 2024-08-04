<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class languages extends Model
{
    use HasFactory;

    protected $fillable = ['name','prefix'];

    public function prefix(): Attribute
    {
        return Attribute::make(
            set: fn() => Str::substr(ucfirst($this->attributes['name']),0,2)
        );
    }
}
