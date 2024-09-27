<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Tags\Tag;

class UserGroup extends Model
{
	use HasFactory;
	use \Spatie\Tags\HasTags;
	protected $guarded = [];

	protected $casts = [
		'data_policies' => 'array',
		'data_rooms' => 'array',
	];

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy('weight', 'asc');
		});
	}

    public function scopeOfOrg($query, $user_org_id)
    {
        return $query->where('user_org_id', $user_org_id);
    }

	public function scopeMyOrg($query)
	{
		return $query->where('user_org_id', Auth::user()->user_org_id ?? config('bookdose.default.user_org'));
	}

	public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

	public function scopeIsDefault($query)
	{
		return $query->where('is_default', 1);
	}

	public function users()
	{
		return $this->hasMany('App\Models\User');
	}
}
