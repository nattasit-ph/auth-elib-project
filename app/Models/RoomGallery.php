<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RoomGallery extends Model
{
	use HasFactory;
	protected $guarded = [];

   protected static function boot() {
    	parent::boot();
    	static::addGlobalScope('order', function (Builder $builder) {
     		$builder->orderBy('is_cover', 'desc')
     			->orderBy('id', 'asc');
    	});
	}

	public function scopeActive($query) 
	{
		return $query->where('is_cover', 1);
	}

	public function room()
	{
		return $this->belongsTo('App\Models\Room');
	}

	public function room_bookings()
	{
		return $this->belongsTo('App\Models\RoomBooking');
	}

}
