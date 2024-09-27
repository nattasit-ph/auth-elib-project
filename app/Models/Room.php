<?php

namespace App\Models;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
 	protected $guarded = [];
    protected $casts = [
        'facilities' => 'array'
    ];
	
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

    public function scopeSortTitle($query) 
	{
        return $query->orderBy('title', 'asc');
    }

    public function room_galleries()
    {
        return $this->hasMany('App\Models\RoomGallery');
    }

    public function room_bookings()
    {
        return $this->hasMany('App\Models\RoomBooking');
    }

	public function room_type()
	{
		return $this->belongsTo('App\Models\RoomType');
	}

}
