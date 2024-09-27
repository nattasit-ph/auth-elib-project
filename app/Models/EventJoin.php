<?php

namespace App\Models;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventJoin extends Model
{
	use HasFactory;
	protected $guarded = [];
	protected $table = 'event_join_kms';

	public function scopeMe($query) 
	{
		return $query->where('user_id', Auth::user()->id);
	}

	public function scopeUser($query, $user_id) 
	{
		return $query->where('user_id', $user_id);
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function event()
	{
		return $this->belongsTo('App\Models\Event');
	}

}
