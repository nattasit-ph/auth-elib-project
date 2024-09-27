<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogChatbot extends Model
{
	use HasFactory;
 	protected $guarded = [];

	 protected $casts = [
		'data' => 'array',
	];
	
}
