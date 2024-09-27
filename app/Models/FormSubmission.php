<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
	use HasFactory;
	protected $guarded = [];

	protected $casts = [
		'data_fields' => 'array',
	];

	public function form()
	{
		return $this->belongsTo('App\Models\Form');
	}

	public function creator()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}

}
