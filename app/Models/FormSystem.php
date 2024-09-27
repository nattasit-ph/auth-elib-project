<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;
use Auth;

class FormSystem extends Pivot
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'form_systems';
    public $timestamps = false;

	public function form()
	{
		return $this->belongsTo('App\Models\Form');
	}

    public function scopeMyOrg($query)
	{
   	if (Auth::check())
   		return $query->where('user_org_id', Auth::user()->user_org_id);
   	else
   		return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}



}
