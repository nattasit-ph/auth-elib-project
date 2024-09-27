<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceRegister extends Model
{
	use HasFactory;
	protected $guarded = [];

	public function scopeActive($query) 
	{
     	return $query->where('status', 1);
 	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

}
