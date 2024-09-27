<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FormField extends Model
{
	use HasFactory;
	protected $guarded = [];

   protected static function boot() {
    	parent::boot();
    	static::addGlobalScope('order', function (Builder $builder) {
     		$builder->orderBy('weight', 'asc');
    	});
	}

	protected $casts = [
		'options' => 'array',
	];

	public function form()
	{
		return $this->belongsTo('App\Models\Form');
	}

}
