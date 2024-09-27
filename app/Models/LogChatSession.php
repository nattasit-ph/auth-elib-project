<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogChatSession extends Model
{

	protected $primaryKey = null;
	public $incrementing = false;	
	
	protected $fillable = [
		'from', 'to', 'unread', 'lastest_message', 'lastest_at'
	];

	public function user_to()
	{
		return $this->belongsTo('App\Models\User', 'email', 'email');
	}

	public function user_from()
	{
		return $this->belongsTo('App\Models\User', 'email', 'email');
	}

}