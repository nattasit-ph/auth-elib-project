<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductMain extends Model
{
	use HasFactory;
	protected $guarded = [];
	protected $casts = [
		'data_fields_template' => 'array',
		'data_marc21_template' => 'array'
	];

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy('weight', 'asc');
		});
	}

	public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

	public function scopeOfOrg($query, $user_org_id)
	{
		return $query->where('user_org_id', $user_org_id);
	}

	// public function scopeOrg($query, $user_org_id)
	// {
	// 	return $query->where('user_org_id', $user_org_id);
	// }

	public function scopeHasElibrary($query)
	{
		$total = $query->where('is_digital', 1)
			->where('status', 1)
			->count();
		return ($total > 0 ? true : false);
	}

	public function scopeHasLibrary($query)
	{
		$total = $query->where('is_digital', 0)
			->where('status', 1)
			->count();
		return ($total > 0 ? true : false);
	}

	public function categories()
	{
		return $this->belongsToMany('App\Models\ProductCategory', 'ref_product_main_categories', 'product_main_id', 'product_category_id')
			->using('App\Models\RefProductMainCategory');
	}

	public function products()
	{
		return $this->hasMany('App\Models\Product');
	}

	public function product_magazine_titles()
	{
		return $this->hasMany('App\Models\ProductMagazineTitle');
	}
}
