<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Banner extends Model
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

    public function scopeOfOrg($query, $user_org_id)
    {
        return $query->where('user_org_id', $user_org_id);
    }

	public function scopeMyOrg($query)
	{
		if (Auth::check())
			return $query->where('user_org_id', Auth::user()->user_org_id);
		else
			return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

	public function scopeBelib($query)
	{
		return $query->where('system', 'belib');
	}

	public function scopeLearnext($query)
	{
		return $query->where('system', 'learnext');
	}

	public function scopeArea($query, $display_area)
	{
		return $query->where('display_area', $display_area);
	}

	public function scopeForWeb($query)
	{
		return $query->where('for_web', 1);
	}

	public function scopeForMobile($query)
	{
		return $query->where('for_mobile', 1);
	}
}
