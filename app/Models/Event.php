<?php

namespace App\Models;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Tags\Tag;

class Event extends Model
{
	use HasFactory;
	use \Spatie\Tags\HasTags;

	protected $guarded = [];
	protected $table = 'event_kms';

	protected static function boot() {
		parent::boot();
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy('event_start', 'asc');
		});
	}

	public function scopeActive($query) 
	{
		return $query->where('status', 1);
	}

	public function event_joins()
	{
	  return $this->hasMany('App\Models\EventJoin');
	}

}
