<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardItem extends Model
{
	use HasFactory;
	protected $guarded = [];

	public function scopeActive($query) 
	{
     	return $query->where('status', 1);
 	}

	public function scopeInStock($query) 
	{
     	return $query->where('stock_avail', '>', 0);
 	}

	public function scopeNotExpired($query) 
	{
		return $query->whereDate('expired_at','>=' ,today())
		->orWhere('expired_at' , null);
	}

	public function scopeStarted($query) 
	{
		return $query->whereDate('started_at','<=' ,today())
		->orWhere('started_at' , null);
	}
 
	public function rewardCategory()
	{
		return $this->belongsTo('App\Models\RewardCategory');
	}

	public function rewardGalleries()
	{
	  return $this->hasMany('App\Models\RewardItemGallery');
	}

	public function rewardRedemptionHistory()
	{
		return $this->hasMany('App\Models\RewardRedemptionHistory');
	}

}
