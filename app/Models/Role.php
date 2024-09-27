<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	use HasFactory;
	protected $guarded = [];

	public function scopeDefaultBelib($query) 
	{
		return $query->where('is_default', 1)->where('system', 'belib');
	}

	public function scopeDefaultKm($query) 
	{
		return $query->where('is_default', 1)->where('system', 'km');
	}

	public function scopeDefaultLearnext($query) 
	{
		return $query->where('is_default', 1)->where('system', 'learnext');
	}

}
