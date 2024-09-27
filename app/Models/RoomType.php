<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class RoomType extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function rooms()
    {
        return $this->hasMany('App\Models\Room');
    }

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
}
