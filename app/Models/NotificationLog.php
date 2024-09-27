<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NotificationLog extends Model
{
    use HasFactory;
    protected $guarded = [];

	protected $casts = [
		'data_devices' => 'array',
	];

	public function scopeActive($query) 
	{
   		return $query->where('status', 1);
	}

	public function scopeOrg($query, $user_org_id)
	{
		return $query->where('user_org_id', $user_org_id);
	}

	public function notification()
	{
		return $this->belongsTo('App\Models\Notification');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function scopeUnread($query)
	{
		return $query->where('is_read', 0);
	}

}
