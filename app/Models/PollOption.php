<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;
    protected $guarded = [];

	public function scopeActive($query) 
	{
		return $query->where('status', 1);
	}

	public function poll()
	{
		return $this->belongsTo('App\Models\Poll');
	}

	public function pollVotes()
	{
		return $this->hasMany('App\Models\PollVote');
	}

	public function pollOptions()
	{
	  return $this->hasMany('App\Models\PollOption');
	}

}
