<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ConsentControl extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'consent_control';

    protected static function boot() {
    	parent::boot();
    	static::addGlobalScope('order', function (Builder $builder) {
     		$builder->orderBy('version', 'desc');
    	});
	}

    public function scopeMyOrg($query)
    {
        if (Auth::check())
            return $query->where('user_org_id', Auth::user()->user_org_id);
        else
            return $query->where('user_org_id', config('bookdose.default.user_org'));
    }

}
