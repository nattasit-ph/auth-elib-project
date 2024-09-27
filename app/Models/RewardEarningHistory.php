<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardEarningHistory extends Model
{
    use HasFactory;
	protected $guarded = [];
	protected $casts = [
        'data_info' => 'array',
    ];
	
	public function user()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}

	public function rewardActivity()
	{
		return $this->belongsTo('App\Models\RewardActivity');
	}
}
