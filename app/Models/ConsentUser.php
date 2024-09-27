<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ConsentUser extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'consent_user';


    public function scopeMyOrg($query)
    {
        if (Auth::check())
            return $query->where('user_org_id', Auth::user()->user_org_id);
        else
            return $query->where('user_org_id', config('bookdose.default.user_org'));
    }

}
