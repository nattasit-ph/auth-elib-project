<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SiteInfo extends Model
{
	use HasFactory;
	protected $guarded = [];

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy('weight', 'asc')
				->orderBy('id', 'asc');
		});
	}

	public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

	public function scopeLang($query, $lang)
	{
		return $query->where('meta_lang', $lang);
	}

	public function scopeMyOrg($query, $user_org_id = '')
	{
		if (!empty($user_org_id))
			return $query->where('site_infos.user_org_id', $user_org_id);
		else
			return $query->where('site_infos.user_org_id', Auth::user()->user_org_id);
	}
}
