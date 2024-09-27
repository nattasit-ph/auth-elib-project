<?php

namespace App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\Tag;

class PodcastStation extends Model
{
	use HasFactory;
	use \Spatie\Tags\HasTags;
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

	public function category()
	{
		return $this->belongsTo('App\Models\PodcastCategory');
	}
	
	public function categories()
	{
		return $this->belongsToMany('App\Models\PodcastCategory', 'ref_podcast_categories', 'podcast_id', 'podcast_category_id')
			->using('App\Models\RefPodcastCategory');
	}

}
