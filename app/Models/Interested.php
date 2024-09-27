<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Interested extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'interested_topic';

    public function getStatusShowAttribute()
    {
        if ($this->status == 1)
            return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
        else
            return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
    }

    public function getStatusActionAttribute()
    {
        if ($this->status == 1)
            return '
				    <span class="dropdown">
		                <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
		                    <i class="la la-ellipsis-h"></i>
		                </a>
		                <div class="dropdown-menu dropdown-menu-right">
		                    <a class="dropdown-item" href="javascript:;" data-id=' . json_encode($this->id) . ' data-status=' . json_encode($this->status) . ' data-title=' . json_encode($this->title, JSON_UNESCAPED_UNICODE) . ' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
		                    <a class="dropdown-item" href="javascript:;" data-id=' . json_encode($this->id) . ' data-title=' . json_encode($this->title, JSON_UNESCAPED_UNICODE) . ' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
		                </div>
		            </span>';
        else
            return '
				    <span class="dropdown">
		                <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
		                    <i class="la la-ellipsis-h"></i>
		                </a>
		                <div class="dropdown-menu dropdown-menu-right">
		                    <a class="dropdown-item" href="javascript:;" data-id=' . json_encode($this->id) . ' data-status=' . json_encode($this->status) . ' data-title=' . json_encode($this->title, JSON_UNESCAPED_UNICODE) . ' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
		                    <a class="dropdown-item" href="javascript:;" data-id=' . json_encode($this->id) . ' data-title=' . json_encode($this->title, JSON_UNESCAPED_UNICODE) . ' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
		                </div>
		            </span>';
    }

    public function scopeMyOrg($query)
	{
		if (Auth::check())
			return $query->where('user_org_id', Auth::user()->user_org_id);
		else
			return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

	public function scopeActive($query) 
	{
     	return $query->where('status', 1);
 	}
}
