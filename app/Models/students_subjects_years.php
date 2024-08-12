<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class students_subjects_years extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','year_id'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function year()
    {
        return $this->belongsTo(categories::class,'year_id');
    }
}
