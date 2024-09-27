<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
	use HasFactory;
	protected $guarded = [];

	public function poll()
	{
		return $this->belongsTo('App\Models\Poll');
	}

	public function pollOptions()
	{
		return $this->belongsTo('App\Models\PollOption');
	}

}
