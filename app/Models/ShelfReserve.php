<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShelfReserve extends Model
{
	use HasFactory,SoftDeletes;

	protected $guarded = [];
	protected $dates = ['approved_at', 'received_at', 'expired_at', 'created_at', 'updated_at', 'deleted_at'];

	public function getStatusNameAttribute()
	{
		switch ($this->status) {
			case '0': return 'ยกเลิก'; break;
			case '1': return 'รอคิว'; break;
			case '2': return 'รอรับหนังสือ'; break;
			case '3': return 'รับแล้ว'; break;
			default: return 'N/A'; break;
		}
	}

	public function getExpiredAtDisplayAttribute()
	{
		return $this->expired_at ? $this->expired_at->format(config('bookdose.default.datetime_format')) : '';
	}

	public function scopeMyReserve($query)
	{
		if (Auth::check()){
			return  $query->where('user_id', Auth::user()->id);
		}else{
			return $query->where('user_id', null);
		}
	}

	public function scopeMyOrg($query)
	{
		if (Auth::check())
			return $query->where('user_org_id', Auth::user()->user_org_id);
		else
			return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}

	public function productMain()
	{
		return $this->belongsTo('App\Models\ProductMain');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
