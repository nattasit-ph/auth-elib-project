<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Article extends Model
{
	use HasFactory;
	protected $guarded = [];

	protected $casts = [
		'data_blocks' => 'array',
	];

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy('created_at', 'desc');
		});
	}

	public function scopePermission($query, $arr_child_permission)
	{
		return $query->with('categories')
							->when(config('bookdose.article.category_level') > 1, function ($q) use($arr_child_permission) {
								return $q->whereHas('categories', function ($query) use ($arr_child_permission) {
									$query->whereIn('id', $arr_child_permission);
								});
							});
	}

	public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

	public function scopeRecommended($query)
	{
		return $query->where('is_recommended', 1);
	}

	public function scopeMyOrg($query)
	{
   	if (Auth::check())
   		return $query->where('user_org_id', Auth::user()->user_org_id);
   	else
   		return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

	public function creator()
	{
		return $this->hasOne('App\Models\User', 'id', 'created_by');
	}

	public function comments()
	{
		return $this->hasMany('App\Models\ArticleComment')->Active();
	}

	public function actions()
	{
		return $this->hasMany('App\Models\ArticleAction');
	}

	public function categories()
	{
		return $this->belongsToMany('App\Models\ArticleCategory', 'ref_article_categories', 'article_id', 'article_category_id')
			->using('App\Models\RefArticleCategory');
	}

	public function getFilePath($obj)
	{
		if (!is_object($obj)) {
			return false;
		}
		return $obj->cover_file_path;
	}

	public function earningPoint()
	{
		return $this->morphMany(RewardEarningHistory::class, 'model');
	}
}
