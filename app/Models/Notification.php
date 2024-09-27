<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    use HasFactory;
    protected $guarded = [];

	public function scopeActive($query) 
	{
   		return $query->where('status', 1);
	}

	public function notification_users()
	{
		return $this->hasMany('App\Models\NotificationUser');
	}

	public function notification_logs()
	{
		return $this->hasMany('App\Models\NotificationLog');
	}

}
