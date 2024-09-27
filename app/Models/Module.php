<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Module extends Model
{
 	use HasFactory;
 	protected $guarded = [];
   
   protected static function boot() {
    	parent::boot();
    	static::addGlobalScope('order', function (Builder $builder) {
     		$builder->orderBy('weight', 'asc');
    	});
	}

	public function scopeActive($query) 
	{
   	return $query->where('status', 1);
	}

	public function scopeInCenter($query) 
	{
   	return $query->where('in_center', 1);
	}

	public function scopeMyOrg($query) 
	{
   	if (Auth::check())
   		return $query->where('user_org_id', Auth::user()->user_org_id);
   	else
   		return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

}
