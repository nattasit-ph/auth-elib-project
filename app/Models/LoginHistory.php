<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LoginHistory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeMyOrg($query)
    {
        return $query->where('login_histories.user_org_id', Auth::user()->user_org_id);
    }
}
