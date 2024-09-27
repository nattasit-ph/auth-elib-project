<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\Tag;

class Product extends Model
{
	use HasFactory;
	use \Spatie\Tags\HasTags;
	protected $guarded = [];

	protected $casts = [
		'data_fields' => 'array',
		'data_marc21' => 'array',
	];

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

	public function product_main()
	{
		return $this->belongsTo('App\Models\ProductMain');
	}
}
