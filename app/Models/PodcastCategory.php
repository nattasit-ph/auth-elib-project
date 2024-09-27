<?php

namespace App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodcastCategory extends Model
{
	use HasFactory;
	protected $guarded = [];

	public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

	public function scopeMyOrg($query)
	{
		if (Auth::check())
			return $query->where('user_org_id', Auth::user()->user_org_id);
		else
			return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

	public function stations()
	{
		return $this->hasMany('App\Models\PodcastStation');
	}
	
	public function podcast()
	{
	  return $this->belongsToMany('App\Models\PodcastStation', 'ref_podcast_categories', 'podcast_category_id', 'podcast_id')
	  		->using('App\Models\RefPodcastCategory');
	}

	public function podcast_stations()
	{
	  return $this->belongsToMany('App\Models\PodcastStation', 'ref_podcast_categories', 'podcast_category_id', 'podcast_id')
			->where('podcast_stations.status', 1)
	  		->using('App\Models\RefPodcastCategory');
	}
	
}
