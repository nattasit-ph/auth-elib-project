<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserOrgUnit extends Model
{
 	use HasFactory;
 	protected $guarded = [];

   protected static function boot() {
    	parent::boot();
    	static::addGlobalScope('order', function (Builder $builder) {
     		$builder->orderBy('weight', 'asc')->orderBy('title', 'asc');
    	});
	}

	public function scopeActive($query) 
	{
      	return $query->where('status', 1);
	}
	
	public function knowledges()
	{
		return $this->hasMany('App\Models\Knowledge');
	}

	public function articles()
	{
		return $this->hasMany('App\Models\Article');
	}

}
