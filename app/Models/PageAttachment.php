<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageAttachment extends Model
{
 	use HasFactory;
  	protected $guarded = [];

	public function page()
	{
		return $this->belongsTo('App\Models\Page');
	}
}
