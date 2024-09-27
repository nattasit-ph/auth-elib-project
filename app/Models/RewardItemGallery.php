<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RewardItemGallery extends Model
{
	use HasFactory;
	protected $guarded = [];

   protected static function boot() {
    	parent::boot();
    	static::addGlobalScope('order', function (Builder $builder) {
     		$builder->orderBy('is_cover', 'desc')
     			->orderBy('id', 'asc');
    	});
	}

	public function scopeActive($query) 
	{
		return $query->where('is_cover', 1);
	}

	public function rewardItem()
	{
		return $this->belongsTo('App\Models\RewardItem');
	}

}
