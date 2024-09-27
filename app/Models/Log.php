<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
	use HasFactory;
 	protected $guarded = [];

	public function scopeMyOrg($query) 
	{
   	return $query->where('logs.user_org_id', Auth::user()->user_org_id);
	}
	
}
