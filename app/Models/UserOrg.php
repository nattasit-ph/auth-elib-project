<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class UserOrg extends Model
{
	use HasFactory;
	protected $guarded = [];

	protected $casts = [
		'data_info' => 'array',
		'data_contact' => 'array',
        'data_summary' => 'array',
		'user_info_template' => 'array',
	];

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy('name_' . app()->getLocale(), 'asc');
		});
	}

	public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

    public function scroptExpired($query, $date=NULL)
    {
        $date = $date ?? Carbon::now();
        return $query->where(function ($q) use ($date) {
            $q->whereNotNull('expires_at')->where('expires_at', '<=', $date);
        });
    }

    public function scopeNotExpired($query, $date=NULL)
    {
        $date = $date ?? Carbon::now();
        return  $query->where(function ($q) use ($date) {
            $q->whereNull('expires_at')->orWhere(function ($q) use ($date) {
                $q->whereNotNull('expires_at')->where('expires_at', '>', $date);
            });
        });
    }

	public function scopeIsBookdose($query)
	{
		return $query->where('is_bd', 1);
	}

	public function scopeIsClient($query)
	{
		return $query->where('is_bd', 0);
	}

	public function users()
	{
		return $this->hasMany('App\Models\User');
	}

    public function scopeOfSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeOfOrg($query, $id)
    {
        return $query->where('id', $id);
    }

	public function ScopeMyOrg($query)
	{
		if (Auth::check())
			return $query->where('id', Auth::user()->user_org_id);
		else
			return $query->where('id', config('bookdose.default.user_org'));
	}

    public function questionBelib()
    {
        return $this->belongsToMany('App\Models\Form', 'form_systems', 'user_org_id', 'form_id')
            ->where('status', 1)
            ->using('App\Models\FormSystem')->where('system', 'belib');
    }
    public function questionKm()
    {
        return $this->belongsToMany('App\Models\Form', 'form_systems', 'user_org_id', 'form_id')
            ->where('status', 1)
			->using('App\Models\FormSystem')->where('system', 'km');
    }
    public function questionLearnext()
    {
        return $this->belongsToMany('App\Models\Form', 'form_systems', 'user_org_id', 'form_id')
            ->where('status', 1)
			->using('App\Models\FormSystem')->where('system', 'learnext');
    }

    public function banner()
    {
        return $this->hasMany('App\Models\Banner', 'user_org_id', 'id');
    }
}
