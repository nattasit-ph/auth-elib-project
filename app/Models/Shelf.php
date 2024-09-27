<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
	use HasFactory;
	protected $guarded = [];
	protected $dates = ['borrowed_date', 'expiration_date', 'returned_date'];

	public function scopeMyOrg($query)
	{
		if (Auth::check())
			return $query->where('user_org_id', Auth::user()->user_org_id);
		else
			return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

	public function scopeMyShelf($query)
	{
		if (Auth::check()) {
			return  $query->where('user_id', Auth::user()->id);
		} else {
			return $query->where('user_id', null);
		}
	}

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function productMain()
	{
		return $this->belongsTo('App\Models\ProductMain');
	}

	public function product_copy()
	{
		return $this->belongsTo('App\Models\ProductCopy');
	}

	public function reserve()
	{
		return  $this->hasMany('App\Models\ShelfReserve', 'product_id', 'product_id')
			->where('status', 0)
			->where('user_id', Auth::user()->id);
	}
}
