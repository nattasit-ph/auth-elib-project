<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardRedemptionHistory extends Model
{
    use HasFactory;
	protected $guarded = [];

	public function scopeIsDelivered($query) 
	{
     	return $query->where('is_delivered', 1);
 	}

    public function scopeIsRefunded($query) 
    {
        return $query->where('is_refunded', 1);
    }

	public function rewardItem()
	{
		return $this->belongsTo('App\Models\RewardItem');
	}
	
	public function user()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}

}
