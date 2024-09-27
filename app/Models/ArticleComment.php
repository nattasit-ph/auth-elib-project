<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
 	use HasFactory;
  	protected $guarded = [];

	public function article()
	{
		return $this->belongsTo('App\Models\Article');
	}

	public function creator()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}

	public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

}
