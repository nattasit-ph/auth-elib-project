<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Poll extends Model
{
 	use HasFactory;
 	protected $guarded = [];

	public function scopeActive($query) 
	{
		return $query->where('status', 1);
	}

	public function scopeExpired($query) 
	{
      	return $query->where('poll_end', '<', Carbon::today()->format('Y-m-d'));
   	}

	public function scopeNotExpired($query) 
	{
		return $query->where(function($qry){
			$qry
			// ->where(function($qry){
			// 	$qry->whereNull('poll_start')
			// 	    ->orWhere('poll_start', '>=', Carbon::today()->format('Y-m-d'));
			// })
			->where(function($qry){
				$qry->whereNull('poll_end')
				    ->orWhere('poll_end', '>=', Carbon::today()->format('Y-m-d'));
			});
		});
   	}

	public function scopeInDate($query, $key, $value) 
	{
		if($key == "year") {
			return $query->whereYear('poll_start', $value)
					->whereYear('poll_end', $value);
		} else if($key == "month") {
			return $query->whereMonth('poll_start', $value)
					->whereMonth('poll_end', $value);
		}
	}

	public function knowledge()
	{
		return $this->belongsTo('App\Models\Knowledge');
	}

	public function pollOptions()
	{
		return $this->hasMany('App\Models\PollOption');
	}

	public function pollVotes()
	{
		return $this->hasMany('App\Models\PollVote');
	}


	public function categories()
	{
		return $this->belongsToMany('App\Models\PollCategory', 'ref_poll_categories', 'poll_id', 'poll_category_id')
			->using('App\Models\RefPollCategory');
	}

}
