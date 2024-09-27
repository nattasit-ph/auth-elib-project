<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleAction extends Model
{
 	use HasFactory;
  	protected $guarded = [];

	public function article()
	{
		return $this->belongsTo('App\Models\Article');
	}

	public function creator()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}

	public function scopeMyOrg($query)
	{
   	if (Auth::check())
   		return $query->where('user_org_id', Auth::user()->user_org_id);
   	else
   		return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

}
