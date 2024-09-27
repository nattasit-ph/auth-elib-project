<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollCategory extends Model
{
   use HasFactory;
   protected $guarded = [];
    
	public function scopeActive($query) 
	{
   	return $query->where('status', 1);
	}

	public function scopeMyOrg($query) 
	{
   	if (Auth::check())
   		return $query->where('user_org_id', Auth::user()->user_org_id);
   	else
   		return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

	public function polls()
	{
	  return $this->belongsToMany('App\Models\Poll', 'ref_poll_categories', 'poll_category_id', 'poll_id')
	  		->using('App\Models\RefPollCategory');
	}
	
}
