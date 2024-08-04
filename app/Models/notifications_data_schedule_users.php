<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifications_data_schedule_users extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','notification_data_schedule_id'];

    public function notification_data_schedule()
    {
        return $this->belongsTo(notifications_data_schedule::class,'notification_data_schedule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
