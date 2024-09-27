<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ArticleGroup extends Model
{
	use HasFactory;
	protected $guarded = [];

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy('created_at', 'desc');
		});
	}

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

	public function article_categories()
	{
		return $this->hasMany('App\Models\ArticleCategory');
	}
	
}
