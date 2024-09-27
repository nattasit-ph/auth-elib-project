<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class userOrgSetting extends Model
{
    use HasFactory;
    protected $fillable = ['user_org_id','slug','data_value'];

    protected $casts = [
        'data_value' => 'array',
    ];

    public function scopeOfOrg($query, $user_org_id)
    {
        return $query->where('user_org_id', $user_org_id);
    }

    public function ScopeMyOrg($query)
    {
        if (Auth::check())
            return $query->where('user_org_id', Auth::user()->user_org_id);
        else
            return 0;
    }
}
